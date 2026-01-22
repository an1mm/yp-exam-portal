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
        
        // Special case: Student 1 can access all exams
        if ($user->email === 'student1@test.com') {
            $exams = Exam::where('status', Exam::STATUS_PUBLISHED)
                ->with('subject')
                ->orderBy('start_time')
                ->paginate(10);
        } elseif ($user->schoolClass) {
            // Get subject IDs from classes that have this subject (many-to-many)
            $subjectIds = \App\Models\Subject::whereHas('classes', function($query) use ($user) {
                $query->where('school_classes.id', $user->class_id);
            })->pluck('id');
            
            $exams = Exam::whereIn('subject_id', $subjectIds)
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
        
        // Special case: Student 1 can access all exams
        if ($user->email !== 'student1@test.com') {
            if ($user->schoolClass) {
                // Check if subject is assigned to student's class (many-to-many)
                $hasAccess = $exam->subject->classes()->where('school_classes.id', $user->class_id)->exists();
                if (!$hasAccess) {
                    abort(403, 'You do not have access to this exam.');
                }
            } else {
                abort(403, 'You do not have access to this exam.');
            }
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
        
        // Special case: Student 1 can access all exams
        if ($user->email !== 'student1@test.com') {
            if ($user->schoolClass) {
                // Check if subject is assigned to student's class (many-to-many)
                $hasAccess = $exam->subject->classes()->where('school_classes.id', $user->class_id)->exists();
                if (!$hasAccess) {
                    abort(403, 'You do not have access to this exam.');
                }
            } else {
                abort(403, 'You do not have access to this exam.');
            }
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

        // Check if attempt already exists (resume)
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
            ->first();

        if ($existingAttempt) {
            // Resume existing attempt - timer already started
            return redirect()->route('student.exams.take', $exam);
        }

        // Create new attempt - started_at will be set when student actually starts (clicks Start Exam button)
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $user->id,
            'started_at' => null, // Will be set when timer actually starts
            'total_marks' => $exam->total_marks,
            'status' => ExamAttempt::STATUS_IN_PROGRESS
        ]);

        // Store attempt ID in session to track if instruction was shown
        session(['exam_instruction_shown_' . $exam->id => false]);

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

        // Calculate remaining time - if started_at is null, timer hasn't started yet
        $totalSeconds = $exam->duration_minutes * 60;
        $remainingSeconds = $totalSeconds;
        
        if ($attempt->started_at) {
            $elapsedSeconds = now()->diffInSeconds($attempt->started_at);
            $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);
        }

        $instructionShown = session('exam_instruction_shown_' . $exam->id, false);

        return view('student.exams.take', compact('exam', 'attempt', 'answers', 'remainingSeconds', 'instructionShown'));
    }

    public function startTimer(Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
            ->firstOrFail();

        // Only set started_at if it's null (first time starting)
        if (!$attempt->started_at) {
            $attempt->update(['started_at' => now()]);
            session(['exam_instruction_shown_' . $exam->id => true]);
        }

        return response()->json(['success' => true]);
    }

    public function saveAnswer(Request $request, Exam $exam)
    {
        try {
            $user = Auth::user();
            
            $attempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', $user->id)
                ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'error' => 'No active exam attempt found. Please start the exam first.'
                ], 404);
            }

            $validated = $request->validate([
                'question_id' => 'required|exists:questions,id',
                'answer' => 'nullable|string',
            ]);

            $question = Question::findOrFail($validated['question_id']);
            
            // Verify question belongs to this exam
            $exam->load('questions');
            if (!$exam->questions->contains($question->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'This question does not belong to this exam.'
                ], 400);
            }

            // Save answer (marks will be calculated on final submission)
            ExamAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id
                ],
                [
                    'answer' => $validated['answer'] ?? null,
                    'marks_obtained' => 0 // Will be calculated on submission
                ]
            );

            return response()->json([
                'success' => true, 
                'message' => 'Answer saved successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation error: ' . implode(', ', $e->errors()['question_id'] ?? [])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Save Answer Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to save answer. Please try again or contact support if the problem persists.'
            ], 500);
        }
    }

    public function review(Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
            ->firstOrFail();

        $exam->load('questions');
        $answers = ExamAnswer::where('attempt_id', $attempt->id)
            ->pluck('answer', 'question_id')
            ->toArray();

        // Calculate remaining time
        $totalSeconds = $exam->duration_minutes * 60;
        $remainingSeconds = $totalSeconds;
        
        if ($attempt->started_at) {
            $elapsedSeconds = now()->diffInSeconds($attempt->started_at);
            $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);
        }

        return view('student.exams.review', compact('exam', 'attempt', 'answers', 'remainingSeconds'));
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
                
                if ($answerText !== null && $answerText !== '') {
                    if ($question->question_type === Question::TYPE_MULTIPLE_CHOICE || $question->question_type === Question::TYPE_TRUE_FALSE) {
                        if ($answerText == $question->correct_answer) {
                            $marksObtained = $question->pivot->marks ?? $question->marks;
                        }
                    }
                    // For short_answer and essay, marks_obtained remains 0 (needs manual grading)
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

        // Check if this was an auto-submit due to time limit
        $isAutoSubmit = $request->has('auto_submit') && $request->input('auto_submit') === 'true';
        
        if ($isAutoSubmit) {
            return redirect()->route('student.exams.history')
                ->with('success', 'Masa anda telah tamat! Exam telah disubmit secara automatik. Semua jawapan anda telah disimpan.');
        }
        
        return redirect()->route('student.exams.results', $exam)
            ->with('success', 'Exam submitted successfully!');
    }

    public function results(Exam $exam)
    {
        $user = Auth::user();
        
        // Special case: Student 1 can access all exams
        if ($user->email !== 'student1@test.com') {
            if ($user->schoolClass) {
                // Check if subject is assigned to student's class (many-to-many)
                $hasAccess = $exam->subject->classes()->where('school_classes.id', $user->class_id)->exists();
                if (!$hasAccess) {
                    abort(403, 'You do not have access to this exam.');
                }
            } else {
                abort(403, 'You do not have access to this exam.');
            }
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
        
        // Special case: Student 1 can access all exams
        if ($user->email !== 'student1@test.com') {
            if ($user->schoolClass) {
                // Check if subject is assigned to student's class (many-to-many)
                $hasAccess = $exam->subject->classes()->where('school_classes.id', $user->class_id)->exists();
                if (!$hasAccess) {
                    abort(403, 'You do not have access to this exam.');
                }
            } else {
                abort(403, 'You do not have access to this exam.');
            }
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