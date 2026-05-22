@extends('layouts.student')

@section('title', 'Edit Profile')

@section('content')
<div class="dashboard-content-wrapper">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('student.settings') }}" style="color: var(--color-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            ← Back to Settings
        </a>
    </div>

    <div class="student-card">
        <h1 style="margin: 0 0 var(--spacing-4) 0; color: var(--color-gray-900);">Edit Profile</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4); border-left: 4px solid #dc2626;">
                <strong>Validation Errors:</strong>
                <ul style="margin: var(--spacing-2) 0 0 0; padding-left: var(--spacing-4);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div style="background-color: rgba(16, 185, 129, 0.1); color: #059669; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-4); border-left: 4px solid #059669;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" style="display: grid; gap: var(--spacing-4);">
            @csrf
            @method('PUT')

            <!-- Profile Picture Section -->
            <div style="padding: var(--spacing-4); background-color: var(--color-gray-50); border-radius: var(--radius-lg); border: 1px solid var(--color-gray-200);">
                <label style="display: block; margin-bottom: var(--spacing-2); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Profile Picture</label>

                <div style="display: flex; gap: var(--spacing-4); align-items: flex-start;">
                    <!-- Current Avatar Preview -->
                    <div style="flex: 0 0 auto;">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar"
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-gray-300);">
                        @else
                            <div style="width: 100px; height: 100px; border-radius: 50%; background-color: var(--color-primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; font-weight: bold;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Upload Input -->
                    <div style="flex: 1;">
                        <input type="file" name="avatar" accept="image/*" style="display: block; padding: var(--spacing-2); border: 2px dashed var(--color-gray-300); border-radius: var(--radius-lg); width: 100%; box-sizing: border-box; cursor: pointer;">
                        <p style="margin: var(--spacing-2) 0 0 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">
                            Accepted formats: JPEG, PNG, GIF (Max 2MB)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div style="padding: var(--spacing-4); background-color: var(--color-gray-50); border-radius: var(--radius-lg); border: 1px solid var(--color-gray-200);">
                <h3 style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-900); font-size: var(--font-size-lg);">Personal Information</h3>

                <!-- Full Name -->
                <div style="margin-bottom: var(--spacing-3);">
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Full Name <span style="color: var(--color-danger);">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-base); box-sizing: border-box;">
                    @error('name')
                        <p style="color: var(--color-danger); font-size: var(--font-size-sm); margin: var(--spacing-1) 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Read-only) -->
                <div style="margin-bottom: var(--spacing-3);">
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-base); box-sizing: border-box; background-color: var(--color-gray-100); color: var(--color-gray-600);">
                    <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Email cannot be changed</p>
                </div>

                <!-- Phone -->
                <div style="margin-bottom: var(--spacing-3);">
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-base); box-sizing: border-box;">
                    @error('phone')
                        <p style="color: var(--color-danger); font-size: var(--font-size-sm); margin: var(--spacing-1) 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio/About -->
                <div>
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">About You</label>
                    <textarea name="bio" rows="4"
                              style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-base); box-sizing: border-box; font-family: inherit;">{{ old('bio', $user->bio) }}</textarea>
                    <p style="margin: var(--spacing-1) 0 0 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Maximum 500 characters</p>
                    @error('bio')
                        <p style="color: var(--color-danger); font-size: var(--font-size-sm); margin: var(--spacing-1) 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Academic Information -->
            <div style="padding: var(--spacing-4); background-color: var(--color-gray-50); border-radius: var(--radius-lg); border: 1px solid var(--color-gray-200);">
                <h3 style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-900); font-size: var(--font-size-lg);">Academic Information</h3>

                <!-- Standard -->
                <div style="margin-bottom: var(--spacing-3);">
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Current Standard</label>
                    <select name="standard" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-base); box-sizing: border-box;">
                        <option value="">Select Standard</option>
                        <option value="11" @selected(old('standard', $user->standard) == '11')>11th</option>
                        <option value="12" @selected(old('standard', $user->standard) == '12')>12th</option>
                        <option value="drop" @selected(old('standard', $user->standard) == 'drop')>Drop Year</option>
                    </select>
                    @error('standard')
                        <p style="color: var(--color-danger); font-size: var(--font-size-sm); margin: var(--spacing-1) 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Medical Background -->
                <div style="margin-bottom: var(--spacing-3);">
                    <label style="display: flex; align-items: center; gap: var(--spacing-2); font-weight: var(--font-weight-medium); color: var(--color-gray-900); cursor: pointer;">
                        <input type="checkbox" name="has_medical_background" value="1"
                               @checked(old('has_medical_background', $user->has_medical_background))
                               style="width: 18px; height: 18px; cursor: pointer;">
                        <span>I have a medical background / PCB stream</span>
                    </label>
                    @error('has_medical_background')
                        <p style="color: var(--color-danger); font-size: var(--font-size-sm); margin: var(--spacing-1) 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Year -->
                <div>
                    <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Target Year</label>
                    <select name="target_year" style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-size: var(--font-size-base); box-sizing: border-box;">
                        <option value="">Select Year</option>
                        <option value="2024" @selected(old('target_year', $user->target_year) == '2024')>2024</option>
                        <option value="2025" @selected(old('target_year', $user->target_year) == '2025')>2025</option>
                        <option value="2026" @selected(old('target_year', $user->target_year) == '2026')>2026</option>
                    </select>
                    @error('target_year')
                        <p style="color: var(--color-danger); font-size: var(--font-size-sm); margin: var(--spacing-1) 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: var(--spacing-3); justify-content: flex-end;">
                <a href="{{ route('student.settings') }}"
                   style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-200); color: var(--color-gray-900); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); transition: background-color 0.2s;">
                    Cancel
                </a>
                <button type="submit"
                        style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: white; border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-medium); cursor: pointer; transition: background-color 0.2s;">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .student-card {
        background-color: white;
        border: 1px solid var(--color-gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-4);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="file"],
    textarea,
    select {
        transition: all 0.2s;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    input[type="file"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    button[type="submit"]:hover {
        background-color: #2563eb;
    }
</style>
@endsection
