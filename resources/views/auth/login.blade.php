<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} - Hệ Thống Đặt Vé Xem Phim</title>
    @php $assetVersion = config('app.asset_version'); @endphp
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') . '?v=' . $assetVersion }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        rose: {
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                        },
                        zinc: {
                            400: '#a1a1aa',
                            500: '#71717a',
                            600: '#52525b',
                            700: '#3f3f46',
                            800: '#27272a',
                            900: '#18181b',
                            950: '#09090b',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-zinc-950 flex" x-data="{ showPassword: false, isLoading: false }">
    <!-- Left Side - Movie Showcase -->
    <div class="hidden lg:flex lg:w-[55%] relative overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img 
                src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=1920&q=80" 
                alt="Cinema" 
                class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-zinc-950 via-zinc-950/80 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-zinc-950/50"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-rose-600 flex items-center justify-center">
                    <i data-lucide="film" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <span class="text-2xl font-bold text-white tracking-tight">CINEVERSE</span>
                    <p class="text-xs text-zinc-400 tracking-widest uppercase">Premium Cinema</p>
                </div>
            </div>

            <!-- Main Headline -->
            <div class="space-y-8">
                <div class="space-y-4">
                    <p class="text-rose-500 font-medium tracking-wider uppercase text-sm">Now Showing</p>
                    <h1 class="text-5xl xl:text-6xl font-bold text-white leading-tight">
                        Experience Cinema
                        <br />
                        <span class="text-rose-500">Like Never Before</span>
                    </h1>
                    <p class="text-zinc-400 text-lg max-w-md leading-relaxed">
                        Book your tickets instantly. Choose your seats. Enjoy the magic of movies on the big screen.
                    </p>
                </div>

                <!-- Movie Cards -->
                <div class="flex gap-4">
                    <div class="group relative w-32 h-48 rounded-xl overflow-hidden cursor-pointer transition-transform hover:scale-105">
                        <img src="https://images.unsplash.com/photo-1578632767115-351597cf2477?w=400&q=80" alt="Movie 1" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3">
                            <p class="text-white text-xs font-medium truncate">Dune: Part Two</p>
                            <div class="flex items-center gap-1 mt-1">
                                <i data-lucide="star" class="w-3 h-3 text-yellow-500 fill-yellow-500"></i>
                                <span class="text-yellow-500 text-xs">8.9</span>
                            </div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-10 h-10 rounded-full bg-rose-600 flex items-center justify-center">
                                <i data-lucide="play" class="w-5 h-5 text-white fill-white ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                    <div class="group relative w-32 h-48 rounded-xl overflow-hidden cursor-pointer transition-transform hover:scale-105">
                        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=400&q=80" alt="Movie 2" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3">
                            <p class="text-white text-xs font-medium truncate">Oppenheimer</p>
                            <div class="flex items-center gap-1 mt-1">
                                <i data-lucide="star" class="w-3 h-3 text-yellow-500 fill-yellow-500"></i>
                                <span class="text-yellow-500 text-xs">9.2</span>
                            </div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-10 h-10 rounded-full bg-rose-600 flex items-center justify-center">
                                <i data-lucide="play" class="w-5 h-5 text-white fill-white ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                    <div class="group relative w-32 h-48 rounded-xl overflow-hidden cursor-pointer transition-transform hover:scale-105">
                        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=400&q=80" alt="Movie 3" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3">
                            <p class="text-white text-xs font-medium truncate">The Batman</p>
                            <div class="flex items-center gap-1 mt-1">
                                <i data-lucide="star" class="w-3 h-3 text-yellow-500 fill-yellow-500"></i>
                                <span class="text-yellow-500 text-xs">8.5</span>
                            </div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-10 h-10 rounded-full bg-rose-600 flex items-center justify-center">
                                <i data-lucide="play" class="w-5 h-5 text-white fill-white ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="flex gap-12">
                <div>
                    <p class="text-3xl font-bold text-white">50+</p>
                    <p class="text-zinc-500 text-sm">Cinemas</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">500K+</p>
                    <p class="text-zinc-500 text-sm">Happy Customers</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">1000+</p>
                    <p class="text-zinc-500 text-sm">Movies Screened</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-[45%] flex items-center justify-center p-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Mobile Logo -->
            <div class="flex items-center gap-3 lg:hidden">
                <div class="w-10 h-10 rounded-xl bg-rose-600 flex items-center justify-center">
                    <i data-lucide="film" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold text-white">CINEVERSE</span>
            </div>

            <!-- Header -->
            <div class="space-y-2">
                <h2 class="text-3xl font-bold text-white">{{ __('Welcome back') }}</h2>
                <p class="text-zinc-500">{{ __('Sign in to book your next movie experience') }}</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-950/50 border border-red-900/50">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-400 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-green-950/50 border border-green-900/50">
                    <p class="text-green-400 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-3">
                <button type="button" class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-zinc-900 border border-zinc-800 hover:border-zinc-700 hover:bg-zinc-800/50 transition-colors">
                    <i data-lucide="ticket" class="w-5 h-5 text-rose-500"></i>
                    <span class="text-zinc-300 text-sm font-medium">{{ __('My Tickets') }}</span>
                </button>
                <button type="button" class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-zinc-900 border border-zinc-800 hover:border-zinc-700 hover:bg-zinc-800/50 transition-colors">
                    <i data-lucide="clock" class="w-5 h-5 text-rose-500"></i>
                    <span class="text-zinc-300 text-sm font-medium">{{ __('Showtimes') }}</span>
                </button>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('web.login') }}" @submit="isLoading = true" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="text-zinc-400 text-sm font-medium uppercase tracking-wider">
                        {{ __('Email Address') }}
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="{{ __('your@email.com') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full h-12 px-4 bg-zinc-900 border border-zinc-800 text-white placeholder:text-zinc-600 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 rounded-xl transition-colors"
                    />
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-zinc-400 text-sm font-medium uppercase tracking-wider">
                            Password
                        </label>
                        <a href="#" class="text-sm text-rose-500 hover:text-rose-400 transition-colors">
                            {{ __('Forgot?') }}
                        </a>
                    </div>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="{{ __('Enter your password') }}"
                            required
                            autocomplete="current-password"
                            class="w-full h-12 px-4 pr-12 bg-zinc-900 border border-zinc-800 text-white placeholder:text-zinc-600 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 rounded-xl transition-colors"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300 transition-colors"
                        >
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        value="1"
                        class="w-4 h-4 rounded bg-zinc-900 border-zinc-700 text-rose-600 focus:ring-rose-500/20 focus:ring-2"
                    />
                    <label for="remember" class="text-zinc-400 text-sm cursor-pointer">
                        {{ __('Keep me signed in') }}
                    </label>
                </div>

                <button
                    type="submit"
                    :disabled="isLoading"
                    class="w-full h-12 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <template x-if="isLoading">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            <span>{{ __('Signing in...') }}</span>
                        </div>
                    </template>
                    <template x-if="!isLoading">
                        <span>{{ __('Sign In') }}</span>
                    </template>
                </button>
            </form>

            <!-- Sign Up Link -->
            <p class="text-center text-zinc-500">
                {{ __('New to Cineverse?') }} 
                <a href="#" class="text-rose-500 hover:text-rose-400 font-medium transition-colors">
                    {{ __('Create an account') }}
                </a>
            </p>

            <!-- Feature Pills -->
            <div class="flex flex-wrap justify-center gap-2 pt-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-xs text-zinc-400">
                    <i data-lucide="map-pin" class="w-3 h-3 text-rose-500"></i>
                    50+ Locations
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-xs text-zinc-400">
                    <i data-lucide="ticket" class="w-3 h-3 text-rose-500"></i>
                    Instant Booking
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-xs text-zinc-400">
                    <i data-lucide="star" class="w-3 h-3 text-rose-500"></i>
                    VIP Access
                </span>
            </div>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        // Re-initialize icons when Alpine updates
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                setTimeout(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 100);
            });
        });
    </script>
</body>
</html>
