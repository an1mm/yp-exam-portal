@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-600 mt-1">Manage your courses and exams from here.</p>
    </div>

    <!-- Quick Stats Cards -->
    <div class="flex flex-row gap-4 mb-6" style="display: flex; flex-direction: row;">
        <!-- Total Subjects -->
        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col">
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; line-height: 1;">{{ $totalSubjects ?? 0 }}</p>
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.125rem;">Total Subjects</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Subjects you teach</p>
                <div class="flex justify-end mt-auto">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Classes -->
        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col">
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; line-height: 1;">{{ $totalClasses ?? 0 }}</p>
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.125rem;">Total Classes</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Classes assigned</p>
                <div class="flex justify-end mt-auto">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Students -->
        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col">
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; line-height: 1;">{{ $totalStudents ?? 0 }}</p>
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.125rem;">Total Students</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Enrolled students</p>
                <div class="flex justify-end mt-auto">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Submissions -->
        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col">
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; line-height: 1;">{{ $totalSubmissions ?? 0 }}</p>
                <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.125rem;">Student Submissions</p>
                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Submitted exams</p>
                <div class="flex justify-end mt-auto">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('lecturer.exams.create') }}" 
               class="flex items-center space-x-3 p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="font-medium text-gray-900">Create Exam</span>
            </a>
            <a href="{{ route('lecturer.questions.create') }}" 
               class="flex items-center space-x-3 p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="font-medium text-gray-900">Create Question</span>
            </a>
            <a href="{{ route('lecturer.subjects.index') }}" 
               class="flex items-center space-x-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="font-medium text-gray-900">View Subjects</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Exams -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Recent Exams</h3>
                <a href="{{ route('lecturer.exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All</a>
            </div>
            @if($recentExams->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($recentExams as $exam)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <a href="{{ route('lecturer.exams.show', $exam) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                    {{ $exam->title }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">{{ $exam->subject->name }}</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $exam->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $exam->start_time->format('d M Y, H:i') }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-500 text-sm">No exams created yet.</p>
                    <a href="{{ route('lecturer.exams.create') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-700 font-medium">Create your first exam</a>
                </div>
            @endif
        </div>

        <!-- My Subjects -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">My Subjects</h3>
                <a href="{{ route('lecturer.subjects.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All</a>
            </div>
            @if($recentSubjects->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($recentSubjects as $subject)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <a href="{{ route('lecturer.subjects.show', $subject) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                    {{ $subject->name }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">{{ $subject->code }} • {{ $subject->schoolClass->name ?? 'No class' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-500 text-sm">No subjects assigned yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
