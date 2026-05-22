@extends('layouts.admin')

@section('title', 'Create User - NEET LMS Admin')

@section('content')
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4);">
        <a href="{{ route('admin.users.index') }}" style="color: var(--color-primary); text-decoration: none;">← Back to Users</a>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h1 style="margin-top: 0; margin-bottom: var(--spacing-4); color: var(--color-gray-900);">Create New User</h1>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626; padding: var(--spacing-3); border-radius: var(--radius-lg); margin-bottom: var(--spacing-3); border-left: 4px solid #dc2626;">
                <strong>Error:</strong>
                <ul style="margin: var(--spacing-2) 0 0 0; padding-left: var(--spacing-3);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" style="display: grid; gap: var(--spacing-4);">
            @csrf

            <!-- Name -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Full Name <span style="color: var(--color-danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; transition: all var(--transition-fast);" placeholder="John Doe">
            </div>

            <!-- Email -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Email Address <span style="color: var(--color-danger);">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; transition: all var(--transition-fast);" placeholder="john@example.com">
            </div>

            <!-- Password -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Password <span style="color: var(--color-danger);">*</span></label>
                <input type="password" name="password" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; transition: all var(--transition-fast);" placeholder="Min 12 characters, mixed case, numbers, symbols">
            </div>

            <!-- Confirm Password -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Confirm Password <span style="color: var(--color-danger);">*</span></label>
                <input type="password" name="password_confirmation" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; transition: all var(--transition-fast);" placeholder="Confirm password">
            </div>

            <!-- Role -->
            <div>
                <label style="display: block; margin-bottom: var(--spacing-1); font-weight: var(--font-weight-medium); color: var(--color-gray-900);">Role <span style="color: var(--color-danger);">*</span></label>
                <select name="role" required style="width: 100%; padding: var(--spacing-2); border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); font-family: var(--font-secondary); font-size: var(--font-size-base); box-sizing: border-box; transition: all var(--transition-fast);">
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" @if(old('role') === $role->value) selected @endif>{{ ucfirst($role->value) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Is Active -->
            <div style="display: flex; align-items: center; gap: var(--spacing-2);">
                <input type="checkbox" name="is_active" value="1" checked id="is_active" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="is_active" style="margin: 0; font-weight: var(--font-weight-medium); color: var(--color-gray-900); cursor: pointer;">Account Active</label>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: var(--spacing-2); margin-top: var(--spacing-4);">
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; transition: all var(--transition-fast);">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-gray-100); color: var(--color-gray-900); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer; text-decoration: none; display: inline-block; transition: all var(--transition-fast);">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
