@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto w-full p-6">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-600 mt-1">View your exam statistics and upcoming exams.</p>
        @if(auth()->user()->schoolClass)
            <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Class: {{ auth()->user()->schoolClass->name }}
                @if(auth()->user()->schoolClass->academic_year)
                    ({{ auth()->user()->schoolClass->academic_year }})
                @endif
            </div>
        @endif
    </div>

    <!-- Quick Stats Cards -->
    <div class="flex flex-row gap-6 w-full mb-10">
        <!-- Total Attempts -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $totalAttempts ?? 0 }}</p>
                <p class="text-sm text-gray-500">Total Attempts</p>
            </div>
            <svg class="w-6 h-6 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>

        <!-- Completed -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $completedAttempts ?? 0 }}</p>
                <p class="text-sm text-gray-500">Completed</p>
            </div>
            <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <!-- Average Score -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $averageScore ?? 0 }}%</p>
                <p class="text-sm text-gray-500">Average Score</p>
            </div>
            <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
    </div>

    <!-- Active Exams & Missed Exams Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Active Exams (Priority - Currently in Progress) -->
        @if(isset($activeExams) && $activeExams->count() > 0)
            <div class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-red-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-red-800">Active Exams</h3>
                            <p class="text-xs text-red-600 mt-0.5">Take Now!</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold bg-red-600 text-white rounded-full">{{ $activeExams->count() }}</span>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($activeExams as $exam)
                            <a href="{{ route('student.exams.show', $exam->id) }}" 
                               class="block p-4 {{ isset($exam->not_started) && $exam->not_started ? 'bg-red-100 border-2 border-red-400' : 'bg-white border border-red-200' }} rounded-lg hover:border-red-500 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $exam->title }}</h4>
                                            @if(isset($exam->not_started) && $exam->not_started)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white animate-pulse">
                                                    URGENT
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $exam->subject->name }}</p>
                                        <div class="flex flex-col mt-2 text-xs text-gray-600 space-y-1">
                                            <span>Duration: {{ $exam->duration_minutes }} min</span>
                                            <span>Ends: {{ $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</span>
                                        </div>
                                        @if(isset($exam->not_started) && $exam->not_started)
                                            <p class="text-xs font-semibold text-red-700 mt-2">⚠️ Not started yet!</p>
                                        @endif
                                    </div>
                                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Missed Exams (Ended but not attempted) -->
        @if(isset($missedExams) && $missedExams->count() > 0)
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-300 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Missed Exams</h3>
                            <p class="text-xs text-gray-600 mt-0.5">Ended but not attempted</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold bg-gray-600 text-white rounded-full">{{ $missedExams->count() }}</span>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($missedExams as $exam)
                            <div class="block p-4 bg-white border-2 border-gray-300 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $exam->title }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-600 text-white">
                                                MISSED
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $exam->subject->name }}</p>
                                        <div class="flex flex-col mt-2 text-xs text-gray-600 space-y-1">
                                            <span>Duration: {{ $exam->duration_minutes }} min</span>
                                            <span class="text-red-600 font-semibold">Ended: {{ $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-700 mt-2">⚠️ Not attempted before deadline</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-12">
        <!-- Upcoming Exams -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Upcoming Exams</h3>
                <a href="{{ route('student.exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All</a>
            </div>
            <div class="p-6">
                @if($upcomingExams->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingExams as $exam)
                            <a href="{{ route('student.exams.show', $exam->id) }}" 
                               class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $exam->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $exam->subject->name }}</p>
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                            <span>Duration: {{ $exam->duration_minutes }} minutes</span>
                                            <span>•</span>
                                            <span>Starts: {{ $exam->start_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">No upcoming exams.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Recent Attempts</h3>
                <a href="{{ route('student.exams.history') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All</a>
            </div>
            <div class="p-6">
                @if($recentAttempts->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAttempts as $attempt)
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $attempt->exam->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $attempt->exam->subject->name }}</p>
                                        @if($attempt->status === 'submitted' || $attempt->status === 'graded')
                                            <div class="flex items-center space-x-4 mt-2 text-sm">
                                                <span class="font-semibold text-gray-900">{{ $attempt->total_score }} / {{ $attempt->total_marks }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $attempt->percentage >= 70 ? 'bg-green-100 text-green-800' : ($attempt->percentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ number_format($attempt->percentage, 1) }}%
                                                </span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-2">
                                                In Progress
                                            </span>
                                        @endif
                                    </div>
                                    @if($attempt->status === 'submitted' || $attempt->status === 'graded')
                                        <a href="{{ route('student.exams.results', $attempt->exam) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            View
                                        </a>
                                    @else
                                        <a href="{{ route('student.exams.take', $attempt->exam) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Continue
                                        </a>
                                    @endif
                                </div>
                                @if($attempt->submitted_at)
                                    <p class="text-xs text-gray-500 mt-2">{{ $attempt->submitted_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-4 text-gray-500">No recent attempts.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
