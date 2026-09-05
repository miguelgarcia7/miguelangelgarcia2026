<?php

namespace App\Http\Requests;

use App\Models\AiKnowledgeEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiKnowledgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $isStar = $this->input('kind') === AiKnowledgeEntry::KIND_STAR;
        $entry = $this->route('entry');

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('ai_knowledge_entries', 'slug')->ignore($entry),
            ],
            'category' => ['required', 'string', 'max:80'],
            'kind' => ['required', Rule::in(AiKnowledgeEntry::KINDS)],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => [$isStar ? 'nullable' : 'required', 'string', 'max:20000'],
            'situation' => [$isStar ? 'required' : 'nullable', 'string', 'max:5000'],
            'task' => [$isStar ? 'required' : 'nullable', 'string', 'max:5000'],
            'action' => [$isStar ? 'required' : 'nullable', 'string', 'max:5000'],
            'result' => [$isStar ? 'required' : 'nullable', 'string', 'max:5000'],
            'tags' => ['present', 'array', 'max:20'],
            'tags.*' => ['string', 'max:40'],
            'importance' => ['required', 'integer', 'between:1,5'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'Use lower-case letters, numbers and hyphens only.',
            'content.required' => 'Write the entry content, or switch to a STAR story.',
            'situation.required' => 'A STAR story needs a situation.',
            'task.required' => 'A STAR story needs a task.',
            'action.required' => 'A STAR story needs an action.',
            'result.required' => 'A STAR story needs a result.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tags' => AiKnowledgeEntry::normaliseTags((array) $this->input('tags', [])),
            'slug' => filled($this->input('slug')) ? trim($this->input('slug')) : null,
        ]);
    }
}
