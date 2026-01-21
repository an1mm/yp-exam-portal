<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'description',
        'academic_year'
    ];

    /**
     * Get the students in this class
     */
    public function students()
    {
        return $this->hasMany(User::class, 'class_id')->where('role', 'student');
    }

    /**
     * Get the subjects for this class (many-to-many).
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'school_class_id', 'subject_id')
            ->withTimestamps();
    }

    /**
     * Get the exams for this class
     */
    public function exams()
    {
        $subjectIds = $this->subjects()->pluck('subjects.id');
        return \App\Models\Exam::whereIn('subject_id', $subjectIds);
    }
}
