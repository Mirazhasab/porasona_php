<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class McqQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcq_set_id',
        'question',
        'ans_1',
        'ans_2',
        'ans_3',
        'ans_4',
        'correct_ans',
        'notes',
        'report',
        'marks',
    ];

    /**
     * Get the MCQ set that owns the question.
     */
    public function mcqSet(): BelongsTo
    {
        return $this->belongsTo(McqSet::class);
    }

    /**
     * Get the answers for the question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(McqAnswer::class, 'question_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(McqNote::class, 'mcq_question_id');
    }

    /**
     * Get the correct answer text.
     */
    public function getCorrectAnswerText()
    {
        return match($this->correct_ans) {
            '1' => $this->ans_1,
            '2' => $this->ans_2,
            '3' => $this->ans_3,
            '4' => $this->ans_4,
            default => null,
        };
    }

    /**
     * Get all answers as an array.
     */
    public function getAllAnswers()
    {
        return [
            '1' => $this->ans_1,
            '2' => $this->ans_2,
            '3' => $this->ans_3,
            '4' => $this->ans_4,
        ];
    }

    /**
     * Check if given answer is correct.
     */
    public function isCorrectAnswer($answerNumber)
    {
        return $this->correct_ans == $answerNumber;
    }
}
