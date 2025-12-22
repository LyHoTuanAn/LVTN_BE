<header class="bg-white border-b border-gray-200 px-4 md:px-6 py-3 md:py-4 flex flex-col gap-2 md:flex-row md:justify-between md:items-center md:gap-3">
    <div class="flex items-center justify-between w-full md:w-auto">
        <h1 class="text-lg md:text-2xl font-semibold text-gray-900">@yield('page-title', __('Admin Dashboard'))</h1>
        <button 
            class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50"
            @click="sidebarOpen = !sidebarOpen"
            aria-label="{{ __('Toggle navigation') }}"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>
    <div class="flex items-center gap-4 justify-start md:justify-end">
        <!-- Language Switcher -->
        <div class="flex items-center gap-2 border border-gray-300 rounded-md overflow-hidden">
            <a 
                href="{{ route('language.switch', 'vi') }}" 
                class="px-3 py-1.5 text-sm font-medium transition-colors {{ app()->getLocale() === 'vi' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                🇻🇳 VI
            </a>
            <a 
                href="{{ route('language.switch', 'en') }}" 
                class="px-3 py-1.5 text-sm font-medium transition-colors {{ app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                🇬🇧 EN
            </a>
        </div>

        {{-- User info / auth controls moved to sidebar --}}
    </div>
</header>

