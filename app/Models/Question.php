<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    protected $fillable = [
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'marks',
        'subject_id',
        'created_by'
    ];

    protected $casts = [
        'options' => 'array',
    ];

    // Question types
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_TRUE_FALSE = 'true_false';
    public const TYPE_SHORT_ANSWER = 'short_answer';
    public const TYPE_ESSAY = 'essay';

    /**
     * Get the subject that owns the question.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the user who created the question.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The exams that include this question.
     */
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')
            ->withPivot('marks')
            ->withTimestamps();
    }

    /**
     * Check if the question is multiple choice.
     */
    public function isMultipleChoice(): bool
    {
        return $this->question_type === self::TYPE_MULTIPLE_CHOICE;
    }

    /**
     * Get the correct answer for display.
     */
    public function getFormattedCorrectAnswer()
    {
        if ($this->isMultipleChoice() && is_array($this->options)) {
            $index = (int)$this->correct_answer - 1;
            return $this->options[$index] ?? 'Invalid option';
        }
        
        return $this->correct_answer;
    }
}
