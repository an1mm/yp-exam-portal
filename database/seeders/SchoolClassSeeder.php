<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        SchoolClass::create([
            'name' => 'CS101',
            'description' => 'Computer Science Year 1',
            'academic_year' => '2024/2025',
        ]);

        SchoolClass::create([
            'name' => 'CS201',
            'description' => 'Computer Science Year 2',
            'academic_year' => '2024/2025',
        ]);

        SchoolClass::create([
            'name' => 'IT101',
            'description' => 'Information Technology Year 1',
            'academic_year' => '2024/2025',
        ]);
    }
}
