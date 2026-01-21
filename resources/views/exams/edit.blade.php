@extends('layouts.app')

@section('page-title', 'Edit Exam')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Exam</h2>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('lecturer.exams.update', $exam) }}">
                @csrf
                @method('PATCH')

                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $exam->title) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <select id="subject_id" name="subject_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('subject_id') border-red-500 @enderror">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="datetime-local" id="start_time" name="start_time" 
                               value="{{ old('start_time', $exam->start_time->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('start_time') border-red-500 @enderror">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="datetime-local" id="end_time" name="end_time" 
                               value="{{ old('end_time', $exam->end_time->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('end_time') border-red-500 @enderror">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                        <input type="number" id="duration_minutes" name="duration_minutes" 
                               value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('duration_minutes') border-red-500 @enderror">
                        @error('duration_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-2">Total Marks</label>
                        <input type="number" id="total_marks" name="total_marks" 
                               value="{{ old('total_marks', $exam->total_marks) }}" min="1" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('total_marks') border-red-500 @enderror">
                        @error('total_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-8">
                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                    <textarea id="instructions" name="instructions" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('instructions') border-red-500 @enderror">{{ old('instructions', $exam->instructions) }}</textarea>
                    @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="mt-8 pt-6 border-t-2 border-gray-300">
                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('lecturer.exams.index') }}" 
                           class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                style="background-color: #4f46e5 !important; color: #ffffff !important; padding: 12px 32px !important; font-weight: 700 !important; font-size: 16px !important; border-radius: 8px !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; border: none !important; cursor: pointer !important; display: inline-flex !important; align-items: center !important; gap: 8px !important;"
                                onmouseover="this.style.backgroundColor='#4338ca'" 
                                onmouseout="this.style.backgroundColor='#4f46e5'">
                            <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span style="color: white !important; font-weight: 700 !important;">Update Exam</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        startTimeInput.addEventListener('change', function() {
            endTimeInput.min = this.value;
        });
    });
</script>
@endpush
@endsection
