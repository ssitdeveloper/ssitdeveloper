<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->value === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|regex:/^[0-9\+\-\(\)\s]{10,15}$/',
            'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
            'role' => 'required|in:student,admin,moderator,instructor',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Name can only contain letters, spaces, hyphens, and apostrophes.',
            'phone.regex' => 'Phone number format is invalid.',
            'password.required' => 'Password is required and must be at least 12 characters with uppercase, lowercase, numbers, and symbols.',
        ];
    }
}
