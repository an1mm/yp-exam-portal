@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Edit Question</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('lecturer.questions.update', $question) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" 
                                    id="subject_id" name="subject_id" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="question_type" class="form-label">Question Type</label>
                            <select class="form-select @error('question_type') is-invalid @enderror" 
                                    id="question_type" name="question_type" required>
                                <option value="">Select Type</option>
                                <option value="multiple_choice" {{ old('question_type', $question->question_type) == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="true_false" {{ old('question_type', $question->question_type) == 'true_false' ? 'selected' : '' }}>True/False</option>
                                <option value="short_answer" {{ old('question_type', $question->question_type) == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                <option value="essay" {{ old('question_type', $question->question_type) == 'essay' ? 'selected' : '' }}>Essay</option>
                            </select>
                            @error('question_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text</label>
                            <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                      id="question_text" name="question_text" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="options-container" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Options (for Multiple Choice)</label>
                                <div id="options-list">
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary" id="add-option">Add Option</button>
                                @error('options')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="correct_answer" class="form-label">Correct Answer (Select option number)</label>
                                <select class="form-select @error('correct_answer') is-invalid @enderror" 
                                        id="correct_answer" name="correct_answer">
                                    <option value="">Select correct option</option>
                                </select>
                                @error('correct_answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="answer-container" style="display: none;">
                            <div class="mb-3">
                                <label for="correct_answer_text" class="form-label">Correct Answer</label>
                                <input type="text" class="form-control @error('correct_answer') is-invalid @enderror" 
                                       id="correct_answer_text" name="correct_answer" 
                                       value="{{ old('correct_answer', $question->correct_answer) }}">
                                @error('correct_answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="marks" class="form-label">Marks</label>
                            <input type="number" class="form-control @error('marks') is-invalid @enderror" 
                                   id="marks" name="marks" value="{{ old('marks', $question->marks) }}" min="1" required>
                            @error('marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('lecturer.questions.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionType = document.getElementById('question_type');
        const optionsContainer = document.getElementById('options-container');
        const answerContainer = document.getElementById('answer-container');
        const correctAnswerSelect = document.getElementById('correct_answer');
        const correctAnswerText = document.getElementById('correct_answer_text');
        const optionsList = document.getElementById('options-list');
        const addOptionBtn = document.getElementById('add-option');
        let optionCount = 0;

        const questionData = @json($question);
        const options = questionData.options || [];

        function updateOptions() {
            correctAnswerSelect.innerHTML = '<option value="">Select correct option</option>';
            const inputs = optionsList.querySelectorAll('input[name="options[]"]');
            inputs.forEach((input, index) => {
                const option = document.createElement('option');
                option.value = index + 1;
                option.textContent = `Option ${index + 1}`;
                if (questionData.correct_answer == (index + 1).toString()) {
                    option.selected = true;
                }
                correctAnswerSelect.appendChild(option);
            });
        }

        function loadOptions() {
            if (options.length > 0) {
                options.forEach((opt, index) => {
                    optionCount++;
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2';
                    div.innerHTML = `
                        <span class="input-group-text">${optionCount}</span>
                        <input type="text" class="form-control" name="options[]" value="${opt}" placeholder="Option ${optionCount}">
                        ${optionCount > 2 ? '<button type="button" class="btn btn-outline-danger remove-option">Remove</button>' : ''}
                    `;
                    optionsList.appendChild(div);
                });
                updateOptions();
            } else {
                for (let i = 0; i < 2; i++) {
                    optionCount++;
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2';
                    div.innerHTML = `
                        <span class="input-group-text">${optionCount}</span>
                        <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount}">
                    `;
                    optionsList.appendChild(div);
                }
                updateOptions();
            }
        }

        questionType.addEventListener('change', function() {
            if (this.value === 'multiple_choice') {
                optionsContainer.style.display = 'block';
                answerContainer.style.display = 'none';
                correctAnswerText.removeAttribute('required');
                correctAnswerSelect.setAttribute('required', 'required');
                if (optionsList.children.length === 0) {
                    loadOptions();
                }
            } else {
                optionsContainer.style.display = 'none';
                answerContainer.style.display = 'block';
                correctAnswerSelect.removeAttribute('required');
                correctAnswerText.setAttribute('required', 'required');
            }
        });

        addOptionBtn.addEventListener('click', function() {
            optionCount++;
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <span class="input-group-text">${optionCount}</span>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount}">
                <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
            `;
            optionsList.appendChild(div);
            updateOptions();
        });

        optionsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option')) {
                if (optionsList.children.length > 2) {
                    e.target.closest('.input-group').remove();
                    optionCount--;
                    updateOptions();
                }
            }
        });

        if (questionType.value === 'multiple_choice') {
            loadOptions();
            questionType.dispatchEvent(new Event('change'));
        } else {
            answerContainer.style.display = 'block';
        }
    });
</script>
@endpush
@endsection
