@extends('layouts.app')

@section('page-title', 'Taking Exam')

@section('content')
@php
    $attemptStarted = $attempt->started_at !== null;
@endphp

<!-- Pre-Exam Instruction Modal (only show if timer hasn't started) -->
@if(!$attemptStarted && !($instructionShown ?? false))
<div id="instructionModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: block;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-60 backdrop-blur-md"></div>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-gradient-to-br from-white to-gray-50 px-8 py-8">
                <!-- Exam Details -->
                <div class="space-y-4 mb-8">
                    <div class="flex items-start bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-base font-semibold text-gray-900 mb-1">Duration</p>
                            <p class="text-sm text-gray-600">{{ $exam->duration_minutes }} minutes</p>
                            <p class="text-xs text-gray-500 mt-1">Timer will start after you click "Start Exam"</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-base font-semibold text-gray-900 mb-1">Total Questions</p>
                            <p class="text-sm text-gray-600">{{ $exam->questions->count() }} questions</p>
                            <p class="text-xs text-gray-500 mt-1">You can review and change answers before final submission</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-base font-semibold text-gray-900 mb-1">Total Marks</p>
                            <p class="text-sm text-gray-600">{{ $exam->total_marks }} marks</p>
                            <p class="text-xs text-gray-500 mt-1">Each question shows its marks value</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-center pt-6 border-t border-gray-200">
                    <button type="button" 
                            onclick="startExamTimer()"
                            style="background: linear-gradient(to right, #16a34a, #15803d); min-width: 60px; min-height: 60px;"
                            class="w-16 h-16 text-white font-bold rounded-full hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-110 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Enhanced Sticky Progress Bar -->
<div id="progressBar" class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-md" style="margin-left: 300px;">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="font-medium text-gray-700">Progress</span>
                    <span id="progressText" class="font-semibold text-indigo-600">0/{{ $exam->questions->count() }} Questions Answered - 0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                    <div id="progressFill" class="bg-gradient-to-r from-indigo-500 via-indigo-400 to-emerald-500 h-3 rounded-full transition-all duration-300 shadow-lg" style="width: 0%; box-shadow: 0 0 10px rgba(99, 102, 241, 0.5), 0 0 20px rgba(16, 185, 129, 0.3);"></div>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <div class="text-xs text-gray-500 mb-1">Time Remaining</div>
                <div id="timer" class="text-2xl font-bold text-indigo-600">--:--:--</div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto" id="examContent" style="margin-top: 100px; {{ !$attemptStarted && !($instructionShown ?? false) ? 'filter: blur(8px); pointer-events: none; opacity: 0.3;' : '' }}">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="px-8 py-6 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $exam->title }}</h2>
            <p class="text-sm text-gray-600">{{ $exam->subject->name }}</p>
        </div>
    </div>

    <form id="exam-form" action="{{ route('student.exams.submit', $exam) }}" method="POST">
        @csrf
        
        <div class="space-y-6 mb-6" id="questionsContainer">
            @foreach($exam->questions as $index => $question)
            <div class="question-card bg-white rounded-xl border border-gray-100 shadow-sm p-8 mb-6" data-question-id="{{ $question->id }}" data-question-index="{{ $index }}">
                <div class="flex items-start space-x-5 mb-6">
                    <span class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-indigo-600 text-white rounded-full font-bold text-lg shadow-md">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 leading-relaxed">{{ $question->question_text }}</h3>
                        <div class="flex items-center space-x-4 mb-6">
                            <span class="text-xs text-gray-500">Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                            <span class="text-xs text-gray-500">Marks: {{ $question->pivot->marks ?? $question->marks }}</span>
                        </div>

                        @if($question->question_type === 'multiple_choice')
                            <div class="space-y-3">
                                @foreach($question->options as $optionIndex => $option)
                                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer answer-option transition-all duration-200 {{ isset($answers[$question->id]) && $answers[$question->id] == ($optionIndex + 1) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-blue-50' }}" data-option="{{ $optionIndex + 1 }}">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $optionIndex + 1 }}"
                                           data-question-id="{{ $question->id }}"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] == ($optionIndex + 1) ? 'checked' : '' }}
                                           class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 answer-input">
                                    <span class="ml-4 text-gray-900 font-medium">{{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                        @elseif($question->question_type === 'true_false')
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer answer-option transition-all duration-200 {{ isset($answers[$question->id]) && $answers[$question->id] == 'true' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-blue-50' }}" data-option="true">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="true"
                                           data-question-id="{{ $question->id }}"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] == 'true' ? 'checked' : '' }}
                                           class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 answer-input">
                                    <span class="ml-4 text-gray-900 font-medium">True</span>
                                </label>
                                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer answer-option transition-all duration-200 {{ isset($answers[$question->id]) && $answers[$question->id] == 'false' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-blue-50' }}" data-option="false">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="false"
                                           data-question-id="{{ $question->id }}"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] == 'false' ? 'checked' : '' }}
                                           class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 answer-input">
                                    <span class="ml-4 text-gray-900 font-medium">False</span>
                                </label>
                            </div>
                        @elseif($question->question_type === 'short_answer' || $question->question_type === 'essay')
                            <textarea name="answers[{{ $question->id }}]" 
                                      data-question-id="{{ $question->id }}"
                                      rows="{{ $question->question_type === 'essay' ? 6 : 3 }}"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 answer-input transition-all duration-200"
                                      placeholder="Type your answer here...">{{ $answers[$question->id] ?? '' }}</textarea>
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 flex items-center justify-between pt-6 border-t border-gray-200">
                    <div id="saveStatus{{ $question->id }}" class="text-sm text-gray-500"></div>
                    <button type="button" 
                            onclick="saveAnswer({{ $question->id }}, {{ $index }})"
                            style="background: linear-gradient(to right, #4f46e5, #6366f1);"
                            class="save-next-btn px-10 py-4 text-white font-bold rounded-xl transition-all duration-200 flex items-center gap-3 shadow-lg hover:shadow-2xl transform hover:scale-110 active:scale-95">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="font-bold">Save & Next</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('student.exams.review', $exam) }}" 
                   style="background: linear-gradient(to right, #4b5563, #6b7280);"
                   class="px-10 py-4 text-white font-bold rounded-xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Review Answers
                </a>
                <button type="button" 
                        onclick="confirmSubmit()"
                        style="background: linear-gradient(to right, #16a34a, #22c55e);"
                        class="px-10 py-4 text-white font-bold rounded-xl hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Final Submit
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const examId = {{ $exam->id }};
const totalQuestions = {{ $exam->questions->count() }};
const durationMinutes = {{ $exam->duration_minutes }};
const attemptStarted = {{ $attempt->started_at ? 'true' : 'false' }};
let remainingSeconds = {{ $remainingSeconds }};
let timerInterval = null;
let timerStarted = attemptStarted;

// Load timer state from localStorage if exam already started
if (attemptStarted) {
    const savedTimer = localStorage.getItem(`exam_timer_${examId}`);
    const savedStartTime = localStorage.getItem(`exam_start_time_${examId}`);
    
    if (savedTimer && savedStartTime) {
        const startTime = parseInt(savedStartTime);
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        remainingSeconds = Math.max(0, (durationMinutes * 60) - elapsed);
    }
} else {
    // Timer hasn't started - set to full duration
    remainingSeconds = durationMinutes * 60;
}

const timerElement = document.getElementById('timer');
const progressBar = document.getElementById('progressBar');
let progressFill = null;
let progressText = null;

// Initialize progress elements after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    progressFill = document.getElementById('progressFill');
    progressText = document.getElementById('progressText');
    updateProgress();
});

// Also try to initialize immediately if elements exist
if (document.readyState === 'loading') {
    // DOM hasn't finished loading yet
} else {
    // DOM is already loaded
    progressFill = document.getElementById('progressFill');
    progressText = document.getElementById('progressText');
    updateProgress();
}

function updateTimer() {
    if (!timerElement) {
        return;
    }
    
    if (!timerStarted) {
        // Show initial duration even if timer hasn't started
        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;
        const formatted = [
            hours.toString().padStart(2, '0'),
            minutes.toString().padStart(2, '0'),
            seconds.toString().padStart(2, '0')
        ].join(':');
        timerElement.textContent = formatted;
        return;
    }

    const hours = Math.floor(remainingSeconds / 3600);
    const minutes = Math.floor((remainingSeconds % 3600) / 60);
    const seconds = remainingSeconds % 60;
    
    const formatted = [
        hours.toString().padStart(2, '0'),
        minutes.toString().padStart(2, '0'),
        seconds.toString().padStart(2, '0')
    ].join(':');
    
    timerElement.textContent = formatted;
    
    // Change color when time is running out
    if (remainingSeconds <= 300) { // 5 minutes
        timerElement.classList.remove('text-indigo-600');
        timerElement.classList.add('text-red-600');
        timerElement.classList.add('animate-pulse');
    } else {
        timerElement.classList.remove('text-red-600', 'animate-pulse');
        timerElement.classList.add('text-indigo-600');
    }
    
    if (remainingSeconds <= 0) {
        clearInterval(timerInterval);
        timerElement.textContent = '00:00:00';
        // Auto submit when time is up
        autoSubmitOnTimeUp();
        return;
    }
    
    remainingSeconds--;
    localStorage.setItem(`exam_timer_${examId}`, remainingSeconds);
}

function startTimer() {
    if (timerStarted) return;
    
    timerStarted = true;
    const startTime = Date.now();
    localStorage.setItem(`exam_start_time_${examId}`, startTime.toString());
    localStorage.setItem(`exam_timer_${examId}`, remainingSeconds.toString());
    
    // Update started_at in database
    fetch(`{{ route('student.exams.start-timer', $exam) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    }).catch(() => {
        // Silently handle error - timer will still work from localStorage
    });
    
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
}

function startExamTimer() {
    // Hide instruction modal
    const modal = document.getElementById('instructionModal');
    const examContent = document.getElementById('examContent');
    
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Show and unblur exam content
    if (examContent) {
        examContent.style.filter = 'none';
        examContent.style.pointerEvents = 'auto';
        examContent.style.opacity = '1';
    }
    
    // Start the timer
    startTimer();
    
    // Force update timer display immediately
    if (timerElement) {
        updateTimer();
    }
}

function cancelExam() {
    if (confirm('Are you sure you want to cancel? Your attempt will be saved but timer will not start.')) {
        window.location.href = '{{ route('student.exams.index') }}';
    }
}

// Initialize timer display
if (timerElement) {
    if (timerStarted) {
        // Timer already started - begin counting immediately
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    } else {
        // Timer not started yet - show initial duration
        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;
        const formatted = [
            hours.toString().padStart(2, '0'),
            minutes.toString().padStart(2, '0'),
            seconds.toString().padStart(2, '0')
        ].join(':');
        timerElement.textContent = formatted;
    }
}

function updateProgress() {
    // Check if elements exist before updating
    if (!progressFill || !progressText) {
        return;
    }
    
    let answeredCount = 0;
    document.querySelectorAll('.answer-input').forEach(input => {
        if (input.type === 'radio' && input.checked) {
            answeredCount++;
        } else if (input.type === 'textarea' && input.value.trim() !== '') {
            answeredCount++;
        }
    });
    
    const percentage = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;
    if (progressFill) {
        progressFill.style.width = percentage + '%';
    }
    if (progressText) {
        progressText.textContent = `${answeredCount}/${totalQuestions} Questions Answered - ${percentage}%`;
    }
}

function saveAnswer(questionId, questionIndex) {
    const questionCard = document.querySelector(`[data-question-id="${questionId}"]`);
    const answerInput = questionCard.querySelector(`input[name="answers[${questionId}]"]:checked, textarea[name="answers[${questionId}]"]`);
    const saveStatus = document.getElementById(`saveStatus${questionId}`);
    const saveBtn = questionCard.querySelector('.save-next-btn');
    
    if (!answerInput || (answerInput.type === 'radio' && !answerInput.checked) || (answerInput.type === 'textarea' && answerInput.value.trim() === '')) {
        saveStatus.innerHTML = '<span class="text-yellow-600">⚠️ Please select or enter an answer</span>';
        return;
    }
    
    const answerValue = answerInput.type === 'radio' ? answerInput.value : answerInput.value;
    
    // Show saving state
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span class="font-bold">Saving...</span>';
    saveBtn.style.background = 'linear-gradient(to right, #6366f1, #818cf8)';
    saveBtn.className = 'save-next-btn px-10 py-4 text-white font-bold rounded-xl transition-all duration-200 flex items-center gap-3 shadow-lg cursor-not-allowed opacity-75';
    saveStatus.innerHTML = '<span class="text-blue-600">Saving...</span>';
    
    fetch(`{{ route('student.exams.save-answer', $exam) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            question_id: questionId,
            answer: answerValue
        })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || data.message || 'Failed to save answer');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            saveStatus.innerHTML = '<span class="text-green-600 font-semibold">✓ Saved</span>';
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg><span class="font-bold">Save & Next</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>';
            saveBtn.style.background = 'linear-gradient(to right, #16a34a, #22c55e)';
            saveBtn.className = 'save-next-btn px-10 py-4 text-white font-bold rounded-xl hover:opacity-90 transition-all duration-200 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105';
            updateProgress();
            
            // Scroll to next question after a brief delay
            setTimeout(() => {
                const nextQuestion = document.querySelector(`[data-question-index="${questionIndex + 1}"]`);
                if (nextQuestion) {
                    nextQuestion.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 500);
        } else {
            throw new Error(data.error || data.message || 'Failed to save answer');
        }
    })
    .catch(error => {
        const errorMessage = error.message || 'Network error. Please check your connection and try again.';
        saveStatus.innerHTML = `<span class="text-red-600 font-semibold">✗ ${errorMessage}</span>`;
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg><span class="font-bold">Save & Next</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>';
        saveBtn.style.background = 'linear-gradient(to right, #4f46e5, #6366f1)';
        saveBtn.className = 'save-next-btn px-10 py-4 text-white font-bold rounded-xl hover:opacity-90 transition-all duration-200 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105';
    });
}

function confirmSubmit() {
    if (confirm('Final Submit? You cannot change your answers after this.')) {
        submitExam();
    }
}

function autoSubmitOnTimeUp() {
    // Show notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 z-50 bg-red-600 text-white px-8 py-4 rounded-xl shadow-2xl font-bold text-lg animate-pulse';
    notification.innerHTML = '⏰ Masa anda telah tamat! Exam akan disubmit secara automatik.';
    document.body.appendChild(notification);
    
    // Add hidden input to indicate auto-submit
    const form = document.getElementById('exam-form');
    const autoSubmitInput = document.createElement('input');
    autoSubmitInput.type = 'hidden';
    autoSubmitInput.name = 'auto_submit';
    autoSubmitInput.value = 'true';
    form.appendChild(autoSubmitInput);
    
    // Auto submit after 2 seconds
    setTimeout(() => {
        clearInterval(timerInterval);
        localStorage.removeItem(`exam_timer_${examId}`);
        localStorage.removeItem(`exam_start_time_${examId}`);
        form.submit();
    }, 2000);
}

function submitExam() {
    clearInterval(timerInterval);
    localStorage.removeItem(`exam_timer_${examId}`);
    localStorage.removeItem(`exam_start_time_${examId}`);
    document.getElementById('exam-form').submit();
}

// Auto-save on input change
document.querySelectorAll('.answer-input').forEach(input => {
    input.addEventListener('change', function() {
        updateProgress();
    });
    
    if (input.type === 'textarea') {
        input.addEventListener('input', function() {
            updateProgress();
        });
    }
});

// Initialize progress on load
window.addEventListener('load', function() {
    // Re-initialize progress elements in case they weren't found earlier
    if (!progressFill) progressFill = document.getElementById('progressFill');
    if (!progressText) progressText = document.getElementById('progressText');
    updateProgress();
    
    // Initialize timer display
    if (timerElement) {
        if (timerStarted) {
            // Timer already started - begin counting immediately
            updateTimer();
            if (!timerInterval) {
                timerInterval = setInterval(updateTimer, 1000);
            }
        } else {
            // Timer not started yet - show initial duration
            updateTimer(); // This will show the formatted time
        }
    }
});

// Prevent accidental page refresh
window.addEventListener('beforeunload', function(e) {
    if (timerStarted && remainingSeconds > 0) {
        e.preventDefault();
        e.returnValue = 'Are you sure you want to leave? Your progress may be lost.';
    }
});
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@endsection
