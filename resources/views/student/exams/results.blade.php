@extends('layouts.app')

@section('page-title', 'Exam Results')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Exam Results</h2>
        </div>

        <div class="p-6">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $exam->title }}</h3>
                <p class="text-gray-600">{{ $exam->subject->name }}</p>
            </div>

            <!-- Score Summary -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-8 mb-6 text-center">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Final Score</p>
                    <p class="text-5xl font-bold text-indigo-600 mb-2">
                        {{ $attempt->total_score }} / {{ $attempt->total_marks }}
                    </p>
                    <div class="inline-flex items-center px-4 py-2 rounded-full {{ $attempt->percentage >= 70 ? 'bg-green-100 text-green-800' : ($attempt->percentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        <span class="text-lg font-semibold">{{ number_format($attempt->percentage, 1) }}%</span>
                    </div>
                </div>
                
                @php
                    $correctCount = 0;
                    $incorrectCount = 0;
                    foreach($exam->questions as $question) {
                        if(isset($answers[$question->id])) {
                            $answer = $answers[$question->id];
                            if($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                                if($answer == $question->correct_answer) {
                                    $correctCount++;
                                } else {
                                    $incorrectCount++;
                                }
                            }
                        }
                    }
                @endphp
                
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="bg-white rounded-lg p-4 border-2 border-green-200">
                        <p class="text-sm text-gray-600 mb-1">Total Correct</p>
                        <p class="text-3xl font-bold text-green-600">{{ $correctCount }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-2 border-red-200">
                        <p class="text-sm text-gray-600 mb-1">Total Incorrect</p>
                        <p class="text-3xl font-bold text-red-600">{{ $incorrectCount }}</p>
                    </div>
                </div>
                
                @if($exam->passing_marks)
                    @if($attempt->total_score >= $exam->passing_marks)
                        <div class="mt-4">
                            <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Passed (Passing: {{ $exam->passing_marks }})
                            </span>
                        </div>
                    @else
                        <div class="mt-4">
                            <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Failed (Passing: {{ $exam->passing_marks }})
                            </span>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Exam Info -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Submitted At</p>
                    <p class="font-semibold text-gray-900">{{ $attempt->submitted_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Time Taken</p>
                    <p class="font-semibold text-gray-900">{{ $attempt->started_at->diffInMinutes($attempt->submitted_at) }} minutes</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-center gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('student.dashboard') }}" 
                   style="background: linear-gradient(to right, #4f46e5, #6366f1);"
                   class="inline-flex items-center px-10 py-4 text-white font-bold rounded-xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Return to Dashboard
                </a>
                <a href="{{ route('student.exams.detailed-results', $exam) }}" 
                   style="background: linear-gradient(to right, #6366f1, #818cf8);"
                   class="inline-flex items-center px-10 py-4 text-white font-bold rounded-xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    View Detailed Results
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
