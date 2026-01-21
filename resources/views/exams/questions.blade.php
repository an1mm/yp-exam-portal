@extends('layouts.app')

@section('page-title', 'Add Questions')

@section('content')
<div class="max-w-4xl mx-auto" x-data="questionForm()">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
            <h2 class="text-xl font-semibold text-gray-800">Add Questions to Exam</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $exam->title }} - {{ $exam->subject->name }}</p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('lecturer.exams.questions.store', $exam) }}">
                @csrf

                <div class="space-y-6" id="questions-container">
                    <template x-for="(question, index) in questions" :key="index">
                        <div class="border-2 border-gray-200 rounded-lg p-6 bg-gray-50 hover:border-indigo-300 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800" x-text="'Question ' + (index + 1)"></h3>
                                <button type="button" 
                                        @click="removeQuestion(index)"
                                        x-show="questions.length > 1"
                                        class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>
                                    <textarea 
                                        x-model="question.question_text"
                                        :name="'questions[' + index + '][question_text]'"
                                        rows="3"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Enter your question here..."></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                                        <select 
                                            x-model="question.question_type"
                                            @change="if(question.question_type === 'multiple_choice' && (!question.options || question.options.length === 0)) { question.options = ['', '']; } else if(question.question_type !== 'multiple_choice') { question.options = []; } question.correct_answer = ''"
                                            :name="'questions[' + index + '][question_type]'"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="multiple_choice">Multiple Choice</option>
                                            <option value="true_false">True/False</option>
                                            <option value="short_answer">Short Answer</option>
                                            <option value="essay">Essay</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Marks</label>
                                        <input 
                                            type="number"
                                            x-model="question.marks"
                                            :name="'questions[' + index + '][marks]'"
                                            min="1"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <!-- Multiple Choice Options -->
                                <div x-show="question.question_type === 'multiple_choice'" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                                    <div class="space-y-2">
                                        <template x-for="(option, optIndex) in question.options" :key="optIndex">
                                            <div class="flex items-center gap-2">
                                                <span class="w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-700 rounded-lg font-medium text-sm" x-text="optIndex + 1"></span>
                                                <input 
                                                    type="text"
                                                    x-model="question.options[optIndex]"
                                                    :name="'questions[' + index + '][options][' + optIndex + ']'"
                                                    required
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                    :placeholder="'Option ' + (optIndex + 1)">
                                                <button 
                                                    type="button"
                                                    @click="removeOption(index, optIndex)"
                                                    x-show="question.options.length > 2"
                                                    class="px-3 py-2 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                                    Remove
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <button 
                                        type="button"
                                        @click="addOption(index)"
                                        class="mt-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                        + Add Option
                                    </button>

                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                        <select 
                                            x-model="question.correct_answer"
                                            :name="'questions[' + index + '][correct_answer]'"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Select correct option</option>
                                            <template x-for="(option, optIndex) in question.options" :key="optIndex">
                                                <option :value="optIndex + 1" x-text="'Option ' + (optIndex + 1)"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <!-- Text Answer (True/False, Short Answer, Essay) -->
                                <div x-show="question.question_type !== 'multiple_choice'" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                    <input 
                                        type="text"
                                        x-model="question.correct_answer"
                                        :name="'questions[' + index + '][correct_answer]'"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Enter correct answer">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex items-center justify-between pt-6 border-t-2 border-gray-300">
                    <button 
                        type="button"
                        @click="addQuestion"
                        class="px-6 py-3 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Question
                    </button>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('lecturer.exams.index') }}" 
                           class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                        <button 
                            type="submit"
                            style="background-color: #4f46e5 !important; color: #ffffff !important; padding: 12px 32px !important; font-weight: 700 !important; font-size: 16px !important; border-radius: 8px !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; border: none !important; cursor: pointer !important; display: inline-flex !important; align-items: center !important; gap: 8px !important;"
                            onmouseover="this.style.backgroundColor='#4338ca'" 
                            onmouseout="this.style.backgroundColor='#4f46e5'">
                            <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span style="color: white !important; font-weight: 700 !important;">Save Exam</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function questionForm() {
    return {
        questions: [
            {
                question_text: '',
                question_type: 'multiple_choice',
                marks: 1,
                options: ['', ''],
                correct_answer: ''
            }
        ],

        addQuestion() {
            this.questions.push({
                question_text: '',
                question_type: 'multiple_choice',
                marks: 1,
                options: ['', ''],
                correct_answer: ''
            });
        },

        removeQuestion(index) {
            if (this.questions.length > 1) {
                this.questions.splice(index, 1);
            }
        },

        addOption(questionIndex) {
            this.questions[questionIndex].options.push('');
        },

        removeOption(questionIndex, optionIndex) {
            if (this.questions[questionIndex].options.length > 2) {
                this.questions[questionIndex].options.splice(optionIndex, 1);
                if (this.questions[questionIndex].correct_answer == optionIndex + 1) {
                    this.questions[questionIndex].correct_answer = '';
                }
            }
        },

    }
}
</script>
@endpush
@endsection
