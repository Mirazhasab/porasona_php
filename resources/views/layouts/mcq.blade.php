<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'MCQ Pro')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <script>
    function toggleMobileSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('mobileOverlay');
      if (!sidebar || !overlay) return;
      
      if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
      }
    }
    
    function closeMobileSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('mobileOverlay');
      if (!sidebar || !overlay) return;
      
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      document.body.style.overflow = '';
    }
    
    function toggleAvatarDropdown(type) {
      const dropdown = document.getElementById(type + 'AvatarDropdown');
      if (!dropdown) return;
      
      const isVisible = dropdown.classList.contains('show');
      document.querySelectorAll('.avatar-dropdown').forEach(d => d.classList.remove('show'));
      
      if (!isVisible) {
        dropdown.classList.add('show');
      }
    }
    
    window.toggleMobileSidebar = toggleMobileSidebar;
    window.closeMobileSidebar = closeMobileSidebar;
    window.toggleAvatarDropdown = toggleAvatarDropdown;
  </script>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide icons (with CDN fallback) for pages using data-lucide -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" onerror="(function(){var s=document.createElement('script');s.src='https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js';document.head.appendChild(s);})();"></script>
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- CSS with cache-busting for cPanel compatibility -->
  <link rel="stylesheet" href="{{ asset('css/asset-loader.css') }}?v={{ filemtime(public_path('css/asset-loader.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/force-light-theme.css') }}?v={{ filemtime(public_path('css/force-light-theme.css')) }}">
  
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { 50: '#f9fafb', 500: '#6b7280', 600: '#4b5563', 700: '#374151' },
            gray: { 50: '#f9fafb', 100: '#f3f4f6', 800: '#4b5563', 900: '#374151' }
          }
        }
      }
    }
  </script>
  @livewireStyles
  @stack('styles')
  
  <style>
    /* Force Light Theme Override */
    * { color: #1e293b !important; }
    body { background-color: #f8fafc !important; }
    .bg-gray-50, .bg-gray-100 { background-color: #f8fafc !important; }
    .bg-white { background-color: #ffffff !important; }
    .bg-gray-800, .bg-gray-900, .bg-black { background-color: #ffffff !important; }
    .text-white { color: #1e293b !important; }
    .text-gray-900, .text-gray-800, .text-gray-700 { color: #1e293b !important; }
    .text-gray-600 { color: #475569 !important; }
    .text-gray-500, .text-gray-400 { color: #64748b !important; }
    
    /* Navigation */
    .nav-item { color: #475569 !important; }
    .nav-item:hover { background-color: #f1f5f9 !important; color: #1e293b !important; }
    .nav-item.active { background-color: #dbeafe !important; color: #3b82f6 !important; }
    
    /* Mobile Navigation */
    .mobile-nav-item { color: #64748b !important; }
    .mobile-nav-item.active { color: #3b82f6 !important; }
    
    /* Buttons */
    button, .btn { background-color: #ffffff !important; color: #1e293b !important; border-color: #cbd5e1 !important; }
    button:hover, .btn:hover { background-color: #f1f5f9 !important; }
    .btn-primary, button[type="submit"] { background-color: #3b82f6 !important; color: #ffffff !important; }
    
    /* Forms */
    input, textarea, select { background-color: #ffffff !important; color: #1e293b !important; border-color: #cbd5e1 !important; }
    
    /* Icons */
    i { color: inherit !important; }
    
    /* Mobile Sidebar Fix */
    #sidebar { 
      z-index: 50 !important; 
      transition: transform 0.3s ease !important;
    }
    #mobileOverlay { 
      z-index: 40 !important; 
      transition: opacity 0.3s ease !important;
    }
    .mobile-nav { z-index: 60 !important; }
    
    /* Sidebar states */
    #sidebar.-translate-x-full {
      transform: translateX(-100%) !important;
    }
    
    #sidebar:not(.-translate-x-full) {
      transform: translateX(0) !important;
    }
    
    #mobileOverlay.hidden {
      display: none !important;
      opacity: 0 !important;
    }
    
    #mobileOverlay:not(.hidden) {
      display: block !important;
      opacity: 1 !important;
    }
    
    /* Avatar Dropdown */
    .avatar-dropdown { 
      position: absolute; 
      top: 100%; 
      right: 0; 
      background: white; 
      border: 1px solid #e2e8f0; 
      border-radius: 8px; 
      box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
      min-width: 200px; 
      z-index: 1000; 
      display: none; 
    }
    .avatar-dropdown.show { display: block; }
    .avatar-dropdown a, .avatar-dropdown button { 
      display: block; 
      width: 100%; 
      padding: 8px 16px; 
      text-align: left; 
      border: none; 
      background: none; 
      color: #1e293b !important; 
    }
    .avatar-dropdown a:hover, .avatar-dropdown button:hover { 
      background-color: #f1f5f9 !important; 
    }
  </style>
</head>
<body style="background-color: #f8fafc !important; color: #1e293b !important;">
  <!-- Mobile Header -->
  <div class="lg:hidden fixed top-0 left-0 right-0 bg-white z-60 border-b border-gray-200">
    <div class="flex items-center justify-between p-3">
      <button id="mobileMenuToggle" class="p-2 rounded-xl hover:bg-gray-100 transition-colors" onclick="toggleMobileSidebar()">
        <i class="fas fa-bars w-5 h-5 text-gray-700"></i>
      </button>
      <h1 class="text-lg font-semibold text-gray-900">MCQ Pro</h1>
      <div class="relative">
        <button onclick="toggleAvatarDropdown('mobile')" class="w-8 h-8 rounded border-2 border-gray-300 bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
          <span class="text-xs font-medium text-gray-700">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
        </button>
        <div id="mobileAvatarDropdown" class="avatar-dropdown">
          @php
            $user = auth()->user();
            $isAdmin = false;
            if ($user) {
              try {
                if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                  $isAdmin = true;
                }
              } catch (\Exception $e) {}
              if (!$isAdmin && $user->role === 'admin') {
                $isAdmin = true;
              }
            }
          @endphp
          <div class="p-3 border-b border-gray-200">
            <p class="font-medium text-sm">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
            <p class="text-xs text-blue-600">{{ $isAdmin ? 'Administrator' : 'User' }}</p>
          </div>
          <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50"><i class="fas fa-user mr-2"></i>Profile</a>
          <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50"><i class="fas fa-cog mr-2"></i>Settings</a>
          <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600">
              <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="flex h-screen">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-white border-r border-gray-200">
      <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-900">MCQ Pro</h1>
      </div>
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        @php
          $user = auth()->user();
          $isAdmin = false;
          if ($user) {
            try {
              // Try Spatie role first
              if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                $isAdmin = true;
              }
            } catch (\Exception $e) {
              // Spatie not working, use role column
            }
            // Fallback to role column
            if (!$isAdmin && $user->role === 'admin') {
              $isAdmin = true;
            }
          }
          
          $userAccess = $user->access;
          
          $hasAccess = function($permission) use ($userAccess, $isAdmin) {
              if ($isAdmin) return true;
              if (!$userAccess) return false;
              return $userAccess->{$permission} ?? false;
          };
        @endphp
        
        <a href="{{ route('mcq.dashboard') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.dashboard') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-home w-5 h-5"></i>
          <span>Dashboard</span>
          @if(request()->routeIs('mcq.dashboard'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        
        @if($hasAccess('practice'))
        <a href="{{ route('mcq.questions') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.questions') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-question-circle w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Question Bank' : 'Practice Questions' }}</span>
          @if(request()->routeIs('mcq.questions'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif
        
        @if($hasAccess('exams'))
        <a href="{{ route('exams.index') }}" class="nav-item {{ request()->routeIs('exams.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-clipboard-list w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Manage Exams' : 'My Exams' }}</span>
          @if(request()->routeIs('exams.*'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif
        
        @if($hasAccess('results'))
        <a href="{{ route('mcq.analytics') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.analytics') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-chart-bar w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Analytics' : 'My Results' }}</span>
          @if(request()->routeIs('mcq.analytics'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif
        
        @if($hasAccess('classmate'))
        <a href="{{ route('mcq.students') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.students') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-users w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Manage Students' : 'Classmates' }}</span>
          @if(request()->routeIs('mcq.students'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif
        
        @if($hasAccess('leaderboard'))
        <a href="{{ route('mcq.leaderboard') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.leaderboard') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-trophy w-5 h-5"></i>
          <span>Leaderboard</span>
          @if(request()->routeIs('mcq.leaderboard'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif
        
        @if($hasAccess('post'))
        <a href="{{ route('mcq.posts.index') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.posts.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-newspaper w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Manage Posts' : 'Posts' }}</span>
          @if(request()->routeIs('mcq.posts.*'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif

        <a href="{{ route('subscriptions.index') }}" class="nav-item {{ request()->routeIs('subscriptions.*') ? 'active' : '' }} flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-gem w-5 h-5"></i>
          <span>Subscription Plans</span>
          @if(request()->routeIs('subscriptions.*'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @elseif(!auth()->user()?->hasActiveSubscription())
            <span class="ml-auto text-xs font-semibold text-indigo-600">Upgrade</span>
          @endif
        </a>

        @if($hasAccess('read_access'))
        <a href="{{ route('mcq.read') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.read') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-book-open w-5 h-5"></i>
          <span>Read MCQ</span>
          @if(request()->routeIs('mcq.read'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif

        @if($hasAccess('notes'))
        <a href="{{ route('mcq.notes.index') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.notes.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-sticky-note w-5 h-5"></i>
          <span>Notes Hub</span>
          @if(request()->routeIs('mcq.notes.*'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif
        
        @if($hasAccess('mcq_management'))
        <a href="{{ route('mcq_sets.index') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq_sets.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-list-check w-5 h-5"></i>
          <span>MCQ Management</span>
          @if(request()->routeIs('mcq_sets.*'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        @endif
        
        @if($isAdmin)
        <a href="{{ route('mcq.settings') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.settings') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-cog w-5 h-5"></i>
          <span>Settings</span>
          @if(request()->routeIs('mcq.settings'))
            <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full"></div>
          @endif
        </a>
        
        <div class="border-t border-gray-200 mt-2 pt-2">
          <p class="text-xs font-medium text-gray-500 px-3 py-1 uppercase tracking-wider">Admin Tools</p>
          <a href="{{ route('admin.user-access.index') }}" wire:navigate class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
            <i class="fas fa-shield-alt w-5 h-5"></i>
            <span>User Access</span>
          </a>
          <a href="{{ route('admin.subscriptions.index') }}" class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
            <i class="fas fa-dollar-sign w-5 h-5"></i>
            <span>Subscriptions</span>
          </a>
          <a href="{{ route('admin.notes.index') }}" class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
            <i class="fas fa-sticky-note w-5 h-5"></i>
            <span>Notes Moderation</span>
          </a>
        </div>
        @endif
        
        <div class="border-t border-gray-200 mt-4 pt-4">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group w-full text-left">
              <i class="fas fa-sign-out-alt w-5 h-5"></i>
              <span>Logout</span>
            </button>
          </form>
        </div>
      </nav>
    </aside>

    <!-- Mobile Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 border-r transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden bg-white flex flex-col">
      <div class="flex items-center justify-between p-4 border-b border-gray-200 flex-shrink-0">
        <h1 class="text-xl font-bold text-gray-900">MCQ Pro</h1>
        <button id="closeSidebar" class="p-2 rounded-xl hover:bg-gray-100 transition-colors" onclick="closeMobileSidebar()">
          <i class="fas fa-times w-5 h-5"></i>
        </button>
      </div>
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto pb-20">
        <a href="{{ route('mcq.dashboard') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.dashboard') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-home w-5 h-5"></i>
          <span>Dashboard</span>
        </a>
        
        @if($hasAccess('practice'))
        <a href="{{ route('mcq.questions') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.questions') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-question-circle w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Question Bank' : 'Practice Questions' }}</span>
        </a>
        @endif
        
        @if($hasAccess('exams'))
        <a href="{{ route('exams.index') }}" class="nav-item {{ request()->routeIs('exams.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-clipboard-list w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Manage Exams' : 'My Exams' }}</span>
        </a>
        @endif
        
        @if($hasAccess('results'))
        <a href="{{ route('mcq.analytics') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.analytics') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-chart-bar w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Analytics' : 'My Results' }}</span>
        </a>
        @endif
        
        @if($hasAccess('classmate'))
        <a href="{{ route('mcq.students') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.students') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-users w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Manage Students' : 'Classmates' }}</span>
        </a>
        @endif
        
        @if($hasAccess('leaderboard'))
        <a href="{{ route('mcq.leaderboard') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.leaderboard') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-trophy w-5 h-5"></i>
          <span>Leaderboard</span>
        </a>
        @endif
        
        @if($hasAccess('post'))
        <a href="{{ route('mcq.posts.index') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.posts.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-newspaper w-5 h-5"></i>
          <span>{{ $isAdmin ? 'Manage Posts' : 'Posts' }}</span>
        </a>
        @endif

        <a href="{{ route('subscriptions.index') }}" class="nav-item {{ request()->routeIs('subscriptions.*') ? 'active' : '' }} flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-gem w-5 h-5"></i>
          <span>Subscription Plans</span>
          @if(!auth()->user()?->hasActiveSubscription())
            <span class="ml-auto text-xs font-semibold text-indigo-600">Upgrade</span>
          @endif
        </a>

        @if($hasAccess('read_access'))
        <a href="{{ route('mcq.read') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.read') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-book-open w-5 h-5"></i>
          <span>Read MCQ</span>
        </a>
        @endif

        @if($hasAccess('notes'))
        <a href="{{ route('mcq.notes.index') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.notes.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-sticky-note w-5 h-5"></i>
          <span>Notes Hub</span>
        </a>
        @endif
        
        @if($hasAccess('mcq_management'))
        <a href="{{ route('mcq_sets.index') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq_sets.*') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-list-check w-5 h-5"></i>
          <span>MCQ Management</span>
        </a>
        @endif
        
        @if($isAdmin)
        <a href="{{ route('mcq.settings') }}" wire:navigate class="nav-item {{ request()->routeIs('mcq.settings') ? 'active' : '' }} flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
          <i class="fas fa-cog w-5 h-5"></i>
          <span>Settings</span>
        </a>
        
        <div class="border-t border-gray-200 mt-2 pt-2">
          <p class="text-xs font-medium text-gray-500 px-3 py-1 uppercase tracking-wider">Admin Tools</p>
          <a href="{{ route('admin.user-access.index') }}" wire:navigate class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
            <i class="fas fa-shield-alt w-5 h-5"></i>
            <span>User Access</span>
          </a>
          <a href="{{ route('admin.subscriptions.index') }}" class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group">
            <i class="fas fa-dollar-sign w-5 h-5"></i>
            <span>Subscriptions</span>
          </a>
        </div>
        @endif
        
        <div class="border-t border-gray-200 mt-4 pt-4">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group w-full text-left">
              <i class="fas fa-sign-out-alt w-5 h-5"></i>
              <span>Logout</span>
            </button>
          </form>
        </div>
      </nav>
    </aside>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 z-40 lg:hidden hidden transition-opacity duration-300" style="background-color: rgba(30, 41, 59, 0.5) !important;" onclick="closeMobileSidebar()"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden pt-14 lg:pt-0 lg:ml-64">
      <!-- Header -->
      <header class="hidden lg:block bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
            <p class="text-sm text-gray-600 mt-1">@yield('page-subtitle', 'Welcome back')</p>
          </div>
          <div class="flex items-center space-x-4">
            <div class="relative">
              <button onclick="toggleAvatarDropdown('desktop')" class="w-8 h-8 rounded-full flex items-center justify-center hover:ring-2 hover:ring-blue-300 transition-all" style="background-color: #3b82f6 !important;">
                <span class="text-sm font-medium" style="color: #ffffff !important;">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
              </button>
              <div id="desktopAvatarDropdown" class="avatar-dropdown">
                <div class="p-3 border-b border-gray-200">
                  <p class="font-medium text-sm">{{ auth()->user()->name ?? 'User' }}</p>
                  <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                  <p class="text-xs text-blue-600">{{ $isAdmin ? 'Administrator' : 'User' }}</p>
                </div>
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50"><i class="fas fa-user mr-2"></i>Profile</a>
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50"><i class="fas fa-cog mr-2"></i>Settings</a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content Area -->
      <main class="flex-1 overflow-y-auto pb-20 lg:pb-6" style="background-color: #f8fafc !important;">
        @if(session('success'))
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            alert('{{ session('success') }}');
          });
        </script>
        @endif
        
        @if(session('error'))
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            alert('{{ session('error') }}');
          });
        </script>
        @endif
        
        @if(session('warning'))
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            alert('{{ session('warning') }}');
          });
        </script>
        @endif
        
        @if(session('info'))
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            alert('{{ session('info') }}');
          });
        </script>
        @endif
        
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Mobile Bottom Navigation -->
  <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-60">
    <div class="flex items-center justify-around py-2">
      <a href="{{ route('mcq.dashboard') }}" wire:navigate class="mobile-nav-item {{ request()->routeIs('mcq.dashboard') ? 'active' : '' }} flex flex-col items-center px-3 py-2 rounded-lg transition-colors">
        <i class="fas fa-home text-xl mb-1"></i>
        <span class="text-xs">Home</span>
      </a>
      <a href="{{ route('exams.index') }}" class="mobile-nav-item {{ request()->routeIs('exams.*') ? 'active' : '' }} flex flex-col items-center px-3 py-2 rounded-lg transition-colors">
        <i class="fas fa-clipboard-list text-xl mb-1"></i>
        <span class="text-xs">Exams</span>
      </a>
      <a href="{{ route('mcq.leaderboard') }}" wire:navigate class="mobile-nav-item {{ request()->routeIs('mcq.leaderboard') ? 'active' : '' }} flex flex-col items-center px-3 py-2 rounded-lg transition-colors">
        <i class="fas fa-trophy text-xl mb-1"></i>
        <span class="text-xs">Ranks</span>
      </a>
      <a href="{{ route('mcq.posts.index') }}" wire:navigate class="mobile-nav-item {{ request()->routeIs('mcq.posts.*') ? 'active' : '' }} flex flex-col items-center px-3 py-2 rounded-lg transition-colors">
        <i class="fas fa-newspaper text-xl mb-1"></i>
        <span class="text-xs">Posts</span>
      </a>
      <button type="button" id="mobileMoreBtn" class="mobile-nav-item flex flex-col items-center px-3 py-2 rounded-lg transition-colors" onclick="toggleMobileSidebar()">
        <i class="fas fa-bars text-xl mb-1"></i>
        <span class="text-xs">More</span>
      </button>
    </div>
  </nav>

  @livewireScripts
  <!-- Global JS with cache-busting for cPanel -->
  <script src="{{ asset('js/global-fixes.js') }}?v={{ filemtime(public_path('js/global-fixes.js')) }}"></script>
  <script src="{{ asset('js/sidebar-universal.js') }}?v={{ filemtime(public_path('js/sidebar-universal.js')) }}"></script>
  <script src="{{ asset('js/prevent-double-submit.js') }}?v={{ filemtime(public_path('js/prevent-double-submit.js')) }}"></script>
  <script src="{{ asset('js/no-reload-system.js') }}?v={{ filemtime(public_path('js/no-reload-system.js')) }}"></script>
  
  @stack('scripts')
</body>
</html>