<?php

namespace App\Services\Ai;

use App\Models\AiQuestionLog;
use App\Services\Ai\Contracts\KnowledgeRetriever;
use App\Services\Ai\Contracts\LlmClient;
use App\Services\Ai\Data\Classification;
use App\Services\Ai\Data\ConversationTurn;
use App\Services\Ai\Data\LlmRequest;
use App\Services\Ai\Data\LlmResponse;
use App\Services\Ai\Data\RetrievedEntry;
use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Orchestrates one question: classify → retrieve → build prompt → stream
 * the answer → log what happened.
 *
 * ask() yields events shaped like ['event' => string, 'data' => array]:
 *   meta  — classification and the titles of the entries used
 *   delta — a fragment of answer text
 *   done  — the answer is complete
 *   error — something failed; "message" is safe to show the visitor
 */
class PortfolioAiService
{
    public const NO_INFORMATION_ANSWER = "I don't have documented information about that in Miguel's portfolio, so I'd rather not guess. You can ask about his work history, projects, technical skills, or leadership experience — or reach him directly through the contact form on this page.";

    public const ERROR_ANSWER = 'Sorry — the assistant ran into a problem answering that. Please try again in a moment.';

    public function __construct(
        private readonly QuestionClassifier $classifier,
        private readonly KnowledgeRetriever $retriever,
        private readonly PromptBuilder $prompts,
        private readonly LlmClient $llm,
        private readonly CostEstimator $costs,
    ) {}

    /**
     * @param  array<int, ConversationTurn>  $history
     * @return Generator<int, array{event: string, data: array<string, mixed>}>
     */
    public function ask(string $question, array $history = []): Generator
    {
        $startedAt = hrtime(true);
        $log = new AiQuestionLog(['question' => mb_substr($question, 0, 1000)]);

        try {
            $classification = $this->classifier->classify($question, $history);
            $log->question_type = $classification->type->value;
            $log->classification = $classification->toArray();

            $entries = $classification->type->usesKnowledgeBase()
                ? $this->retriever->retrieve($question, $classification, (int) config('ai.retrieval.limit'))
                : collect();

            $log->retrieved_entry_ids = $entries->map(fn (RetrievedEntry $hit) => $hit->entry->id)->all();

            yield $this->event('meta', [
                'type' => $classification->type->value,
                'sources' => $entries->map(fn (RetrievedEntry $hit) => $hit->entry->title)->values()->all(),
            ]);

            // An ABOUT_ME question with nothing to ground it never reaches
            // the model: the only honest answer is that nothing is documented.
            if ($classification->type->usesKnowledgeBase() && $entries->isEmpty()) {
                $log->answer = self::NO_INFORMATION_ANSWER;
                $log->answered = false;

                yield $this->event('delta', ['text' => self::NO_INFORMATION_ANSWER]);
                yield $this->event('done', ['answered' => false]);

                return;
            }

            $response = yield from $this->streamAnswer($question, $classification, $entries, $history);

            $log->answer = $response->text;
            $log->answered = $response->text !== '';
            $log->model = $response->model;
            $log->input_tokens = $response->inputTokens;
            $log->output_tokens = $response->outputTokens;
            $log->cache_read_tokens = $response->cacheReadTokens;
            $log->estimated_cost = $this->costs->estimate($response);

            yield $this->event('done', ['answered' => $log->answered]);
        } catch (Throwable $e) {
            Log::error('AI answer failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            $log->error = mb_substr(get_class($e).': '.$e->getMessage(), 0, 500);
            $log->answered = false;

            yield $this->event('error', ['message' => self::ERROR_ANSWER]);
        } finally {
            $log->duration_ms = (int) ((hrtime(true) - $startedAt) / 1_000_000);
            $this->persist($log);
        }
    }

    /**
     * @param  Collection<int, RetrievedEntry>  $entries
     * @param  array<int, ConversationTurn>  $history
     * @return Generator<int, array{event: string, data: array<string, mixed>}, mixed, LlmResponse>
     */
    private function streamAnswer(string $question, Classification $classification, Collection $entries, array $history): Generator
    {
        $request = new LlmRequest(
            model: config('ai.models.answer'),
            system: $this->prompts->system(),
            messages: $this->prompts->messages(
                $history,
                $this->prompts->userMessage($question, $classification, $entries),
            ),
            maxTokens: (int) config('ai.max_tokens.answer'),
        );

        $stream = $this->llm->stream($request);

        foreach ($stream as $fragment) {
            if ($fragment !== '') {
                yield $this->event('delta', ['text' => $fragment]);
            }
        }

        return $stream->getReturn();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{event: string, data: array<string, mixed>}
     */
    private function event(string $event, array $data): array
    {
        return ['event' => $event, 'data' => $data];
    }

    private function persist(AiQuestionLog $log): void
    {
        try {
            $log->save();

            Log::info('AI question answered', [
                'type' => $log->question_type,
                'answered' => $log->answered,
                'entries' => $log->retrieved_entry_ids,
                'model' => $log->model,
                'input_tokens' => $log->input_tokens,
                'output_tokens' => $log->output_tokens,
                'cache_read_tokens' => $log->cache_read_tokens,
                'estimated_cost' => (float) $log->estimated_cost,
                'duration_ms' => $log->duration_ms,
                'error' => $log->error,
            ]);
        } catch (Throwable $e) {
            // Logging must never break an answer that already streamed.
            Log::warning('Could not persist AI question log', ['exception' => $e->getMessage()]);
        }
    }
}
