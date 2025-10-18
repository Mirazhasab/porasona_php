<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccess extends Model
{
    use HasFactory;

    public const FEATURE_FIELDS = [
        'practice',
        'exams',
        'results',
        'classmate',
        'leaderboard',
        'post',
        'read_access',
        'mcq_management',
        'notes',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'user_accesses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'practice',
        'exams',
        'results',
        'classmate',
        'leaderboard',
        'post',
        'read_access',
        'mcq_management',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'classmate' => 'boolean',
        'leaderboard' => 'boolean',
        'post' => 'boolean',
        'practice' => 'boolean',
        'exams' => 'boolean',
        'results' => 'boolean',
        'mcq_management' => 'boolean',
        'read_access' => 'boolean',
        'notes' => 'boolean',
    ];

    /**
     * Get the user that owns the access record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user has access to a specific page.
     */
    public function hasAccess(string $page): bool
    {
        return $this->{$page} ?? false;
    }

    /**
     * Grant access to a specific page.
     */
    public function grantAccess(string $page): bool
    {
        if (in_array($page, $this->fillable) && $page !== 'user_id') {
            $this->{$page} = true;
            return $this->save();
        }
        return false;
    }

    /**
     * Revoke access to a specific page.
     */
    public function revokeAccess(string $page): bool
    {
        if (in_array($page, $this->fillable) && $page !== 'user_id') {
            $this->{$page} = false;
            return $this->save();
        }
        return false;
    }

    /**
     * Get all accessible pages for this user.
     */
    public function getAccessiblePages(): array
    {
        $pages = [];

        foreach (self::FEATURE_FIELDS as $field) {
            if ($this->{$field}) {
                $pages[] = $field;
            }
        }
        
        return $pages;
    }
}
