@extends('layouts.app')

@section('page-title', 'Create Question')

@section('content')
<div class="max-w-4xl mx-auto">
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Create New Question</h2>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('lecturer.questions.store') }}" 
                  x-data="{ questionType: '{{ old('question_type', '') }}' }">
                @csrf
                
                @if(isset($examId))
                    <input type="hidden" name="exam_id" value="{{ $examId }}">
                @endif

                <input type="hidden" name="subject_id" value="{{ $subjectId ?? ($subjects->first()->id ?? '') }}">

                <div class="mb-6">
                    <label for="question_type" class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                    <select id="question_type" name="question_type" required x-model="questionType"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('question_type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                        <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                        <option value="short_answer" {{ old('question_type') == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                        <option value="essay" {{ old('question_type') == 'essay' ? 'selected' : '' }}>Essay</option>
                    </select>
                    @error('question_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>
                    <textarea id="question_text" name="question_text" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('question_text') border-red-500 @enderror">{{ old('question_text') }}</textarea>
                    @error('question_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Multiple Choice Options -->
                <div x-show="questionType === 'multiple_choice'" x-cloak class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Options (for Multiple Choice)</label>
                    <div id="options-list" class="space-y-3 mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-700 rounded-lg font-medium text-sm">1</span>
                            <input type="text" name="options[]" placeholder="Option 1" required
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-700 rounded-lg font-medium text-sm">2</span>
                            <input type="text" name="options[]" placeholder="Option 2" required
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <button type="button" id="add-option" 
                            class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                        + Add Option
                    </button>
                    @error('options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-4">
                        <label for="correct_answer" class="block text-sm font-medium text-gray-700 mb-2">Correct Answer (Select option number)</label>
                        <select id="correct_answer" name="correct_answer" 
                                x-bind:required="questionType === 'multiple_choice'"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('correct_answer') border-red-500 @enderror">
                            <option value="">Select correct option</option>
                        </select>
                        @error('correct_answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Text Answer (for True/False, Short Answer, Essay) -->
                <div x-show="questionType !== 'multiple_choice' && questionType !== ''" x-cloak class="mb-6">
                    <label for="correct_answer_text" class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                    <input type="text" id="correct_answer_text" name="correct_answer" 
                           value="{{ old('correct_answer') }}"
                           x-bind:required="questionType !== 'multiple_choice' && questionType !== ''"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('correct_answer') border-red-500 @enderror">
                    @error('correct_answer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="marks" class="block text-sm font-medium text-gray-700 mb-2">Marks</label>
                    <input type="number" id="marks" name="marks" value="{{ old('marks', 1) }}" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('marks') border-red-500 @enderror">
                    @error('marks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    @if(isset($examId))
                        <a href="{{ route('lecturer.exams.show', $examId) }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                    @else
                        <a href="{{ route('lecturer.questions.index') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                    @endif
                    <button type="submit" 
                            style="background-color: #4f46e5 !important; color: #ffffff !important; padding: 12px 32px !important; font-weight: 700 !important; font-size: 16px !important; border-radius: 8px !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; border: none !important; cursor: pointer !important; display: inline-flex !important; align-items: center !important; gap: 8px !important; min-width: 150px !important;"
                            onmouseover="this.style.backgroundColor='#4338ca'"
                            onmouseout="this.style.backgroundColor='#4f46e5'">
                        <span style="color: white !important; font-weight: 700 !important;">Save Question</span>
                        <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionType = document.getElementById('question_type');
        const optionsList = document.getElementById('options-list');
        const correctAnswerSelect = document.getElementById('correct_answer');
        const addOptionBtn = document.getElementById('add-option');
        let optionCount = 2;

        function updateOptions() {
            correctAnswerSelect.innerHTML = '<option value="">Select correct option</option>';
            const inputs = optionsList.querySelectorAll('input[name="options[]"]');
            inputs.forEach((input, index) => {
                const option = document.createElement('option');
                option.value = index + 1;
                option.textContent = `Option ${index + 1}`;
                correctAnswerSelect.appendChild(option);
            });
        }

        addOptionBtn.addEventListener('click', function() {
            optionCount++;
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2';
            div.innerHTML = `
                <span class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-700 rounded-lg font-medium text-sm">${optionCount}</span>
                <input type="text" name="options[]" placeholder="Option ${optionCount}" required
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="button" class="px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200 remove-option">Remove</button>
            `;
            optionsList.appendChild(div);
            updateOptions();
        });

        optionsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option')) {
                if (optionsList.children.length > 2) {
                    e.target.closest('.flex').remove();
                    optionCount--;
                    updateOptions();
                }
            }
        });

        // Initialize options dropdown
        if (questionType.value === 'multiple_choice') {
            updateOptions();
        }
    });
</script>
@endpush
@endsection
