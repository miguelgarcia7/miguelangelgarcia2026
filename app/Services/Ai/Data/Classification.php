<?php

namespace App\Services\Ai\Data;

/**
 * What the classifier decided about a visitor's question. Always built
 * through validated data — see App\Services\Ai\QuestionClassifier.
 */
final class Classification
{
    /**
     * @param  array<int, string>  $categories
     * @param  array<int, string>  $searchTerms
     */
    public function __construct(
        public readonly QuestionType $type,
        public readonly array $categories = [],
        public readonly array $searchTerms = [],
        /** The question rewritten to stand alone, when it was a follow-up. */
        public readonly ?string $standaloneQuestion = null,
        public readonly bool $fromFallback = false,
    ) {}

    /**
     * Used when the classifier fails or returns something unusable.
     * ABOUT_ME is the safest bucket: it forces knowledge-base grounding.
     */
    public static function fallback(string $question): self
    {
        return new self(
            type: QuestionType::AboutMe,
            searchTerms: array_values(array_filter(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($question)) ?: [])),
            fromFallback: true,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'categories' => $this->categories,
            'search_terms' => $this->searchTerms,
            'standalone_question' => $this->standaloneQuestion,
            'from_fallback' => $this->fromFallback,
        ];
    }
}
