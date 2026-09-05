<?php

namespace App\Services\Ai;

use App\Services\Ai\Data\LlmResponse;

/**
 * Rough USD cost of a response from the per-million-token prices in
 * config/ai.php. Unknown models cost zero rather than failing.
 */
class CostEstimator
{
    public function estimate(LlmResponse $response): float
    {
        $prices = config('ai.pricing.'.$response->model);

        if (! is_array($prices)) {
            // Provider model ids may carry a date suffix; match on prefix.
            foreach (config('ai.pricing', []) as $id => $candidate) {
                if (str_starts_with($response->model, $id)) {
                    $prices = $candidate;
                    break;
                }
            }
        }

        if (! is_array($prices)) {
            return 0.0;
        }

        $uncachedInput = max(0, $response->inputTokens - $response->cacheReadTokens);

        $usd = ($uncachedInput * $prices['input'])
            + ($response->cacheReadTokens * ($prices['cache_read'] ?? $prices['input']))
            + ($response->cacheWriteTokens * $prices['input'] * 1.25)
            + ($response->outputTokens * $prices['output']);

        return round($usd / 1_000_000, 6);
    }
}
