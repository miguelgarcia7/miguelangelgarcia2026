<?php

namespace App\Services\Ai\Data;

final class ConversationTurn
{
    public function __construct(
        public readonly string $role,
        public readonly string $content,
    ) {}

    /**
     * @param  array<int, array{role: string, content: string}>  $turns
     * @return array<int, self>
     */
    public static function fromArray(array $turns): array
    {
        return array_map(fn (array $turn) => new self($turn['role'], $turn['content']), $turns);
    }
}
