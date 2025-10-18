@extends('layouts.admin')

@section('title', 'Manual Payment Reviews')

@section('content')
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Manual Payment Reviews</h1>
            <p class="text-sm text-gray-500">Review pending offline submissions, confirm genuine payments, or reject suspicious entries.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">
            <span class="text-sm text-gray-500">Awaiting approval</span>
            <span class="text-xl font-semibold text-gray-800">{{ number_format($totalPending) }}</span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">{{ session('error') }}</div>
@endif

@if($pendingManuals->isEmpty())
    <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
        <i data-lucide="inbox" class="w-10 h-10 mx-auto text-gray-300"></i>
        <h2 class="mt-4 text-lg font-semibold text-gray-800">No manual payments to review</h2>
        <p class="mt-2 text-sm text-gray-500">Once students submit offline payment information, it will appear here for approval.</p>
    </div>
@else
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Payment Details</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($pendingManuals as $manual)
                    @php
                        $payment = $manual->latestManualPayment;
                        $manualReference = $payment?->meta['manual_reference'] ?? 'N/A';
                        $manualNotes = $payment?->meta['manual_notes'] ?? null;
                        $amount = $payment?->amount ? number_format($payment->amount, 2) : null;
                    @endphp
                    <tr>
                        <td class="px-4 py-4 align-top">
                            <div class="font-semibold text-gray-800">{{ $manual->user?->name ?? 'Unknown user' }}</div>
                            <div class="text-xs text-gray-500">{{ $manual->user?->email ?? 'No email on file' }}</div>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <div class="font-semibold text-gray-800">{{ $manual->subscription?->name ?? 'Archived plan' }}</div>
                            <div class="text-xs text-gray-500">{{ strtoupper($manual->subscription?->currency ?? 'BDT') }} {{ number_format($manual->subscription?->price ?? 0, 2) }}</div>
                            <div class="text-xs text-gray-400 mt-1">Interval: {{ ucfirst($manual->subscription?->interval ?? 'month') }} x {{ $manual->subscription?->interval_count ?? 1 }}</div>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <div class="text-sm text-gray-800">Reference: <span class="font-mono text-gray-700">{{ $manualReference }}</span></div>
                            @if($amount)
                                <div class="text-sm text-gray-600 mt-1">Amount: {{ strtoupper($payment->currency ?? $manual->subscription?->currency ?? 'BDT') }} {{ $amount }}</div>
                            @endif
                            @if($manualNotes)
                                <div class="mt-2 text-sm text-gray-600">
                                    <span class="font-medium text-gray-700">Student notes:</span>
                                    <span class="block text-gray-600">{{ $manualNotes }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-top">
                            <div class="text-sm text-gray-700">{{ optional($manual->created_at)->diffForHumans() }}</div>
                            <div class="text-xs text-gray-500">{{ optional($manual->created_at)->toDayDateTimeString() }}</div>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <div class="space-y-3">
                                <form method="POST" action="{{ route('admin.subscriptions.manual.approve', $manual) }}" class="space-y-2">
                                    @csrf
                                    <input type="text" name="review_notes" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400" placeholder="Optional admin notes" maxlength="1000">
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Approve & Activate
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.subscriptions.manual.reject', $manual) }}" class="space-y-2" onsubmit="return confirm('Reject this manual payment request?');">
                                    @csrf
                                    <input type="text" name="reason" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400" placeholder="Reason for rejection" maxlength="1000" required>
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Reject Request
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $pendingManuals->links() }}
        </div>
    </div>
@endif
@endsection
