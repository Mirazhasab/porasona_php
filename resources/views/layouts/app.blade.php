<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MCQ PRO')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- All CSS Files for Consistent Styling with cache-busting -->
    <link rel="stylesheet" href="{{ asset('css/asset-loader.css') }}?v={{ filemtime(public_path('css/asset-loader.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/button-fixes.css') }}?v={{ filemtime(public_path('css/button-fixes.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/global-fixes.css') }}?v={{ filemtime(public_path('css/global-fixes.css')) }}">
    
    
    <!-- JavaScript Files with cache-busting -->
    <script src="{{ asset('js/global-fixes.js') }}?v={{ filemtime(public_path('js/global-fixes.js')) }}"></script>

    @php
        use App\Helpers\ViteHelper;

        $usingHotReload = file_exists(public_path('hot'));
        $viteCss = ViteHelper::asset('resources/css/app.css');
        $viteJs = ViteHelper::asset('resources/js/app.js');
    @endphp

    @if ($usingHotReload)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @elseif ($viteCss)
        <link rel="stylesheet" href="{{ $viteCss }}">
        @if ($viteJs)
            <script type="module" src="{{ $viteJs }}" defer></script>
        @endif
    @endif
    @include('components.fontawesome-loader')
    
    <style>
    /* Professional text colors for app layout */
    body, p, span, div, h1, h2, h3, h4, h5, h6, li, td, th {
        color: #1f2937 !important;
    }
    
    .text-gray-900, .text-gray-800, .text-gray-700 {
        color: #1f2937 !important;
    }
    
    .text-gray-600 {
        color: #4b5563 !important;
    }
    
    .text-gray-500 {
        color: #6b7280 !important;
    }
    
    /* Navigation colors */
    .nav-link {
        color: #374151 !important;
    }
    
    .nav-link:hover {
        color: #1f2937 !important;
    }
    
    /* Card text colors */
    .card *, .bg-white *, .bg-light * {
        color: #1f2937 !important;
    }
    
    /* Exception for buttons */
    button, .btn, a.btn {
        color: inherit !important;
    }
    </style>
    
    @livewireStyles
</head>
<body class="bg-light">
    @php
        use Illuminate\Support\Facades\Route;
    @endphp
    <!-- Navigation -->
    @auth
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="/">MCQ PRO</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                    </li>
                    @role('user|moderator|admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('exams.index') }}" wire:navigate>Take Exams</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('results.index') }}" wire:navigate>My Results</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('mcq_sets.index') }}" wire:navigate>MCQ Sets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('mcq.posts.index') }}" wire:navigate>Posts</a>
                    </li>
                    @if(Route::has('subscriptions.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('subscriptions.*') ? 'text-primary fw-semibold' : '' }}" href="{{ route('subscriptions.index') }}" wire:navigate>
                            {{ auth()->user()?->hasActiveSubscription() ? 'My Subscription' : 'Subscription Plans' }}
                        </a>
                    </li>
                    @endif
                    @endrole
                    
                    @role('moderator|admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('moderate.comments') }}" wire:navigate>Moderate</a>
                    </li>
                    @endrole
                    
                    @role('admin')
                    <li class="nav-item">
                        @if(Route::has('admin.subscriptions.index'))
                            <a class="nav-link text-danger fw-semibold" href="{{ route('admin.subscriptions.index') }}" wire:navigate>MCQ Admin</a>
                        @elseif(Route::has('mcq.dashboard'))
                            <a class="nav-link text-danger fw-semibold" href="{{ route('mcq.dashboard') }}" wire:navigate>MCQ Admin</a>
                        @endif
                    </li>
                    @endrole
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ auth()->user()->name }}
                            @foreach(auth()->user()->roles as $role)
                                <span class="badge 
                                    @if($role->name === 'admin') bg-danger
                                    @elseif($role->name === 'moderator') bg-primary
                                    @else bg-success
                                    @endif ms-1">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}" wire:navigate>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth
    
    <!-- Main Content -->
    <main>
        @if(session('success'))
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            showAlert('{{ session('success') }}', 'success');
          });
        </script>
        @endif
        
        @if(session('error'))
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            showAlert('{{ session('error') }}', 'error');
          });
        </script>
        @endif
        
        @if(session('info'))
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            showAlert('{{ session('info') }}', 'info');
          });
        </script>
        @endif

        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/animated-alerts.js') }}?v={{ filemtime(public_path('js/animated-alerts.js')) }}"></script>
    <script src="{{ asset('js/alert-replacer.js') }}?v={{ filemtime(public_path('js/alert-replacer.js')) }}"></script>
    <script src="{{ asset('js/no-reload-system.js') }}?v={{ filemtime(public_path('js/no-reload-system.js')) }}"></script>
    <script src="{{ asset('js/button-handler.js') }}?v={{ filemtime(public_path('js/button-handler.js')) }}"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
