<?php

namespace App\Services\Ai;

use Anthropic\Client;
use Anthropic\Messages\RawContentBlockDeltaEvent;
use Anthropic\Messages\RawMessageDeltaEvent;
use Anthropic\Messages\RawMessageStartEvent;
use Anthropic\Messages\TextDelta;
use App\Services\Ai\Contracts\LlmClient;
use App\Services\Ai\Data\LlmRequest;
use App\Services\Ai\Data\LlmResponse;
use Generator;
use RuntimeException;

/**
 * Talks to the Claude API through the official SDK. Nothing outside this
 * class knows which provider is in use.
 */
class AnthropicLlmClient implements LlmClient
{
    private ?Client $client = null;

    public function __construct(private readonly ?string $apiKey) {}

    public function complete(LlmRequest $request): LlmResponse
    {
        $message = $this->client()->messages->create(...$this->params($request));

        $text = '';
        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $text .= $block->text;
            }
        }

        return new LlmResponse(
            text: $text,
            model: $message->model,
            inputTokens: $message->usage->inputTokens,
            outputTokens: $message->usage->outputTokens,
            cacheReadTokens: $message->usage->cacheReadInputTokens ?? 0,
            cacheWriteTokens: $message->usage->cacheCreationInputTokens ?? 0,
            stopReason: $message->stopReason,
        );
    }

    public function stream(LlmRequest $request): Generator
    {
        $stream = $this->client()->messages->createStream(...$this->params($request));

        $text = '';
        $model = $request->model;
        $inputTokens = 0;
        $outputTokens = 0;
        $cacheRead = 0;
        $cacheWrite = 0;
        $stopReason = null;

        foreach ($stream as $event) {
            if ($event instanceof RawMessageStartEvent) {
                $model = $event->message->model;
                $inputTokens = $event->message->usage->inputTokens;
                $cacheRead = $event->message->usage->cacheReadInputTokens ?? 0;
                $cacheWrite = $event->message->usage->cacheCreationInputTokens ?? 0;
            } elseif ($event instanceof RawContentBlockDeltaEvent && $event->delta instanceof TextDelta) {
                $text .= $event->delta->text;
                yield $event->delta->text;
            } elseif ($event instanceof RawMessageDeltaEvent) {
                $outputTokens = $event->usage->outputTokens;
                $stopReason = $event->delta->stopReason ?? $stopReason;
            }
        }

        return new LlmResponse(
            text: $text,
            model: $model,
            inputTokens: $inputTokens,
            outputTokens: $outputTokens,
            cacheReadTokens: $cacheRead,
            cacheWriteTokens: $cacheWrite,
            stopReason: $stopReason,
        );
    }

    /**
     * Named arguments for the SDK's create / createStream methods.
     *
     * @return array<string, mixed>
     */
    private function params(LlmRequest $request): array
    {
        $system = [['type' => 'text', 'text' => $request->system]];

        if ($request->cacheSystem) {
            $system[0]['cacheControl'] = ['type' => 'ephemeral'];
        }

        $params = [
            'model' => $request->model,
            'maxTokens' => $request->maxTokens,
            'system' => $system,
            'messages' => $request->messages,
            // Short portfolio answers do not need extended thinking, and
            // leaving it off keeps every request fast and cheap.
            'thinking' => ['type' => 'disabled'],
            'requestOptions' => ['timeout' => 60, 'maxRetries' => 1],
        ];

        if ($request->jsonSchema !== null) {
            $params['outputConfig'] = ['format' => ['type' => 'json_schema', 'schema' => $request->jsonSchema]];
        }

        return $params;
    }

    private function client(): Client
    {
        if (blank($this->apiKey)) {
            throw new RuntimeException('ANTHROPIC_API_KEY is not configured.');
        }

        return $this->client ??= new Client(apiKey: $this->apiKey);
    }
}
