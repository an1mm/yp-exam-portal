<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SchoolClassController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

// Dashboard route (will redirect based on role)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Lecturer routes
Route::prefix('lecturer')
    ->middleware(['auth', 'lecturer'])
    ->name('lecturer.')
    ->group(function () {
        Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
        Route::get('/exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
        Route::patch('/exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('/exams/{exam}/preview', [ExamController::class, 'preview'])->name('exams.preview');
        Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('exams.results');
        Route::post('/exams/{exam}/attempts/{attempt}/answers/{answer}/grade', [ExamController::class, 'gradeAnswer'])->name('exams.answers.grade');
        Route::post('/exams/{exam}/results/publish', [ExamController::class, 'publishResults'])->name('exams.results.publish');
        Route::post('/exams/{exam}/results/unpublish', [ExamController::class, 'unpublishResults'])->name('exams.results.unpublish');
        Route::get('/exams/{exam}/questions/create', [ExamController::class, 'addQuestions'])->name('exams.questions.create');
        Route::post('/exams/{exam}/questions', [ExamController::class, 'storeQuestions'])->name('exams.questions.store');
        Route::post('/exams/{exam}/questions/attach', [ExamController::class, 'attachQuestion'])->name('exams.questions.attach');
        Route::delete('/exams/{exam}/questions/{question}', [ExamController::class, 'detachQuestion'])->name('exams.questions.detach');
        
        Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
        Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');
        Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::patch('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
        
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
        Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
        Route::patch('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
        Route::post('/subjects/{subject}/students', [SubjectController::class, 'addStudent'])->name('subjects.students.add');
        Route::delete('/subjects/{subject}/students/{student}', [SubjectController::class, 'removeStudent'])->name('subjects.students.remove');
        Route::get('/classes', [SubjectController::class, 'classes'])->name('classes.index');
        Route::get('/classes/manage', [SchoolClassController::class, 'index'])->name('classes.manage');
        Route::get('/classes/create', [SchoolClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit', [SchoolClassController::class, 'edit'])->name('classes.edit');
        Route::patch('/classes/{class}', [SchoolClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');
    });

// Student routes
Route::prefix('student')
    ->middleware(['auth', 'student'])
    ->name('student.')
    ->group(function() {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/subjects', [StudentController::class, 'subjects'])->name('subjects.index');
        Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/history', [StudentExamController::class, 'history'])->name('exams.history');
        Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
        Route::get('/exams/{exam}/take', [StudentExamController::class, 'take'])->name('exams.take');
        Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
        Route::get('/exams/{exam}/results', [StudentExamController::class, 'results'])->name('exams.results');
        Route::get('/exams/{exam}/detailed-results', [StudentExamController::class, 'detailedResults'])->name('exams.detailed-results');
    });