@extends('layouts.admin')

@section('title', 'Subscription Settings')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Subscription Settings</h1>
        <p class="text-sm text-gray-500">Configure limits applied to users without an active subscription.</p>
    </div>

    <form action="{{ route('admin.subscription-settings.update') }}" method="POST" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Free MCQ Limit Per Set</label>
            <input type="number" name="free_mcq_limit_per_set" value="{{ old('free_mcq_limit_per_set', $limit) }}" min="1" max="500" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            @error('free_mcq_limit_per_set')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-gray-500 mt-1">Users without an active subscription can view up to this number of questions in each MCQ set.</p>
        </div>

        <div>
            <h2 class="text-sm font-medium text-gray-700 mb-3">Module Access Matrix</h2>
            <p class="text-xs text-gray-500 mb-4">Select which modules remain available for free users and which unlock automatically after a subscription is activated.</p>

            @php
                $featureLabels = [
                    'practice' => 'Practice Questions',
                    'exams' => 'Exams',
                    'results' => 'Results & Analytics',
                    'classmate' => 'Classmates',
                    'leaderboard' => 'Leaderboard',
                    'post' => 'Posts / Community',
                    'read_access' => 'Read MCQ Sets',
                    'mcq_management' => 'MCQ Management',
                ];
                $selectedFree = old('free_features', array_keys(array_filter($featureMatrix['free'])));
                $selectedSubscribed = old('subscribed_features', array_keys(array_filter($featureMatrix['subscribed'])));
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Free Users</h3>
                    <div class="space-y-3">
                        @foreach($featureFields as $feature)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="free_features[]" value="{{ $feature }}" class="rounded border-gray-300 text-indigo-600" {{ in_array($feature, $selectedFree, true) ? 'checked' : '' }}>
                                <span>{{ $featureLabels[$feature] ?? ucfirst(str_replace('_', ' ', $feature)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Subscribed Users</h3>
                    <div class="space-y-3">
                        @foreach($featureFields as $feature)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="subscribed_features[]" value="{{ $feature }}" class="rounded border-gray-300 text-indigo-600" {{ in_array($feature, $selectedSubscribed, true) ? 'checked' : '' }}>
                                <span>{{ $featureLabels[$feature] ?? ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                @if($feature === 'mcq_management')
                                    <span class="ml-auto text-xs text-gray-400" title="Reserved for admins by default">Admin only</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.subscriptions.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">Back</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Save Settings</button>
        </div>
    </form>
</div>
@endsection
