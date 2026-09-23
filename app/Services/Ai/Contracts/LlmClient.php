<?php

namespace App\Services\Ai\Contracts;

use App\Services\Ai\Data\LlmRequest;
use App\Services\Ai\Data\LlmResponse;
use Generator;

/**
 * The only place the application talks to an AI provider.
 */
interface LlmClient
{
    /**
     * Run the request to completion and return the whole answer.
     */
    public function complete(LlmRequest $request): LlmResponse;

    /**
     * Yield text fragments as the model produces them. The generator's
     * return value is the final LlmResponse with usage figures.
     *
     * @return Generator<int, string, mixed, LlmResponse>
     */
    public function stream(LlmRequest $request): Generator;
}
