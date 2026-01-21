@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h3>
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

    <!-- Stats Cards -->
    <div class="flex flex-row gap-4 mb-6" style="display: flex; flex-direction: row;">
        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col">
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; line-height: 1;">{{ $totalAttempts ?? 0 }}</p>
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.125rem;">Total Attempts</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">All exam attempts</p>
                <div class="flex justify-end mt-auto">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col">
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; line-height: 1;">{{ $completedAttempts ?? 0 }}</p>
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.125rem;">Completed</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Submitted exams</p>
                <div class="flex justify-end mt-auto">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col">
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; line-height: 1;">{{ $averageScore ?? 0 }}%</p>
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.125rem;">Average Score</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Overall performance</p>
                <div class="flex justify-end mt-auto">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Exams -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Upcoming Exams</h3>
                <a href="{{ route('student.exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
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
                                            <span>{{ $exam->start_time->diffForHumans() }}</span>
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Recent Attempts</h3>
                <a href="{{ route('student.exams.history') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
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
