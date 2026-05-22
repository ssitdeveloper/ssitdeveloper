<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');
        return auth()->check() && (auth()->user()->role->value === 'admin' || auth()->user()->id === $user->id);
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'sometimes|string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'phone' => 'nullable|regex:/^[0-9\+\-\(\)\s]{10,15}$/',
            'password' => ['nullable', Password::min(12)->mixedCase()->numbers()->symbols()],
            'role' => 'sometimes|in:student,admin,moderator,instructor',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Name can only contain letters, spaces, hyphens, and apostrophes.',
            'phone.regex' => 'Phone number format is invalid.',
            'password.required' => 'Password must be at least 12 characters with uppercase, lowercase, numbers, and symbols.',
        ];
    }
}
