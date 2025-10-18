<?php

namespace App\Services;

use App\Models\McqSet;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SubscriptionAccessManager
{
    private ?User $user;

    private readonly array $limits;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?: Auth::user();
        $this->limits = Setting::getSubscriptionLimits();
    }

    public static function for(?User $user = null): self
    {
        return new self($user);
    }

    public function hasActiveSubscription(): bool
    {
        return (bool) $this->user?->hasActiveSubscription();
    }

    public function isAdmin(): bool
    {
        return (bool) $this->user?->isAdmin();
    }

    public function isLimited(): bool
    {
        if (!$this->user) {
            return true;
        }

        if ($this->isAdmin()) {
            return false;
        }

        return !$this->hasActiveSubscription();
    }

    public function questionLimit(string $context = 'mcq_preview'): ?int
    {
        $map = [
            'mcq_preview' => 'mcq_preview_per_set',
            'exam_preview' => 'exam_questions_per_attempt',
            'exam_attempt' => 'exam_questions_per_attempt',
            'exam_review' => 'exam_review_questions',
            'practice_session' => 'practice_questions_per_session',
        ];

        $key = $map[$context] ?? $context;
        $limit = $this->limits[$key] ?? null;

        if (($limit === null || $limit === 0) && in_array($context, ['exam_preview', 'exam_attempt', 'exam_review', 'practice_session'], true)) {
            $limit = $this->limits['mcq_preview_per_set'] ?? null;
        }

        if ($limit === null || $limit === 0) {
            return null;
        }

        return max(0, (int) $limit) ?: null;
    }

    public function questionsForSet(McqSet $mcqSet, string $context = 'mcq_preview', bool $randomOrder = false): Collection
    {
        $query = $mcqSet->questions();

        if ($randomOrder) {
            $query->inRandomOrder();
        } else {
            $query->orderBy('id');
        }

        $limit = $this->questionLimit($context);

        if ($this->isLimited() && $limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function applyLimitToBuilder(Builder $builder, string $context = 'mcq_preview'): Builder
    {
        $limit = $this->questionLimit($context);

        if ($this->isLimited() && $limit !== null) {
            $builder->limit($limit);
        }

        return $builder;
    }

    public function clampCollection(Collection $collection, string $context = 'mcq_preview'): Collection
    {
        $limit = $this->questionLimit($context);

        if ($this->isLimited() && $limit !== null) {
            return $collection->take($limit);
        }

        return $collection;
    }
}
