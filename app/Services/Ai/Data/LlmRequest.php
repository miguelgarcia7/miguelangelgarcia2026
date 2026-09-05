<?php

namespace App\Services\Ai\Data;

/**
 * A provider-neutral description of one model call.
 */
final class LlmRequest
{
    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>|null  $jsonSchema  When set, the model must answer with JSON matching this schema.
     */
    public function __construct(
        public readonly string $model,
        public readonly string $system,
        public readonly array $messages,
        public readonly int $maxTokens,
        public readonly ?array $jsonSchema = null,
        public readonly float $temperature = 0.3,
        /** Ask the provider to cache the system prompt across requests. */
        public readonly bool $cacheSystem = true,
    ) {}
}
