<?php

namespace Database\Factories;

use App\Models\AiKnowledgeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiKnowledgeEntry>
 */
class AiKnowledgeEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category' => 'Technical Skills',
            'kind' => AiKnowledgeEntry::KIND_GENERAL,
            'title' => $this->faker->unique()->sentence(4),
            'summary' => $this->faker->sentence(12),
            'content' => $this->faker->paragraphs(2, true),
            'tags' => ['laravel', 'php'],
            'importance' => 3,
            'is_active' => true,
        ];
    }

    public function star(): static
    {
        return $this->state(fn () => [
            'category' => 'STAR Stories',
            'kind' => AiKnowledgeEntry::KIND_STAR,
            'content' => null,
            'situation' => $this->faker->paragraph(),
            'task' => $this->faker->paragraph(),
            'action' => $this->faker->paragraph(),
            'result' => $this->faker->paragraph(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
