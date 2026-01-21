<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'class_id',
        'lecturer_id'
    ];

    /**
     * Get the classes that have this subject (many-to-many).
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'school_class_id')
            ->withTimestamps();
    }

    /**
     * Get the primary class that owns the subject (for backward compatibility).
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the lecturer who teaches this subject.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get the exams for the subject.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get the students enrolled in this subject's classes.
     */
    public function students()
    {
        $classIds = $this->classes()->pluck('school_classes.id');
        return \App\Models\User::whereIn('class_id', $classIds)
            ->where('role', 'student');
    }
}
