@extends('layouts.app')

@section('page-title', 'Review Answers')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Review Your Answers</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $exam->title }} - {{ $exam->subject->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Time Remaining</div>
                <div id="timer" class="text-2xl font-bold text-indigo-600">--:--:--</div>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-semibold text-blue-900">Review Summary</p>
                <p class="text-sm text-blue-700 mt-1">Click on any question number below to go back and edit your answer.</p>
            </div>
        </div>
    </div>

    <!-- Question Summary Grid -->
    <div class="grid grid-cols-5 md:grid-cols-10 gap-3 mb-6">
        @foreach($exam->questions as $index => $question)
            @php
                $isAnswered = isset($answers[$question->id]) && $answers[$question->id] !== null && $answers[$question->id] !== '';
            @endphp
            <a href="#question-{{ $question->id }}" 
               class="question-link flex items-center justify-center w-12 h-12 rounded-lg border-2 transition-all duration-200 {{ $isAnswered ? 'bg-green-100 border-green-500 text-green-700 font-semibold' : 'bg-yellow-100 border-yellow-400 text-yellow-700' }} hover:scale-110">
                {{ $index + 1 }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-center space-x-6 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-100 border-2 border-green-500 rounded mr-2"></div>
                <span class="text-gray-700">Answered</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-100 border-2 border-yellow-400 rounded mr-2"></div>
                <span class="text-gray-700">Skipped</span>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <form id="exam-form" action="{{ route('student.exams.submit', $exam) }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @foreach($exam->questions as $index => $question)
            <div id="question-{{ $question->id }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start space-x-4 mb-4">
                    <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-700 rounded-full font-semibold">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $question->question_text }}</h3>
                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                            <span>Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                            <span>Marks: {{ $question->pivot->marks ?? $question->marks }}</span>
                            @if(isset($answers[$question->id]) && $answers[$question->id] !== null && $answers[$question->id] !== '')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Answered
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ⚠ Skipped
                                </span>
                            @endif
                        </div>

                        @if($question->question_type === 'multiple_choice')
                            <div class="space-y-2">
                                @foreach($question->options as $optionIndex => $option)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer {{ isset($answers[$question->id]) && $answers[$question->id] == ($optionIndex + 1) ? 'bg-indigo-50 border-indigo-300' : '' }}">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $optionIndex + 1 }}"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] == ($optionIndex + 1) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-gray-900">{{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                        @elseif($question->question_type === 'true_false')
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer {{ isset($answers[$question->id]) && $answers[$question->id] == 'true' ? 'bg-indigo-50 border-indigo-300' : '' }}">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="true"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] == 'true' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-gray-900">True</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer {{ isset($answers[$question->id]) && $answers[$question->id] == 'false' ? 'bg-indigo-50 border-indigo-300' : '' }}">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="false"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] == 'false' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-gray-900">False</span>
                                </label>
                            </div>
                        @elseif($question->question_type === 'short_answer' || $question->question_type === 'essay')
                            <textarea name="answers[{{ $question->id }}]" 
                                      rows="{{ $question->question_type === 'essay' ? 6 : 3 }}"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Type your answer here...">{{ $answers[$question->id] ?? '' }}</textarea>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('student.exams.take', $exam) }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Exam
                </a>
                <button type="submit" 
                        onclick="return confirm('Final Submit? You cannot change your answers after this.');"
                        class="px-8 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    Final Submit Exam
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let remainingSeconds = {{ $remainingSeconds }};
const timerElement = document.getElementById('timer');

// Load timer from localStorage
const savedTimer = localStorage.getItem(`exam_timer_{{ $exam->id }}`);
if (savedTimer) {
    remainingSeconds = parseInt(savedTimer);
}

function updateTimer() {
    const hours = Math.floor(remainingSeconds / 3600);
    const minutes = Math.floor((remainingSeconds % 3600) / 60);
    const seconds = remainingSeconds % 60;
    
    const formatted = [
        hours.toString().padStart(2, '0'),
        minutes.toString().padStart(2, '0'),
        seconds.toString().padStart(2, '0')
    ].join(':');
    
    timerElement.textContent = formatted;
    
    if (remainingSeconds <= 0) {
        alert('Time is up! Your exam will be submitted automatically.');
        document.getElementById('exam-form').submit();
        return;
    }
    
    remainingSeconds--;
    localStorage.setItem(`exam_timer_{{ $exam->id }}`, remainingSeconds);
}

updateTimer();
setInterval(updateTimer, 1000);

// Smooth scroll to question when clicking number
document.querySelectorAll('.question-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
</script>
@endpush
@endsection
