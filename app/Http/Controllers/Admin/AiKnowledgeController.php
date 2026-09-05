<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AiKnowledgeRequest;
use App\Models\AiKnowledgeEntry;
use App\Services\Ai\Data\Classification;
use App\Services\Ai\Data\RetrievedEntry;
use App\Services\Ai\KeywordKnowledgeRetriever;
use App\Services\Ai\PromptBuilder;
use App\Services\Ai\QuestionClassifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AiKnowledgeController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'category' => (string) $request->query('category', ''),
            'status' => in_array($request->query('status'), ['active', 'inactive'], true) ? $request->query('status') : 'all',
            'kind' => in_array($request->query('kind'), AiKnowledgeEntry::KINDS, true) ? $request->query('kind') : '',
        ];

        $entries = AiKnowledgeEntry::query()
            ->when($filters['search'], function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
                    $q->where('title', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('content', 'like', $like)
                        ->orWhere('tags', 'like', $like);
                });
            })
            ->when($filters['category'], fn ($query, string $category) => $query->where('category', $category))
            ->when($filters['status'] === 'active', fn ($query) => $query->where('is_active', true))
            ->when($filters['status'] === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($filters['kind'], fn ($query, string $kind) => $query->where('kind', $kind))
            ->orderByDesc('importance')
            ->orderBy('title')
            ->paginate(25)
            ->withQueryString()
            ->through(fn (AiKnowledgeEntry $entry) => [
                'id' => $entry->id,
                'title' => $entry->title,
                'slug' => $entry->slug,
                'category' => $entry->category,
                'kind' => $entry->kind,
                'summary' => $entry->summary,
                'tags' => $entry->tags ?? [],
                'importance' => $entry->importance,
                'is_active' => $entry->is_active,
                'updated_at' => $entry->updated_at?->toIso8601String(),
            ]);

        return Inertia::render('Knowledge/Index', [
            'entries' => $entries,
            'filters' => $filters,
            'categories' => $this->categories(),
            'stats' => [
                'total' => AiKnowledgeEntry::count(),
                'active' => AiKnowledgeEntry::query()->active()->count(),
                'star' => AiKnowledgeEntry::where('kind', AiKnowledgeEntry::KIND_STAR)->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Knowledge/Form', [
            'entry' => null,
            'categories' => $this->categories(),
        ]);
    }

    public function store(AiKnowledgeRequest $request): RedirectResponse
    {
        $entry = AiKnowledgeEntry::create($request->validated());

        return redirect()
            ->route('admin.knowledge.edit', $entry)
            ->with('success', 'Entry created.');
    }

    public function edit(AiKnowledgeEntry $entry): Response
    {
        return Inertia::render('Knowledge/Form', [
            'entry' => $this->entryProps($entry),
            'categories' => $this->categories(),
        ]);
    }

    public function update(AiKnowledgeRequest $request, AiKnowledgeEntry $entry): RedirectResponse
    {
        $entry->update($request->validated());

        return back()->with('success', 'Entry saved.');
    }

    public function toggle(AiKnowledgeEntry $entry): RedirectResponse
    {
        $entry->update(['is_active' => ! $entry->is_active]);

        return back()->with('success', $entry->is_active ? 'Entry activated.' : 'Entry deactivated — the assistant will no longer use it.');
    }

    public function destroy(AiKnowledgeEntry $entry): RedirectResponse
    {
        $entry->delete();

        return redirect()
            ->route('admin.knowledge.index')
            ->with('success', 'Entry deleted.');
    }

    /**
     * Show how an entry reaches the assistant: the text the model would
     * see, and where the entry ranks for a sample question.
     */
    public function preview(Request $request, AiKnowledgeEntry $entry, KeywordKnowledgeRetriever $retriever, QuestionClassifier $classifier, PromptBuilder $prompts): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['nullable', 'string', 'max:600'],
        ]);

        $question = trim((string) ($validated['question'] ?? ''));
        $payload = [
            'prompt_text' => $entry->toPromptText(),
            'is_active' => $entry->is_active,
            'question' => $question,
            'classification' => null,
            'classification_source' => null,
            'ranking' => [],
            'would_be_used' => null,
        ];

        if ($question !== '') {
            try {
                $classification = $classifier->classify($question);
                $payload['classification_source'] = $classification->fromFallback ? 'keywords' : 'model';
            } catch (Throwable) {
                $classification = Classification::fallback($question);
                $payload['classification_source'] = 'keywords';
            }

            $limit = (int) config('ai.retrieval.limit');
            $minScore = (float) config('ai.retrieval.min_score');
            $ranked = $retriever->scoreAll($question, $classification);

            $payload['classification'] = $classification->toArray();
            $payload['ranking'] = $ranked
                ->take(10)
                ->map(fn (RetrievedEntry $hit, int $position) => [
                    'id' => $hit->entry->id,
                    'title' => $hit->entry->title,
                    'score' => $hit->score,
                    'breakdown' => $hit->scoreBreakdown,
                    'selected' => $position < $limit && $hit->score >= $minScore,
                    'is_this' => $hit->entry->is($entry),
                ])
                ->values()
                ->all();

            $payload['would_be_used'] = $entry->is_active
                && collect($payload['ranking'])->contains(fn (array $row) => $row['is_this'] && $row['selected']);
        }

        return response()->json($payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function entryProps(AiKnowledgeEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'title' => $entry->title,
            'slug' => $entry->slug,
            'category' => $entry->category,
            'kind' => $entry->kind,
            'summary' => $entry->summary ?? '',
            'content' => $entry->content ?? '',
            'situation' => $entry->situation ?? '',
            'task' => $entry->task ?? '',
            'action' => $entry->action ?? '',
            'result' => $entry->result ?? '',
            'tags' => $entry->tags ?? [],
            'importance' => $entry->importance,
            'is_active' => $entry->is_active,
            'updated_at' => $entry->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Suggested categories plus every category already in use.
     *
     * @return array<int, string>
     */
    private function categories(): array
    {
        return collect(config('ai.categories'))
            ->merge(AiKnowledgeEntry::query()->distinct()->orderBy('category')->pluck('category'))
            ->unique()
            ->values()
            ->all();
    }
}
