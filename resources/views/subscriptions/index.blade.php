@extends('layouts.mcq')

@section('title', 'Subscriptions')
@section('page-title', 'Subscriptions')
@section('page-subtitle', 'Choose the plan that unlocks full access to every MCQ set')

@section('content')
<div class="space-y-6">
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @foreach($subscriptions as $subscription)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $subscription->name }}</h2>
                        @if($currentSubscription && $currentSubscription->subscription_id === $subscription->id && $currentSubscription->isActive())
                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Active</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">{{ $subscription->description }}</p>
                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-900">{{ strtoupper($subscription->currency) }} {{ number_format($subscription->price, 2) }}</span>
                        <span class="text-sm text-gray-500">/ {{ $subscription->interval_count > 1 ? $subscription->interval_count . ' ' : '' }}{{ \Illuminate\Support\Str::plural($subscription->interval, $subscription->interval_count) }}</span>
                    </div>
                </div>
                <div class="p-6 flex-1">
                    <ul class="space-y-2 text-sm text-gray-700">
                        @forelse($subscription->features ?? [] as $feature)
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @empty
                            <li class="text-gray-500">This plan does not list specific features yet.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="p-6 border-t border-gray-200">
                    <div class="space-y-3">
                        <form action="{{ route('subscriptions.checkout') }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                            <input type="hidden" name="payment_type" value="{{ \App\Http\Requests\StartSubscriptionCheckoutRequest::PAYMENT_TYPE_BKASH }}">
                            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700" @disabled($currentSubscription && $currentSubscription->isActive() && $currentSubscription->subscription_id === $subscription->id)>
                                @if($currentSubscription && $currentSubscription->subscription_id === $subscription->id && $currentSubscription->isActive())
                                    Current Plan
                                @else
                                    Pay with bKash
                                @endif
                            </button>
                        </form>

                        <button type="button" class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50" data-open-manual-modal="{{ $subscription->id }}">
                            Submit Manual Payment
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="manualPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Submit Manual Payment</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-close-manual-modal>&times;</button>
            </div>
            <form method="POST" action="{{ route('subscriptions.checkout') }}" class="px-6 py-4 space-y-4">
                @csrf
                <input type="hidden" name="subscription_id" id="manual_subscription_id">
                <input type="hidden" name="payment_type" value="{{ \App\Http\Requests\StartSubscriptionCheckoutRequest::PAYMENT_TYPE_MANUAL }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Reference</label>
                    <input type="text" name="manual_transaction_reference" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter the bKash TRX ID or bank reference" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <textarea name="manual_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Add any additional details for the team"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                    <button type="button" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800" data-close-manual-modal>Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Current status</h3>
            @if($currentSubscription && $currentSubscription->isActive())
                <dl class="space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <dt>Plan</dt>
                        <dd>{{ $currentSubscription->subscription?->name ?? 'Custom' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Started</dt>
                        <dd>{{ optional($currentSubscription->starts_at)->toDayDateTimeString() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Expires</dt>
                        <dd>{{ optional($currentSubscription->expires_at)->toDayDateTimeString() ?? 'Renews automatically' }}</dd>
                    </div>
                </dl>
            @elseif($pendingSubscription)
                <p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-3">Payment pending. We will notify you as soon as it is reviewed or confirmed.</p>
            @else
                <p class="text-sm text-gray-600">You do not have an active subscription. Without one you can view up to <strong>{{ $freeLimit }}</strong> questions per MCQ set.</p>
            @endif
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Why subscribe?</h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start"><i class="fas fa-infinity text-indigo-500 mt-1 mr-2"></i><span>Unlimited access to every question in all MCQ sets.</span></li>
                <li class="flex items-start"><i class="fas fa-bolt text-indigo-500 mt-1 mr-2"></i><span>Support ongoing improvements and new features.</span></li>
                <li class="flex items-start"><i class="fas fa-envelope-open-text text-indigo-500 mt-1 mr-2"></i><span>Email alerts when new content drops or subscriptions renew.</span></li>
            </ul>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('manualPaymentModal');
        const subscriptionInput = document.getElementById('manual_subscription_id');

        function openModal(subscriptionId) {
            subscriptionInput.value = subscriptionId;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        document.querySelectorAll('[data-open-manual-modal]').forEach(button => {
            button.addEventListener('click', () => openModal(button.getAttribute('data-open-manual-modal')));
        });

        document.querySelectorAll('[data-close-manual-modal]').forEach(button => {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    });
</script>
@endpush
@endsection
