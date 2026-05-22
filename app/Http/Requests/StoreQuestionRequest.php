<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->value === 'admin';
    }

    public function rules(): array
    {
        return [
            'chapter_id' => 'required|integer|exists:chapters,id',
            'question_text' => 'required|string|max:5000|min:10',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'explanation' => 'nullable|string|max:10000',
            'is_published' => 'boolean',
            'options' => 'required|array|min:2|max:6',
            'options.*.text' => 'required|string|max:1000|min:3',
            'options.*.is_correct' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'question_text.min' => 'Question must be at least 10 characters.',
            'options.min' => 'At least 2 options are required.',
            'options.max' => 'Maximum 6 options allowed.',
        ];
    }
}
