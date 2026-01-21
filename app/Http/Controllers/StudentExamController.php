<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentExamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->schoolClass) {
            $exams = Exam::whereHas('subject', function($query) use ($user) {
                $query->where('class_id', $user->class_id);
            })
            ->where('status', Exam::STATUS_PUBLISHED)
            ->with('subject')
            ->orderBy('start_time')
            ->paginate(10);
        } else {
            $exams = Exam::whereRaw('1 = 0')->paginate(10);
        }

        return view('student.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        $user = Auth::user();
        
        if ($user->schoolClass && $exam->subject->class_id !== $user->class_id) {
            abort(403, 'You do not have access to this exam.');
        }
        
        $exam->load('subject');
        
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->first();
        
        return view('student.exams.show', compact('exam', 'existingAttempt'));
    }

    public function start(Exam $exam)
    {
        $user = Auth::user();
        
        if ($user->schoolClass && $exam->subject->class_id !== $user->class_id) {
            abort(403, 'You do not have access to this exam.');
        }

        if (!$exam->isActive()) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'This exam is not currently available.');
        }

        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->status === ExamAttempt::STATUS_SUBMITTED || 
                $existingAttempt->status === ExamAttempt::STATUS_GRADED) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', 'You have already submitted this exam.');
            }
            return redirect()->route('student.exams.take', $exam);
        }

        $exam->load('questions');
        
        if ($exam->questions->count() === 0) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'This exam has no questions yet.');
        }

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $user->id,
            'started_at' => now(),
            'total_marks' => $exam->total_marks,
            'status' => ExamAttempt::STATUS_IN_PROGRESS
        ]);

        return redirect()->route('student.exams.take', $exam);
    }

    public function take(Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$attempt || $attempt->status !== ExamAttempt::STATUS_IN_PROGRESS) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'No active attempt found.');
        }

        $exam->load('questions');

        $answers = ExamAnswer::where('attempt_id', $attempt->id)
            ->pluck('answer', 'question_id')
            ->toArray();

        $elapsedSeconds = now()->diffInSeconds($attempt->started_at);
        $remainingSeconds = max(0, ($exam->duration_minutes * 60) - $elapsedSeconds);

        return view('student.exams.take', compact('exam', 'attempt', 'answers', 'remainingSeconds'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
            ->firstOrFail();

        $exam->load('questions');
        
        $answers = $request->input('answers', []);
        $totalScore = 0;

        DB::transaction(function() use ($attempt, $exam, $answers, &$totalScore) {
            foreach ($exam->questions as $question) {
                $answerText = $answers[$question->id] ?? null;
                
                $marksObtained = 0;
                
                if ($answerText !== null) {
                    if ($question->question_type === Question::TYPE_MULTIPLE_CHOICE) {
                        if ($answerText == $question->correct_answer) {
                            $marksObtained = $question->pivot->marks ?? $question->marks;
                        }
                    }
                }

                ExamAnswer::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id
                    ],
                    [
                        'answer' => $answerText,
                        'marks_obtained' => $marksObtained
                    ]
                );

                $totalScore += $marksObtained;
            }

            $attempt->update([
                'total_score' => $totalScore,
                'submitted_at' => now(),
                'status' => ExamAttempt::STATUS_SUBMITTED
            ]);
        });

        return redirect()->route('student.exams.results', $exam)
            ->with('success', 'Exam submitted successfully!');
    }

    public function results(Exam $exam)
    {
        $user = Auth::user();
        
        if ($user->schoolClass && $exam->subject->class_id !== $user->class_id) {
            abort(403, 'You do not have access to this exam.');
        }

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->firstOrFail();

        if ($attempt->status === ExamAttempt::STATUS_IN_PROGRESS) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Please complete the exam first.');
        }

        $exam->load(['questions', 'subject']);
        $attempt->load(['answers.question']);

        $answers = $attempt->answers->keyBy('question_id');

        return view('student.exams.results', compact('exam', 'attempt', 'answers'));
    }

    public function history()
    {
        $user = Auth::user();
        
        $attempts = ExamAttempt::where('student_id', $user->id)
            ->with(['exam.subject'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);

        return view('student.exams.history', compact('attempts'));
    }

    public function detailedResults(Exam $exam)
    {
        $user = Auth::user();
        
        if ($user->schoolClass && $exam->subject->class_id !== $user->class_id) {
            abort(403, 'You do not have access to this exam.');
        }

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->firstOrFail();

        if ($attempt->status === ExamAttempt::STATUS_IN_PROGRESS) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Please complete the exam first.');
        }

        $exam->load(['questions' => function($query) {
            $query->orderBy('exam_questions.order');
        }, 'subject']);
        
        $attempt->load(['answers.question']);

        $answers = $attempt->answers->keyBy('question_id');

        return view('student.exams.detailed-results', compact('exam', 'attempt', 'answers'));
    }
}