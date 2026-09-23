<?php

namespace App\Providers;

use App\Services\Ai\AnthropicLlmClient;
use App\Services\Ai\Contracts\KnowledgeRetriever;
use App\Services\Ai\Contracts\LlmClient;
use App\Services\Ai\KeywordKnowledgeRetriever;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // The AI provider and the retrieval strategy are the two seams that
        // are expected to change; everything else depends on these contracts.
        $this->app->singleton(LlmClient::class, fn () => new AnthropicLlmClient(config('services.anthropic.key')));
        $this->app->singleton(KnowledgeRetriever::class, KeywordKnowledgeRetriever::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('ask', fn (Request $request) => [
            Limit::perMinute((int) config('ai.rate_limits.per_minute'))->by($request->ip()),
            Limit::perDay((int) config('ai.rate_limits.per_day'))->by($request->ip()),
        ]);

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
    }
}
