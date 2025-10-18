@extends('layouts.admin')

@section('title', 'Edit Subscription')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Subscription Plan</h1>
        <p class="text-sm text-gray-500">Update plan details. Existing active subscribers are not affected automatically.</p>
    </div>

    <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $subscription->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Slug (optional)</label>
            <input type="text" name="slug" value="{{ old('slug', $subscription->slug) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Price</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $subscription->price) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $subscription->currency) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase" maxlength="3" required>
                @error('currency')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Interval</label>
                <select name="interval" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['day','week','month','year'] as $interval)
                        <option value="{{ $interval }}" {{ old('interval', $subscription->interval) === $interval ? 'selected' : '' }}>{{ ucfirst($interval) }}</option>
                    @endforeach
                </select>
                @error('interval')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Interval Count</label>
                <input type="number" name="interval_count" value="{{ old('interval_count', $subscription->interval_count) }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('interval_count')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Trial Days</label>
                <input type="number" name="trial_days" value="{{ old('trial_days', $subscription->trial_days) }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @error('trial_days')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2 mt-5">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Plan is active</span>
            </div>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $subscription->description) }}</textarea>
            @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Features</label>
            <textarea name="features" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="One feature per line">{{ old('features', $subscription->features ? implode("\n", $subscription->features) : '') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Update carefully – decide whether changes should apply to existing subscribers.</p>
            @error('features')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.subscriptions.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Update Plan</button>
        </div>
    </form>
</div>
@endsection
