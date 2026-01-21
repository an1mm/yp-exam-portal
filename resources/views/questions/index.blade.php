@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>My Questions</span>
                    <a href="{{ route('lecturer.questions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create New Question
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($questions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Type</th>
                                        <th>Subject</th>
                                        <th>Marks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questions as $question)
                                    <tr>
                                        <td>{{ Str::limit($question->question_text, 100) }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                            </span>
                                        </td>
                                        <td>{{ $question->subject->name }}</td>
                                        <td>{{ $question->marks }}</td>
                                        <td>
                                            <a href="{{ route('lecturer.questions.show', $question) }}" class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('lecturer.questions.edit', $question) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('lecturer.questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No questions found. <a href="{{ route('lecturer.questions.create') }}">Create your first question</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
