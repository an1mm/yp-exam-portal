@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Question Details</span>
                    <div>
                        <a href="{{ route('lecturer.questions.edit', $question) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('lecturer.questions.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Subject:</strong> {{ $question->subject->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong>
                            <span class="badge bg-info">
                                {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Question:</strong>
                        <p>{{ $question->question_text }}</p>
                    </div>

                    @if($question->question_type === 'multiple_choice' && $question->options)
                    <div class="mb-3">
                        <strong>Options:</strong>
                        <ul class="list-group">
                            @foreach($question->options as $index => $option)
                            <li class="list-group-item {{ $question->correct_answer == ($index + 1) ? 'list-group-item-success' : '' }}">
                                {{ $index + 1 }}. {{ $option }}
                                @if($question->correct_answer == ($index + 1))
                                    <span class="badge bg-success float-end">Correct</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <div class="mb-3">
                        <strong>Correct Answer:</strong>
                        <p class="alert alert-success">{{ $question->correct_answer }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Marks:</strong> {{ $question->marks }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
