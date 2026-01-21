@extends('layouts.app')

@section('page-title', 'Exam Results')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('lecturer.exams.show', $exam) }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Exam
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Exam Results</h2>
                <p class="text-gray-600 mt-1">{{ $exam->title }} - {{ $exam->subject->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($exam->is_published)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Results Published
                    </span>
                    <form action="{{ route('lecturer.exams.results.unpublish', $exam) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Unpublish Results
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        Results Not Published
                    </span>
                    <form action="{{ route('lecturer.exams.results.publish', $exam) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200">
                            Publish Results
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500 mb-1">Total Attempts</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_attempts'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500 mb-1">Submitted</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['submitted'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500 mb-1">Average Score</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_score'], 1) }} / {{ $exam->total_marks }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500 mb-1">Average %</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_percentage'], 1) }}%</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Student Results</h3>
        </div>

        @if($attempts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attempts as $attempt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $attempt->student->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attempt->student->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-semibold">{{ $attempt->total_score }}</span> / {{ $attempt->total_marks }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attempt->percentage >= 70 ? 'bg-green-100 text-green-800' : ($attempt->percentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800' : ($attempt->status === 'submitted' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('lecturer.exams.show', ['exam' => $exam, 'attempt' => $attempt->id]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Grade & Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Results Yet</h3>
                <p class="text-gray-500 text-sm">No students have attempted this exam yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Attempt Details Modal -->
<div id="attempt-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAttemptModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Attempt Details</h3>
                    <button onclick="closeAttemptModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="attempt-details-content" class="max-h-96 overflow-y-auto">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const attemptsData = @json($attempts->map(function($attempt) {
    return [
        'id' => $attempt->id,
        'student_name' => $attempt->student->name,
        'student_email' => $attempt->student->email,
        'total_score' => $attempt->total_score,
        'total_marks' => $attempt->total_marks,
        'percentage' => number_format($attempt->percentage, 1),
        'status' => $attempt->status,
        'submitted_at' => $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : null,
        'answers' => $attempt->answers->map(function($answer) {
            return [
                'question_text' => $answer->question->question_text,
                'question_type' => $answer->question->question_type,
                'answer' => $answer->answer,
                'marks_obtained' => $answer->marks_obtained,
                'question_marks' => $answer->question->pivot->marks ?? $answer->question->marks,
                'correct_answer' => $answer->question->correct_answer,
                'options' => $answer->question->options,
            ];
        })
    ];
}));

function viewAttemptDetails(attemptId) {
    const attempt = attemptsData.find(a => a.id === attemptId);
    if (!attempt) return;

    let html = `
        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Student:</span>
                    <span class="font-semibold text-gray-900 ml-2">${attempt.student_name}</span>
                </div>
                <div>
                    <span class="text-gray-500">Email:</span>
                    <span class="text-gray-900 ml-2">${attempt.student_email}</span>
                </div>
                <div>
                    <span class="text-gray-500">Score:</span>
                    <span class="font-semibold text-gray-900 ml-2">${attempt.total_score} / ${attempt.total_marks}</span>
                </div>
                <div>
                    <span class="text-gray-500">Percentage:</span>
                    <span class="font-semibold text-gray-900 ml-2">${attempt.percentage}%</span>
                </div>
            </div>
        </div>
        <div class="space-y-4">
    `;

    attempt.answers.forEach((answer, index) => {
        const isCorrect = answer.answer == answer.correct_answer;
        html += `
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-700">Question ${index + 1}</span>
                    <span class="text-sm ${isCorrect ? 'text-green-600' : 'text-red-600'} font-medium">
                        ${answer.marks_obtained} / ${answer.question_marks} marks
                    </span>
                </div>
                <p class="text-sm text-gray-900 mb-3">${answer.question_text}</p>
                <div class="space-y-2">
                    <div>
                        <span class="text-xs text-gray-500">Student Answer:</span>
                        <p class="text-sm text-gray-900 mt-1">${answer.answer || 'No answer provided'}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Correct Answer:</span>
                        <p class="text-sm ${isCorrect ? 'text-green-600' : 'text-gray-900'} font-medium mt-1">${answer.correct_answer}</p>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    document.getElementById('attempt-details-content').innerHTML = html;
    document.getElementById('attempt-modal').classList.remove('hidden');
}

function closeAttemptModal() {
    document.getElementById('attempt-modal').classList.add('hidden');
}
</script>
@endpush
@endsection
