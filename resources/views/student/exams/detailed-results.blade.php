@extends('layouts.app')

@section('page-title', 'Detailed Results')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('student.exams.results', $exam) }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Results Summary
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Detailed Results</h2>
        <p class="text-gray-600 mt-1">{{ $exam->title }} - {{ $exam->subject->name }}</p>
    </div>

    <!-- Score Summary Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-2">Your Score</p>
                <p class="text-4xl font-bold text-indigo-600 mb-2">
                    {{ $attempt->total_score }} / {{ $attempt->total_marks }}
                </p>
                <div class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold {{ $attempt->percentage >= 70 ? 'bg-green-100 text-green-800' : ($attempt->percentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ number_format($attempt->percentage, 1) }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Questions and Answers -->
    <div class="space-y-4">
        @foreach($exam->questions as $index => $question)
            @php
                $answer = $answers[$question->id] ?? null;
                $isCorrect = false;
                $marksObtained = 0;
                
                if ($answer) {
                    $marksObtained = $answer->marks_obtained ?? 0;
                    if ($question->question_type === 'multiple_choice') {
                        $isCorrect = $answer->answer == $question->correct_answer;
                    }
                }
                
                $questionMarks = $question->pivot->marks ?? $question->marks;
            @endphp

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start space-x-3">
                        <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-700 rounded-full font-semibold">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $question->question_text }}</h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                <span>Marks: {{ $questionMarks }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm {{ $isCorrect ? 'text-green-600' : ($question->question_type === 'multiple_choice' ? 'text-red-600' : 'text-yellow-600') }} font-semibold">
                            {{ $marksObtained }} / {{ $questionMarks }}
                        </div>
                        @if($question->question_type === 'multiple_choice')
                            @if($isCorrect)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                    Correct
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                    Incorrect
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                Pending Review
                            </span>
                        @endif
                    </div>
                </div>

                @if($question->question_type === 'multiple_choice' && $question->options)
                    <div class="ml-13 space-y-2 mb-4">
                        @foreach($question->options as $optionIndex => $option)
                            @php
                                $isSelected = $answer && $answer->answer == ($optionIndex + 1);
                                $isCorrectOption = ($optionIndex + 1) == $question->correct_answer;
                            @endphp
                            <div class="flex items-center p-3 rounded-lg border-2 {{ $isCorrectOption ? 'border-green-300 bg-green-50' : ($isSelected ? 'border-red-300 bg-red-50' : 'border-gray-200') }}">
                                <div class="flex items-center flex-1">
                                    @if($isCorrectOption)
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    @if($isSelected && !$isCorrectOption)
                                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    <span class="{{ $isCorrectOption ? 'font-semibold text-green-900' : ($isSelected ? 'font-semibold text-red-900' : 'text-gray-700') }}">
                                        {{ $option }}
                                    </span>
                                    @if($isCorrectOption)
                                        <span class="ml-2 text-xs text-green-600 font-medium">(Correct Answer)</span>
                                    @endif
                                    @if($isSelected && !$isCorrectOption)
                                        <span class="ml-2 text-xs text-red-600 font-medium">(Your Answer)</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="ml-13 space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Your Answer:</p>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-gray-900">{{ $answer && $answer->answer ? $answer->answer : 'No answer provided' }}</p>
                            </div>
                        </div>
                        @if($question->question_type === 'short_answer' || $question->question_type === 'essay')
                            <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-sm text-yellow-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    This question is pending review by your lecturer.
                                </p>
                            </div>
                        @else
                            <div>
                                <p class="text-sm font-medium text-green-700 mb-1">Correct Answer:</p>
                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                    <p class="text-green-900 font-semibold">{{ $question->correct_answer }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('student.exams.history') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
            View All Exam History
        </a>
    </div>
</div>
@endsection
