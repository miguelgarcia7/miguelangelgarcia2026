<?php

namespace App\Http\Requests;

use App\Services\Ai\Data\ConversationTurn;
use Illuminate\Foundation\Http\FormRequest;

class AskQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $maxQuestion = (int) config('ai.conversation.max_question_length');
        $maxHistory = (int) config('ai.conversation.max_history');
        $maxMessage = (int) config('ai.conversation.max_history_message_length');

        return [
            'question' => ['required', 'string', 'min:2', "max:{$maxQuestion}"],
            'history' => ['sometimes', 'array', "max:{$maxHistory}"],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', "max:{$maxMessage}"],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => 'Please type a question first.',
            'question.min' => 'Please type a question first.',
            'question.max' => 'That question is a little long — please keep it under :max characters.',
            'history.max' => 'Too much conversation history was sent.',
        ];
    }

    public function question(): string
    {
        return trim($this->validated('question'));
    }

    /**
     * Recent turns the browser sent back, oldest first. Leading assistant
     * turns (a greeting) are dropped so the transcript starts with a visitor.
     *
     * @return array<int, ConversationTurn>
     */
    public function history(): array
    {
        $turns = collect($this->validated('history', []))
            ->map(fn (array $turn) => ['role' => $turn['role'], 'content' => trim($turn['content'])])
            ->filter(fn (array $turn) => $turn['content'] !== '')
            ->skipWhile(fn (array $turn) => $turn['role'] !== 'user')
            ->values()
            ->all();

        return ConversationTurn::fromArray($turns);
    }
}
