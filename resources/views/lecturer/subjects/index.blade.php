@extends('layouts.app')

@section('page-title', 'My Subjects')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">My Subjects</h2>
        <p class="text-gray-600 mt-1">Manage subjects and classes you teach</p>
    </div>

    @if($subjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($subjects as $subject)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $subject->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $subject->code }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded">
                            {{ $subject->exams_count }} {{ Str::plural('exam', $subject->exams_count) }}
                        </span>
                    </div>

                    @if($subject->description)
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $subject->description }}</p>
                    @endif

                    <div class="space-y-2 mb-4">
                        <div class="flex items-start text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div>
                                @if($subject->classes->count() > 0)
                                    @foreach($subject->classes as $class)
                                        <span class="inline-block mb-1">{{ $class->name }}</span>@if(!$loop->last), @endif
                                    @endforeach
                                @elseif($subject->schoolClass)
                                    <span>{{ $subject->schoolClass->name }}</span>
                                @else
                                    <span class="text-gray-400">No classes assigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>{{ $subject->student_count ?? 0 }} {{ Str::plural('student', $subject->student_count ?? 0) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('lecturer.subjects.show', $subject) }}" 
                           class="flex-1 text-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                            View Details
                        </a>
                        <a href="{{ route('lecturer.subjects.edit', $subject) }}" 
                           class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            Edit
                        </a>
                        <form action="{{ route('lecturer.subjects.destroy', $subject) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this subject?');"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-3 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                Delete
                            </button>
                        </form>
                    </div>
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
            <p class="text-gray-600 mb-4">Get started by creating your first subject.</p>
            <a href="{{ route('lecturer.subjects.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                Create Subject
            </a>
        </div>
    @endif
</div>
@endsection
