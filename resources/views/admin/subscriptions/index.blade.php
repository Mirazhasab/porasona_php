@extends('layouts.admin')

@section('title', 'Subscriptions')

@section('content')
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Subscription Hub</h1>
            <p class="text-sm text-gray-500">Centralize plan management, assignments, and billing settings.</p>
        </div>
    </div>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <a href="#plans-table" class="group block bg-white border border-gray-200 rounded-xl shadow-sm p-5 hover:border-indigo-200 hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </span>
                    <h2 class="mt-4 text-lg font-semibold text-gray-800">Manage Plans</h2>
                    <p class="mt-2 text-sm text-gray-500">Review existing subscriptions, pricing, and intervals.</p>
                </div>
                <i data-lucide="arrow-up-right" class="w-5 h-5 text-gray-300 group-hover:text-indigo-400"></i>
            </div>
        </a>
        <a href="{{ route('admin.subscriptions.manual-review') }}" class="group block bg-white border border-gray-200 rounded-xl shadow-sm p-5 hover:border-indigo-200 hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 text-blue-600">
                        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                    </span>
                    <h2 class="mt-4 text-lg font-semibold text-gray-800">Manual Reviews</h2>
                    <p class="mt-2 text-sm text-gray-500">Approve or reject manually submitted payments awaiting review.</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-semibold text-gray-800">{{ $pendingManualCount }}</div>
                    <p class="text-xs text-gray-500 mt-1">Pending</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.subscriptions.create') }}" class="group block bg-white border border-gray-200 rounded-xl shadow-sm p-5 hover:border-indigo-200 hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 text-green-600">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    </span>
                    <h2 class="mt-4 text-lg font-semibold text-gray-800">Create Plan</h2>
                    <p class="mt-2 text-sm text-gray-500">Launch a new subscription tier with custom billing logic.</p>
                </div>
                <i data-lucide="arrow-up-right" class="w-5 h-5 text-gray-300 group-hover:text-indigo-400"></i>
            </div>
        </a>
        <a href="{{ route('admin.subscriptions.assign-form') }}" class="group block bg-white border border-gray-200 rounded-xl shadow-sm p-5 hover:border-indigo-200 hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-amber-100 text-amber-600">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </span>
                    <h2 class="mt-4 text-lg font-semibold text-gray-800">Manual Assignment</h2>
                    <p class="mt-2 text-sm text-gray-500">Grant premium access manually or process offline payments.</p>
                </div>
                <i data-lucide="arrow-up-right" class="w-5 h-5 text-gray-300 group-hover:text-indigo-400"></i>
            </div>
        </a>
        <a href="{{ route('admin.subscription-settings.edit') }}" class="group block bg-white border border-gray-200 rounded-xl shadow-sm p-5 hover:border-indigo-200 hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-sky-100 text-sky-600">
                        <i data-lucide="sliders" class="w-5 h-5"></i>
                    </span>
                    <h2 class="mt-4 text-lg font-semibold text-gray-800">Subscription Settings</h2>
                    <p class="mt-2 text-sm text-gray-500">Adjust free limits, renewal windows, and global preferences.</p>
                </div>
                <i data-lucide="arrow-up-right" class="w-5 h-5 text-gray-300 group-hover:text-indigo-400"></i>
            </div>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">{{ session('error') }}</div>
@endif

<div id="plans-table" class="bg-white border border-gray-200 rounded-xl shadow-sm">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interval</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active Users</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($subscriptions as $subscription)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $subscription->name }}</div>
                        <div class="text-xs text-gray-500">{{ $subscription->description }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ strtoupper($subscription->currency) }} {{ number_format($subscription->price, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        {{ ucfirst($subscription->interval) }}
                        @if($subscription->interval_count > 1)
                            <span class="text-xs text-gray-500">× {{ $subscription->interval_count }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($subscription->is_active)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $subscription->active_users_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Edit</a>
                            <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" onsubmit="return confirm('Delete this plan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No subscriptions configured yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-4 py-3 border-t border-gray-200">{{ $subscriptions->links() }}</div>
</div>
@endsection
