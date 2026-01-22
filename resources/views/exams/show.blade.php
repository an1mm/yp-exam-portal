@extends('layouts.app')

@section('page-title', 'Exam Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Exam Details</h2>
            <div class="flex items-center space-x-2">
                @if($exam->attempts()->count() > 0)
                <a href="{{ route('lecturer.exams.results', $exam) }}" 
                   class="px-4 py-2 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    View Results ({{ $exam->attempts()->count() }})
                </a>
                @endif
                <a href="{{ route('lecturer.exams.preview', $exam) }}" 
                   target="_blank"
                   class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Preview
                </a>
                <a href="{{ route('lecturer.exams.edit', $exam) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Edit Exam
                </a>
                <a href="{{ route('lecturer.exams.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Back to List
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Exam Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Title</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->title }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Subject</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->subject->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Duration</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->duration_minutes }} minutes</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Total Marks</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->total_marks }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Start Time</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->start_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }} (Kuala Lumpur)</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">End Time</label>
                    <p class="text-gray-900 font-semibold mt-1">{{ $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }} (Kuala Lumpur)</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $exam->status === 'published' ? 'bg-green-100 text-green-800' : ($exam->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </p>
                </div>
            </div>

            @if($exam->instructions)
            <div class="mb-6">
                <label class="text-sm font-medium text-gray-500">Instructions</label>
                <p class="text-gray-900 mt-1">{{ $exam->instructions }}</p>
            </div>
            @endif

            <hr class="my-6">

            <!-- Questions Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Questions ({{ $exam->questions->count() }})</h3>
                    @if($exam->questions->count() === 0)
                        <a href="{{ route('lecturer.questions.create', ['exam_id' => $exam->id]) }}" 
                           class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                            Create Question
                        </a>
                    @endif
                </div>

                @if($exam->questions->count() > 0)
                    <div class="space-y-3 mb-6">
                        @foreach($exam->questions as $question)
                        <div class="flex items-start justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <div class="flex items-start space-x-3">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-700 rounded-full font-semibold text-sm">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-gray-900 font-medium mb-1">{{ $question->question_text }}</p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span>Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                            <span>Marks: {{ $question->pivot->marks ?? $question->marks }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('lecturer.exams.questions.detach', [$exam, $question]) }}" method="POST" 
                                  onsubmit="return confirm('Remove this question from exam?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="ml-4 px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                    Remove
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="mb-6 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-yellow-800 mb-1">No questions added yet</h4>
                                <p class="text-sm text-yellow-700 mb-3">Add questions to complete your exam. You can create new questions or select from your question bank.</p>
                                <a href="{{ route('lecturer.questions.create', ['exam_id' => $exam->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                    Create Question
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Add Questions from Question Bank -->
                @if($availableQuestions->count() > 0)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-4">Add Questions from Question Bank</h4>
                        <div class="space-y-3">
                            @foreach($availableQuestions as $question)
                            <div class="flex items-start justify-between p-4 bg-white rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <p class="text-gray-900 font-medium mb-1">{{ Str::limit($question->question_text, 150) }}</p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                        <span>Marks: {{ $question->marks }}</span>
                                    </div>
                                </div>
                                <form action="{{ route('lecturer.exams.questions.attach', $exam) }}" method="POST" class="ml-4">
                                    @csrf
                                    <input type="hidden" name="question_id" value="{{ $question->id }}">
                                    <input type="hidden" name="marks" value="{{ $question->marks }}">
                                    <button type="submit" 
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                        Add to Exam
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($exam->questions->count() === 0)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            No questions available in your question bank for this subject. 
                            <a href="{{ route('lecturer.questions.create', ['exam_id' => $exam->id]) }}" class="font-medium underline">Create questions</a> first to add them to this exam.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
