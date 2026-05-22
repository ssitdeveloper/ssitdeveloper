<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|regex:/^[0-9\+\-\(\)\s]{10,15}$/',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image' => 'Avatar must be a valid image file.',
            'avatar.mimes' => 'Avatar must be JPG, JPEG, PNG, or GIF.',
            'avatar.max' => 'Avatar file size must not exceed 2MB.',
            'avatar.dimensions' => 'Avatar dimensions must be between 100x100 and 2000x2000 pixels.',
        ];
    }

    protected function prepareForValidation()
    {
        // Prevent role change by removing it from input
        $this->request->remove('role');
    }
}
