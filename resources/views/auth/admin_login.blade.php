<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin login for NEET LMS">
    <title>Admin Login - NEET LMS</title>

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <style>
        /* Admin-specific auth overrides */
        .auth-bg {
            background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
        }

        .admin-badge {
            display: inline-block;
            background-color: var(--color-danger);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-semibold);
            margin-bottom: var(--spacing-2);
        }

        .admin-logo {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-2);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-content">
            <div class="auth-form-container">
                <div class="admin-badge">ADMIN PORTAL</div>

                <div class="auth-form-header">
                    <h1>Admin Dashboard</h1>
                    <p>Sign in to your admin account to manage NEET LMS</p>
                </div>

                <form id="loginForm" method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
                    @csrf

                    @if ($errors->any())
                        <div class="auth-error">
                            <span>⚠️</span>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="auth-success">
                            <span>✓</span>
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="email" class="form-label">Admin Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') is-invalid @enderror"
                            placeholder="admin@neetlms.com"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="auth-form-footer">
                        <div class="form-checkbox">
                            <input
                                type="checkbox"
                                id="rememberMe"
                                name="remember"
                                value="remember-me"
                            >
                            <label for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password" style="color: var(--color-primary);">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large">Sign In to Admin</button>
                </form>

                <div style="margin-top: var(--spacing-3); text-align: center; font-size: var(--font-size-sm); color: var(--color-gray-600);">
                    <p>Not an admin? <a href="{{ route('login') }}" style="color: var(--color-primary); font-weight: var(--font-weight-semibold);">Student login</a></p>
                </div>
            </div>
        </div>

        <div class="auth-bg">
            <div class="admin-logo">⚙️</div>
            <h2>Admin Management</h2>
            <p>Manage users, questions, tests, and platform settings</p>

            <div style="margin-top: var(--spacing-5);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-3); color: rgba(255,255,255,0.9); font-size: var(--font-size-sm);">
                    <div>
                        <div style="font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">User Management</div>
                        <p style="margin: 0;">Create, edit, and manage student accounts</p>
                    </div>
                    <div>
                        <div style="font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">Content Management</div>
                        <p style="margin: 0;">Manage questions, tests, and subjects</p>
                    </div>
                    <div>
                        <div style="font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">Analytics</div>
                        <p style="margin: 0;">View performance and usage data</p>
                    </div>
                    <div>
                        <div style="font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">Settings</div>
                        <p style="margin: 0;">Configure platform settings</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
