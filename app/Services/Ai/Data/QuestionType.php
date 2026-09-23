<?php

namespace App\Services\Ai\Data;

enum QuestionType: string
{
    /** About Miguel's actual experience, skills, projects, personality. */
    case AboutMe = 'ABOUT_ME';

    /** A general programming or technology question. */
    case GeneralTechnical = 'GENERAL_TECHNICAL';

    /** General technical knowledge in the context of Miguel's experience. */
    case Mixed = 'MIXED';

    /** Unrelated, inappropriate, or unsupported. */
    case OutOfScope = 'OUT_OF_SCOPE';

    /**
     * Whether the knowledge base should be searched for this question.
     */
    public function usesKnowledgeBase(): bool
    {
        return $this !== self::GeneralTechnical && $this !== self::OutOfScope;
    }
}
