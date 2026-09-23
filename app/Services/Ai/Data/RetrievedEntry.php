<?php

namespace App\Services\Ai\Data;

use App\Models\AiKnowledgeEntry;

final class RetrievedEntry
{
    /**
     * @param  array<string, float>  $scoreBreakdown  How the score was reached, for the admin preview.
     */
    public function __construct(
        public readonly AiKnowledgeEntry $entry,
        public readonly float $score,
        public readonly array $scoreBreakdown = [],
    ) {}
}
