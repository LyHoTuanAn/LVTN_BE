<div 
    class="bg-gray-100 w-64 md:w-64 h-screen flex flex-col border-r border-gray-200 fixed md:static inset-y-0 left-0 z-30 transform md:transform-none transition-transform duration-200 ease-in-out"
    :class="{'-translate-x-full md:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen}"
>
<div class="p-4 h-20 border-b border-gray-200 flex items-center justify-center">
    <img 
        src="{{ asset('img/logo_admin.png') }}"
        alt="Admin Logo"
        class="!max-w-none !h-auto w-[459px]"
    />
</div>

    <nav class="flex-1 py-4 overflow-y-auto">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    {{ __('Dashboard') }}
                </a>
            </li>
            <li>
                <a href="{{ route('api-docs') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('api-docs') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    {{ __('API Documentation') }}
                </a>
            </li>
            <li>
                <a href="{{ route('api-management') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('api-management') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    {{ __('API Management') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    {{ __('Users') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.movies.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('admin.movies.*') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('Movies') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.rooms.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('admin.rooms.*') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    {{ __('Rooms') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.showtimes.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('admin.showtimes.*') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Showtimes') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.news.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('admin.news.*') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V7a2 2 0 00-2-2H5A2 2 0 003 7v4m4 8h10"></path>
                    </svg>
                    {{ __('News') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    {{ __('Bookings') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.media.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('admin.media.*') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('Media') }}
                </a>
            </li>
            <li>
                <a href="{{ route('settings') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-200 transition-colors {{ request()->routeIs('settings') ? 'bg-gray-200 font-medium' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ __('Settings') }}
                </a>
            </li>
        </ul>
    </nav>

    @auth
        <div class="p-4 border-t border-gray-200 mt-auto">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    <div class="font-medium">{{ auth()->user()->name }}</div>
                    @if (auth()->user()->email)
                        <div class="text-gray-500 text-xs">{{ auth()->user()->email }}</div>
                    @endif
                </div>
                <form method="POST" action="{{ route('web.logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors">
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>
    @endauth
</div>

