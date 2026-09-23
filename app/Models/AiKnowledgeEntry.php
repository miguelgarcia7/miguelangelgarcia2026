<?php

namespace App\Models;

use Database\Factories\AiKnowledgeEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * One fact, story, or topic the assistant is allowed to talk about.
 *
 * Only active entries are ever shown to the model. A "star" entry keeps its
 * text in the Situation / Task / Action / Result fields; a "general" entry
 * keeps it in "content".
 */
class AiKnowledgeEntry extends Model
{
    /** @use HasFactory<AiKnowledgeEntryFactory> */
    use HasFactory;

    public const KIND_GENERAL = 'general';

    public const KIND_STAR = 'star';

    public const KINDS = [self::KIND_GENERAL, self::KIND_STAR];

    protected $fillable = [
        'categories',
        'kind',
        'title',
        'slug',
        'summary',
        'content',
        'situation',
        'task',
        'action',
        'result',
        'tags',
        'importance',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'tags' => 'array',
            'importance' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $entry) {
            if (blank($entry->slug)) {
                $entry->slug = static::uniqueSlug($entry->title, $entry->id);
            }

            $entry->categories = static::normaliseCategories($entry->categories ?? []);
            $entry->tags = static::normaliseTags($entry->tags ?? []);
        });

        // Retrieval reads active entries from cache; any change invalidates it.
        static::saved(fn () => static::forgetRetrievalCache());
        static::deleted(fn () => static::forgetRetrievalCache());
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * The first category, used where only one can be shown.
     */
    public function primaryCategory(): ?string
    {
        return $this->categories[0] ?? null;
    }

    public function isStar(): bool
    {
        return $this->kind === self::KIND_STAR;
    }

    /**
     * The body as one string, used for keyword scoring and previews.
     */
    public function body(): string
    {
        if ($this->isStar()) {
            return collect([
                'Situation' => $this->situation,
                'Task' => $this->task,
                'Action' => $this->action,
                'Result' => $this->result,
            ])->filter()->map(fn ($text, $label) => "{$label}: ".trim($text))->implode("\n\n");
        }

        return trim((string) $this->content);
    }

    /**
     * Everything a keyword search should look at.
     */
    public function searchableText(): string
    {
        return implode("\n", array_filter([
            $this->title,
            implode(' ', $this->categories ?? []),
            implode(' ', $this->tags ?? []),
            $this->summary,
            $this->body(),
        ]));
    }

    /**
     * How the entry is presented to the model inside the context block.
     */
    public function toPromptText(): string
    {
        $lines = [
            "### {$this->title}",
            'Categories: '.implode(', ', $this->categories ?? []),
        ];

        if (filled($this->tags)) {
            $lines[] = 'Tags: '.implode(', ', $this->tags);
        }

        if (filled($this->summary)) {
            $lines[] = 'Summary: '.trim($this->summary);
        }

        $lines[] = '';
        $lines[] = $this->body();

        return implode("\n", $lines);
    }

    public static function forgetRetrievalCache(): void
    {
        Cache::forget(config('ai.retrieval.cache_key'));
    }

    /**
     * Trimmed, de-duplicated (case-insensitively) category names in the
     * order given, so "Leadership" and "leadership" never coexist.
     *
     * @param  array<int, string>  $categories
     * @return array<int, string>
     */
    public static function normaliseCategories(array $categories): array
    {
        return collect($categories)
            ->map(fn ($category) => trim(preg_replace('/\s+/', ' ', (string) $category)))
            ->filter()
            ->unique(fn (string $category) => mb_strtolower($category))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $tags
     * @return array<int, string>
     */
    public static function normaliseTags(array $tags): array
    {
        return collect($tags)
            ->map(fn ($tag) => Str::of((string) $tag)->lower()->trim()->replaceMatches('/\s+/', '-')->toString())
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'entry';
        $slug = $base;
        $n = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }
}
