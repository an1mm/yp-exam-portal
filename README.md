# Exam Portal - Online Examination and Student Management System

A comprehensive Laravel 11 application for managing online examinations and student management with role-based access control.

## Features

### Core Features
- **Role-Based Access Control**: Two main roles - Lecturer and Student
- **Authentication**: Secure login system with role-based redirection
- **Exam Management**: Lecturers can create and manage exams with multiple-choice and open-text questions
- **Class Management**: Students are organized into classes
- **Subject Management**: Each class can have multiple subjects
- **Access Control**: Students can only access exams assigned to their class
- **Time Management**: Exams have configurable time limits and scheduling

### Lecturer Features
- Create, edit, and delete exams
- View exam statistics and dashboard
- Manage subjects
- View all exams created

### Student Features
- View upcoming exams assigned to their class
- View exam details and instructions
- Dashboard with exam overview

## Technical Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Breeze
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL
- **PHP Version**: 8.2+

## Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/yp-exam-portal.git
cd yp-exam-portal
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Update `.env` with your database credentials

5. Run migrations
```bash
php artisan migrate
```

6. Seed database (optional)
```bash
php artisan db:seed
```

7. Build assets
```bash
npm run build
```

8. Start development server
```bash
php artisan serve
```

## Database Structure

### Main Tables
- `users` - User accounts with role-based access
- `school_classes` - Class management
- `subjects` - Subject management linked to classes
- `exams` - Exam creation and management
- `questions` - Question bank
- `exam_questions` - Pivot table linking exams and questions

## Usage

### Lecturer Account
1. Login as lecturer
2. Access lecturer dashboard
3. Create exams for assigned subjects
4. Manage exam details and questions

### Student Account
1. Login as student
2. View upcoming exams for your class
3. Access exam details and instructions

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   ├── ExamController.php
│   │   ├── LecturerController.php
│   │   ├── StudentController.php
│   │   └── StudentExamController.php
│   └── Middleware/
│       ├── LecturerMiddleware.php
│       └── StudentMiddleware.php
├── Models/
│   ├── Exam.php
│   ├── Question.php
│   ├── SchoolClass.php
│   ├── Subject.php
│   └── User.php
database/
└── migrations/
resources/
└── views/
    ├── auth/
    ├── exams/
    ├── lecturer/
    └── student/
routes/
└── web.php
```

## Security

- Role-based middleware protection
- CSRF protection enabled
- Password hashing
- Authorization checks on all sensitive operations

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
