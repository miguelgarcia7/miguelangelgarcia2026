<?php

namespace App\Services\Ai\Data;

final class LlmResponse
{
    public function __construct(
        public readonly string $text,
        public readonly string $model,
        public readonly int $inputTokens = 0,
        public readonly int $outputTokens = 0,
        public readonly int $cacheReadTokens = 0,
        public readonly int $cacheWriteTokens = 0,
        public readonly ?string $stopReason = null,
    ) {}

    /**
     * Decoded JSON body, or null when the text is not a JSON object.
     *
     * @return array<string, mixed>|null
     */
    public function json(): ?array
    {
        $decoded = json_decode(trim($this->text), true);

        return is_array($decoded) ? $decoded : null;
    }
}
