<?php

use App\Models\AiKnowledgeEntry;
use App\Models\AiQuestionLog;
use App\Models\User;
use App\Services\Ai\Contracts\LlmClient;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\FakeLlmClient;

beforeEach(function () {
    $this->owner = User::factory()->create(['email' => 'owner@example.com']);
});

test('the admin area requires sign-in', function () {
    $this->get('/admin/ai-knowledge')->assertRedirect('/admin/login');
    $this->get('/admin')->assertRedirect('/admin/login');
    $this->get('/admin/login')->assertOk()->assertSee('Sign in');
    $this->actingAs($this->owner)->get('/admin')->assertRedirect('/admin/ai-knowledge');
    $this->actingAs($this->owner)->get('/admin/login')->assertRedirect('/admin/ai-knowledge');
});

test('the owner can sign in and out', function () {
    $this->post('/admin/login', ['email' => 'owner@example.com', 'password' => 'wrong'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();

    $this->post('/admin/login', ['email' => 'owner@example.com', 'password' => 'password'])
        ->assertRedirect('/admin/ai-knowledge');
    $this->assertAuthenticatedAs($this->owner);

    $this->post('/admin/logout')->assertRedirect('/admin/login');
    $this->assertGuest();
});

test('the index lists and filters entries', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Laravel and PHP', 'categories' => ['Technical Skills']]);
    AiKnowledgeEntry::factory()->inactive()->create(['title' => 'Old draft', 'categories' => ['Career']]);

    $this->actingAs($this->owner)->get('/admin/ai-knowledge')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Knowledge/Index')
            ->has('entries.data', 2)
            ->where('stats.total', 2)
            ->where('stats.active', 1));

    $this->actingAs($this->owner)->get('/admin/ai-knowledge?search=laravel&status=active')
        ->assertInertia(fn (Assert $page) => $page
            ->has('entries.data', 1)
            ->where('entries.data.0.title', 'Laravel and PHP')
            ->where('filters.search', 'laravel'));
});

test('entries can be created with a generated slug and normalised tags', function () {
    $this->actingAs($this->owner)->post('/admin/ai-knowledge', [
        'title' => 'Laravel & PHP',
        'categories' => ['Technical Skills'],
        'kind' => 'general',
        'summary' => 'Primary backend.',
        'content' => 'Laravel everywhere.',
        'tags' => ['Laravel', ' PHP ', 'laravel', 'Team Lead'],
        'importance' => 5,
        'is_active' => true,
    ])->assertRedirect();

    $entry = AiKnowledgeEntry::sole();
    expect($entry->slug)->toBe('laravel-php')
        ->and($entry->categories)->toBe(['Technical Skills'])
        ->and($entry->tags)->toBe(['laravel', 'php', 'team-lead'])
        ->and($entry->importance)->toBe(5);
});

test('an entry needs at least one category and at most five, de-duplicated', function () {
    $base = ['title' => 'Categories', 'kind' => 'general', 'content' => 'x', 'tags' => [], 'importance' => 3, 'is_active' => true];

    $this->actingAs($this->owner)->post('/admin/ai-knowledge', $base + ['categories' => []])
        ->assertSessionHasErrors('categories');

    $this->actingAs($this->owner)->post('/admin/ai-knowledge', $base + ['categories' => ['A', 'B', 'C', 'D', 'E', 'F']])
        ->assertSessionHasErrors('categories');

    $this->actingAs($this->owner)->post('/admin/ai-knowledge', $base + ['categories' => [' Leadership ', 'leadership', 'Problem  Solving']])
        ->assertRedirect();

    expect(AiKnowledgeEntry::sole()->categories)->toBe(['Leadership', 'Problem Solving']);
});

test('the index filters by any of an entry\'s categories', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Story', 'categories' => ['Leadership', 'Problem Solving']]);
    AiKnowledgeEntry::factory()->create(['title' => 'Skills', 'categories' => ['Technical Skills']]);

    $this->actingAs($this->owner)->get('/admin/ai-knowledge?category=Problem+Solving')
        ->assertInertia(fn (Assert $page) => $page
            ->has('entries.data', 1)
            ->where('entries.data.0.title', 'Story')
            ->where('entries.data.0.categories', ['Leadership', 'Problem Solving']));
});

test('a STAR story needs all four parts', function () {
    $this->actingAs($this->owner)->post('/admin/ai-knowledge', [
        'title' => 'Tight deadline',
        'categories' => ['Leadership'],
        'kind' => 'star',
        'situation' => 'A launch date was fixed.',
        'tags' => [],
        'importance' => 3,
        'is_active' => true,
    ])->assertSessionHasErrors(['task', 'action', 'result'])->assertSessionDoesntHaveErrors('content');

    $this->actingAs($this->owner)->post('/admin/ai-knowledge', [
        'title' => 'Tight deadline',
        'categories' => ['Leadership'],
        'kind' => 'star',
        'situation' => 'S',
        'task' => 'T',
        'action' => 'A',
        'result' => 'R',
        'tags' => ['deadline'],
        'importance' => 4,
        'is_active' => true,
    ])->assertRedirect();

    expect(AiKnowledgeEntry::sole()->toPromptText())->toContain("Situation: S\n\nTask: T\n\nAction: A\n\nResult: R");
});

test('entries can be updated, toggled and deleted', function () {
    $entry = AiKnowledgeEntry::factory()->create(['title' => 'Before']);

    $this->actingAs($this->owner)->put("/admin/ai-knowledge/{$entry->id}", [
        'title' => 'After',
        'slug' => 'after',
        'categories' => ['Career'],
        'kind' => 'general',
        'content' => 'Updated.',
        'tags' => [],
        'importance' => 2,
        'is_active' => true,
    ])->assertRedirect()->assertSessionHas('success');

    expect($entry->refresh()->title)->toBe('After')->and($entry->slug)->toBe('after');

    $this->actingAs($this->owner)->patch("/admin/ai-knowledge/{$entry->id}/toggle")->assertRedirect();
    expect($entry->refresh()->is_active)->toBeFalse();

    $this->actingAs($this->owner)->delete("/admin/ai-knowledge/{$entry->id}")->assertRedirect('/admin/ai-knowledge');
    expect(AiKnowledgeEntry::count())->toBe(0);
});

test('the preview shows the prompt text and where the entry ranks', function () {
    $llm = (new FakeLlmClient)->queueJson([
        'type' => 'ABOUT_ME',
        'categories' => ['Leadership'],
        'search_terms' => ['deadline', 'pressure'],
        'standalone_question' => null,
    ]);
    app()->instance(LlmClient::class, $llm);

    $story = AiKnowledgeEntry::factory()->create(['title' => 'Tight deadline', 'categories' => ['Leadership'], 'tags' => ['deadline']]);
    AiKnowledgeEntry::factory()->create(['title' => 'Unrelated', 'categories' => ['Career'], 'tags' => [], 'content' => 'x']);

    $this->actingAs($this->owner)
        ->postJson("/admin/ai-knowledge/{$story->id}/preview", ['question' => 'Tell me about a difficult deadline'])
        ->assertOk()
        ->assertJsonPath('would_be_used', true)
        ->assertJsonPath('classification_source', 'model')
        ->assertJsonPath('classification.type', 'ABOUT_ME')
        ->assertJsonPath('ranking.0.title', 'Tight deadline')
        ->assertJsonPath('ranking.0.is_this', true)
        ->assertJsonPath('ranking.0.selected', true)
        ->assertJsonPath('ranking.1.selected', false)
        ->assertJsonPath('prompt_text', $story->toPromptText());
});

test('the question log page renders', function () {
    AiQuestionLog::factory()->create(['question' => 'Laravel?', 'answered' => false]);

    $this->actingAs($this->owner)->get('/admin/ai-questions?filter=unanswered')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Questions/Index')
            ->has('logs.data', 1)
            ->where('stats.unanswered', 1));
});
