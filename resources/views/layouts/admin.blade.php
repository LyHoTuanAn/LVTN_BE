<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Admin Dashboard')) - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 20px;
            background: #1a252f;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-header h2 {
            font-size: 1.3em;
            color: white;
        }

        .sidebar-menu {
            list-style: none;
            padding: 10px 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: background 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            background: #34495e;
            border-left-color: #3498db;
        }

        .sidebar-menu a.active {
            background: #34495e;
            border-left-color: #3498db;
            font-weight: 600;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5em;
            color: #2c3e50;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
        }

        .btn-logout {
            padding: 8px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-logout:hover {
            background: #c0392b;
        }

        .content {
            padding: 30px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>{{ __('Admin Panel') }}</h2>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        {{ __('Users') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.media.index') }}" class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                        {{ __('Media') }}
                    </a>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>@yield('page-title', __('Admin Dashboard'))</h1>
                <div class="header-actions">
                    <div class="user-info">
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('web.logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>

            <div class="content">
                @if (session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>

