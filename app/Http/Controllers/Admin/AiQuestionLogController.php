<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiKnowledgeEntry;
use App\Models\AiQuestionLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Read-only view of what visitors ask. Full analytics are a later phase;
 * this exists so unanswered questions are visible from day one.
 */
class AiQuestionLogController extends Controller
{
    public function index(Request $request): Response
    {
        $filter = in_array($request->query('filter'), ['unanswered', 'errors'], true) ? $request->query('filter') : 'all';

        $logs = AiQuestionLog::query()
            ->when($filter === 'unanswered', fn ($q) => $q->where('answered', false)->whereNull('error'))
            ->when($filter === 'errors', fn ($q) => $q->whereNotNull('error'))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        $entryTitles = AiKnowledgeEntry::query()
            ->whereIn('id', $logs->getCollection()->flatMap(fn ($log) => $log->retrieved_entry_ids ?? [])->unique())
            ->pluck('title', 'id');

        $logs->through(fn (AiQuestionLog $log) => [
            'id' => $log->id,
            'question' => $log->question,
            'question_type' => $log->question_type,
            'answered' => $log->answered,
            'answer' => $log->answer,
            'sources' => collect($log->retrieved_entry_ids ?? [])->map(fn ($id) => $entryTitles[$id] ?? "#{$id}")->all(),
            'model' => $log->model,
            'input_tokens' => $log->input_tokens,
            'output_tokens' => $log->output_tokens,
            'estimated_cost' => (float) $log->estimated_cost,
            'duration_ms' => $log->duration_ms,
            'error' => $log->error,
            'created_at' => $log->created_at?->toIso8601String(),
        ]);

        return Inertia::render('Questions/Index', [
            'logs' => $logs,
            'filter' => $filter,
            'stats' => [
                'total' => AiQuestionLog::count(),
                'unanswered' => AiQuestionLog::where('answered', false)->whereNull('error')->count(),
                'errors' => AiQuestionLog::whereNotNull('error')->count(),
                'cost' => round((float) AiQuestionLog::sum('estimated_cost'), 4),
            ],
        ]);
    }
}
