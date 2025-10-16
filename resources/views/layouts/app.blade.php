<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pangasinan State University</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style type="text/css">
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f6f8fa;
            color: #222;
        }
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .navbar-brand {
            font-weight: 700;
            color: #2563eb !important;
            letter-spacing: 0.5px;
        }
        .nav-link {
            color: #374151 !important;
            font-weight: 500;
            margin-right: 1rem;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #2563eb !important;
        }
        .container {
            margin-top: 32px;
        }
        main {
            padding: 32px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .footer {
            background: #f3f4f6;
            color: #6b7280;
            text-align: center;
            padding: 16px 0;
            font-size: 0.95rem;
            border-top: 1px solid #e5e7eb;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #2563eb;
            font-weight: 700;
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            main {
                padding: 12px;
                border-radius: 8px;
            }
        }
    </style>
    @stack('style')
</head>
<body>
    <div id="app" class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <nav class="sidebar bg-white shadow-sm d-flex flex-column p-3" style="width: 260px; min-height: 100vh;">
            <a class="navbar-brand mb-4" href="{{ url('/') }}" style="font-size: 1.5rem;">
                <span style="color:#2563eb;">BMS</span> <span style="color:#374151;">Dashboard</span>
            </a>
            <ul class="nav nav-pills flex-column mb-auto">
                @guest
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="{{ route('inquiry') }}">Inquiry</a>
                    </li>
                   <!-- {{-- REGISTRATION BUTTON START --}}
                    @if (Route::has('register'))
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endif
                    {{-- REGISTRATION BUTTON END --}} -->
                @else
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="{{ route('home') }}">Students</a>
                    </li>
                    @if(\Auth::user()->role === 'admin')
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('messages') }}">Messages</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('statistics') }}">Statistics</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('course') }}">Course</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('block') }}">Blocks</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('users') }}">Users</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('monthly-reports.index') }}">Monthly Reports</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="{{ route('violations') }}">Violations</a>
                        </li>
                    @endif
                    <li class="nav-item mt-4">
                        <a class="nav-link text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @endguest
            </ul>
            <div class="mt-auto pt-4">
            </div>
        </nav>
        <!-- Main Content -->
        <main class="flex-grow-1 p-4" style="background: #f6f8fa;">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>