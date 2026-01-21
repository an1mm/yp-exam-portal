@extends('layouts.app')

@section('page-title', 'Preview Exam')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Exam Preview</h2>
                    <p class="text-sm text-gray-600 mt-1">This is how students will see this exam</p>
                </div>
                <a href="{{ route('lecturer.exams.show', $exam) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Back to Exam
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Exam Header -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $exam->title }}</h1>
                <p class="text-lg text-gray-600 mb-4">{{ $exam->subject->name }}</p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Duration:</span>
                        <span class="font-semibold text-gray-900 ml-1">{{ $exam->duration_minutes }} minutes</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Total Marks:</span>
                        <span class="font-semibold text-gray-900 ml-1">{{ $exam->total_marks }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Start Time:</span>
                        <span class="font-semibold text-gray-900 ml-1">{{ $exam->start_time->format('d M Y, H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">End Time:</span>
                        <span class="font-semibold text-gray-900 ml-1">{{ $exam->end_time->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                @if($exam->instructions)
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Instructions</h3>
                    <p class="text-sm text-blue-800 whitespace-pre-line">{{ $exam->instructions }}</p>
                </div>
                @endif
            </div>

            <!-- Questions -->
            @if($exam->questions->count() > 0)
                <div class="space-y-6">
                    @foreach($exam->questions as $index => $question)
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-start space-x-4 mb-4">
                            <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-700 rounded-full font-semibold">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="text-gray-900 font-medium mb-3">{{ $question->question_text }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst(str_replace('_', ' ', $question->question_type)) }} • {{ $question->pivot->marks ?? $question->marks }} marks
                                </span>
                            </div>
                        </div>

                        <!-- Multiple Choice Options -->
                        @if($question->question_type === 'multiple_choice' && $question->options)
                            <div class="ml-12 space-y-2">
                                @foreach($question->options as $optionIndex => $option)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <input type="radio" 
                                           name="question_{{ $question->id }}" 
                                           id="option_{{ $question->id }}_{{ $optionIndex }}"
                                           value="{{ $optionIndex + 1 }}"
                                           disabled
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <label for="option_{{ $question->id }}_{{ $optionIndex }}" class="flex-1 text-gray-700 cursor-not-allowed">
                                        {{ $option }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        @elseif($question->question_type === 'true_false')
                            <div class="ml-12 space-y-2">
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <input type="radio" name="question_{{ $question->id }}" value="true" disabled class="w-4 h-4 text-indigo-600 border-gray-300">
                                    <label class="text-gray-700 cursor-not-allowed">True</label>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <input type="radio" name="question_{{ $question->id }}" value="false" disabled class="w-4 h-4 text-indigo-600 border-gray-300">
                                    <label class="text-gray-700 cursor-not-allowed">False</label>
                                </div>
                            </div>
                        @else
                            <div class="ml-12">
                                <textarea 
                                    rows="4"
                                    disabled
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                                    placeholder="Your answer here..."></textarea>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg mb-2">No questions added yet</p>
                    <p class="text-gray-400 text-sm">Add questions to preview the exam</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
