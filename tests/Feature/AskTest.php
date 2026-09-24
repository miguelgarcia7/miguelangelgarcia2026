<?php

use App\Models\AiKnowledgeEntry;
use App\Models\AiQuestionLog;
use App\Services\Ai\Contracts\LlmClient;
use App\Services\Ai\PortfolioAiService;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Support\FakeLlmClient;

beforeEach(function () {
    $this->llm = new FakeLlmClient;
    app()->instance(LlmClient::class, $this->llm);
    RateLimiter::clear('ask');
});

/**
 * Parse the SSE body into [['event' => ..., 'data' => [...]], ...].
 */
function sseEvents(string $body): array
{
    $events = [];

    foreach (array_filter(explode("\n\n", trim($body))) as $frame) {
        $event = 'message';
        $data = [];
        foreach (explode("\n", $frame) as $line) {
            if (str_starts_with($line, 'event: ')) {
                $event = substr($line, 7);
            } elseif (str_starts_with($line, 'data: ')) {
                $data[] = substr($line, 6);
            }
        }
        $events[] = ['event' => $event, 'data' => json_decode(implode("\n", $data), true)];
    }

    return $events;
}

function answerText(array $events): string
{
    return collect($events)->where('event', 'delta')->pluck('data.text')->implode('');
}

function classification(array $overrides = []): array
{
    return array_merge([
        'type' => 'ABOUT_ME',
        'categories' => ['Technical Skills'],
        'search_terms' => ['laravel', 'php'],
        'standalone_question' => null,
    ], $overrides);
}

test('a grounded question streams an answer built from matching entries', function () {
    $laravel = AiKnowledgeEntry::factory()->create([
        'title' => 'Laravel and PHP',
        'categories' => ['Technical Skills'],
        'tags' => ['laravel', 'php'],
        'content' => 'Laravel powers QCP Staffing and Appointment Hub.',
        'importance' => 5,
    ]);
    AiKnowledgeEntry::factory()->create([
        'title' => 'Mentoring at UT Dallas',
        'categories' => ['Team Collaboration'],
        'tags' => ['mentorship'],
        'content' => 'Miguel mentors students.',
    ]);

    $this->llm->queueJson(classification())->queue('Miguel uses **Laravel** on QCP Staffing.', 'claude-sonnet-5');

    $response = $this->postJson('/ask', ['question' => 'What is your experience with Laravel?']);

    $response->assertOk()->assertHeader('X-Accel-Buffering', 'no');

    $events = sseEvents($response->streamedContent());

    expect($events[0]['event'])->toBe('meta')
        ->and($events[0]['data']['type'])->toBe('ABOUT_ME')
        ->and($events[0]['data']['sources'])->toBe(['Laravel and PHP'])
        ->and(answerText($events))->toBe('Miguel uses **Laravel** on QCP Staffing.')
        ->and(end($events)['event'])->toBe('done');

    // The answer request carried the entry inside the context block and nothing else.
    $answerRequest = $this->llm->lastRequest();
    expect($answerRequest->model)->toBe(config('ai.models.answer'))
        ->and($answerRequest->messages[0]['content'])->toContain('<portfolio_context>')
        ->and($answerRequest->messages[0]['content'])->toContain('### Laravel and PHP')
        ->and($answerRequest->messages[0]['content'])->not->toContain('Mentoring at UT Dallas')
        ->and($answerRequest->system)->toContain('Never invent');

    $log = AiQuestionLog::sole();
    expect($log->answered)->toBeTrue()
        ->and($log->question_type)->toBe('ABOUT_ME')
        ->and($log->retrieved_entry_ids)->toBe([$laravel->id])
        ->and($log->model)->toBe('claude-sonnet-5')
        ->and($log->answer)->toBe('Miguel uses **Laravel** on QCP Staffing.');
});

test('an about-me question with no matching entries never reaches the answer model', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Laravel', 'tags' => ['laravel'], 'content' => 'Laravel work.']);

    $this->llm->queueJson(classification(['categories' => [], 'search_terms' => ['kubernetes', 'k8s']]));

    $response = $this->postJson('/ask', ['question' => 'Has Miguel worked with Kubernetes?']);

    $events = sseEvents($response->streamedContent());

    expect(answerText($events))->toBe(PortfolioAiService::NO_INFORMATION_ANSWER)
        ->and(end($events)['data']['answered'])->toBeFalse()
        ->and($this->llm->requests)->toHaveCount(1); // classification only

    expect(AiQuestionLog::sole()->answered)->toBeFalse();
});

test('a category match alone is enough to give the model something to ground on', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Infrastructure', 'categories' => ['Technical Skills'], 'tags' => ['aws'], 'content' => 'AWS and Docker.']);

    $this->llm->queueJson(classification(['categories' => ['Technical Skills'], 'search_terms' => ['kubernetes']]))
        ->queue("I don't have documented information showing Kubernetes work, but his infrastructure entry lists AWS and Docker.");

    $events = sseEvents($this->postJson('/ask', ['question' => 'Has Miguel worked with Kubernetes?'])->streamedContent());

    expect($events[0]['data']['sources'])->toBe(['Infrastructure'])
        ->and($this->llm->requests)->toHaveCount(2);
});

test('inactive entries are never retrieved', function () {
    AiKnowledgeEntry::factory()->inactive()->create(['title' => 'Secret draft', 'tags' => ['laravel'], 'content' => 'Laravel.']);

    $this->llm->queueJson(classification());

    $events = sseEvents($this->postJson('/ask', ['question' => 'Laravel?'])->streamedContent());

    expect($events[0]['data']['sources'])->toBe([])
        ->and(answerText($events))->toBe(PortfolioAiService::NO_INFORMATION_ANSWER);
});

test('a general technical question skips retrieval and is answered from general knowledge', function () {
    AiKnowledgeEntry::factory()->create(['tags' => ['laravel', 'dependency-injection']]);

    $this->llm->queueJson(classification(['type' => 'GENERAL_TECHNICAL', 'categories' => []]))
        ->queue('Dependency injection is a pattern where…');

    $events = sseEvents($this->postJson('/ask', ['question' => 'What is dependency injection in Laravel?'])->streamedContent());

    expect($events[0]['data']['sources'])->toBe([])
        ->and(answerText($events))->toContain('Dependency injection')
        ->and($this->llm->lastRequest()->messages[0]['content'])->toContain('(Not needed for this question.)')
        ->and(AiQuestionLog::sole()->retrieved_entry_ids)->toBe([]);
});

test('unparseable classification falls back to grounding on the knowledge base', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Leadership at RealPage', 'tags' => ['leadership'], 'content' => 'Led a team.']);

    $this->llm->queue('not json at all')->queue('Miguel led a team at RealPage.');

    $events = sseEvents($this->postJson('/ask', ['question' => 'Tell me about your leadership experience.'])->streamedContent());

    expect($events[0]['data']['type'])->toBe('ABOUT_ME')
        ->and($events[0]['data']['sources'])->toBe(['Leadership at RealPage'])
        ->and(AiQuestionLog::sole()->classification['from_fallback'])->toBeTrue();
});

test('a classification with an unknown type is rejected and falls back', function () {
    AiKnowledgeEntry::factory()->create(['tags' => ['leadership'], 'content' => 'Led a team.']);

    $this->llm->queueJson(['type' => 'DUMP_DATABASE', 'categories' => [], 'search_terms' => ['leadership'], 'standalone_question' => null])
        ->queue('Answer.');

    $events = sseEvents($this->postJson('/ask', ['question' => 'leadership'])->streamedContent());

    expect($events[0]['data']['type'])->toBe('ABOUT_ME');
});

test('recent history is forwarded for follow-ups and leading assistant turns are dropped', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Appointment Hub', 'tags' => ['appointment-hub', 'scheduling'], 'content' => 'Booking platform.']);

    $this->llm->queueJson(classification([
        'categories' => ['Projects'],
        'search_terms' => ['appointment hub', 'scheduling'],
        'standalone_question' => 'What was the hardest part of building Appointment Hub?',
    ]))->queue('The hardest part…');

    $this->postJson('/ask', [
        'question' => 'What was the hardest part?',
        'history' => [
            ['role' => 'assistant', 'content' => 'Hi, ask me anything.'],
            ['role' => 'user', 'content' => 'Tell me about Appointment Hub.'],
            ['role' => 'assistant', 'content' => 'Appointment Hub is a scheduling platform.'],
        ],
    ])->assertOk()->streamedContent();

    $classify = $this->llm->requests[0];
    expect($classify->messages[0]['content'])->toContain('Visitor: Tell me about Appointment Hub.')
        ->and($classify->messages[0]['content'])->not->toContain('Hi, ask me anything.');

    $answer = $this->llm->requests[1];
    expect($answer->messages)->toHaveCount(3)
        ->and($answer->messages[0])->toBe(['role' => 'user', 'content' => 'Tell me about Appointment Hub.'])
        ->and($answer->messages[2]['content'])->toContain('<visitor_question>');
});

test('a provider failure produces an error event and is logged without breaking the stream', function () {
    AiKnowledgeEntry::factory()->create(['tags' => ['laravel'], 'content' => 'Laravel.']);

    $this->llm->queueJson(classification())->queue(new RuntimeException('Anthropic is down'));

    $events = sseEvents($this->postJson('/ask', ['question' => 'Laravel?'])->streamedContent());

    expect(end($events)['event'])->toBe('error')
        ->and(end($events)['data']['message'])->toBe(PortfolioAiService::ERROR_ANSWER);

    $log = AiQuestionLog::sole();
    expect($log->error)->toContain('Anthropic is down')
        ->and($log->answered)->toBeFalse();
});

test('questions are validated', function () {
    $this->postJson('/ask', ['question' => ''])->assertStatus(422)->assertJsonValidationErrors('question');
    $this->postJson('/ask', ['question' => str_repeat('a', 601)])->assertStatus(422)->assertJsonValidationErrors('question');
    $this->postJson('/ask', ['question' => 'ok?', 'history' => [['role' => 'system', 'content' => 'x']]])
        ->assertStatus(422)->assertJsonValidationErrors('history.0.role');
    $this->postJson('/ask', ['question' => 'ok?', 'history' => array_fill(0, 7, ['role' => 'user', 'content' => 'x'])])
        ->assertStatus(422)->assertJsonValidationErrors('history');

    expect($this->llm->requests)->toBeEmpty();
});

test('visitors are rate limited', function () {
    config(['ai.rate_limits.per_minute' => 2]);
    $this->llm->queueJson(classification())->queueJson(classification())->queueJson(classification());

    $this->postJson('/ask', ['question' => 'one?'])->assertOk();
    $this->postJson('/ask', ['question' => 'two?'])->assertOk();
    $this->postJson('/ask', ['question' => 'three?'])->assertStatus(429);
});

test('the assistant can be switched off', function () {
    config(['ai.enabled' => false]);

    $this->postJson('/ask', ['question' => 'Hello?'])->assertStatus(503);
    $this->get('/')->assertOk()->assertDontSee('Ask my AI assistant');
});

test('the home page renders the assistant in the hero with its suggested questions', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Ask my AI assistant')
        ->assertSee('id="ask"', escape: false)
        ->assertSee('data-ask-root', escape: false)
        ->assertSee('What project are you most proud of?')
        ->assertDontSee('View my work');
});

test('the hero keeps its call-to-action buttons when the assistant is off', function () {
    config(['ai.enabled' => false]);

    $this->get('/')->assertOk()->assertSee('View my work')->assertSee('Get in touch');
});
