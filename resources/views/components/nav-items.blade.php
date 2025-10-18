@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';
    
    // Get user access permissions (load relationship if not already loaded)
    $userAccess = $user->access;
    
    // Helper function to check access
    $hasAccess = function($permission) use ($userAccess, $isAdmin) {
        // Admins have access to everything
        if ($isAdmin) return true;
        
        // If no access record exists, deny access
        if (!$userAccess) return false;
        
        // Check specific permission
        return $userAccess->{$permission} ?? false;
    };
    
    $navItems = [
        // Always visible items
        ['route' => 'mcq.dashboard', 'icon' => 'home', 'label' => 'Dashboard', 'admin' => false, 'access' => null],
        
        // Practice Questions - requires general access (you can customize this)
        ['route' => 'mcq.questions', 'icon' => 'help-circle', 'label' => $isAdmin ? 'Question Bank' : 'Practice Questions', 'admin' => false, 'access' => 'practice'],
        
        // My Exams - requires exam access (you can customize this)
        ['route' => 'mcq.examinations', 'icon' => 'clipboard-list', 'label' => $isAdmin ? 'Manage Exams' : 'My Exams', 'admin' => false, 'access' => 'exams'],
        
        // My Results - requires results access (you can customize this)
        ['route' => 'mcq.analytics', 'icon' => 'bar-chart-3', 'label' => $isAdmin ? 'Analytics' : 'My Results', 'admin' => false, 'access' => 'results'],
        
        // Classmates - requires classmate access
        ['route' => 'mcq.students', 'icon' => 'users', 'label' => $isAdmin ? 'Students' : 'Classmates', 'admin' => false, 'access' => 'classmate'],
        
        // Leaderboard - requires leaderboard access
        ['route' => 'mcq.leaderboard', 'icon' => 'trophy', 'label' => 'Leaderboard', 'admin' => false, 'access' => 'leaderboard'],
        
        // Posts - requires post access
        ['url' => '/mcq/posts', 'icon' => 'newspaper', 'label' => $isAdmin ? 'Manage Posts' : 'Posts', 'admin' => false, 'access' => 'post'],
        
        // MCQ Management - requires admin or specific access
        ['route' => 'mcq_sets.index', 'icon' => 'layers', 'label' => 'MCQ Management', 'admin' => false, 'access' => 'mcq_management'],
        
        // Admin only items
        ['route' => 'mcq.settings', 'icon' => 'settings', 'label' => 'Settings', 'admin' => true, 'access' => null],
        ['route' => 'admin.users.index', 'icon' => 'user-cog', 'label' => 'User Management', 'admin' => true, 'access' => null],
    ];
@endphp

@foreach($navItems as $item)
    @php
        // Check admin access
        $showForAdmin = !$item['admin'] || $isAdmin;
        
        // Check user access permission
        $showForAccess = $item['access'] === null || $hasAccess($item['access']);
        
        // Show item only if both conditions are met
        $showItem = $showForAdmin && $showForAccess;
    @endphp
    
    @if($showItem)
        @php
            $isActive = isset($item['route']) ? request()->routeIs($item['route']) : request()->is(ltrim($item['url'] ?? '', '/'));
            $href = isset($item['route']) ? route($item['route']) : ($item['url'] ?? '#');
        @endphp
        
        <a href="{{ $href }}" 
           class="nav-item flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 group {{ $isActive ? 'bg-primary-50  text-primary-600 ' : 'text-blue-800  hover:bg-gray-100 ' }}">
            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 {{ $isActive ? 'text-primary-600 ' : 'text-blue-700  group-hover:text-blue-800 ' }}"></i>
            <span>{{ $item['label'] }}</span>
            @if($isActive)
            @endif
        </a>
    @endif
@endforeach

<div class="pt-3 mt-3 border-t border-gray-200 ">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium text-blue-800  hover:bg-red-50  hover:text-red-600  transition-all duration-200 group">
            <i data-lucide="log-out" class="w-5 h-5 text-blue-700  group-hover:text-red-600 "></i>
            <span>Logout</span>
        </button>
    </form>
</div>