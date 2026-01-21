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
                    <p class="text-sm text-gray-600 mb-2">Your Score</p>
                    <p class="text-5xl font-bold text-indigo-600 mb-2">
                        {{ $attempt->total_score }} / {{ $attempt->total_marks }}
                    </p>
                    <div class="inline-flex items-center px-4 py-2 rounded-full {{ $attempt->percentage >= 70 ? 'bg-green-100 text-green-800' : ($attempt->percentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        <span class="text-lg font-semibold">{{ number_format($attempt->percentage, 1) }}%</span>
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
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('student.exams.index') }}" 
                   class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Exams
                </a>
                <a href="{{ route('student.exams.detailed-results', $exam) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    View Detailed Results
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
