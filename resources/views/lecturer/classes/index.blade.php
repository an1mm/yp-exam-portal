@extends('layouts.app')

@section('page-title', 'My Classes')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">My Classes</h2>
        <p class="text-gray-600 mt-1">Manage classes and students for your subjects</p>
    </div>

    @if($classes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $classData)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $classData['class']->name }}</h3>
                        @if($classData['class']->academic_year)
                            <p class="text-sm text-gray-500">{{ $classData['class']->academic_year }}</p>
                        @endif
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center">
                            <img src="{{ asset('images/class.jpg') }}" alt="Class" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                @if($classData['class']->description)
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($classData['class']->description, 100) }}</p>
                @endif

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500">Students</p>
                        <p class="text-xl font-bold text-gray-900">{{ $classData['student_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Subjects</p>
                        <p class="text-xl font-bold text-gray-900">{{ $classData['subjects']->count() }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-500 mb-2">Subjects Taught:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($classData['subjects'] as $subject)
                            <a href="{{ route('lecturer.subjects.show', $subject) }}" 
                               class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                {{ $subject->code }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <span class="text-xs text-gray-500">{{ $classData['exam_count'] }} exams</span>
                    <a href="{{ route('lecturer.subjects.show', $classData['subjects']->first()) }}" 
                       class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        View Details →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Classes Assigned</h3>
            <p class="text-gray-500 text-sm mb-4">You don't have any classes assigned yet.</p>
            <a href="{{ route('lecturer.subjects.index') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                View My Subjects
            </a>
        </div>
    @endif
</div>
@endsection
