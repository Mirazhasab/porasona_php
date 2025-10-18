@extends('layouts.admin')

@section('title', 'Create Subscription')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Create Subscription Plan</h1>
        <p class="text-sm text-gray-500">Define a new subscription that users can purchase.</p>
    </div>

    <form action="{{ route('admin.subscriptions.store') }}" method="POST" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-5">
        @csrf

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Price</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', 'usd') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase" maxlength="3" required>
                @error('currency')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Interval</label>
                <select name="interval" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['day','week','month','year'] as $interval)
                        <option value="{{ $interval }}" {{ old('interval', 'month') === $interval ? 'selected' : '' }}>{{ ucfirst($interval) }}</option>
                    @endforeach
                </select>
                @error('interval')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Interval Count</label>
                <input type="number" name="interval_count" value="{{ old('interval_count', 1) }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('interval_count')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Trial Days</label>
                <input type="number" name="trial_days" value="{{ old('trial_days') }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @error('trial_days')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2 mt-5">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" {{ old('is_active', true) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Plan is active</span>
            </div>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>
            @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Features</label>
            <textarea name="features" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="One feature per line">{{ old('features') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Separate each feature with a new line. The list renders on the public purchase page.</p>
            @error('features')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.subscriptions.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Create Plan</button>
        </div>
    </form>
</div>
@endsection
