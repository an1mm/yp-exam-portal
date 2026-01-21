<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Question;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;

class LecturerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Subjects & Classes Stats
        $totalSubjects = Subject::where('lecturer_id', $user->id)->count();
        
        // Get all classes for this lecturer's subjects (many-to-many)
        $subjects = Subject::where('lecturer_id', $user->id)->with('classes')->get();
        $allClassIds = $subjects->flatMap(function($subject) {
            return $subject->classes->pluck('id');
        })->unique();
        
        $totalClasses = $allClassIds->count();
        
        $totalStudents = \App\Models\User::whereIn('class_id', $allClassIds)
            ->where('role', 'student')
            ->count();
        
        // Exams Stats
        $totalExams = Exam::where('created_by', $user->id)->count();
        $activeExams = Exam::where('created_by', $user->id)
            ->where('status', Exam::STATUS_PUBLISHED)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();
        $draftExams = Exam::where('created_by', $user->id)
            ->where('status', Exam::STATUS_DRAFT)
            ->count();
        $upcomingExams = Exam::where('created_by', $user->id)
            ->where('status', Exam::STATUS_PUBLISHED)
            ->where('start_time', '>', now())
            ->count();
        
        // Questions Stats
        $totalQuestions = Question::where('created_by', $user->id)->count();
        
        // Student Submissions Stats
        $totalSubmissions = \App\Models\ExamAttempt::whereHas('exam', function($query) use ($user) {
            $query->where('created_by', $user->id);
        })
        ->where('status', '!=', 'in_progress')
        ->count();
        
        // Pending Grading (open-text questions that need manual grading)
        $pendingGrading = \App\Models\ExamAttempt::whereHas('exam', function($query) use ($user) {
            $query->where('created_by', $user->id);
        })
        ->where('status', 'submitted')
        ->whereHas('answers.question', function($query) {
            $query->whereIn('question_type', ['short_answer', 'essay']);
        })
        ->whereHas('answers', function($query) {
            $query->where('marks_obtained', 0)
                  ->orWhereNull('marks_obtained');
        })
        ->count();
        
        // Exams with submissions (for quick access to results)
        $examsWithSubmissions = Exam::where('created_by', $user->id)
            ->has('attempts')
            ->with('subject')
            ->latest()
            ->take(3)
            ->get();
        
        // Recent Data
        $recentExams = Exam::where('created_by', $user->id)
            ->with('subject')
            ->latest()
            ->take(5)
            ->get();
        
        $recentSubjects = Subject::where('lecturer_id', $user->id)
            ->with(['classes', 'schoolClass'])
            ->latest()
            ->take(3)
            ->get();

        return view('lecturer.dashboard', compact(
            'user', 
            'totalSubjects',
            'totalClasses',
            'totalStudents',
            'totalExams', 
            'activeExams',
            'draftExams',
            'upcomingExams',
            'totalQuestions',
            'totalSubmissions',
            'pendingGrading',
            'examsWithSubmissions',
            'recentExams',
            'recentSubjects'
        ));
    }
}
