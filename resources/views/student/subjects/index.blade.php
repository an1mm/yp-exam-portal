@extends('layouts.app')

@section('page-title', 'My Subjects')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">My Subjects</h2>
        <p class="text-gray-600 mt-1">View all subjects for your class</p>
    </div>

    @if($class)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $class->name }}</h3>
                    @if($class->academic_year)
                        <p class="text-sm text-gray-600">{{ $class->academic_year }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total Subjects</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $subjects->count() }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($subjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($subjects as $subject)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $subject->name }}</h3>
                            @if($subject->code)
                                <p class="text-sm text-gray-500 mb-2">{{ $subject->code }}</p>
                            @endif
                        </div>
                    </div>

                    @if($subject->description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $subject->description }}</p>
                    @endif

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Lecturer: <span class="font-medium">{{ $subject->lecturer->name ?? 'N/A' }}</span></span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Exams: <span class="font-medium">{{ $subject->exams_count ?? 0 }}</span></span>
                        </div>
                    </div>

                    <a href="{{ route('student.exams.index', ['subject' => $subject->id]) }}" 
                       class="block w-full text-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        View Exams
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Subjects Found</h3>
            <p class="text-gray-500 text-sm">
                @if(!$class)
                    You are not assigned to any class yet. Please contact your administrator.
                @else
                    No subjects are available for your class yet.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
