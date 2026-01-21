<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Exam;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Role constants
    public const ROLE_LECTURER = 'lecturer';
    public const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'class_id' 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Role scopes
    public function scopeLecturers($query)
    {
        return $query->where('role', self::ROLE_LECTURER);
    }
    
    public function scopeStudents($query)
    {
        return $query->where('role', self::ROLE_STUDENT);
    }
    
    // Role check methods
    public function isLecturer(): bool
    {
        return $this->role === self::ROLE_LECTURER;
    }
    
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    // Relationships
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function taughtSubjects()
    {
        return $this->hasMany(Subject::class, 'lecturer_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }
}