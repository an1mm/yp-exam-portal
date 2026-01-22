<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\User;
use App\Models\SchoolClass;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = User::where('email', 'lecturer@test.com')->first();
        
        if (!$lecturer) {
            return;
        }

        $cs101 = SchoolClass::where('name', 'CS101')->first();
        $cs201 = SchoolClass::where('name', 'CS201')->first();
        $it101 = SchoolClass::where('name', 'IT101')->first();

        $web101 = Subject::firstOrCreate(
            ['code' => 'WEB101'],
            [
                'name' => 'Web Development',
                'description' => 'Introduction to web development using HTML, CSS, and JavaScript',
                'class_id' => $cs101->id ?? 1,
                'lecturer_id' => $lecturer->id,
            ]
        );
        // Attach to class via pivot table
        if ($cs101 && !$web101->classes()->where('school_classes.id', $cs101->id)->exists()) {
            $web101->classes()->attach($cs101->id);
        }

        $db101 = Subject::firstOrCreate(
            ['code' => 'DB101'],
            [
                'name' => 'Database Systems',
                'description' => 'Fundamentals of database design and SQL',
                'class_id' => $cs101->id ?? 1,
                'lecturer_id' => $lecturer->id,
            ]
        );
        // Attach to class via pivot table
        if ($cs101 && !$db101->classes()->where('school_classes.id', $cs101->id)->exists()) {
            $db101->classes()->attach($cs101->id);
        }

        $sad201 = Subject::firstOrCreate(
            ['code' => 'SAD201'],
            [
                'name' => 'System Analysis and Design',
                'description' => 'Principles of system analysis, design methodologies, and UML',
                'class_id' => $cs201->id ?? 2,
                'lecturer_id' => $lecturer->id,
            ]
        );
        // Attach to class via pivot table
        if ($cs201 && !$sad201->classes()->where('school_classes.id', $cs201->id)->exists()) {
            $sad201->classes()->attach($cs201->id);
        }

        $prog101 = Subject::firstOrCreate(
            ['code' => 'PROG101'],
            [
                'name' => 'Programming Fundamentals',
                'description' => 'Introduction to programming concepts and logic',
                'class_id' => $it101->id ?? 3,
                'lecturer_id' => $lecturer->id,
            ]
        );
        // Attach to class via pivot table
        if ($it101 && !$prog101->classes()->where('school_classes.id', $it101->id)->exists()) {
            $prog101->classes()->attach($it101->id);
        }
    }
}
