<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = User::where('email', 'lecturer@test.com')->first();
        
        if (!$lecturer) {
            $this->command->warn('Lecturer not found. Please run UserSeeder first.');
            return;
        }

        $subjects = Subject::where('lecturer_id', $lecturer->id)->get();
        
        if ($subjects->isEmpty()) {
            $this->command->warn('No subjects found. Please run SubjectSeeder first.');
            return;
        }

        // Create multiple exams for testing
        $exams = [
            [
                'title' => 'Web Development Midterm Exam',
                'subject' => 'WEB101',
                'duration_minutes' => 60,
                'start_time' => Carbon::now()->addDays(1)->setTime(9, 0), // Tomorrow 9 AM
                'end_time' => Carbon::now()->addDays(1)->setTime(10, 0), // Tomorrow 10 AM
                'questions' => [
                    [
                        'question_text' => 'What does HTML stand for?',
                        'question_type' => 'multiple_choice',
                        'marks' => 5,
                        'options' => [
                            'HyperText Markup Language',
                            'High Tech Modern Language',
                            'Home Tool Markup Language',
                            'Hyperlink and Text Markup Language'
                        ],
                        'correct_answer' => 'HyperText Markup Language'
                    ],
                    [
                        'question_text' => 'Which CSS property is used to change the text color?',
                        'question_type' => 'multiple_choice',
                        'marks' => 5,
                        'options' => [
                            'text-color',
                            'color',
                            'font-color',
                            'text-style'
                        ],
                        'correct_answer' => 'color'
                    ],
                    [
                        'question_text' => 'JavaScript is a compiled language.',
                        'question_type' => 'true_false',
                        'marks' => 5,
                        'correct_answer' => 'false'
                    ],
                    [
                        'question_text' => 'Explain the difference between let, const, and var in JavaScript.',
                        'question_type' => 'short_answer',
                        'marks' => 10,
                        'correct_answer' => 'let and const are block-scoped, var is function-scoped. const cannot be reassigned.'
                    ]
                ]
            ],
            [
                'title' => 'Database Systems Quiz',
                'subject' => 'DB101',
                'duration_minutes' => 30,
                'start_time' => Carbon::now()->addDays(2)->setTime(14, 0), // Day after tomorrow 2 PM
                'end_time' => Carbon::now()->addDays(2)->setTime(14, 30),
                'questions' => [
                    [
                        'question_text' => 'What does SQL stand for?',
                        'question_type' => 'multiple_choice',
                        'marks' => 5,
                        'options' => [
                            'Structured Query Language',
                            'Simple Query Language',
                            'Standard Query Language',
                            'Sequential Query Language'
                        ],
                        'correct_answer' => 'Structured Query Language'
                    ],
                    [
                        'question_text' => 'Which SQL command is used to retrieve data from a database?',
                        'question_type' => 'multiple_choice',
                        'marks' => 5,
                        'options' => [
                            'GET',
                            'SELECT',
                            'RETRIEVE',
                            'FETCH'
                        ],
                        'correct_answer' => 'SELECT'
                    ],
                    [
                        'question_text' => 'A primary key can have NULL values.',
                        'question_type' => 'true_false',
                        'marks' => 5,
                        'correct_answer' => 'false'
                    ]
                ]
            ],
            [
                'title' => 'Programming Fundamentals Test',
                'subject' => 'PROG101',
                'duration_minutes' => 45,
                'start_time' => Carbon::now()->addHours(2), // 2 hours from now
                'end_time' => Carbon::now()->addHours(2)->addMinutes(45),
                'questions' => [
                    [
                        'question_text' => 'What is the output of: print(2 + 3 * 4)?',
                        'question_type' => 'multiple_choice',
                        'marks' => 5,
                        'options' => [
                            '20',
                            '14',
                            '24',
                            '11'
                        ],
                        'correct_answer' => '14'
                    ],
                    [
                        'question_text' => 'A variable can store multiple values of different types.',
                        'question_type' => 'true_false',
                        'marks' => 5,
                        'correct_answer' => 'true'
                    ],
                    [
                        'question_text' => 'Explain what a loop is in programming.',
                        'question_type' => 'short_answer',
                        'marks' => 10,
                        'correct_answer' => 'A loop is a control structure that repeats a block of code until a condition is met.'
                    ]
                ]
            ],
            [
                'title' => 'System Analysis and Design Assignment',
                'subject' => 'SAD201',
                'duration_minutes' => 90,
                'start_time' => Carbon::now()->addDays(3)->setTime(10, 0),
                'end_time' => Carbon::now()->addDays(3)->setTime(11, 30),
                'questions' => [
                    [
                        'question_text' => 'What does UML stand for?',
                        'question_type' => 'multiple_choice',
                        'marks' => 5,
                        'options' => [
                            'Unified Modeling Language',
                            'Universal Modeling Language',
                            'Unified Markup Language',
                            'Universal Markup Language'
                        ],
                        'correct_answer' => 'Unified Modeling Language'
                    ],
                    [
                        'question_text' => 'Describe the purpose of a use case diagram in system analysis.',
                        'question_type' => 'essay',
                        'marks' => 15,
                        'correct_answer' => 'A use case diagram shows the interactions between actors and the system, representing functional requirements.'
                    ]
                ]
            ]
        ];

        foreach ($exams as $examData) {
            $subject = $subjects->where('code', $examData['subject'])->first();
            
            if (!$subject) {
                continue;
            }

            // Calculate total marks
            $totalMarks = array_sum(array_column($examData['questions'], 'marks'));

            // Create exam
            $exam = Exam::firstOrCreate(
                [
                    'title' => $examData['title'],
                    'subject_id' => $subject->id,
                ],
                [
                    'description' => 'Test exam for ' . $subject->name,
                    'duration_minutes' => $examData['duration_minutes'],
                    'start_time' => $examData['start_time'],
                    'end_time' => $examData['end_time'],
                    'total_marks' => $totalMarks,
                    'passing_marks' => (int)($totalMarks * 0.5), // 50% passing marks
                    'status' => Exam::STATUS_PUBLISHED,
                    'created_by' => $lecturer->id,
                    'is_published' => true,
                    'instructions' => 'Please read all questions carefully. Good luck!'
                ]
            );

            // Create questions for this exam
            foreach ($examData['questions'] as $questionData) {
                $options = null;
                if ($questionData['question_type'] === 'multiple_choice' && isset($questionData['options'])) {
                    $options = array_values(array_filter($questionData['options']));
                }

                $question = Question::firstOrCreate(
                    [
                        'question_text' => $questionData['question_text'],
                        'subject_id' => $subject->id,
                    ],
                    [
                        'question_type' => $questionData['question_type'],
                        'marks' => $questionData['marks'],
                        'options' => $options,
                        'correct_answer' => $questionData['correct_answer'],
                        'created_by' => $lecturer->id,
                    ]
                );

                // Attach question to exam if not already attached
                if (!$exam->questions()->where('question_id', $question->id)->exists()) {
                    $exam->questions()->attach($question->id, ['marks' => $questionData['marks']]);
                }
            }

            // Update exam total marks
            $actualTotalMarks = $exam->questions()->sum('exam_questions.marks');
            $exam->update(['total_marks' => $actualTotalMarks]);

            $this->command->info("Created exam: {$exam->title} with " . count($examData['questions']) . " questions");
        }

        $this->command->info('Exam seeding completed!');
    }
}
