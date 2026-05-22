<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'NEET LMS - Master your medical entrance exam with comprehensive study materials, mock tests, and expert guidance.')">
    <title>@yield('title', 'NEET LMS - Medical Entrance Exam Preparation')</title>

    <!-- External CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

    <!-- Alpine Icons (SVG Library) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/cdn@3.x.x/dist/cdn.min.js"></script>

    @yield('extra_css')
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('home') }}" class="nav-brand">NEET LMS</a>

            <button class="nav-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('home') }}" @if(request()->routeIs('home')) class="active" @endif>Home</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('features') }}" @if(request()->routeIs('features')) class="active" @endif>Features</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pricing') }}" @if(request()->routeIs('pricing')) class="active" @endif>Pricing</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('about') }}" @if(request()->routeIs('about')) class="active" @endif>About</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('contact') }}" @if(request()->routeIs('contact')) class="active" @endif>Contact</a>
                </li>
            </ul>

            <div class="nav-auth">
                @auth
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary btn-small">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-small">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-small">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-small">Sign Up</a>
                @endguest
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>About NEET LMS</h4>
                    <p>Your comprehensive platform for medical entrance exam preparation with 3,000+ questions and expert guidance.</p>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('features') }}">Features</a></li>
                        <li><a href="{{ route('pricing') }}">Pricing</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Blog & Tips</a></li>
                        <li><a href="#">Study Guides</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-bottom">
                <div class="footer-copyright">
                    &copy; 2026 NEET LMS. All rights reserved. | Helping you achieve your medical dreams!
                </div>
                <div class="footer-social">
                    <a href="#" title="Twitter" aria-label="Follow us on Twitter">𝕏</a>
                    <a href="#" title="LinkedIn" aria-label="Follow us on LinkedIn">in</a>
                    <a href="#" title="Facebook" aria-label="Follow us on Facebook">f</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- External JavaScript Files -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('extra_js')
</body>
</html>
