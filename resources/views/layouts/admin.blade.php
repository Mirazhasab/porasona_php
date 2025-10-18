<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'MCQ System')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  
  <!-- All CSS Files with cache-busting for cPanel -->
  <link rel="stylesheet" href="{{ asset('css/asset-loader.css') }}?v={{ filemtime(public_path('css/asset-loader.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/button-fixes.css') }}?v={{ filemtime(public_path('css/button-fixes.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard-spacing.css') }}?v={{ filemtime(public_path('css/dashboard-spacing.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/global-fixes.css') }}?v={{ filemtime(public_path('css/global-fixes.css')) }}">
  
  <!-- JavaScript Files with cache-busting -->
  <script src="{{ asset('js/global-fixes.js') }}?v={{ filemtime(public_path('js/global-fixes.js')) }}"></script>
  <script src="{{ asset('js/no-reload-system.js') }}?v={{ filemtime(public_path('js/no-reload-system.js')) }}"></script>
  
  <script src="{{ asset('js/dashboard-enhanced.js') }}?v={{ file_exists(public_path('js/dashboard-enhanced.js')) ? filemtime(public_path('js/dashboard-enhanced.js')) : time() }}"></script>
  <script src="{{ asset('js/admin-enhanced.js') }}?v={{ file_exists(public_path('js/admin-enhanced.js')) ? filemtime(public_path('js/admin-enhanced.js')) : time() }}"></script>
  <script src="{{ asset('js/mcq-utils.js') }}?v={{ file_exists(public_path('js/mcq-utils.js')) ? filemtime(public_path('js/mcq-utils.js')) : time() }}"></script>
  
  <style>
  /* Professional text colors for admin layout */
  body, p, span, div, h1, h2, h3, h4, h5, h6, li, td, th, label {
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
  
  /* Sidebar text colors */
  .sidebar *, .nav-item * {
      color: #374151 !important;
  }
  
  /* Card and content text */
  .card *, .bg-white *, .content * {
      color: #1f2937 !important;
  }
  
  /* Table text */
  table, table *, .table * {
      color: #1f2937 !important;
  }
  
  /* Exception for buttons and special elements */
  button, .btn, a.btn, .badge {
      color: inherit !important;
  }
  </style>
  
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3b82f6',
            secondary: '#1e40af',
            accent: '#f59e0b'
          }
        }
      }
    }
  </script>
  @stack('styles')
</head>
<body class="bg-gray-50 font-sans">
  <!-- Toast Notification -->
  <div id="toast-container" style="position: fixed; top: 32px; right: 32px; z-index: 9999; display: none;">
    <div id="toast-message" class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2 min-w-[220px]" style="display: none;">
      <span id="toast-text"></span>
      <button onclick="hideToast()" class="ml-4 text-white hover:text-gray-200 focus:outline-none">&times;</button>
    </div>
  </div>
  <!-- Mobile Header (hidden on desktop) -->
  <div class="lg:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-50 border-b border-gray-200">
    <div class="flex items-center justify-between px-4 py-3">
      <button id="adminMenuToggle" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="menu" class="lucide lucide-menu w-6 h-6" style="color: rgb(51, 51, 51); stroke: rgb(51, 51, 51);"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>
      </button>
      <h1 class="text-lg font-bold text-gray-800">Admin Panel</h1>
      <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
        <i data-lucide="user" class="w-4 h-4 text-white"></i>
      </div>
    </div>
  </div>

  <div class="flex h-screen">
    <!-- Sidebar (Desktop: always visible, Mobile: slide-in) -->
    <aside id="adminSidebar" class="w-64 lg:w-64 w-full h-full lg:h-auto bg-white shadow-lg border-r border-gray-200 fixed lg:static inset-0 lg:inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-[70]">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div>
          <h1 class="text-xl font-bold text-gray-800">MCQ System</h1>
          <p class="text-sm text-gray-500 mt-1">Management Portal</p>
        </div>
        <button id="closeAdminSidebar" class="lg:hidden p-3 rounded-xl hover:bg-gray-100 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6" style="color: rgb(51, 51, 51); stroke: rgb(51, 51, 51);"><path d="m18 6-12 12"></path><path d="m6 6 12 12"></path></svg>
        </button>
      </div>
      
    @php
    use Illuminate\Support\Facades\Route;

    $navigationBlueprint = [
      'Overview' => [
        [
          'label' => 'Dashboard',
          'icon' => 'home',
          'routes' => ['admin.dashboard', 'mcq.dashboard'],
          'active' => ['admin.dashboard', 'mcq.dashboard'],
        ],
        [
          'label' => 'Analytics',
          'icon' => 'bar-chart-3',
          'routes' => ['admin.analytics.index', 'mcq.analytics'],
          'active' => ['admin.analytics.*', 'mcq.analytics'],
        ],
      ],
      'Learning Content' => [
        [
          'label' => 'Question Bank',
          'icon' => 'help-circle',
          'routes' => ['admin.questions.index', 'mcq.questions'],
          'active' => ['admin.questions.*', 'mcq.questions'],
        ],
        [
          'label' => 'Examinations',
          'icon' => 'clipboard-list',
          'routes' => ['admin.examinations.index', 'exams.index'],
          'active' => ['admin.examinations.*', 'exams.*'],
        ],
        [
          'label' => 'Posts',
          'icon' => 'newspaper',
          'routes' => ['admin.posts.index', 'mcq.posts.index'],
          'active' => ['admin.posts.*', 'mcq.posts.*'],
        ],
        [
          'label' => 'Leaderboard',
          'icon' => 'trophy',
          'routes' => ['admin.leaderboard.index', 'mcq.leaderboard'],
          'active' => ['admin.leaderboard.*', 'mcq.leaderboard'],
        ],
      ],
      'People & Access' => [
        [
          'label' => 'Students',
          'icon' => 'users',
          'routes' => ['admin.students.index', 'mcq.students'],
          'active' => ['admin.students.*', 'mcq.students'],
        ],
        [
          'label' => 'User Management',
          'icon' => 'user-cog',
          'routes' => ['admin.users.index'],
          'active' => ['admin.users.*'],
        ],
        [
          'label' => 'User Access',
          'icon' => 'shield-check',
          'routes' => ['admin.user-access.index'],
          'active' => ['admin.user-access.*'],
        ],
      ],
      'Billing & Monetization' => [
        [
          'label' => 'Subscriptions',
          'icon' => 'dollar-sign',
          'routes' => ['admin.subscriptions.index'],
          'active' => [
            'admin.subscriptions.index',
            'admin.subscriptions.create',
            'admin.subscriptions.edit',
            'admin.subscriptions.assign-form',
            'admin.subscriptions.assign',
            'admin.subscription-settings.*',
          ],
          'badge' => 'New',
        ],
        [
          'label' => 'Manual Reviews',
          'icon' => 'clipboard-check',
          'routes' => ['admin.subscriptions.manual-review'],
          'active' => ['admin.subscriptions.manual-review'],
        ],
      ],
      'System' => [
        [
          'label' => 'Settings',
          'icon' => 'settings',
          'routes' => ['admin.settings.index', 'mcq.settings'],
          'active' => ['admin.settings.*', 'mcq.settings'],
        ],
      ],
    ];

    $adminNavGroups = [];

    foreach ($navigationBlueprint as $groupTitle => $entries) {
      $resolvedEntries = [];

      foreach ($entries as $entry) {
        $routeNames = (array) ($entry['routes'] ?? []);
        $activePatterns = (array) ($entry['active'] ?? $routeNames);

        $resolvedUrl = null;
        foreach ($routeNames as $routeName) {
          if (Route::has($routeName)) {
            $resolvedUrl = route($routeName);
            break;
          }
        }

        if (!$resolvedUrl) {
          continue;
        }

        $isActive = false;
        foreach ($activePatterns as $pattern) {
          if (request()->routeIs($pattern)) {
            $isActive = true;
            break;
          }
        }

        $resolvedEntries[] = [
          'label' => $entry['label'],
          'icon' => $entry['icon'],
          'url' => $resolvedUrl,
          'active' => $isActive,
          'badge' => $entry['badge'] ?? null,
        ];
      }

      if (!empty($resolvedEntries)) {
        $adminNavGroups[] = [
          'title' => $groupTitle,
          'items' => $resolvedEntries,
        ];
      }
    }
    @endphp

    <nav class="mt-6 pb-20">
    <div class="px-4 space-y-6">
      @foreach ($adminNavGroups as $group)
      <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">{{ $group['title'] }}</p>
        <div class="space-y-2">
        @foreach ($group['items'] as $item)
          <a href="{{ $item['url'] }}" class="nav-item {{ $item['active'] ? 'active' : '' }} flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors">
          <span class="flex items-center">
            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 mr-3"></i>
            {{ $item['label'] }}
          </span>
          @if(!empty($item['badge']))
            <span class="ml-2 inline-flex items-center px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide rounded-full bg-indigo-100 text-indigo-600">{{ $item['badge'] }}</span>
          @endif
          </a>
        @endforeach
        </div>
      </div>
      @endforeach
    </div>
        
        <div class="mt-8 px-4">
          <div class="border-t border-gray-200 pt-4">
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors w-full text-left">
                <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
                <span>Logout</span>
              </button>
            </form>
          </div>
        </div>
      </nav>
    </aside>

    <!-- Mobile Overlay -->
  <div id="adminOverlay" class="fixed inset-0 bg-white bg-opacity-80 z-30 lg:hidden hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden pt-14 lg:pt-0">
      <!-- Header (Desktop only) -->
      <header class="hidden lg:block bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
            <p class="text-sm text-gray-600 mt-1">@yield('page-subtitle', 'Welcome back, Administrator')</p>
          </div>
          <div class="flex items-center space-x-4">
            <a href="{{ route('admin.subscriptions.index') }}" class="hidden lg:inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-indigo-200 text-indigo-600 hover:bg-indigo-50 text-sm font-medium transition-colors">
              <i data-lucide="dollar-sign" class="w-4 h-4"></i>
              Subscription Hub
            </a>
            <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
              <i data-lucide="bell" class="w-5 h-5"></i>
            </button>
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-4 h-4 text-white"></i>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-6 pb-20 lg:pb-6">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Mobile Bottom Navigation -->
  <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
  <div class="flex items-center justify-start gap-2 py-2 px-2 overflow-x-auto">
      @foreach (
        collect($adminNavGroups)->flatMap(fn ($group) => $group['items'])->all()
      as $item)
        <a href="{{ $item['url'] }}" class="mobile-nav-item {{ $item['active'] ? 'active' : '' }} flex flex-col items-center px-3 py-2 rounded-lg">
          <i data-lucide="{{ $item['icon'] }}" class="w-6 h-6 mb-1"></i>
          <span class="text-xs">{{ $item['label'] }}</span>
        </a>
      @endforeach
    </div>
  </nav>

  <style>
    .nav-item {
      @apply text-gray-600 hover:text-primary hover:bg-blue-50;
    }
    .nav-item.active {
      @apply text-primary bg-blue-50 border-r-2 border-primary;
    }
    
    .mobile-nav-item {
      @apply text-gray-600 transition-colors;
    }
    .mobile-nav-item.active {
      @apply text-primary bg-blue-50;
    }
    .mobile-nav-item:active {
      transform: scale(0.95);
    }
    
    @media (max-width: 1023px) {
      #adminSidebar {
        height: 100vh;
        overflow-y: auto;
      }
      button, a {
        min-height: 44px;
      }
    }
    
    body.sidebar-open {
      overflow: hidden;
    }
  </style>

  <script src="{{ asset('js/animated-alerts.js') }}?v={{ filemtime(public_path('js/animated-alerts.js')) }}"></script>
  <script src="{{ asset('js/alert-replacer.js') }}?v={{ filemtime(public_path('js/alert-replacer.js')) }}"></script>
  <script src="{{ asset('js/no-reload-system.js') }}?v={{ filemtime(public_path('js/no-reload-system.js')) }}"></script>
  <script src="{{ asset('js/button-handler.js') }}?v={{ filemtime(public_path('js/button-handler.js')) }}"></script>

  <script>
    // Toast Notification Functions
    function showToast(message, type = 'success', duration = 3500) {
      const toastContainer = document.getElementById('toast-container');
      const toastMessage = document.getElementById('toast-message');
      const toastText = document.getElementById('toast-text');
      toastText.textContent = message;
      toastMessage.className = 'px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2 min-w-[220px]';
      if (type === 'success') {
        toastMessage.classList.add('bg-green-500', 'text-white');
      } else if (type === 'error') {
        toastMessage.classList.add('bg-red-500', 'text-white');
      } else {
        toastMessage.classList.add('bg-gray-800', 'text-white');
      }
      toastContainer.style.display = 'block';
      toastMessage.style.display = 'flex';
      setTimeout(hideToast, duration);
    }
    function hideToast() {
      document.getElementById('toast-message').style.display = 'none';
      document.getElementById('toast-container').style.display = 'none';
    }
    // Optionally, show a toast if a session message is present (from backend)
    document.addEventListener('DOMContentLoaded', function() {
      const toastData = document.getElementById('toast-data');
      if (toastData) {
        showToast(toastData.value, toastData.dataset.type || 'success');
      }
    });
  </script>
  @if(session('success'))
    <input type="hidden" id="toast-data" value="{{ session('success') }}" data-type="success">
  @elseif(session('error'))
    <input type="hidden" id="toast-data" value="{{ session('error') }}" data-type="error">
  @endif
  
  <script>
    // Mobile menu toggle
    const adminToggle = document.getElementById('adminMenuToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const adminOverlay = document.getElementById('adminOverlay');
    const closeAdminSidebar = document.getElementById('closeAdminSidebar');
    
    function closeSidebar() {
      adminSidebar.classList.add('-translate-x-full');
      adminOverlay.classList.add('hidden');
      document.body.classList.remove('sidebar-open');
    }
    
    function openSidebar() {
      adminSidebar.classList.remove('-translate-x-full');
      adminOverlay.classList.remove('hidden');
      document.body.classList.add('sidebar-open');
    }
    
    if (adminToggle) {
      adminToggle.addEventListener('click', () => {
        if (adminSidebar.classList.contains('-translate-x-full')) {
          openSidebar();
        } else {
          closeSidebar();
        }
      });
    }
    
    if (closeAdminSidebar) {
      closeAdminSidebar.addEventListener('click', () => {
        closeSidebar();
      });
    }
    
    if (adminOverlay) {
      adminOverlay.addEventListener('click', () => {
        closeSidebar();
      });
    }
    
    // Close sidebar on link click (mobile)
    if (adminSidebar) {
      const adminLinks = adminSidebar.querySelectorAll('a');
      adminLinks.forEach(link => {
        link.addEventListener('click', () => {
          if (window.innerWidth < 1024) {
            closeSidebar();
          }
        });
      });
    }
    
    // Initialize Lucide icons
    lucide.createIcons();
  </script>

  @stack('scripts')
</body>
</html>
