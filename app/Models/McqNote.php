<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class McqNote extends Model
{
    use HasFactory;

    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_PENDING = 'pending';
    public const VISIBILITY_APPROVED = 'approved';
    public const VISIBILITY_REJECTED = 'rejected';

    protected $fillable = [
        'mcq_question_id',
        'user_id',
        'title',
        'content',
        'visibility',
        'share_requested',
        'approved_at',
        'rejected_at',
        'admin_feedback',
    ];

    protected $casts = [
        'share_requested' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(McqQuestion::class, 'mcq_question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('visibility', self::VISIBILITY_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('visibility', self::VISIBILITY_PENDING);
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', self::VISIBILITY_PRIVATE);
    }

    public function markAsApproved(?string $feedback = null): void
    {
        $this->visibility = self::VISIBILITY_APPROVED;
        $this->share_requested = false;
        $this->approved_at = now();
        $this->rejected_at = null;
        $this->admin_feedback = $feedback;
        $this->save();
    }

    public function markAsRejected(?string $feedback = null): void
    {
        $this->visibility = self::VISIBILITY_REJECTED;
        $this->share_requested = false;
        $this->approved_at = null;
        $this->rejected_at = now();
        $this->admin_feedback = $feedback;
        $this->save();
    }

    public function requestShare(): void
    {
        if ($this->visibility === self::VISIBILITY_APPROVED) {
            return;
        }

        $this->visibility = self::VISIBILITY_PENDING;
        $this->share_requested = true;
        $this->rejected_at = null;
        $this->admin_feedback = null;
        $this->save();
    }

    public function makePrivate(): void
    {
        $this->visibility = self::VISIBILITY_PRIVATE;
        $this->share_requested = false;
        $this->approved_at = null;
        $this->rejected_at = null;
        $this->admin_feedback = null;
        $this->save();
    }

    public function getContentHtmlAttribute(): HtmlString
    {
        $html = Str::markdown($this->content ?? '', [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        if (str_contains($html, '[[hl:')) {
            $colorMap = [
                'sun' => 'note-highlight-sun',
                'mint' => 'note-highlight-mint',
                'sky' => 'note-highlight-sky',
                'rose' => 'note-highlight-rose',
                'violet' => 'note-highlight-violet',
            ];

            $html = preg_replace_callback(
                '/\[\[hl:([a-z0-9_-]{2,20})\]\](.*?)\[\[\/hl\]\]/si',
                function ($matches) use ($colorMap) {
                    $colorKey = strtolower($matches[1]);
                    $class = $colorMap[$colorKey] ?? $colorMap['sun'];
                    $inner = $matches[2];

                    return '<mark class="note-highlight ' . $class . '">' . $inner . '</mark>';
                },
                $html
            );
        }

        return new HtmlString($html);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->visibility) {
            self::VISIBILITY_APPROVED => 'Approved',
            self::VISIBILITY_PENDING => 'Pending Review',
            self::VISIBILITY_REJECTED => 'Rejected',
            default => 'Private',
        };
    }
}
