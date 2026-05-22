<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('student.profile-edit', ['user' => auth()->user()]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $validated = $request->validated();

        // Handle file upload securely
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if (auth()->user()->avatar) {
                Storage::delete(auth()->user()->avatar);
            }

            // Store new avatar with secure filename
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        auth()->user()->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
