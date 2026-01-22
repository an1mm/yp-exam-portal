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
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->start_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }} (Kuala Lumpur)</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">End Time</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }} (Kuala Lumpur)</p>
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

            @php
                // Use Kuala Lumpur timezone for all comparisons
                $now = now()->setTimezone('Asia/Kuala_Lumpur');
                $startTime = $exam->start_time->setTimezone('Asia/Kuala_Lumpur');
                $endTime = $exam->end_time->setTimezone('Asia/Kuala_Lumpur');
                $isActive = $exam->status === 'published' && $startTime <= $now && $endTime >= $now;
                $hasStarted = $startTime <= $now;
                $hasEnded = $endTime < $now;
            @endphp

            @php
                $canTakeExam = $exam->status === 'published' && $startTime <= $now && $endTime >= $now;
                $hasInProgressAttempt = $existingAttempt && $existingAttempt->status === 'in_progress';
                $hasSubmittedAttempt = $existingAttempt && ($existingAttempt->status === 'submitted' || $existingAttempt->status === 'graded');
            @endphp

            @if($hasSubmittedAttempt)
                <!-- Exam Already Submitted - Show Results Only -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center mb-6">
                    <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Exam Submitted</h3>
                    <p class="text-blue-700 mb-4">You have already submitted this exam. You cannot retake it.</p>
                    <div class="text-sm text-blue-600 mb-4">
                        <p>Score: <span class="font-semibold">{{ $existingAttempt->total_score }} / {{ $existingAttempt->total_marks }}</span></p>
                        <p>Percentage: <span class="font-semibold">{{ number_format($existingAttempt->percentage, 1) }}%</span></p>
                        <p class="mt-2">Submitted at: {{ $existingAttempt->submitted_at->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</p>
                    </div>
                    <a href="{{ route('student.exams.results', $exam) }}" 
                       style="background: linear-gradient(to right, #4f46e5, #6366f1);"
                       class="inline-flex items-center px-10 py-4 text-white font-bold text-lg rounded-xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        View Results
                    </a>
                </div>
            @elseif($hasInProgressAttempt)
                <!-- Exam In Progress - Show Continue Button Only -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center mb-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">Exam In Progress</h3>
                    <p class="text-yellow-700 mb-4">You have an ongoing attempt for this exam.</p>
                    <a href="{{ route('student.exams.take', $exam) }}" 
                       style="background: linear-gradient(to right, #4f46e5, #6366f1);"
                       class="inline-flex items-center px-10 py-4 text-white font-bold text-lg rounded-xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        Continue Taking Exam
                    </a>
                </div>
            @elseif($canTakeExam)
                <!-- No Attempt Yet - Show Start Button Only -->
                <div class="mb-6">
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                        @csrf
                        <div class="text-center">
                            <button type="submit" 
                                    style="background: linear-gradient(to right, #16a34a, #22c55e);"
                                    class="px-10 py-4 text-white font-bold text-lg rounded-xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                Start Taking Exam
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Exam Not Available - Show Disabled Button with Warning -->
                <div class="mb-6">
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                        @csrf
                        <div class="text-center">
                            @if(!$hasStarted)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <p class="text-yellow-800 font-semibold mb-2">⚠️ Exam Not Available Yet</p>
                                    <p class="text-yellow-700 text-sm">You can take this exam starting from <strong>{{ $startTime->format('d M Y, H:i') }} (Kuala Lumpur)</strong></p>
                                    <p class="text-yellow-600 text-xs mt-1">Current Time: {{ $now->format('d M Y, H:i:s') }}</p>
                                </div>
                            @elseif($hasEnded)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                    <p class="text-red-800 font-semibold mb-2">❌ Exam Has Ended</p>
                                    <p class="text-red-700 text-sm">This exam ended on <strong>{{ $endTime->format('d M Y, H:i') }} (Kuala Lumpur)</strong></p>
                                </div>
                            @endif
                            <button type="submit" 
                                    disabled
                                    style="background: linear-gradient(to right, #9ca3af, #6b7280);"
                                    class="px-10 py-4 text-white font-bold text-lg rounded-xl cursor-not-allowed opacity-50 transition-all duration-200 shadow-lg">
                                Start Taking Exam
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('student.exams.index') }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Exams
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
