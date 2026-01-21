@extends('layouts.app')

@section('page-title', 'Exam Details')

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

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Exam Details</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $exam->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($exam->status) }}
            </span>
        </div>

        <div class="p-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $exam->title }}</h3>
            <p class="text-gray-600 mb-6">{{ $exam->subject->name }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Start Time</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->start_time->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">End Time</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->end_time->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Duration</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->duration_minutes }} minutes</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Total Marks</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->total_marks }}</p>
                </div>
            </div>

            @if($exam->instructions)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Instructions</h4>
                <p class="text-gray-900 whitespace-pre-line">{{ $exam->instructions }}</p>
            </div>
            @endif

            @if($existingAttempt)
                @if($existingAttempt->status === 'submitted' || $existingAttempt->status === 'graded')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Exam Submitted</h3>
                        <p class="text-blue-700 mb-4">You have already submitted this exam.</p>
                        <div class="text-sm text-blue-600 mb-4">
                            <p>Score: <span class="font-semibold">{{ $existingAttempt->total_score }} / {{ $existingAttempt->total_marks }}</span></p>
                            <p>Percentage: <span class="font-semibold">{{ number_format($existingAttempt->percentage, 1) }}%</span></p>
                            <p class="mt-2">Submitted at: {{ $existingAttempt->submitted_at->format('d M Y, H:i') }}</p>
                        </div>
                        <a href="{{ route('student.exams.results', $exam) }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                            View Results
                        </a>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-2">Exam In Progress</h3>
                        <p class="text-yellow-700 mb-4">You have an ongoing attempt for this exam.</p>
                        <a href="{{ route('student.exams.take', $exam) }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                            Continue Exam
                        </a>
                    </div>
                @endif
            @elseif(now() >= $exam->start_time && now() <= $exam->end_time)
                @if($exam->isActive())
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                        @csrf
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 text-center">
                            <h3 class="text-lg font-semibold text-indigo-900 mb-2">Ready to Start?</h3>
                            <p class="text-indigo-700 mb-4">You will have {{ $exam->duration_minutes }} minutes to complete this exam.</p>
                            <button type="submit" 
                                    class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                Start Exam
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                        <p class="text-gray-700">This exam is not currently active.</p>
                    </div>
                @endif
            @elseif(now() < $exam->start_time)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Exam Not Available Yet</h3>
                    <p class="text-blue-700">This exam will be available on {{ $exam->start_time->format('d M Y, H:i') }}</p>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-red-900 mb-2">Exam Has Ended</h3>
                    <p class="text-red-700">This exam ended on {{ $exam->end_time->format('d M Y, H:i') }}</p>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('student.exams.index') }}" 
                   class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Exams
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
