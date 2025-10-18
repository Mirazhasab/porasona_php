@php
    $bottomNavItems = [
        ['route' => 'mcq.dashboard', 'icon' => 'home', 'label' => 'Home'],
        ['route' => 'mcq.examinations', 'icon' => 'clipboard-list', 'label' => 'Exams'],
        ['route' => 'mcq.leaderboard', 'icon' => 'trophy', 'label' => 'Ranks'],
        ['url' => '/mcq/posts', 'icon' => 'newspaper', 'label' => 'Posts'],
        ['route' => 'mcq.analytics', 'icon' => 'bar-chart-3', 'label' => 'Stats'],
    ];
@endphp

@foreach($bottomNavItems as $item)
        $isActive = isset($item['route']) ? request()->routeIs($item['route']) : request()->is(ltrim($item['url'] ?? '', '/'));
        $href = isset($item['route']) ? route($item['route']) : ($item['url'] ?? '#');
    @endphp
    
    <a href="{{ $href }}" 
       class="flex flex-col items-center space-y-1 px-3 py-2 rounded-xl transition-all duration-200 {{ $isActive ? 'text-primary-600 ' : 'text-blue-700' }}">
        <div class="relative">
            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
            @if($isActive)
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-primary-500 rounded-full"></div>
            @endif
{{ ... }}
        <span class="text-xs font-medium">{{ $item['label'] }}</span>
    </a>
@endforeach