<?php

namespace App\Http\Controllers;

use App\Http\Requests\AskQuestionRequest;
use App\Services\Ai\PortfolioAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\StreamedEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortfolioAiController extends Controller
{
    /**
     * Answer a visitor's question as a server-sent event stream.
     *
     * Validation and rate limiting happen before this runs (the form
     * request and the "throttle:ask" middleware); the service does the rest.
     */
    public function ask(AskQuestionRequest $request, PortfolioAiService $ai): StreamedResponse|JsonResponse
    {
        if (! config('ai.enabled')) {
            return response()->json(['message' => 'The assistant is currently unavailable.'], 503);
        }

        $question = $request->question();
        $history = $request->history();

        return response()->eventStream(function () use ($ai, $question, $history) {
            foreach ($ai->ask($question, $history) as $event) {
                yield new StreamedEvent(
                    event: $event['event'],
                    data: json_encode($event['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                );
            }
        }, headers: [
            'Cache-Control' => 'no-cache, no-store',
            // Tells nginx to pass each chunk through instead of buffering
            // the whole response.
            'X-Accel-Buffering' => 'no',
        ], endStreamWith: null);
    }
}
