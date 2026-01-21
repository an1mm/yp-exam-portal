@extends('layouts.app')

@section('page-title', 'Edit Subject')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('lecturer.subjects.index') }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Subjects
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Edit Subject</h2>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('lecturer.subjects.update', $subject) }}">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Subject Name *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $subject->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Subject Code *</label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code', $subject->code) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_ids" class="block text-sm font-medium text-gray-700 mb-2">Classes *</label>
                    <p class="text-xs text-gray-500 mb-2">Select one or more classes for this subject</p>
                    <select id="class_ids" 
                            name="class_ids[]" 
                            multiple
                            required
                            size="5"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('class_ids') border-red-500 @enderror">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ in_array($class->id, old('class_ids', $subject->classes->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $class->name }} @if($class->academic_year) ({{ $class->academic_year }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Hold Ctrl (Windows) or Cmd (Mac) to select multiple classes</p>
                    @error('class_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $subject->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 mt-6">
                    <a href="{{ route('lecturer.subjects.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            style="display: inline-block; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #ffffff; background-color: #4f46e5; border-radius: 0.5rem; border: none; cursor: pointer;"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        Update Subject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
