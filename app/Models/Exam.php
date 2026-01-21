<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'duration_minutes',
        'start_time',
        'end_time',
        'total_marks',
        'passing_marks',
        'status',
        'created_by',
        'is_published',
        'instructions'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean',
    ];

    // Exam statuses
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the subject that owns the exam.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the user who created the exam.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The questions that belong to the exam.
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot('marks')
            ->withTimestamps();
    }

    /**
     * Get the attempts for the exam.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Check if the exam is active (current time is between start and end time).
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->status === self::STATUS_PUBLISHED &&
               $this->start_time <= $now && 
               $this->end_time >= $now;
    }

    /**
     * Check if the exam has ended.
     */
    public function hasEnded(): bool
    {
        return now() > $this->end_time;
    }

    /**
     * Check if the exam has started.
     */
    public function hasStarted(): bool
    {
        return now() >= $this->start_time;
    }

    /**
     * Get the remaining time in minutes.
     */
    public function getRemainingTimeInMinutes(): int
    {
        if (!$this->hasStarted() || $this->hasEnded()) {
            return 0;
        }
        
        return now()->diffInMinutes($this->end_time, false);
    }

    /**
     * Get the total number of questions in the exam.
     */
    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }
}
