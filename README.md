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
- Manage subjects and classes
- View all exams created
- Set exam time limits and scheduling
- Create multiple question types (Multiple Choice, True/False, Short Answer, Essay)
- View student exam attempts and results

### Student Features
- View upcoming exams assigned to their class
- View exam details and instructions
- Dashboard with exam overview
- Take exams with real-time timer
- Save answers temporarily before final submission
- Review answers before submitting
- Auto-submit when time limit is reached
- View exam results and history

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

4. Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations to create database tables
```bash
php artisan migrate
```

6. Seed database with sample data
```bash
php artisan db:seed
```

**What the seeder creates:**

**School Classes (3):**
- CS101 - Computer Science Year 1
- CS201 - Computer Science Year 2
- IT101 - Information Technology Year 1

**User Accounts:**
- **Lecturer**: lecturer@test.com / password
- **Students**:
  - student1@test.com / password (assigned to CS101)
  - student2@test.com / password (assigned to CS101)
  - student3@test.com / password (assigned to IT101)

**Subjects (4):**
- Web Development (WEB101) - assigned to CS101
- Database Systems (DB101) - assigned to CS101
- System Analysis and Design (SAD201) - assigned to CS201
- Programming Fundamentals (PROG101) - assigned to IT101

**Sample Exams:**
- 4 sample exams with questions (Multiple Choice, True/False, Short Answer, Essay)
- All exams are automatically published and ready for testing
- Questions are automatically attached to their respective exams

**Note**: All sample data is generated through Laravel seeders. This ensures:
- ✅ Consistent test data for all developers
- ✅ No sensitive data in the repository
- ✅ Easy reset: `php artisan migrate:fresh --seed` to start fresh
- ✅ All exams are automatically published and ready for testing
- ✅ Questions are automatically attached to their respective exams

7. Build assets
```bash
npm run build
```

8. Start development server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Database Structure

### Main Tables
- `users` - User accounts with role-based access (lecturer/student)
- `school_classes` - Class management
- `subjects` - Subject management linked to classes
- `exams` - Exam creation and management
- `questions` - Question bank
- `exam_questions` - Pivot table linking exams and questions
- `exam_attempts` - Student exam attempts with timer tracking
- `exam_answers` - Student answers for each question in an attempt

## Login Credentials

After running the database seeder, you can use the following credentials to login:

### Lecturer Account
- **Email**: `lecturer@test.com`
- **Password**: `password`

### Student Accounts
- **Student 1** (CS101 Class):
  - Email: `student1@test.com`
  - Password: `password`
  
- **Student 2** (CS101 Class):
  - Email: `student2@test.com`
  - Password: `password`
  
- **Student 3** (IT101 Class):
  - Email: `student3@test.com`
  - Password: `password`

**Note**: Student 1 has special access and can view all exams regardless of class assignment.

## Usage

### Lecturer Account
1. Login as lecturer using the credentials above
2. Access lecturer dashboard
3. Create exams for assigned subjects
4. Manage exam details and questions
5. View exam statistics and results

### Student Account
1. Login as student using any of the student credentials above
2. View upcoming exams for your class
3. Access exam details and instructions
4. Take exams with timer and auto-submit functionality
5. View exam results and history

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
