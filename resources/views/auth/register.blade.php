<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create account on NEET LMS - Medical entrance exam preparation platform">
    <title>Sign Up - NEET LMS</title>

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-content">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <h1>Create Account</h1>
                    <p>Join NEET LMS and start your medical entrance exam preparation today.</p>
                </div>

                <form id="registerForm" method="POST" action="{{ route('register') }}" class="auth-form">
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

                    <div class="form-group">
                        <label for="name" class="form-label required">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input @error('name') is-invalid @enderror"
                            placeholder="John Doe"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name"
                        >
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label required">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') is-invalid @enderror"
                            placeholder="you@example.com"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label required">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="Create a strong password"
                            required
                            autocomplete="new-password"
                        >
                        <span class="password-strength" style="font-size: var(--font-size-xs); margin-top: 0.25rem; display: block;">Very Weak</span>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label required">Confirm Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-input @error('password_confirmation') is-invalid @enderror"
                            placeholder="Confirm your password"
                            required
                            autocomplete="new-password"
                        >
                        @error('password_confirmation')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-checkbox">
                        <input
                            type="checkbox"
                            id="agreeTerms"
                            name="agree_terms"
                            value="agree"
                            required
                        >
                        <label for="agreeTerms">
                            I agree to the <a href="#" style="color: var(--color-primary); text-decoration: underline;">Terms & Conditions</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large" style="margin-top: var(--spacing-2);">Create Account</button>
                </form>

                <div class="auth-divider">or sign up with</div>

                <div class="social-auth">
                    <button type="button" class="social-btn">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;">
                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>
                    <button type="button" class="social-btn">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;">
                            <path fill="currentColor" d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm3.6 11.9h-2.4v7.9h-3.2v-7.9H8.9v-2.7h1.1V7c0-1.5.4-3.8 3.8-3.8 1.1 0 2 .1 2.2.2v2.5h-1.5c-.3 0-.5.1-.5.6v1.6h2l-.3 2.8z"/>
                        </svg>
                        Facebook
                    </button>
                </div>

                <div style="margin-top: var(--spacing-3); text-align: center; font-size: var(--font-size-sm);">
                    Already have an account?
                    <a href="{{ route('login') }}" style="font-weight: var(--font-weight-semibold);">Sign in here</a>
                </div>
            </div>
        </div>

        <div class="auth-bg">
            <h2>Start Your Success Journey</h2>
            <p>Thousands of medical students trust NEET LMS for their entrance exam preparation</p>

            <div style="margin-top: var(--spacing-5); text-align: center;">
                <div style="font-size: 3rem; margin-bottom: var(--spacing-2);">🎓</div>
                <div style="color: rgba(255,255,255,0.9);">
                    <p style="margin-bottom: var(--spacing-1);"><strong>95%</strong> Success Rate</p>
                    <p style="margin-bottom: var(--spacing-1);"><strong>50,000+</strong> Active Students</p>
                    <p><strong>Trusted</strong> by Experts</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
