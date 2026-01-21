@extends('layouts.app')

@section('page-title', 'Taking Exam')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $exam->title }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $exam->subject->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Time Remaining</div>
                <div id="timer" class="text-2xl font-bold text-indigo-600"></div>
            </div>
        </div>
    </div>

    <form id="exam-form" action="{{ route('student.exams.submit', $exam) }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @foreach($exam->questions as $index => $question)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start space-x-4 mb-4">
                    <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-700 rounded-full font-semibold">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $question->question_text }}</h3>
                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                            <span>Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                            <span>Marks: {{ $question->pivot->marks ?? $question->marks }}</span>
                        </div>

                        @if($question->question_type === 'multiple_choice')
                            <div class="space-y-2">
                                @foreach($question->options as $optionIndex => $option)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $optionIndex + 1 }}"
                                           {{ isset($answers[$question->id]) && $answers[$question->id] == ($optionIndex + 1) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-gray-900">{{ $option }}</span>
                                </label>
                                @endforeach
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

        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">Make sure you have answered all questions before submitting.</p>
                <button type="submit" 
                        onclick="return confirm('Are you sure you want to submit this exam? You cannot change your answers after submission.');"
                        class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                    Submit Exam
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let remainingSeconds = {{ $remainingSeconds }};
const timerElement = document.getElementById('timer');

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
}

updateTimer();
setInterval(updateTimer, 1000);

window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = 'Are you sure you want to leave? Your progress may be lost.';
});
</script>
@endpush
@endsection
