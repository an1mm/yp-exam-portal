<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::where('created_by', auth()->id())
            ->with('subject')
            ->latest()
            ->get();
        
        return view('questions.index', compact('questions'));
    }

    public function create(Request $request)
    {
        $subjects = Subject::where('lecturer_id', auth()->id())->get();
        $examId = $request->query('exam_id');
        $subjectId = null;
        
        if ($examId) {
            $exam = Exam::where('id', $examId)
                ->where('created_by', auth()->id())
                ->first();
            if ($exam) {
                $subjectId = $exam->subject_id;
            }
        }
        
        if (!$subjectId && $subjects->count() > 0) {
            $subjectId = $subjects->first()->id;
        }
        
        return view('questions.create', compact('subjects', 'examId', 'subjectId'));
    }

    public function store(Request $request)
    {
        try {
            $questionType = $request->input('question_type');
            
            $rules = [
                'question_text' => 'required|string',
                'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
                'subject_id' => 'required|exists:subjects,id',
                'marks' => 'required|integer|min:1',
                'exam_id' => 'nullable|exists:exams,id',
            ];

            if ($questionType === 'multiple_choice') {
                $rules['options'] = 'required|array|min:2';
                $rules['options.*'] = 'required|string';
                $rules['correct_answer'] = 'required|string';
            } else {
                $rules['correct_answer'] = 'required|string';
            }

            $validated = $request->validate($rules);

            $subject = Subject::findOrFail($validated['subject_id']);
            
            if ($subject->lecturer_id !== auth()->id()) {
                return back()->withErrors(['subject_id' => 'You can only create questions for your own subjects.'])->withInput();
            }

            if ($validated['question_type'] === 'multiple_choice') {
                $validated['options'] = $validated['options'] ?? [];
            } else {
                $validated['options'] = null;
            }

            $validated['created_by'] = auth()->id();

            $question = Question::create($validated);

            $examId = $request->input('exam_id');
            if ($examId) {
                $exam = Exam::where('id', $examId)
                    ->where('created_by', auth()->id())
                    ->first();
                
                if ($exam && $exam->subject_id == $question->subject_id) {
                    $exam->questions()->attach($question->id, [
                        'marks' => $question->marks
                    ]);
                    
                    return redirect()->route('lecturer.exams.show', $exam)
                        ->with('success', 'Question created and added to exam successfully');
                }
            }

            return redirect()->route('lecturer.questions.index')
                ->with('success', 'Question created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create question: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Question $question)
    {
        if ($question->created_by !== auth()->id()) {
            abort(403);
        }

        $question->load('subject');
        
        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        if ($question->created_by !== auth()->id()) {
            abort(403);
        }

        $subjects = Subject::where('lecturer_id', auth()->id())->get();
        
        return view('questions.edit', compact('question', 'subjects'));
    }

    public function update(Request $request, Question $question)
    {
        if ($question->created_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|integer|min:1',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        
        if ($subject->lecturer_id !== auth()->id()) {
            return back()->withErrors(['subject_id' => 'You can only assign questions to your own subjects.']);
        }

        if ($validated['question_type'] === 'multiple_choice') {
            $validated['options'] = $validated['options'];
        } else {
            $validated['options'] = null;
        }

        $question->update($validated);

        return redirect()->route('lecturer.questions.index')
            ->with('success', 'Question updated successfully');
    }

    public function destroy(Question $question)
    {
        if ($question->created_by !== auth()->id()) {
            abort(403);
        }

        $question->delete();

        return redirect()->route('lecturer.questions.index')
            ->with('success', 'Question deleted successfully');
    }
}
