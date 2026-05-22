<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - NEET LMS Student Portal</title>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">
    <style>
        .student-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
            background: #f8fafc;
        }

        .student-sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: scroll;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            padding: 20px 0;

            /* WordPress-style scrollbar - hidden but functional */
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .student-sidebar::-webkit-scrollbar {
            display: none;
        }

        .student-sidebar-brand {
            font-size: 18px;
            font-weight: 700;
            padding: 0 20px 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            line-height: 1.2;
        }

        .student-sidebar-brand svg {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
            color: #60a5fa;
        }

        .student-sidebar-brand-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            padding: 0 20px;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .sidebar-section {
            margin-bottom: 0;
        }

        .sidebar-section-title {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            padding: 12px 20px 8px;
            margin-top: 16px;
            margin-bottom: 8px;
        }

        .student-sidebar nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .student-sidebar nav li {
            margin: 0;
        }

        .student-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .student-sidebar nav a svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .student-sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            border-left-color: #60a5fa;
            padding-left: 18px;
        }

        .student-sidebar nav a.active {
            background: rgba(96, 165, 250, 0.15);
            color: white;
            border-left-color: #60a5fa;
            font-weight: 600;
        }

        .student-sidebar nav a.active svg {
            opacity: 1;
            color: #60a5fa;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 16px 20px 20px;
        }

        .sidebar-footer form,
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .sidebar-footer button {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            padding: 8px 0;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .sidebar-footer form:hover button,
        .sidebar-footer a:hover {
            color: white;
            padding-left: 4px;
        }

        .sidebar-footer svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .student-main {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .student-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .student-header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
        }

        .student-header-user {
            color: #64748b;
            font-size: 14px;
        }

        .student-content {
            flex: 1;
            overflow-y: auto;
            padding: 32px;
        }

        @media (max-width: 768px) {
            .student-wrapper {
                grid-template-columns: 1fr;
            }

            .student-sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                width: 280px;
            }

            .student-sidebar.open {
                left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="student-wrapper">
        <!-- Sidebar -->
        <aside class="student-sidebar">
            <div class="student-sidebar-brand">
                <svg data-lucide="book-open"></svg>
                NEET LMS
            </div>
            <div class="student-sidebar-brand-sub">Student Portal</div>

            <nav>
                <!-- Main Section -->
                <div class="sidebar-section">
                    <ul>
                        <li><a href="{{ route('student.dashboard') }}" class="@if(request()->routeIs('student.dashboard')) active @endif">
                            <svg data-lucide="grid"></svg>
                            Dashboard
                        </a></li>
                    </ul>
                </div>

                <!-- Learning Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Learning</div>
                    <ul>
                        <li><a href="{{ route('student.practice') }}" class="@if(request()->routeIs('student.practice*')) active @endif">
                            <svg data-lucide="book"></svg>
                            Practice Mode
                        </a></li>
                        <li><a href="{{ route('student.tests') }}" class="@if(request()->routeIs('student.tests*')) active @endif">
                            <svg data-lucide="clipboard-check"></svg>
                            Test Mode
                        </a></li>
                        <li><a href="{{ route('student.bookmarks') }}" class="@if(request()->routeIs('student.bookmarks*')) active @endif">
                            <svg data-lucide="bookmark"></svg>
                            Bookmarks
                        </a></li>
                    </ul>
                </div>

                <!-- Performance Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Performance</div>
                    <ul>
                        <li><a href="{{ route('student.test-history') }}" class="@if(request()->routeIs('student.test-history*')) active @endif">
                            <svg data-lucide="history"></svg>
                            Test History
                        </a></li>
                        <li><a href="{{ route('student.analytics') }}" class="@if(request()->routeIs('student.analytics*')) active @endif">
                            <svg data-lucide="bar-chart-3"></svg>
                            Analytics
                        </a></li>
                    </ul>
                </div>

                <!-- Account Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Account</div>
                    <ul>
                        <li><a href="{{ route('student.subscription') }}" class="@if(request()->routeIs('student.subscription*')) active @endif">
                            <svg data-lucide="credit-card"></svg>
                            Subscription
                        </a></li>
                        <li><a href="{{ route('student.settings') }}" class="@if(request()->routeIs('student.settings*')) active @endif">
                            <svg data-lucide="settings"></svg>
                            Settings
                        </a></li>
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <svg data-lucide="log-out"></svg>
                        <span>Logout</span>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}">
                    <svg data-lucide="lock"></svg>
                    <span>Student Login</span>
                </a>
                @endauth
            </div>
        </aside>

        <!-- Main Content -->
        <div class="student-main">
            <!-- Header -->
            <header class="student-header">
                <div>
                    <h1>@yield('title')</h1>
                </div>
                <div class="student-header-user">
                    <span>{{ auth()->user()->name ?? 'Student User' }}</span>
                </div>
            </header>

            <!-- Content -->
            <div class="student-content">
                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 24px;">
                        <strong>Error:</strong>
                        <ul style="margin: 0; padding-left: 24px; margin-top: 8px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success" style="margin-bottom: 24px;">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
