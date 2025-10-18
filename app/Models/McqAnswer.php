<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'question_id', 
        'mcq_set_id',
        'selected_answer',
        'selected_ans', // For backward compatibility
        'is_correct',
        'marks_obtained',
        'time_taken'
    ];

    protected $casts = [
        'is_correct' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(McqQuestion::class);
    }

    public function mcqSet()
    {
        return $this->belongsTo(McqSet::class);
    }
}