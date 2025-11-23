<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Hello Users') }} - User Dashboard</title>
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

        .header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.8em;
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
        }

        .user-name {
            font-weight: 500;
        }

        .btn-logout {
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background 0.3s;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 60px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .dashboard-card h2 {
            font-size: 3em;
            color: #11998e;
            margin-bottom: 20px;
        }

        .dashboard-card p {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 30px;
        }

        .welcome-message {
            font-size: 1.5em;
            color: #333;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>{{ __('User Dashboard') }}</h1>
            <div class="header-actions">
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('web.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">{{ __('Logout') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-card">
            <h2>{{ __('Hello Users') }}</h2>
            <p>{{ __('Welcome to the User Dashboard') }}</p>
            <div class="welcome-message">
                {{ __('You are logged in as a Customer') }}
            </div>
        </div>
    </div>
</body>
</html>

