@extends('layouts.admin')

@section('title', 'Assign Subscription')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manual Subscription Assignment</h1>
        <p class="text-sm text-gray-500">Grant access to a user without processing a payment through Stripe.</p>
    </div>

    <form action="{{ route('admin.subscriptions.assign') }}" method="POST" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">User</label>
            <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Subscription Plan</label>
            <select name="subscription_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach($subscriptions as $subscription)
                    <option value="{{ $subscription->id }}" {{ old('subscription_id') == $subscription->id ? 'selected' : '' }}>
                        {{ $subscription->name }} — {{ strtoupper($subscription->currency) }} {{ number_format($subscription->price, 2) }} / {{ $subscription->interval }}
                    </option>
                @endforeach
            </select>
            @error('subscription_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Starts At</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('starts_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Expires At</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at', now()->addMonth()->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('expires_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Notes (optional)</label>
            <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            @error('notes')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
            Manual assignments skip Stripe billing and log a paid payment with method <code>manual</code>. Adjust or remove this logic if you prefer to extend existing subscriptions instead of replacing them.
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.subscriptions.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Assign Subscription</button>
        </div>
    </form>
</div>
@endsection
