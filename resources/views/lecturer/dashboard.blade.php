@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto w-full p-6">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-600 mt-1">Manage your courses and exams from here.</p>
    </div>

    <!-- Quick Stats Cards -->
    <div class="flex flex-row gap-6 w-full mb-10">
        <!-- Total Subjects -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $totalSubjects ?? 0 }}</p>
                <p class="text-sm text-gray-500">Total Subjects</p>
            </div>
            <svg class="w-6 h-6 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>

        <!-- Total Classes -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $totalClasses ?? 0 }}</p>
                <p class="text-sm text-gray-500">Total Classes</p>
            </div>
            <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>

        <!-- Total Students -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $totalStudents ?? 0 }}</p>
                <p class="text-sm text-gray-500">Total Students</p>
            </div>
            <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>

        <!-- Student Submissions -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $totalSubmissions ?? 0 }}</p>
                <p class="text-sm text-gray-500">Student Submissions</p>
            </div>
            <svg class="w-6 h-6 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
    </div>


    <!-- Active Exams & Missed Exams Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Active Exams (Priority - Currently in Progress) -->
        @if(isset($activeExamsList) && $activeExamsList->count() > 0)
            <div class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-red-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-base font-semibold text-red-800">Active Exams</h3>
                            <p class="text-xs text-red-600 mt-0.5">Currently in Progress</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold bg-red-600 text-white rounded-full">{{ $activeExamsList->count() }}</span>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($activeExamsList as $exam)
                            <a href="{{ route('lecturer.exams.show', $exam->id) }}" 
                               class="block p-4 bg-white border-2 border-red-300 rounded-lg hover:border-red-500 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-base font-semibold text-gray-900">{{ $exam->title }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">{{ $exam->subject->name }}</p>
                                        <div class="flex flex-col mt-2 text-xs text-gray-600 space-y-1">
                                            <span>Duration: {{ $exam->duration_minutes }} min</span>
                                            <span>Ends: {{ $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="font-semibold text-red-600">{{ $exam->attempts->where('status', '!=', 'in_progress')->count() }} submissions</span>
                                                @if(isset($exam->students_not_attempted) && $exam->students_not_attempted->count() > 0)
                                                    <span>•</span>
                                                    <span class="font-semibold text-orange-600">{{ $exam->students_not_attempted->count() }} belum jawab</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(isset($exam->students_not_attempted) && $exam->students_not_attempted->count() > 0)
                                            <div class="mt-2 p-2 bg-orange-50 border border-orange-200 rounded-lg">
                                                <p class="text-xs font-semibold text-orange-800 mb-1">⚠️ Belum attempt:</p>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($exam->students_not_attempted->take(3) as $student)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                            {{ $student->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($exam->students_not_attempted->count() > 3)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-200 text-orange-900">
                                                            +{{ $exam->students_not_attempted->count() - 3 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
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

        <!-- Missed Exams (Ended but Students Haven't Attempted) -->
        @if(isset($missedExamsList) && $missedExamsList->count() > 0)
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-300 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">Missed Exams</h3>
                            <p class="text-xs text-gray-600 mt-0.5">Ended but not attempted</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold bg-gray-600 text-white rounded-full">{{ $missedExamsList->count() }}</span>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($missedExamsList as $exam)
                            <div class="block p-4 bg-white border-2 border-gray-300 rounded-lg">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h4 class="text-base font-semibold text-gray-900">{{ $exam->title }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">{{ $exam->subject->name }}</p>
                                        <div class="flex flex-col mt-2 text-xs text-gray-600 space-y-1">
                                            <span class="text-red-600 font-semibold">Ended: {{ $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</span>
                                            <span class="font-semibold text-red-600">{{ $exam->students_missed->count() }} students missed</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('lecturer.exams.results', $exam->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex-shrink-0">
                                        View →
                                    </a>
                                </div>
                                @if($exam->students_missed->count() > 0)
                                    <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-xs font-semibold text-red-800 mb-1">⚠️ Students yang miss:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($exam->students_missed->take(4) as $student)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    {{ $student->name }}
                                                    @if($student->schoolClass)
                                                        <span class="ml-1 text-red-600">({{ $student->schoolClass->name }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                            @if($exam->students_missed->count() > 4)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-200 text-red-900">
                                                    +{{ $exam->students_missed->count() - 4 }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-12">
        <!-- Recent Exams -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
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
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $exam->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $exam->start_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</p>
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
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
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
                                <p class="text-xs text-gray-500 mt-1">{{ $subject->code }} - {{ $subject->classes->count() > 0 ? $subject->classes->pluck('name')->join(', ') : ($subject->schoolClass->name ?? 'No class') }}</p>
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
