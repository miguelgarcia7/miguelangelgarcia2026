<?php

use App\Models\AiKnowledgeEntry;
use App\Services\Ai\Data\Classification;
use App\Services\Ai\Data\QuestionType;
use App\Services\Ai\KeywordKnowledgeRetriever;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function classify(array $terms = [], array $categories = [], ?string $standalone = null): Classification
{
    return new Classification(QuestionType::AboutMe, $categories, $terms, $standalone);
}

test('tag and category matches outrank body-only matches', function () {
    $body = AiKnowledgeEntry::factory()->create(['title' => 'Misc', 'tags' => [], 'categories' => ['Career'], 'content' => 'Once there was a deadline.']);
    $tagged = AiKnowledgeEntry::factory()->create(['title' => 'Tight timeline', 'tags' => ['deadline'], 'categories' => ['Career'], 'content' => 'x']);
    $categorised = AiKnowledgeEntry::factory()->create(['title' => 'Story', 'tags' => [], 'categories' => ['Leadership'], 'content' => 'nothing relevant']);

    $hits = app(KeywordKnowledgeRetriever::class)->retrieve('Tell me about a difficult deadline', classify(['deadline'], ['Leadership']), 5);

    expect($hits->map(fn ($h) => $h->entry->id)->all())->toBe([$categorised->id, $tagged->id, $body->id]);
});

test('the category boost applies once no matter how many categories match', function () {
    $many = AiKnowledgeEntry::factory()->create(['title' => 'A', 'tags' => [], 'categories' => ['Leadership', 'Management', 'Career'], 'content' => 'x']);
    $one = AiKnowledgeEntry::factory()->create(['title' => 'B', 'tags' => [], 'categories' => ['Leadership'], 'content' => 'x']);

    $hits = app(KeywordKnowledgeRetriever::class)->retrieve('leadership?', classify([], ['Leadership', 'Management']), 5);

    expect($hits)->toHaveCount(2)
        ->and($hits[0]->score)->toBe($hits[1]->score)
        ->and($hits->pluck('entry.id')->sort()->values()->all())->toBe([$many->id, $one->id]);
});

test('plural and inflected words still match', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Managing deadlines', 'tags' => ['deadlines'], 'content' => 'Managed developers.']);

    $hits = app(KeywordKnowledgeRetriever::class)->retrieve('How does Miguel manage a deadline?', classify(['deadline', 'manage']), 5);

    expect($hits)->toHaveCount(1)
        ->and($hits[0]->scoreBreakdown)->toHaveKeys(['tags', 'title', 'body']);
});

test('entries below the minimum score and inactive entries are left out', function () {
    AiKnowledgeEntry::factory()->create(['title' => 'Nothing related', 'tags' => [], 'content' => 'Cooking pasta.']);
    AiKnowledgeEntry::factory()->inactive()->create(['title' => 'Kubernetes', 'tags' => ['kubernetes'], 'content' => 'k8s']);

    $hits = app(KeywordKnowledgeRetriever::class)->retrieve('Kubernetes?', classify(['kubernetes']), 5);

    expect($hits)->toBeEmpty();
});

test('importance lifts otherwise equal entries and the limit is respected', function () {
    $low = AiKnowledgeEntry::factory()->create(['title' => 'A', 'tags' => ['laravel'], 'content' => 'x', 'importance' => 1]);
    $high = AiKnowledgeEntry::factory()->create(['title' => 'B', 'tags' => ['laravel'], 'content' => 'x', 'importance' => 5]);
    AiKnowledgeEntry::factory()->create(['title' => 'C', 'tags' => ['laravel'], 'content' => 'x', 'importance' => 3]);

    $hits = app(KeywordKnowledgeRetriever::class)->retrieve('laravel', classify(['laravel']), 2);

    expect($hits)->toHaveCount(2)
        ->and($hits[0]->entry->id)->toBe($high->id)
        ->and($hits->pluck('entry.id'))->not->toContain($low->id);
});

test('the standalone rewrite of a follow-up contributes search terms', function () {
    $hub = AiKnowledgeEntry::factory()->create(['title' => 'Appointment Hub', 'tags' => ['appointment-hub'], 'content' => 'Booking.']);

    $hits = app(KeywordKnowledgeRetriever::class)->retrieve('What was the hardest part?', classify([], [], 'What was the hardest part of building Appointment Hub?'), 5);

    expect($hits)->toHaveCount(1)->and($hits[0]->entry->id)->toBe($hub->id);
});

test('the retrieval cache is cleared when an entry changes', function () {
    $retriever = app(KeywordKnowledgeRetriever::class);
    expect($retriever->retrieve('laravel', classify(['laravel']), 5))->toBeEmpty();

    $entry = AiKnowledgeEntry::factory()->create(['tags' => ['laravel'], 'content' => 'x']);
    expect($retriever->retrieve('laravel', classify(['laravel']), 5))->toHaveCount(1);

    $entry->update(['is_active' => false]);
    expect($retriever->retrieve('laravel', classify(['laravel']), 5))->toBeEmpty();
});
