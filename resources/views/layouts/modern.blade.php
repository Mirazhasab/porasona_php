<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MCQ Pro')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            // Force light mode on every page load
            document.addEventListener('DOMContentLoaded', function() {
                document.documentElement.classList.remove('dark');
                try {
                    localStorage.removeItem('theme');
                    sessionStorage.removeItem('theme');
                } catch (e) {}
            });
        </script>
    <link href="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- All CSS Files with cache-busting -->
    <link rel="stylesheet" href="{{ asset('css/asset-loader.css') }}?v={{ filemtime(public_path('css/asset-loader.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/button-fixes.css') }}?v={{ filemtime(public_path('css/button-fixes.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-spacing.css') }}?v={{ filemtime(public_path('css/dashboard-spacing.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/mcq-options-fullwidth.css') }}?v={{ filemtime(public_path('css/mcq-options-fullwidth.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/global-fixes.css') }}?v={{ filemtime(public_path('css/global-fixes.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/auth-pages.css') }}?v={{ filemtime(public_path('css/auth-pages.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/mobile-auth.css') }}?v={{ filemtime(public_path('css/mobile-auth.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/universal-card-fix.css') }}?v={{ filemtime(public_path('css/universal-card-fix.css')) }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                        gray: { 50: '#f9fafb', 100: '#f3f4f6', 800: '#1f2937', 900: '#111827' }
                    },
                    animation: {
                        'slide-up': 'slideUp 0.3s ease-out',
                        'fade-in': 'fadeIn 0.2s ease-out',
                        'bounce-in': 'bounceIn 0.4s ease-out'
                    }
                }
            },
            darkMode: 'class'
        }
    </script>
    <style>
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes bounceIn { 0% { transform: scale(0.3); opacity: 0; } 50% { transform: scale(1.05); } 70% { transform: scale(0.9); } 100% { transform: scale(1); opacity: 1; } }
        .glass { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.8); }
</head>
<body class="h-full bg-gray-50  transition-colors duration-300">
    <!-- CSS and JS with cache-busting -->
    <script src="{{ asset('js/global-fixes.js') }}?v={{ filemtime(public_path('js/global-fixes.js')) }}"></script>
    <script src="{{ asset('js/no-reload-system.js') }}?v={{ filemtime(public_path('js/no-reload-system.js')) }}"></script>
    <!-- Mobile Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-50 glass border-b border-gray-200 ">
        <div class="flex items-center justify-between p-3">
            <button id="mobileMenuBtn" class="p-2 rounded-xl hover:bg-gray-100  transition-colors">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <h1 class="text-lg font-semibold text-blue-900 ">@yield('page-title', 'MCQ Pro')</h1>
            <div class="flex items-center space-x-2">
                <button id="darkModeToggle" class="p-2 rounded-xl hover:bg-gray-100  transition-colors">
                    <i data-lucide="moon" class="w-5 h-5 "></i>
                    <i data-lucide="sun" class="w-5 h-5 hidden "></i>
                </button>
                <div class="w-8 h-8 rounded-xl bg-primary-500 flex items-center justify-center">
                    <span class="text-xs font-medium text-white">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
{{ ... }}
            </header>
            <!-- Content Area -->
            <div class="p-3 lg:p-6 space-y-6">
                @if(session('success'))
                    <div id="successToast" class="animate-slide-up bg-green-50  border border-green-200  rounded-xl p-3 flex items-center space-x-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-blue-900 "></i>
                        <span class="text-sm text-blue-900 ">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
{{ ... }}
        window.showToast = (message, type = 'success') => {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info';
            const colors = {
                success: 'bg-green-50  border-green-200  text-blue-900 ',
                error: 'bg-red-50  border-red-200  text-red-800 ',
                info: 'bg-blue-50  border-blue-200  text-blue-800 '
            };

            toast.className = `animate-bounce-in border rounded-xl p-3 flex items-center space-x-3 ${colors[type]}`;
{{ ... }}
                <i data-lucide="${icon}" class="w-5 h-5"></i>
                <span class="text-sm font-medium">${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto p-1 rounded-lg hover:bg-black hover:bg-opacity-10">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;

            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => toast.remove(), 5000);
        };

        // Global loading functions
        window.showLoading = () => document.getElementById('loadingOverlay').classList.remove('hidden');
        window.hideLoading = () => document.getElementById('loadingOverlay').classList.add('hidden');
    </script>
    @stack('scripts')
</body>
</html>