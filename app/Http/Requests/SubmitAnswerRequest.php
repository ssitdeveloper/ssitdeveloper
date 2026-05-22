<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->value === 'student';
    }

    public function rules(): array
    {
        return [
            'attempt_id' => 'required|integer|exists:test_attempts,id',
            'question_id' => 'required|integer|exists:questions,id',
            'option_id' => 'nullable|integer|exists:options,id',
            'time_spent' => 'required|integer|min:1|max:3600',
        ];
    }

    public function messages(): array
    {
        return [
            'time_spent.max' => 'Time spent cannot exceed 1 hour per question.',
        ];
    }
}
