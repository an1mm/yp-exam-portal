@extends('layouts.app')

@section('page-title', $subject->name)

@section('content')
<div class="max-w-7xl mx-auto">
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

    <div class="mb-6">
        <a href="{{ route('lecturer.subjects.index') }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Subjects
        </a>
        <h2 class="text-2xl font-bold text-gray-900">{{ $subject->name }}</h2>
        <p class="text-gray-600 mt-1">{{ $subject->code }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subject Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Class Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Classes</h3>
                <div class="space-y-3">
                    @if($subject->classes->count() > 0)
                        @foreach($subject->classes as $class)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-900 font-semibold">{{ $class->name }}</p>
                                    @if($class->academic_year)
                                        <p class="text-sm text-gray-500">{{ $class->academic_year }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">{{ $class->students->count() }} students</span>
                            </div>
                        </div>
                        @endforeach
                    @elseif($subject->schoolClass)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-900 font-semibold">{{ $subject->schoolClass->name }}</p>
                                    @if($subject->schoolClass->academic_year)
                                        <p class="text-sm text-gray-500">{{ $subject->schoolClass->academic_year }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">{{ $subject->schoolClass->students->count() }} students</span>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No classes assigned</p>
                    @endif
                    @if($subject->description)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Description</label>
                        <p class="text-gray-900">{{ $subject->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Students ({{ $students->count() }})</h3>
                    @if($availableStudents->count() > 0)
                        <button onclick="document.getElementById('add-student-modal').classList.remove('hidden')"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Student
                        </button>
                    @endif
                </div>
                @if($students->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($students as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $student->email }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        @if($student->schoolClass)
                                            {{ $student->schoolClass->name }}
                                        @else
                                            <span class="text-gray-400">No class</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <form action="{{ route('lecturer.subjects.students.remove', [$subject, $student]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Remove {{ $student->name }} from this class?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-700 font-medium">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm mb-4">No students enrolled in this class.</p>
                        @if($availableStudents->count() > 0)
                            <button onclick="document.getElementById('add-student-modal').classList.remove('hidden')"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                Add First Student
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Add Student Modal -->
            @if($availableStudents->count() > 0)
            <div id="add-student-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('add-student-modal').classList.add('hidden')"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form method="POST" action="{{ route('lecturer.subjects.students.add', $subject) }}">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Student to Class</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Select Class *</label>
                                        <select id="class_id" name="class_id" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Choose a class...</option>
                                            @foreach($subject->classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }} @if($class->academic_year) ({{ $class->academic_year }}) @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Select Student *</label>
                                        <select id="student_id" name="student_id" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Choose a student...</option>
                                            @foreach($availableStudents as $student)
                                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" 
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Add Student
                                </button>
                                <button type="button" 
                                        onclick="document.getElementById('add-student-modal').classList.add('hidden')"
                                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $students->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Exams</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $exams->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Exams -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Exams</h3>
                    <a href="{{ route('lecturer.exams.create') }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Create Exam
                    </a>
                </div>
                @if($exams->count() > 0)
                    <div class="space-y-3">
                        @foreach($exams->take(5) as $exam)
                        <a href="{{ route('lecturer.exams.show', $exam) }}" 
                           class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <p class="text-sm font-medium text-gray-900">{{ $exam->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $exam->start_time->format('d M Y, H:i') }}</p>
                        </a>
                        @endforeach
                        @if($exams->count() > 5)
                        <a href="{{ route('lecturer.exams.index') }}" 
                           class="block text-center text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            View All Exams
                        </a>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-sm mb-3">No exams created yet.</p>
                    <a href="{{ route('lecturer.exams.create') }}" 
                       class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        Create First Exam
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
