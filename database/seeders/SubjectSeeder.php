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

        Subject::firstOrCreate(
            ['code' => 'WEB101'],
            [
                'name' => 'Web Development',
                'description' => 'Introduction to web development using HTML, CSS, and JavaScript',
                'class_id' => $cs101->id ?? 1,
                'lecturer_id' => $lecturer->id,
            ]
        );

        Subject::firstOrCreate(
            ['code' => 'DB101'],
            [
                'name' => 'Database Systems',
                'description' => 'Fundamentals of database design and SQL',
                'class_id' => $cs101->id ?? 1,
                'lecturer_id' => $lecturer->id,
            ]
        );

        Subject::firstOrCreate(
            ['code' => 'SAD201'],
            [
                'name' => 'System Analysis and Design',
                'description' => 'Principles of system analysis, design methodologies, and UML',
                'class_id' => $cs201->id ?? 2,
                'lecturer_id' => $lecturer->id,
            ]
        );

        Subject::firstOrCreate(
            ['code' => 'PROG101'],
            [
                'name' => 'Programming Fundamentals',
                'description' => 'Introduction to programming concepts and logic',
                'class_id' => $it101->id ?? 3,
                'lecturer_id' => $lecturer->id,
            ]
        );
    }
}
