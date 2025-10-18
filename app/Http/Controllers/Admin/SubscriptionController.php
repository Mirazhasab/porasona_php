<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubscriptionAssignRequest;
use App\Http\Requests\Admin\SubscriptionStoreRequest;
use App\Http\Requests\Admin\SubscriptionUpdateRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionActivated;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-subscriptions');
    }

    public function index(): View
    {
        $subscriptions = Subscription::query()
            ->withCount(['userSubscriptions as active_users_count' => function ($query) {
                $query->active();
            }])
            ->latest()
            ->paginate(15);

        $pendingManualCount = UserSubscription::query()
            ->where('status', UserSubscription::STATUS_PENDING)
            ->where('payment_method', 'manual')
            ->count();

        return view('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'pendingManualCount' => $pendingManualCount,
        ]);
    }

    public function create(): View
    {
        return view('admin.subscriptions.create');
    }

    public function store(SubscriptionStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['features'] = $data['features'] ?? [];
        $data['is_active'] = $request->boolean('is_active', true);

        $subscription = Subscription::create($data);

        SubscriptionLog::create([
            'action' => 'subscription_created',
            'description' => "Subscription {$subscription->name} created by admin",
            'meta' => [
                'subscription_id' => $subscription->id,
                'admin_id' => $request->user()->id,
            ],
        ]);

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }

    public function edit(Subscription $subscription): View
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(SubscriptionUpdateRequest $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validated();
        $data['features'] = $data['features'] ?? [];
        $data['is_active'] = $request->boolean('is_active', $subscription->is_active);

        $subscription->update($data);

        SubscriptionLog::create([
            'action' => 'subscription_updated',
            'description' => "Subscription {$subscription->name} updated by admin",
            'meta' => [
                'subscription_id' => $subscription->id,
                'admin_id' => $request->user()->id,
            ],
        ]);

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        if ($subscription->userSubscriptions()->active()->exists()) {
            return redirect()
                ->route('admin.subscriptions.index')
                ->with('error', 'Cannot delete a subscription with active users.');
        }

        $subscription->delete();

        SubscriptionLog::create([
            'action' => 'subscription_deleted',
            'description' => "Subscription {$subscription->name} deleted by admin",
            'meta' => [
                'subscription_id' => $subscription->id,
                'admin_id' => auth()->id(),
            ],
        ]);

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }

    public function assignForm(): View
    {
        $subscriptions = Subscription::active()->orderBy('price')->get();
        $users = User::orderBy('name')->limit(50)->get();

        return view('admin.subscriptions.assign', compact('subscriptions', 'users'));
    }

    public function assign(SubscriptionAssignRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $subscription = Subscription::findOrFail($data['subscription_id']);
        $user = User::findOrFail($data['user_id']);

        // Optional: decide whether to extend existing subscription or replace
        $existingActive = $user->userSubscriptions()->active()->latest('expires_at')->first();
        if ($existingActive) {
            $existingActive->markExpired();
        }

        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'payment_method' => 'manual',
            'starts_at' => $data['starts_at'],
            'expires_at' => $data['expires_at'],
            'metadata' => [
                'assigned_by' => $request->user()->id,
                'notes' => $data['notes'] ?? null,
                'assigned_via' => 'manual',
            ],
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'user_subscription_id' => $userSubscription->id,
            'provider' => 'manual',
            'payment_method' => 'manual',
            'amount' => $subscription->price,
            'currency' => $subscription->currency,
            'status' => 'paid',
            'paid_at' => now(),
            'meta' => [
                'assigned_by' => $request->user()->id,
            ],
        ]);

        SubscriptionLog::create([
            'user_id' => $user->id,
            'user_subscription_id' => $userSubscription->id,
            'action' => 'subscription_assigned_manual',
            'description' => "Subscription manually assigned by admin",
            'meta' => [
                'payment_id' => $payment->id,
                'admin_id' => $request->user()->id,
            ],
        ]);

        Notification::send($user, new SubscriptionActivated($userSubscription));

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'Subscription assigned to user successfully.');
    }

    public function manualReview(Request $request): View
    {
        $pendingManuals = UserSubscription::query()
            ->where('status', UserSubscription::STATUS_PENDING)
            ->where('payment_method', 'manual')
            ->with(['user', 'subscription', 'latestManualPayment'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscriptions.manual-review', [
            'pendingManuals' => $pendingManuals,
            'totalPending' => $pendingManuals->total(),
        ]);
    }

    public function approveManual(Request $request, UserSubscription $userSubscription): RedirectResponse
    {
        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($userSubscription->status !== UserSubscription::STATUS_PENDING || $userSubscription->payment_method !== 'manual') {
            return redirect()
                ->back()
                ->with('error', 'This manual submission has already been processed.');
        }

        $subscriptionPlan = $userSubscription->subscription;
        if (!$subscriptionPlan) {
            return redirect()
                ->back()
                ->with('error', 'Subscription plan is missing for this manual request.');
        }

        $payment = $userSubscription->latestManualPayment()->first();
        if (!$payment) {
            return redirect()
                ->back()
                ->with('error', 'Payment record was not found for this manual submission.');
        }

        $adminId = $request->user()->id;
        $reviewNotes = $request->input('review_notes');
        $reference = data_get($payment->meta, 'manual_reference');
        $startsAt = Carbon::now();
        $expiresAt = $this->calculateExpiry($startsAt->copy(), $subscriptionPlan->interval, (int) $subscriptionPlan->interval_count);

        DB::transaction(function () use ($userSubscription, $payment, $adminId, $reviewNotes, $reference, $startsAt, $expiresAt) {
            $userSubscription->markActive($startsAt, $expiresAt, $reference);

            $subscriptionMeta = array_merge($userSubscription->metadata ?? [], [
                'manual_review_status' => 'approved',
                'manual_reviewed_by' => $adminId,
                'manual_reviewed_at' => now()->toIso8601String(),
            ]);

            if ($reviewNotes) {
                $subscriptionMeta['manual_review_notes'] = $reviewNotes;
            }

            $userSubscription->fill(['metadata' => $subscriptionMeta]);
            $userSubscription->save();

            $paymentMeta = array_merge($payment->meta ?? [], [
                'manual_review_status' => 'approved',
                'manual_reviewed_by' => $adminId,
                'manual_reviewed_at' => now()->toIso8601String(),
            ]);

            if ($reviewNotes) {
                $paymentMeta['manual_review_notes'] = $reviewNotes;
            }

            $payment->fill([
                'status' => 'paid',
                'transaction_id' => $payment->transaction_id ?: $reference,
                'paid_at' => now(),
                'meta' => $paymentMeta,
            ]);
            $payment->save();

            SubscriptionLog::create([
                'user_id' => $userSubscription->user_id,
                'user_subscription_id' => $userSubscription->id,
                'action' => 'manual_payment_approved',
                'description' => 'Manual payment reviewed and approved by admin',
                'meta' => array_filter([
                    'payment_id' => $payment->id,
                    'manual_reference' => $reference,
                    'admin_id' => $adminId,
                    'review_notes' => $reviewNotes,
                ]),
            ]);
        });

        $userSubscription->refresh();

        if ($userSubscription->user) {
            Notification::send($userSubscription->user, new SubscriptionActivated($userSubscription));
        }

        return redirect()
            ->back()
            ->with('success', 'Manual payment approved and subscription activated.');
    }

    public function rejectManual(Request $request, UserSubscription $userSubscription): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        if ($userSubscription->status !== UserSubscription::STATUS_PENDING || $userSubscription->payment_method !== 'manual') {
            return redirect()
                ->back()
                ->with('error', 'This manual submission has already been processed.');
        }

        $payment = $userSubscription->latestManualPayment()->first();
        $adminId = $request->user()->id;
        $reason = $data['reason'];

        DB::transaction(function () use ($userSubscription, $payment, $adminId, $reason) {
            $rejectionMeta = [
                'manual_review_status' => 'rejected',
                'manual_reviewed_by' => $adminId,
                'manual_reviewed_at' => now()->toIso8601String(),
                'manual_rejection_reason' => $reason,
            ];

            $userSubscription->fill([
                'status' => UserSubscription::STATUS_CANCELLED,
                'canceled_at' => now(),
                'metadata' => array_merge($userSubscription->metadata ?? [], $rejectionMeta),
            ]);
            $userSubscription->save();

            if ($payment) {
                $payment->fill([
                    'status' => 'rejected',
                    'meta' => array_merge($payment->meta ?? [], $rejectionMeta),
                ]);
                $payment->save();
            }

            SubscriptionLog::create([
                'user_id' => $userSubscription->user_id,
                'user_subscription_id' => $userSubscription->id,
                'action' => 'manual_payment_rejected',
                'description' => 'Manual payment rejected by admin',
                'meta' => array_filter([
                    'payment_id' => $payment?->id,
                    'admin_id' => $adminId,
                    'reason' => $reason,
                ]),
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Manual payment marked as rejected.');
    }

    private function calculateExpiry(Carbon $startsAt, string $interval, int $intervalCount): Carbon
    {
        return match ($interval) {
            'day' => $startsAt->copy()->addDays($intervalCount),
            'week' => $startsAt->copy()->addWeeks($intervalCount),
            'year' => $startsAt->copy()->addYears($intervalCount),
            default => $startsAt->copy()->addMonths($intervalCount),
        };
    }
}
