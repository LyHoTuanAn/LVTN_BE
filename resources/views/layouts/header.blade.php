<header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', __('Admin Dashboard'))</h1>
    <div class="flex items-center gap-4">
        @auth
            <div class="flex items-center gap-3 text-gray-700">
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('web.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        @else
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                    {{ __('Login') }}
                </a>
            </div>
        @endauth
    </div>
</header>

