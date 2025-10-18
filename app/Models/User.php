<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'email_verified_at',
        'uid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uid)) {
                $user->uid = static::generateUniqueUid();
            }
        });
    }

    /**
     * Generate a unique UID for the user.
     */
    public static function generateUniqueUid(): string
    {
        do {
            $uid = 'USR-' . Str::upper(Str::random(6));
        } while (static::where('uid', $uid)->exists());

        return $uid;
    }

    /**
     * Get the MCQ sets for the user.
     */
    public function mcqSets()
    {
        return $this->hasMany(McqSet::class);
    }

    /**
     * Get the MCQ answers for the user.
     */
    public function mcqAnswers()
    {
        return $this->hasMany(McqAnswer::class);
    }

    /**
     * Get the posts for the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function mcqNotes(): HasMany
    {
        return $this->hasMany(McqNote::class);
    }

    /**
     * Get the access permissions for the user.
     */
    public function access()
    {
        return $this->hasOne(UserAccess::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function isAdmin(): bool
    {
        try {
            if (method_exists($this, 'hasRole') && $this->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable) {
            // Ignore exceptions from role drivers and fallback
        }

        return $this->role === 'admin';
    }

    public function forgetActiveSubscriptionCache(): void
    {
        Cache::forget($this->activeSubscriptionCacheKey());
    }

    protected function activeSubscriptionCacheKey(): string
    {
        return "user_{$this->id}_active_subscription";
    }

    public function latestActiveSubscription(): ?UserSubscription
    {
        return Cache::remember(
            $this->activeSubscriptionCacheKey(),
            now()->addMinutes(5),
            function () {
                return $this->userSubscriptions()
                    ->active()
                    ->orderByDesc('expires_at')
                    ->orderByDesc('id')
                    ->first();
            }
        );
    }

    public function hasActiveSubscription(): bool
    {
        return (bool) $this->latestActiveSubscription();
    }

    /**
     * Get or create access permissions for the user.
     */
    public function getOrCreateAccess()
    {
        if ($this->access) {
            return $this->access;
        }

        $defaults = array_fill_keys(UserAccess::FEATURE_FIELDS, false);
        $access = $this->access()->create($defaults);
        $this->setRelation('access', $access);

        return $access;
    }

    /**
     * Check if user has access to a specific page.
     */
    public function hasPageAccess(string $page): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $access = $this->access;
        return $access ? $access->hasAccess($page) : false;
    }

    public function syncAccessFromMatrix(array $matrix, bool $hasActiveSubscription): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $tier = $hasActiveSubscription ? 'subscribed' : 'free';
        $configuration = $matrix[$tier] ?? [];

        $access = $this->getOrCreateAccess();
        $dirty = false;

        foreach (UserAccess::FEATURE_FIELDS as $feature) {
            $desired = (bool) ($configuration[$feature] ?? false);
            if ($access->{$feature} !== $desired) {
                $access->{$feature} = $desired;
                $dirty = true;
            }
        }

        if ($dirty) {
            $access->save();
            $this->setRelation('access', $access);
        }
    }

    /**
     * Grant access to a specific page.
     */
    public function grantPageAccess(string $page): bool
    {
        $access = $this->getOrCreateAccess();
        return $access->grantAccess($page);
    }

    /**
     * Revoke access to a specific page.
     */
    public function revokePageAccess(string $page): bool
    {
        $access = $this->access;
        return $access ? $access->revokeAccess($page) : false;
    }
}
