<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeEntry;
use App\Services\Ai\Contracts\KnowledgeRetriever;
use App\Services\Ai\Data\Classification;
use App\Services\Ai\Data\RetrievedEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Phase 1 retrieval: scores active entries on category, tag, title and
 * body overlap with the question and the classifier's search terms, then
 * weights by importance. Runs in PHP so it behaves the same on every
 * database; the knowledge base is dozens of entries, not thousands.
 */
class KeywordKnowledgeRetriever implements KnowledgeRetriever
{
    private const WEIGHT_CATEGORY = 2.0;

    private const WEIGHT_TAG = 1.5;

    private const WEIGHT_TITLE = 1.2;

    private const WEIGHT_SUMMARY = 0.6;

    private const WEIGHT_BODY = 0.5;

    /** Words too common to say anything about relevance. */
    private const STOP_WORDS = [
        'a', 'an', 'the', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'do', 'does', 'did', 'has', 'have',
        'had', 'what', 'which', 'who', 'whom', 'how', 'why', 'when', 'where', 'tell', 'me', 'about', 'miguel',
        'miguels', 'you', 'your', 'yours', 'his', 'he', 'him', 'with', 'of', 'in', 'on', 'for', 'to', 'and', 'or',
        'at', 'from', 'by', 'as', 'it', 'its', 'this', 'that', 'these', 'those', 'can', 'could', 'would', 'should',
        'i', 'my', 'some', 'any', 'like', 'kind', 'much', 'many', 'more', 'most', 'very', 'so', 'if', 'than',
        'then', 'there', 'their', 'they', 'them', 'into', 'out', 'up', 'down', 'over', 'under', 'please', 'give',
        'say', 'know', 'think', 'want', 'get', 'got', 'one', 'time', 'something', 'anything', 'everything',
        // Verbs that appear in almost every question and every entry.
        'work', 'works', 'worked', 'working', 'use', 'used', 'using', 'uses', 'build', 'built', 'building', 'make',
        'made', 'done', 'doing', 'ever', 'really', 'well', 'good', 'best', 'also',
    ];

    public function retrieve(string $question, Classification $classification, int $limit): Collection
    {
        $terms = $this->terms($question, $classification);
        $categories = array_map('mb_strtolower', $classification->categories);
        $minScore = (float) config('ai.retrieval.min_score');

        return $this->activeEntries()
            ->map(fn (AiKnowledgeEntry $entry) => $this->score($entry, $terms, $categories))
            ->filter(fn (RetrievedEntry $hit) => $hit->score >= $minScore)
            ->sortByDesc(fn (RetrievedEntry $hit) => $hit->score)
            ->take($limit)
            ->values();
    }

    /**
     * Every active entry with a score, best first, for the admin preview.
     *
     * @return Collection<int, RetrievedEntry>
     */
    public function scoreAll(string $question, Classification $classification): Collection
    {
        $terms = $this->terms($question, $classification);
        $categories = array_map('mb_strtolower', $classification->categories);

        return $this->activeEntries()
            ->map(fn (AiKnowledgeEntry $entry) => $this->score($entry, $terms, $categories))
            ->sortByDesc(fn (RetrievedEntry $hit) => $hit->score)
            ->values();
    }

    /**
     * Active entries, cached as plain rows. Models are rebuilt on read
     * because cache stores cannot be trusted to unserialize objects (the
     * database store hands back an incomplete class).
     *
     * @return Collection<int, AiKnowledgeEntry>
     */
    private function activeEntries(): Collection
    {
        $rows = Cache::remember(
            config('ai.retrieval.cache_key'),
            now()->addHours(12),
            fn () => AiKnowledgeEntry::query()->active()->orderByDesc('importance')->get()->map->getAttributes()->all(),
        );

        return AiKnowledgeEntry::hydrate(is_array($rows) ? $rows : []);
    }

    /**
     * @param  array<int, string>  $terms
     * @param  array<int, string>  $categories
     */
    private function score(AiKnowledgeEntry $entry, array $terms, array $categories): RetrievedEntry
    {
        $breakdown = [];

        // Applied once however many of the entry's categories match, so
        // listing many categories cannot inflate a score.
        $entryCategories = array_map('mb_strtolower', $entry->categories ?? []);
        if ($categories !== [] && array_intersect($entryCategories, $categories) !== []) {
            $breakdown['category'] = self::WEIGHT_CATEGORY;
        }

        $tagStems = $this->stems(implode(' ', $entry->tags ?? []));
        $titleStems = $this->stems($entry->title);
        $summaryStems = $this->stems((string) $entry->summary);
        $bodyStems = $this->stems($entry->body());

        $tagHits = count(array_intersect($terms, $tagStems));
        $titleHits = count(array_intersect($terms, $titleStems));
        $summaryHits = count(array_intersect($terms, $summaryStems));
        $bodyHits = count(array_intersect($terms, $bodyStems));

        if ($tagHits) {
            $breakdown['tags'] = $tagHits * self::WEIGHT_TAG;
        }
        if ($titleHits) {
            $breakdown['title'] = $titleHits * self::WEIGHT_TITLE;
        }
        if ($summaryHits) {
            $breakdown['summary'] = $summaryHits * self::WEIGHT_SUMMARY;
        }
        if ($bodyHits) {
            $breakdown['body'] = $bodyHits * self::WEIGHT_BODY;
        }

        $raw = array_sum($breakdown);

        // Importance 1..5 becomes a 0.9..1.3 multiplier: it can break ties
        // and lift key entries, but never turns a non-match into a match.
        $multiplier = 0.8 + ($entry->importance * 0.1);
        $score = $raw > 0 ? round($raw * $multiplier, 3) : 0.0;

        if ($raw > 0) {
            $breakdown['importance_multiplier'] = $multiplier;
        }

        return new RetrievedEntry($entry, $score, $breakdown);
    }

    /**
     * Unique search stems from the question, the standalone rewrite, and
     * the classifier's search terms.
     *
     * @return array<int, string>
     */
    private function terms(string $question, Classification $classification): array
    {
        $text = implode(' ', array_filter([
            $question,
            $classification->standaloneQuestion,
            implode(' ', $classification->searchTerms),
        ]));

        return $this->stems($text);
    }

    /**
     * Lower-cased, de-duplicated, stop-word-free stems of a text.
     *
     * @return array<int, string>
     */
    private function stems(string $text): array
    {
        $words = preg_split('/[^\p{L}\p{N}+#.]+/u', mb_strtolower($text)) ?: [];

        return collect($words)
            ->map(fn (string $word) => trim($word, '.'))
            ->filter(fn (string $word) => mb_strlen($word) >= 2 && ! in_array($word, self::STOP_WORDS, true))
            ->map(fn (string $word) => $this->stem($word))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * A deliberately small stemmer: enough that "deadlines" meets
     * "deadline" and "managing" meets "manage" without a dependency.
     */
    private function stem(string $word): string
    {
        if (mb_strlen($word) <= 4) {
            return $word;
        }

        foreach (['ations', 'ation', 'ing', 'ies', 'ers', 'er', 'ed', 'es', 's'] as $suffix) {
            if (str_ends_with($word, $suffix) && mb_strlen($word) - strlen($suffix) >= 3) {
                $word = substr($word, 0, -strlen($suffix));
                if ($suffix === 'ies') {
                    $word .= 'y';
                }
                break;
            }
        }

        // A trailing "e" is dropped so "manage" and "managing" agree.
        if (mb_strlen($word) > 4 && str_ends_with($word, 'e')) {
            $word = substr($word, 0, -1);
        }

        return $word;
    }
}
