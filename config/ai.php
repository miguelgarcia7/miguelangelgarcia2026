<?php

return [

    /*
    |--------------------------------------------------------------------------
    | "Ask about me" assistant
    |--------------------------------------------------------------------------
    |
    | The assistant answers visitor questions from the knowledge base managed
    | at /admin/ai-knowledge. Set AI_ENABLED=false to hide the section and
    | refuse requests without touching any code.
    |
    */

    'enabled' => (bool) env('AI_ENABLED', true),

    // Only "anthropic" is implemented; see App\Services\Ai\AnthropicLlmClient.
    'provider' => env('AI_PROVIDER', 'anthropic'),

    'models' => [
        // A small, fast model decides what kind of question was asked.
        'classifier' => env('AI_CLASSIFIER_MODEL', 'claude-haiku-4-5'),
        // A capable but cost-efficient model writes the answer.
        'answer' => env('AI_ANSWER_MODEL', 'claude-sonnet-5'),
    ],

    // Hard caps on model output. Classification is a small JSON object.
    'max_tokens' => [
        'classifier' => 300,
        'answer' => (int) env('AI_ANSWER_MAX_TOKENS', 700),
    ],

    'retrieval' => [
        // How many knowledge entries are sent with each question.
        'limit' => (int) env('AI_RETRIEVAL_LIMIT', 5),
        // Entries scoring below this are not considered a match. When an
        // ABOUT_ME question finds nothing above it the model is not called.
        // Set low on purpose: one mention in an entry's body is enough to
        // qualify, because a missed entry becomes a false "not documented".
        'min_score' => (float) env('AI_RETRIEVAL_MIN_SCORE', 0.4),
        // Active entries are cached for retrieval and cleared on every save.
        'cache_key' => 'ai.knowledge.active',
    ],

    'conversation' => [
        // Recent turns the browser may send back for follow-up questions.
        'max_history' => 6,
        'max_question_length' => 600,
        'max_history_message_length' => 3000,
    ],

    // Per-visitor limits, applied by IP.
    'rate_limits' => [
        'per_minute' => (int) env('AI_RATE_PER_MINUTE', 8),
        'per_day' => (int) env('AI_RATE_PER_DAY', 60),
    ],

    // USD per million tokens, used only for the estimated cost in logs.
    'pricing' => [
        'claude-haiku-4-5' => ['input' => 1.00, 'output' => 5.00, 'cache_read' => 0.10],
        'claude-sonnet-5' => ['input' => 2.00, 'output' => 10.00, 'cache_read' => 0.20],
        'claude-opus-5' => ['input' => 5.00, 'output' => 25.00, 'cache_read' => 0.50],
    ],

    // Suggested categories. Entries carry up to five, and the admin form
    // accepts any name, so this list only seeds the picker. STAR stories
    // are a kind of entry, not a category: file them under their themes.
    'categories' => [
        'About Me',
        'Career',
        'Technical Skills',
        'Projects',
        'Leadership',
        'Management',
        'Accomplishments',
        'Product Thinking',
        'Development Philosophy',
        'Team Collaboration',
        'Problem Solving',
        'Personal Interests',
        'Career Goals',
    ],

    // Shown as clickable prompts under the assistant.
    'suggested_questions' => [
        'What project are you most proud of?',
        'Tell me about your leadership experience.',
        'What is your experience with Laravel?',
        'How do you approach building SaaS products?',
        'Tell me about a difficult deadline.',
        'How do you manage developers?',
        'What technologies do you work with?',
    ],

];
