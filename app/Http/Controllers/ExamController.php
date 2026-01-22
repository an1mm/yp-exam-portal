<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('created_by', auth()->id())
            ->with('subject')
            ->latest()
            ->get();
        
        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = Subject::where('lecturer_id', auth()->id())->get();
        return view('exams.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        
        if ($subject->lecturer_id !== auth()->id()) {
            return back()->withErrors(['subject_id' => 'You can only create exams for your own subjects.']);
        }

        // Convert datetime from Kuala Lumpur timezone to UTC for storage
        // datetime-local input is treated as Kuala Lumpur time
        $validated['start_time'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_time'], 'Asia/Kuala_Lumpur')
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');
        $validated['end_time'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_time'], 'Asia/Kuala_Lumpur')
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');

        $validated['created_by'] = auth()->id();
        $validated['status'] = Exam::STATUS_DRAFT;

        $exam = Exam::create($validated);

        return redirect()->route('lecturer.exams.questions.create', $exam);
    }

    public function addQuestions(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->load('subject');
        
        return view('exams.questions', compact('exam'));
    }

    public function storeQuestions(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $rules = [
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.correct_answer' => 'required',
        ];

        foreach ($request->questions as $index => $question) {
            if ($question['question_type'] === 'multiple_choice') {
                $rules["questions.{$index}.options"] = 'required|array|min:2';
                $rules["questions.{$index}.options.*"] = 'required|string';
            }
        }

        $validated = $request->validate($rules);

        $totalMarks = 0;

        foreach ($validated['questions'] as $questionData) {
            $options = null;
            if ($questionData['question_type'] === 'multiple_choice' && isset($questionData['options'])) {
                $options = array_values(array_filter($questionData['options']));
            }

            $question = Question::create([
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
                'subject_id' => $exam->subject_id,
                'marks' => $questionData['marks'],
                'options' => $options,
                'correct_answer' => $questionData['correct_answer'],
                'created_by' => auth()->id(),
            ]);

            $exam->questions()->attach($question->id, ['marks' => $questionData['marks']]);
            $totalMarks += $questionData['marks'];
        }

        $exam->update(['total_marks' => $totalMarks]);

        return redirect()->route('lecturer.exams.show', $exam)
            ->with('success', 'Exam created successfully with ' . count($validated['questions']) . ' questions!');
    }

    public function show(Exam $exam, Request $request)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $attemptId = $request->query('attempt');
        if ($attemptId) {
            $attempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('id', $attemptId)
                ->with(['student', 'answers.question'])
                ->firstOrFail();

            $exam->load('questions');
            $answers = $attempt->answers->keyBy('question_id');

            return view('exams.grade', compact('exam', 'attempt', 'answers'));
        }

        $exam->load(['subject', 'questions', 'attempts']);
        
        $availableQuestions = Question::where('subject_id', $exam->subject_id)
            ->where('created_by', auth()->id())
            ->whereDoesntHave('exams', function($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            })
            ->get();
        
        return view('exams.show', compact('exam', 'availableQuestions'));
    }

    public function edit(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $subjects = Subject::where('lecturer_id', auth()->id())->get();
        
        return view('exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        
        if ($subject->lecturer_id !== auth()->id()) {
            return back()->withErrors(['subject_id' => 'You can only assign exams to your own subjects.']);
        }

        // Convert datetime from Kuala Lumpur timezone to UTC for storage
        // datetime-local input is treated as Kuala Lumpur time
        $validated['start_time'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_time'], 'Asia/Kuala_Lumpur')
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');
        $validated['end_time'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_time'], 'Asia/Kuala_Lumpur')
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');

        $exam->update($validated);

        return redirect()->route('lecturer.exams.index')
            ->with('success', 'Exam updated successfully');
    }

    public function destroy(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->delete();

        return redirect()->route('lecturer.exams.index')
            ->with('success', 'Exam deleted successfully');
    }

    public function attachQuestion(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'marks' => 'nullable|integer|min:1',
        ]);

        $question = Question::findOrFail($validated['question_id']);
        
        if ($question->created_by !== auth()->id() || $question->subject_id !== $exam->subject_id) {
            return back()->withErrors(['question_id' => 'Invalid question selected.']);
        }

        $marks = $validated['marks'] ?? $question->marks;
        
        $exam->questions()->attach($question->id, ['marks' => $marks]);

        return back()->with('success', 'Question added to exam successfully.');
    }

    public function detachQuestion(Exam $exam, Question $question)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->questions()->detach($question->id);

        return back()->with('success', 'Question removed from exam successfully.');
    }

    public function preview(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->load(['subject', 'questions']);
        
        return view('exams.preview', compact('exam'));
    }

    public function results(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->load(['subject', 'questions', 'attempts.student', 'attempts.answers.question']);
        
        $attempts = $exam->attempts()
            ->with(['student', 'answers.question'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $stats = [
            'total_attempts' => $attempts->count(),
            'submitted' => $attempts->where('status', '!=', 'in_progress')->count(),
            'average_score' => $attempts->where('status', '!=', 'in_progress')->avg('total_score') ?? 0,
            'average_percentage' => $attempts->where('status', '!=', 'in_progress')->avg('percentage') ?? 0,
            'highest_score' => $attempts->max('total_score') ?? 0,
            'lowest_score' => $attempts->where('status', '!=', 'in_progress')->min('total_score') ?? 0,
        ];

        return view('exams.results', compact('exam', 'attempts', 'stats'));
    }

    public function gradeAnswer(Request $request, Exam $exam, ExamAttempt $attempt, ExamAnswer $answer)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        if ($attempt->exam_id !== $exam->id) {
            abort(404);
        }

        if ($answer->attempt_id !== $attempt->id) {
            abort(404);
        }

        $validated = $request->validate([
            'marks_obtained' => 'required|numeric|min:0',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $question = $answer->question;
        $maxMarks = $question->pivot->marks ?? $question->marks;

        if ($validated['marks_obtained'] > $maxMarks) {
            return back()->withErrors(['marks_obtained' => "Marks cannot exceed {$maxMarks}."]);
        }

        $answer->update([
            'marks_obtained' => $validated['marks_obtained'],
            'feedback' => $validated['feedback'] ?? null,
        ]);

        $attempt->refresh();
        $totalScore = $attempt->answers->sum('marks_obtained');
        $attempt->update([
            'total_score' => $totalScore,
            'status' => 'graded'
        ]);

        return back()->with('success', 'Answer graded successfully.');
    }

    public function publishResults(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->update(['is_published' => true]);

        return back()->with('success', 'Exam results published successfully.');
    }

    public function unpublishResults(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->update(['is_published' => false]);

        return back()->with('success', 'Exam results unpublished successfully.');
    }
}