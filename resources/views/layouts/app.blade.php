<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Admin Dashboard')) - {{ __('Admin Panel') }}</title>
    @php $assetVersion = config('app.asset_version'); @endphp
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') . '?v=' . $assetVersion }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: 'hsl(0, 0%, 100%)',
                        foreground: 'hsl(0, 0%, 3.9%)',
                        card: 'hsl(0, 0%, 100%)',
                        'card-foreground': 'hsl(0, 0%, 3.9%)',
                        primary: 'hsl(0, 0%, 9%)',
                        'primary-foreground': 'hsl(0, 0%, 98%)',
                        secondary: 'hsl(0, 0%, 96.1%)',
                        'secondary-foreground': 'hsl(0, 0%, 9%)',
                        muted: 'hsl(0, 0%, 96.1%)',
                        'muted-foreground': 'hsl(0, 0%, 45.1%)',
                        border: 'hsl(0, 0%, 89.8%)',
                    }
                }
            }
        }
    </script>
    
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Flatpickr altInput styling to match other inputs */
        input.flatpickr-alt-input,
        .flatpickr-input.flatpickr-mobile,
        input[type="text"].form-control + input,
        .datepicker + input,
        .datepicker-future + input {
            width: 100% !important;
            padding: 10px 12px !important;
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
            font-size: 1em !important;
            background: white !important;
            cursor: pointer !important;
            box-sizing: border-box !important;
        }
        input.flatpickr-alt-input:focus,
        .datepicker + input:focus,
        .datepicker-future + input:focus {
            border-color: #3498db !important;
            outline: none !important;
        }
    </style>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="h-screen bg-gray-50 font-sans antialiased overflow-hidden" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen">
        {{-- Sidebar --}}
        @include('layouts.sidebar')
        
        {{-- Overlay for mobile --}}
        <div 
            class="fixed inset-0 bg-black bg-opacity-30 z-20 md:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>
        
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            {{-- Header --}}
            @include('layouts.header')
            
            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto p-4 bg-gray-50">
                {{-- Success Messages --}}
                @if (session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Error Messages from Session --}}
                @if (session('error'))
                    <div style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Error Messages from Validation --}}
                @if (isset($errors) && $errors->any())
                    <div style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Flatpickr Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script>
        // Initialize Flatpickr with Vietnamese locale for all date inputs
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr.localize(flatpickr.l10ns.vn);
            
            // Auto-init for elements with class 'datepicker'
            flatpickr('.datepicker', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                altInputClass: 'flatpickr-alt-input',
                locale: 'vn',
                allowInput: true,
                disableMobile: true
            });

            // For datepicker with future dates only (minDate = today)
            flatpickr('.datepicker-future', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                altInputClass: 'flatpickr-alt-input',
                locale: 'vn',
                allowInput: true,
                disableMobile: true,
                minDate: 'today'
            });
        });
    </script>

    @stack('scripts')
</body>
</html>

