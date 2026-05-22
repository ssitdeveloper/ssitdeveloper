<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NEET LMS Admin Dashboard">
    <title>@yield('title') - NEET LMS Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

    <style>
        /* Admin Dashboard Styles */
        body {
            background-color: #f8fafc;
        }

        .admin-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 20px 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: scroll;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;

            /* WordPress-style scrollbar - hidden but functional */
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .admin-sidebar::-webkit-scrollbar {
            display: none;
        }

        .admin-sidebar-brand {
            font-size: 18px;
            font-weight: 700;
            padding: 0 20px 12px;
            margin-bottom: 8px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .admin-sidebar-brand > div {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: flex-start;
        }

        .admin-sidebar-section {
            margin-bottom: 0;
        }

        .admin-sidebar-section-title {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            padding: 12px 20px 8px;
            margin-top: 16px;
            margin-bottom: 8px;
        }

        .admin-sidebar-nav li {
            margin: 0;
        }

        .admin-sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
            border-left: 3px solid transparent;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-sidebar-nav a svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .admin-sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            border-left-color: #60a5fa;
            padding-left: 18px;
        }

        .admin-sidebar-nav a.active {
            background: rgba(96, 165, 250, 0.15);
            color: white;
            border-left-color: #60a5fa;
            font-weight: 600;
        }

        .admin-sidebar-nav a.active svg {
            opacity: 1;
            color: #60a5fa;
        }

        .admin-sidebar-footer {
            margin-top: auto;
            padding: 16px 20px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-sidebar-footer form button,
        .admin-sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .admin-sidebar-footer form button {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            padding: 8px 0;
            cursor: pointer;
        }

        .admin-sidebar-footer form:hover button,
        .admin-sidebar-footer a:hover {
            color: white;
            padding-left: 4px;
        }

        .admin-sidebar-footer svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .admin-main {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .admin-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header-title h1 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
        }

        .admin-header-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-header-user span {
            color: #64748b;
            font-size: 14px;
        }

        .admin-content {
            flex: 1;
            overflow-y: auto;
            padding: 32px;
        }

        .admin-content-inner {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Stat Cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-color: #60a5fa;
        }

        .stat-card-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            color: #60a5fa;
            margin-bottom: 8px;
        }

        .stat-card-change {
            font-size: 12px;
            color: #22c55e;
            font-weight: 600;
        }

        /* Data Table */
        .admin-table {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .admin-table-header {
            padding: 20px 24px;
            background-color: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-table-header h2 {
            margin: 0;
            font-size: 16px;
            color: #0f172a;
            font-weight: 700;
        }

        .admin-table-body {
            overflow-x: auto;
        }

        .admin-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .admin-table th {
            background-color: #f1f5f9;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
        }

        .admin-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        .admin-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background-color: #ecfdf5;
            color: #15803d;
        }

        .status-inactive {
            background-color: #fef2f2;
            color: #b91c1c;
        }

        .status-pending {
            background-color: #fffbeb;
            color: #b45309;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-wrapper {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                width: 280px;
            }

            .admin-sidebar.open {
                left: 0;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .admin-table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-brand">
                NEET LMS
                <div>Admin Panel</div>
            </div>

            <nav class="admin-sidebar-nav">
                <!-- Main Section -->
                <div class="admin-sidebar-section">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) class="active" @endif>
                            <svg data-lucide="grid"></svg>
                            Dashboard
                        </a>
                    </li>
                </div>

                <!-- Users & Subscriptions Section -->
                <div class="admin-sidebar-section">
                    <div class="admin-sidebar-section-title">Users & Subscriptions</div>
                    <li>
                        <a href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) class="active" @endif>
                            <svg data-lucide="users"></svg>
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.subscriptions.index') }}" @if(request()->routeIs('admin.subscriptions.*')) class="active" @endif>
                            <svg data-lucide="credit-card"></svg>
                            Subscriptions
                        </a>
                    </li>
                </div>

                <!-- Payments & Billing Section -->
                <div class="admin-sidebar-section">
                    <div class="admin-sidebar-section-title">Payments & Billing</div>
                    <li>
                        <a href="{{ route('admin.payments.index') }}" @if(request()->routeIs('admin.payments.*')) class="active" @endif>
                            <svg data-lucide="wallet"></svg>
                            Payments
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.invoices.index') }}" @if(request()->routeIs('admin.invoices.*')) class="active" @endif>
                            <svg data-lucide="file-text"></svg>
                            Invoices
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.coupons.index') }}" @if(request()->routeIs('admin.coupons.*')) class="active" @endif>
                            <svg data-lucide="tag"></svg>
                            Coupons
                        </a>
                    </li>
                </div>

                <!-- Content Management Section -->
                <div class="admin-sidebar-section">
                    <div class="admin-sidebar-section-title">Content Management</div>
                    <li>
                        <a href="{{ route('admin.subjects.index') }}" @if(request()->routeIs('admin.subjects.*')) class="active" @endif>
                            <svg data-lucide="book"></svg>
                            Subjects
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.topics.index') }}" @if(request()->routeIs('admin.topics.*')) class="active" @endif>
                            <svg data-lucide="layers"></svg>
                            Topics
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.chapters.index') }}" @if(request()->routeIs('admin.chapters.*')) class="active" @endif>
                            <svg data-lucide="file-check"></svg>
                            Chapters
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.questions.index') }}" @if(request()->routeIs('admin.questions.*')) class="active" @endif>
                            <svg data-lucide="help-circle"></svg>
                            Questions
                        </a>
                    </li>
                </div>

                <!-- Exams & Testing Section -->
                <div class="admin-sidebar-section">
                    <div class="admin-sidebar-section-title">Exams & Testing</div>
                    <li>
                        <a href="{{ route('admin.tests.index') }}" @if(request()->routeIs('admin.tests.*')) class="active" @endif>
                            <svg data-lucide="clipboard-check"></svg>
                            Tests
                        </a>
                    </li>
                </div>

                <!-- Promotions & Marketing Section -->
                <div class="admin-sidebar-section">
                    <div class="admin-sidebar-section-title">Promotions & Marketing</div>
                    <li>
                        <a href="{{ route('admin.banners.index') }}" @if(request()->routeIs('admin.banners.*')) class="active" @endif>
                            <svg data-lucide="image"></svg>
                            Banners
                        </a>
                    </li>
                </div>

                <!-- Analytics & Logs Section -->
                <div class="admin-sidebar-section">
                    <div class="admin-sidebar-section-title">Analytics & Logs</div>
                    <li>
                        <a href="{{ route('admin.activity-logs.index') }}" @if(request()->routeIs('admin.activity-logs.*')) class="active" @endif>
                            <svg data-lucide="activity"></svg>
                            Activity Logs
                        </a>
                    </li>
                </div>
            </nav>

            <div class="admin-sidebar-footer">
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
                    <span>Admin Login</span>
                </a>
                @endauth
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="admin-header-title">
                    <h1>@yield('title')</h1>
                </div>
                <div class="admin-header-user">
                    <span>{{ auth()->user()->name ?? 'Admin Demo' }}</span>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <div class="admin-content-inner">
                    @if (session('success'))
                        <div class="alert alert-success" style="margin-bottom: 24px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" style="margin-bottom: 24px;">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
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
