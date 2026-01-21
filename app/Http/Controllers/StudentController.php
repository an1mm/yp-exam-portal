<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $upcomingExams = collect();
        $totalAttempts = 0;
        $completedAttempts = 0;
        $averageScore = 0;
        $recentAttempts = collect();
        
        if ($user->schoolClass) {
            $upcomingExams = Exam::whereHas('subject', function($query) use ($user) {
                $query->where('class_id', $user->class_id);
            })
            ->where('status', Exam::STATUS_PUBLISHED)
            ->where('start_time', '>', now())
            ->with('subject')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

            $totalAttempts = ExamAttempt::where('student_id', $user->id)->count();
            $completedAttempts = ExamAttempt::where('student_id', $user->id)
                ->whereIn('status', ['submitted', 'graded'])
                ->count();
            
            $completedScores = ExamAttempt::where('student_id', $user->id)
                ->whereIn('status', ['submitted', 'graded'])
                ->where('total_marks', '>', 0)
                ->get();
            
            if ($completedScores->count() > 0) {
                $averageScore = $completedScores->avg(function($attempt) {
                    return ($attempt->total_score / $attempt->total_marks) * 100;
                });
            }

            $recentAttempts = ExamAttempt::where('student_id', $user->id)
                ->with(['exam.subject'])
                ->orderBy('submitted_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('student.dashboard', [
            'user' => $user,
            'upcomingExams' => $upcomingExams,
            'totalAttempts' => $totalAttempts,
            'completedAttempts' => $completedAttempts,
            'averageScore' => round($averageScore, 1),
            'recentAttempts' => $recentAttempts
        ]);
    }

    public function subjects()
    {
        $user = Auth::user();
        
        $subjects = collect();
        $class = null;
        
        if ($user->schoolClass) {
            $class = $user->schoolClass;
            $subjects = Subject::where('class_id', $user->class_id)
                ->with(['lecturer', 'schoolClass', 'exams'])
                ->withCount('exams')
                ->orderBy('name')
                ->get();
        }
        
        return view('student.subjects.index', compact('subjects', 'class'));
    }
}
