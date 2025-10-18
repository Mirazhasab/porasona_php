<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserAccess;

class Setting extends Model
{
    use HasFactory;

    public const FREE_MCQ_LIMIT_KEY = 'free_mcq_limit_per_set';
    public const SUBSCRIPTION_FEATURE_MATRIX_KEY = 'subscription_feature_matrix';
    public const SUBSCRIPTION_LIMITS_KEY = 'subscription_limits';

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return cache()->rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::query()->where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type) ?? $default;
        });
    }

    public static function setValue(string $key, mixed $value, string $type = 'string'): void
    {
        cache()->forget("setting_{$key}");

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
            ]
        );
    }

    public static function getSubscriptionFeatureMatrix(): array
    {
        $default = static::defaultSubscriptionFeatureMatrix();
        $matrix = static::getValue(static::SUBSCRIPTION_FEATURE_MATRIX_KEY, $default);

        if (!is_array($matrix)) {
            return $default;
        }

        return static::normalizeSubscriptionFeatureMatrix($matrix, $default);
    }

    public static function defaultSubscriptionFeatureMatrix(): array
    {
        $base = array_fill_keys(UserAccess::FEATURE_FIELDS, false);

        $free = $base;
        $free['practice'] = true;
        $free['read_access'] = true;

        $subscribed = $base;
        foreach (UserAccess::FEATURE_FIELDS as $feature) {
            $subscribed[$feature] = true;
        }
        // Management stays manual for admins only
        $subscribed['mcq_management'] = false;

        return [
            'free' => $free,
            'subscribed' => $subscribed,
        ];
    }

    public static function normalizeSubscriptionFeatureMatrix(array $matrix, ?array $fallback = null): array
    {
        $normalized = $fallback ?? static::defaultSubscriptionFeatureMatrix();

        foreach (['free', 'subscribed'] as $tier) {
            if (!isset($matrix[$tier]) || !is_array($matrix[$tier])) {
                continue;
            }

            foreach (UserAccess::FEATURE_FIELDS as $feature) {
                if (array_key_exists($feature, $matrix[$tier])) {
                    $normalized[$tier][$feature] = (bool) $matrix[$tier][$feature];
                }
            }
        }

        return $normalized;
    }

    public static function getSubscriptionLimits(): array
    {
        $default = static::defaultSubscriptionLimits();
        $limits = static::getValue(static::SUBSCRIPTION_LIMITS_KEY, $default);

        if (!is_array($limits)) {
            return $default;
        }

        return static::normalizeSubscriptionLimits($limits, $default);
    }

    public static function defaultSubscriptionLimits(): array
    {
        return [
            'mcq_preview_per_set' => (int) (static::getValue(static::FREE_MCQ_LIMIT_KEY, 10) ?? 10),
            'practice_questions_per_session' => null,
            'exam_questions_per_attempt' => null,
            'exam_review_questions' => null,
        ];
    }

    public static function normalizeSubscriptionLimits(array $limits, ?array $fallback = null): array
    {
        $normalized = $fallback ?? static::defaultSubscriptionLimits();

        foreach ($normalized as $key => $value) {
            if (!array_key_exists($key, $limits)) {
                continue;
            }

            $raw = $limits[$key];

            if ($raw === null || $raw === '') {
                $normalized[$key] = null;
                continue;
            }

            if (is_numeric($raw)) {
                $normalized[$key] = max(0, (int) $raw);
            }
        }

        return $normalized;
    }

    protected static function castValue(?string $value, ?string $type): mixed
    {
        return match ($type) {
            'integer' => $value !== null ? (int) $value : null,
            'boolean' => $value !== null ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : null,
            'json' => $value ? json_decode($value, true) : null,
            default => $value,
        };
    }
}
