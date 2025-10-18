<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class McqSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'exam_name',
        'exam_date',
        'exam_time',
        'total_marks',
        'duration',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    /**
     * Get the user that owns the MCQ set.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the questions for the MCQ set.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(McqQuestion::class);
    }

    /**
     * Get the answers for the MCQ set.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(McqAnswer::class);
    }

    /**
     * Get total number of questions.
     */
    public function getTotalQuestionsAttribute()
    {
        return $this->questions()->count();
    }

    /**
     * Scope to get only approved sets.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get only pending sets.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only rejected sets.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if the set is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the set is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the set is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
