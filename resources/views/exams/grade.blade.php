@extends('layouts.app')

@section('page-title', 'Grade Exam')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('lecturer.exams.results', $exam) }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Results
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Grade Exam</h2>
                <p class="text-gray-600 mt-1">{{ $exam->title }} - {{ $exam->subject->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Student</p>
                <p class="text-lg font-semibold text-gray-900">{{ $attempt->student->name }}</p>
                <p class="text-sm text-gray-500">{{ $attempt->student->email }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-6">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Score</p>
                <p class="text-2xl font-bold text-gray-900">{{ $attempt->total_score }} / {{ $attempt->total_marks }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Percentage</p>
                <p class="text-2xl font-bold text-indigo-600">{{ number_format($attempt->percentage, 1) }}%</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                </span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('lecturer.exams.answers.grade', ['exam' => $exam, 'attempt' => $attempt, 'answer' => 0]) }}" id="grading-form">
        @csrf
        <div class="space-y-6">
            @foreach($exam->questions as $index => $question)
                @php
                    $answer = $answers[$question->id] ?? null;
                    $questionMarks = $question->pivot->marks ?? $question->marks;
                    $isOpenText = in_array($question->question_type, ['short_answer', 'essay']);
                @endphp

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-700 rounded-full font-semibold">
                                    {{ $index + 1 }}
                                </span>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $question->question_text }}</h3>
                            </div>
                            <div class="flex items-center space-x-4 text-sm text-gray-500 ml-13">
                                <span>Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                <span>Marks: {{ $questionMarks }}</span>
                            </div>
                        </div>
                        @if($answer)
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Current Score</p>
                                <p class="text-xl font-bold text-indigo-600">{{ $answer->marks_obtained ?? 0 }} / {{ $questionMarks }}</p>
                            </div>
                        @endif
                    </div>

                    @if($question->question_type === 'multiple_choice' && $question->options)
                        <div class="ml-13 space-y-2 mb-4">
                            @foreach($question->options as $optionIndex => $option)
                                @php
                                    $isSelected = $answer && $answer->answer == ($optionIndex + 1);
                                    $isCorrect = ($optionIndex + 1) == $question->correct_answer;
                                @endphp
                                <div class="p-3 border rounded-lg {{ $isCorrect ? 'bg-green-50 border-green-200' : ($isSelected ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200') }}">
                                    <div class="flex items-center">
                                        <input type="radio" disabled {{ $isSelected ? 'checked' : '' }} class="mr-3">
                                        <span class="{{ $isCorrect ? 'font-semibold text-green-900' : ($isSelected ? 'font-semibold text-red-900' : 'text-gray-700') }}">
                                            {{ $option }}
                                        </span>
                                        @if($isCorrect)
                                            <span class="ml-2 text-xs text-green-600 font-medium">(Correct)</span>
                                        @endif
                                        @if($isSelected && !$isCorrect)
                                            <span class="ml-2 text-xs text-red-600 font-medium">(Selected)</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="ml-13 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <strong>Auto-graded:</strong> {{ $answer && $answer->marks_obtained > 0 ? 'Correct' : 'Incorrect' }}
                            </p>
                        </div>
                    @else
                        <div class="ml-13 space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Student Answer:</p>
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $answer && $answer->answer ? $answer->answer : 'No answer provided' }}</p>
                                </div>
                            </div>

                            @if($isOpenText)
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-2">Grade This Answer:</p>
                                    <form method="POST" action="{{ route('lecturer.exams.answers.grade', ['exam' => $exam, 'attempt' => $attempt, 'answer' => $answer->id]) }}" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="marks_obtained_{{ $answer->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Marks Obtained (Max: {{ $questionMarks }})
                                                </label>
                                                <input type="number" 
                                                       id="marks_obtained_{{ $answer->id }}" 
                                                       name="marks_obtained" 
                                                       value="{{ old('marks_obtained', $answer->marks_obtained ?? 0) }}"
                                                       min="0" 
                                                       max="{{ $questionMarks }}"
                                                       step="0.5"
                                                       required
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                            <div>
                                                <label for="feedback_{{ $answer->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Feedback (Optional)
                                                </label>
                                                <input type="text" 
                                                       id="feedback_{{ $answer->id }}" 
                                                       name="feedback" 
                                                       value="{{ old('feedback', $answer->feedback ?? '') }}"
                                                       placeholder="Add feedback..."
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        </div>
                                        <button type="submit" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                            Save Grade
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Note:</strong> This question type is auto-graded. Review the answer above.
                                    </p>
                                </div>
                            @endif

                            @if($answer && $answer->feedback)
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="text-sm font-medium text-blue-900 mb-1">Feedback:</p>
                                    <p class="text-sm text-blue-800">{{ $answer->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </form>
</div>
@endsection
