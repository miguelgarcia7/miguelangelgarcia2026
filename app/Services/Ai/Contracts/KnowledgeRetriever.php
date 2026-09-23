<?php

namespace App\Services\Ai\Contracts;

use App\Services\Ai\Data\Classification;
use App\Services\Ai\Data\RetrievedEntry;
use Illuminate\Support\Collection;

/**
 * Finds the knowledge entries most relevant to a question. Phase 1 scores
 * on categories, tags and keywords; an embedding-based implementation can
 * replace it without touching the rest of the pipeline.
 */
interface KnowledgeRetriever
{
    /**
     * @return Collection<int, RetrievedEntry> Best match first, only active entries, never more than $limit.
     */
    public function retrieve(string $question, Classification $classification, int $limit): Collection;
}
