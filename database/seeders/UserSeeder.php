<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = User::firstOrCreate(
            ['email' => 'lecturer@test.com'],
            [
                'name' => 'Lecturer 1',
                'password' => Hash::make('password'),
                'role' => 'lecturer',
            ]
        );

        $cs101 = SchoolClass::where('name', 'CS101')->first();
        $it101 = SchoolClass::where('name', 'IT101')->first();

        User::firstOrCreate(
            ['email' => 'student1@test.com'],
            [
                'name' => 'Student 1',
                'password' => Hash::make('password'),
                'role' => 'student',
                'class_id' => $cs101->id ?? null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'student2@test.com'],
            [
                'name' => 'Student 2',
                'password' => Hash::make('password'),
                'role' => 'student',
                'class_id' => $cs101->id ?? null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'student3@test.com'],
            [
                'name' => 'Student 3',
                'password' => Hash::make('password'),
                'role' => 'student',
                'class_id' => $it101->id ?? null,
            ]
        );
    }
}
