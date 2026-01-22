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
        $activeExams = collect(); // Exams currently in progress
        $missedExams = collect(); // Exams that have ended but student didn't attempt
        $totalAttempts = 0;
        $completedAttempts = 0;
        $averageScore = 0;
        $recentAttempts = collect();
        
        $now = now()->setTimezone('Asia/Kuala_Lumpur');
        
        // Special case: Student 1 can access all exams
        if ($user->email === 'student1@test.com') {
            // Get active exams (currently in progress - priority)
            $activeExams = Exam::where('status', Exam::STATUS_PUBLISHED)
                ->with('subject')
                ->get()
                ->filter(function($exam) use ($now) {
                    $startTime = $exam->start_time->setTimezone('Asia/Kuala_Lumpur');
                    $endTime = $exam->end_time->setTimezone('Asia/Kuala_Lumpur');
                    return $startTime <= $now && $endTime >= $now;
                })
                ->sortBy('start_time')
                ->values();
            
            // Get upcoming exams (future) - exclude active exams
            $activeExamIds = $activeExams->pluck('id');
            $upcomingExams = Exam::where('status', Exam::STATUS_PUBLISHED)
                ->whereNotIn('id', $activeExamIds)
                ->with('subject')
                ->get()
                ->filter(function($exam) use ($now) {
                    $startTime = $exam->start_time->setTimezone('Asia/Kuala_Lumpur');
                    return $startTime > $now;
                })
                ->sortBy('start_time')
                ->take(5)
                ->values();
            
            // Get missed exams (ended but not attempted)
            $allExamIds = Exam::where('status', Exam::STATUS_PUBLISHED)->pluck('id');
            $attemptedExamIds = ExamAttempt::where('student_id', $user->id)
                ->whereIn('exam_id', $allExamIds)
                ->pluck('exam_id');
            
            $missedExams = Exam::where('status', Exam::STATUS_PUBLISHED)
                ->whereNotIn('id', $activeExamIds)
                ->whereNotIn('id', $upcomingExams->pluck('id'))
                ->with('subject')
                ->get()
                ->filter(function($exam) use ($now, $attemptedExamIds) {
                    $endTime = $exam->end_time->setTimezone('Asia/Kuala_Lumpur');
                    return $endTime < $now && !$attemptedExamIds->contains($exam->id);
                })
                ->sortByDesc('end_time')
                ->take(5)
                ->values();
        } elseif ($user->schoolClass) {
            // Get subject IDs from classes that have this subject (many-to-many)
            $subjectIds = Subject::whereHas('classes', function($query) use ($user) {
                $query->where('school_classes.id', $user->class_id);
            })->pluck('id');
            
            // Get active exams (currently in progress - priority)
            $activeExams = Exam::whereIn('subject_id', $subjectIds)
                ->where('status', Exam::STATUS_PUBLISHED)
                ->with('subject')
                ->get()
                ->filter(function($exam) use ($now) {
                    $startTime = $exam->start_time->setTimezone('Asia/Kuala_Lumpur');
                    $endTime = $exam->end_time->setTimezone('Asia/Kuala_Lumpur');
                    return $startTime <= $now && $endTime >= $now;
                })
                ->sortBy('start_time')
                ->values();
            
            // Get upcoming exams (future) - exclude active exams
            $activeExamIds = $activeExams->pluck('id');
            $upcomingExams = Exam::whereIn('subject_id', $subjectIds)
                ->where('status', Exam::STATUS_PUBLISHED)
                ->whereNotIn('id', $activeExamIds)
                ->with('subject')
                ->get()
                ->filter(function($exam) use ($now) {
                    $startTime = $exam->start_time->setTimezone('Asia/Kuala_Lumpur');
                    return $startTime > $now;
                })
                ->sortBy('start_time')
                ->take(5)
                ->values();
            
            // Get missed exams (ended but not attempted)
            $allExamIds = Exam::whereIn('subject_id', $subjectIds)
                ->where('status', Exam::STATUS_PUBLISHED)
                ->pluck('id');
            $attemptedExamIds = ExamAttempt::where('student_id', $user->id)
                ->whereIn('exam_id', $allExamIds)
                ->pluck('exam_id');
            
            $missedExams = Exam::whereIn('subject_id', $subjectIds)
                ->where('status', Exam::STATUS_PUBLISHED)
                ->whereNotIn('id', $activeExamIds)
                ->whereNotIn('id', $upcomingExams->pluck('id'))
                ->with('subject')
                ->get()
                ->filter(function($exam) use ($now, $attemptedExamIds) {
                    $endTime = $exam->end_time->setTimezone('Asia/Kuala_Lumpur');
                    return $endTime < $now && !$attemptedExamIds->contains($exam->id);
                })
                ->sortByDesc('end_time')
                ->take(5)
                ->values();
        }
        
        // Check which active exams student hasn't started yet
        if ($activeExams->count() > 0) {
            $activeExamIds = $activeExams->pluck('id');
            $attemptedExamIds = ExamAttempt::where('student_id', $user->id)
                ->whereIn('exam_id', $activeExamIds)
                ->pluck('exam_id');
            
            $activeExams->each(function($exam) use ($attemptedExamIds) {
                $exam->not_started = !$attemptedExamIds->contains($exam->id);
            });
        }

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
        
        return view('student.dashboard', [
            'user' => $user,
            'activeExams' => $activeExams,
            'upcomingExams' => $upcomingExams,
            'missedExams' => $missedExams,
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
        
        // Special case: Student 1 can see all subjects
        if ($user->email === 'student1@test.com') {
            $subjects = Subject::with(['lecturer', 'classes', 'exams'])
                ->withCount('exams')
                ->orderBy('name')
                ->get();
        } elseif ($user->schoolClass) {
            $class = $user->schoolClass;
            // Get subjects assigned to student's class (many-to-many)
            $subjects = Subject::whereHas('classes', function($query) use ($user) {
                $query->where('school_classes.id', $user->class_id);
            })
                ->with(['lecturer', 'classes', 'exams'])
                ->withCount('exams')
                ->orderBy('name')
                ->get();
        }
        
        return view('student.subjects.index', compact('subjects', 'class'));
    }
}
