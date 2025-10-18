<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

class UserSubscription extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'status',
        'payment_method',
        'transaction_id',
        'provider_subscription_id',
        'provider_checkout_session_id',
        'starts_at',
        'expires_at',
        'canceled_at',
        'trial_ends_at',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'canceled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function (UserSubscription $subscription): void {
            $subscription->flushActiveSubscriptionCache();
        });

        static::deleted(function (UserSubscription $subscription): void {
            $subscription->flushActiveSubscriptionCache();
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function (Builder $builder): void {
                $builder->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestManualPayment(): HasOne
    {
        return $this->hasOne(Payment::class)
            ->where('payment_method', 'manual')
            ->latestOfMany();
    }

    public function markActive(CarbonInterface $startsAt, CarbonInterface $expiresAt, ?string $transactionId = null): void
    {
        $this->fill([
            'status' => self::STATUS_ACTIVE,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
        ]);

        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }

        $this->save();

        $this->flushActiveSubscriptionCache();
    }

    public function markExpired(): void
    {
        $this->status = self::STATUS_EXPIRED;
        $this->save();

        $this->flushActiveSubscriptionCache();
    }

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->expires_at === null) {
            return true;
        }

        return $this->expires_at->greaterThanOrEqualTo(now());
    }

    public function flushActiveSubscriptionCache(): void
    {
        if (!$this->user_id) {
            return;
        }

        Cache::forget("user_{$this->user_id}_active_subscription");

        if ($this->relationLoaded('user') && $this->user) {
            $this->user->forgetActiveSubscriptionCache();
        }
    }
}
