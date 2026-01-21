<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('lecturer_id', auth()->id())
            ->with(['classes.students', 'schoolClass.students', 'exams'])
            ->withCount('exams')
            ->latest()
            ->get();

        foreach($subjects as $subject) {
            $totalStudents = 0;
            foreach($subject->classes as $class) {
                $totalStudents += $class->students->count();
            }
            $subject->student_count = $totalStudents;
        }

        return view('lecturer.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('lecturer.subjects.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code',
            'description' => 'nullable|string',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:school_classes,id',
        ]);

        $validated['lecturer_id'] = auth()->id();
        $validated['class_id'] = $validated['class_ids'][0]; // Keep first as primary for backward compatibility
        
        $classIds = $validated['class_ids'];
        unset($validated['class_ids']);

        $subject = Subject::create($validated);
        $subject->classes()->attach($classIds);

        return redirect()->route('lecturer.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject)
    {
        if ($subject->lecturer_id !== auth()->id()) {
            abort(403);
        }

        $subject->load(['classes.students', 'schoolClass.students', 'exams']);
        
        $students = collect();
        foreach($subject->classes as $class) {
            $students = $students->merge($class->students);
        }
        $students = $students->unique('id');
        
        $exams = $subject->exams;

        $classIds = $subject->classes->pluck('id');
        $availableStudents = User::where('role', 'student')
            ->where(function($query) use ($classIds) {
                $query->whereNull('class_id')
                      ->orWhereNotIn('class_id', $classIds);
            })
            ->orderBy('name')
            ->get();

        return view('lecturer.subjects.show', compact('subject', 'students', 'exams', 'availableStudents'));
    }

    public function edit(Subject $subject)
    {
        if ($subject->lecturer_id !== auth()->id()) {
            abort(403);
        }

        $classes = SchoolClass::orderBy('name')->get();
        return view('lecturer.subjects.edit', compact('subject', 'classes'));
    }

    public function update(Request $request, Subject $subject)
    {
        if ($subject->lecturer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:school_classes,id',
        ]);

        $validated['class_id'] = $validated['class_ids'][0]; // Keep first as primary for backward compatibility
        
        $classIds = $validated['class_ids'];
        unset($validated['class_ids']);

        $subject->update($validated);
        $subject->classes()->sync($classIds);

        return redirect()->route('lecturer.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->lecturer_id !== auth()->id()) {
            abort(403);
        }

        if ($subject->exams()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete subject with existing exams.']);
        }

        $subject->delete();

        return redirect()->route('lecturer.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    public function addStudent(Request $request, Subject $subject)
    {
        if ($subject->lecturer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $student = User::findOrFail($validated['student_id']);
        
        if ($student->role !== 'student') {
            return back()->withErrors(['student_id' => 'Selected user is not a student.']);
        }

        if (!$subject->classes->contains($validated['class_id'])) {
            return back()->withErrors(['class_id' => 'Selected class is not assigned to this subject.']);
        }

        $student->update(['class_id' => $validated['class_id']]);

        return back()->with('success', 'Student added to class successfully.');
    }

    public function removeStudent(Subject $subject, User $student)
    {
        if ($subject->lecturer_id !== auth()->id()) {
            abort(403);
        }

        if ($student->class_id !== $subject->class_id) {
            return back()->withErrors(['error' => 'Student is not in this class.']);
        }

        $student->update(['class_id' => null]);

        return back()->with('success', 'Student removed from class successfully.');
    }

    public function classes()
    {
        $subjects = Subject::where('lecturer_id', auth()->id())
            ->with(['classes.students', 'schoolClass.students', 'exams'])
            ->get();

        $allClassIds = $subjects->flatMap(function($subject) {
            return $subject->classes->pluck('id');
        })->unique();

        $classes = SchoolClass::whereIn('id', $allClassIds)
            ->with(['subjects' => function($query) {
                $query->where('lecturer_id', auth()->id());
            }, 'students'])
            ->get()
            ->map(function($class) {
                $classSubjects = $class->subjects->where('lecturer_id', auth()->id());
                return [
                    'class' => $class,
                    'subjects' => $classSubjects,
                    'student_count' => $class->students->count() ?? 0,
                    'exam_count' => $classSubjects->sum(function($subject) {
                        return $subject->exams->count();
                    })
                ];
            })
            ->filter(function($item) {
                return $item['subjects']->count() > 0;
            })
            ->values();

        return view('lecturer.classes.index', compact('classes'));
    }
}
