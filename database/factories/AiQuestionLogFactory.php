<?php

namespace Database\Factories;

use App\Models\AiQuestionLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiQuestionLog>
 */
class AiQuestionLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence().'?',
            'question_type' => 'ABOUT_ME',
            'classification' => ['type' => 'ABOUT_ME', 'categories' => [], 'search_terms' => []],
            'retrieved_entry_ids' => [],
            'answered' => true,
            'answer' => $this->faker->paragraph(),
            'model' => 'claude-sonnet-5',
            'input_tokens' => 900,
            'output_tokens' => 200,
            'duration_ms' => 1800,
        ];
    }
}
