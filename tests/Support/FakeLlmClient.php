<?php

namespace Tests\Support;

use App\Services\Ai\Contracts\LlmClient;
use App\Services\Ai\Data\LlmRequest;
use App\Services\Ai\Data\LlmResponse;
use Generator;
use Throwable;

/**
 * Scripted model for tests: one response per call, in order. Records every
 * request so tests can assert on what the model was sent.
 */
class FakeLlmClient implements LlmClient
{
    /** @var array<int, LlmRequest> */
    public array $requests = [];

    /** @var array<int, LlmResponse|Throwable> */
    private array $queue = [];

    public function queue(LlmResponse|Throwable|string $response, string $model = 'fake-model'): static
    {
        $this->queue[] = is_string($response) ? new LlmResponse(text: $response, model: $model) : $response;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function queueJson(array $data): static
    {
        return $this->queue(json_encode($data));
    }

    public function complete(LlmRequest $request): LlmResponse
    {
        $this->requests[] = $request;

        return $this->next();
    }

    public function stream(LlmRequest $request): Generator
    {
        $this->requests[] = $request;
        $response = $this->next();

        foreach (mb_str_split($response->text, 12) as $chunk) {
            yield $chunk;
        }

        return $response;
    }

    public function lastRequest(): ?LlmRequest
    {
        return $this->requests[array_key_last($this->requests)] ?? null;
    }

    private function next(): LlmResponse
    {
        $response = array_shift($this->queue) ?? new LlmResponse(text: '', model: 'fake-model');

        if ($response instanceof Throwable) {
            throw $response;
        }

        return $response;
    }
}
