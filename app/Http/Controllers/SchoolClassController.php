<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount(['students', 'subjects'])
            ->latest()
            ->get();

        return view('lecturer.classes.manage', compact('classes'));
    }

    public function create()
    {
        return view('lecturer.classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name',
            'description' => 'nullable|string',
            'academic_year' => 'nullable|string|max:255',
        ]);

        SchoolClass::create($validated);

        return redirect()->route('lecturer.classes.manage')
            ->with('success', 'Class created successfully.');
    }

    public function edit(SchoolClass $class)
    {
        return view('lecturer.classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name,' . $class->id,
            'description' => 'nullable|string',
            'academic_year' => 'nullable|string|max:255',
        ]);

        $class->update($validated);

        return redirect()->route('lecturer.classes.manage')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->students()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete class with enrolled students.']);
        }

        if ($class->subjects()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete class with assigned subjects.']);
        }

        $class->delete();

        return redirect()->route('lecturer.classes.manage')
            ->with('success', 'Class deleted successfully.');
    }
}
