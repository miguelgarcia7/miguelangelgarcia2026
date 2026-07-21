<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ContactRequest extends FormRequest
{
    /**
     * The URI users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirect = '/#contact';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email.',
            'email.email' => 'That doesn’t look like a valid email.',
            'message.required' => 'Please write a short message.',
            'message.min' => 'A little more detail, please (10+ characters).',
        ];
    }

    /**
     * Get the sender's first name.
     */
    public function senderFirstName(): string
    {
        return Str::of($this->validated('name'))->trim()->before(' ')->toString();
    }
}
