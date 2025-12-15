# Database Setup Instructions

## Prerequisites
- MySQL 5.7 or higher
- PHP 7.4 or higher with mysqli extension

## Installation Steps

### 1. Create Database
Run the following script to create the database:
\`\`\`bash
mysql -u root -p < scripts/00_create_database.sql
\`\`\`

### 2. Create Tables
Run the following script to create all required tables:
\`\`\`bash
mysql -u root -p < scripts/01_create_tables.sql
\`\`\`

### 3. Insert Sample Data
Run the following script to populate the database with sample data:
\`\`\`bash
mysql -u root -p < scripts/02_seed_data.sql
\`\`\`

## Alternative: Run All Scripts at Once
\`\`\`bash
cat scripts/*.sql | mysql -u root -p
\`\`\`

## Demo Login Credentials

### Admin
- **Username:** admin
- **Password:** admin123
- **Access:** Full system management, user management, reports

### Teacher
- **Username:** teacher1 (or teacher2, teacher3)
- **Password:** teacher123
- **Access:** Assignment creation, grading, attendance marking

### Student
- **Username:** student1 (or student2, student3, student4)
- **Password:** student123
- **Access:** View assignments, submit work, check grades

### Parent
- **Username:** parent1 (or parent2)
- **Password:** parent123
- **Access:** Monitor child's progress, attendance, grades

## Database Configuration

Update the database connection settings in `config/database.php`:

\`\`\`php
$host = 'localhost';
$dbname = 'school_management';
$username = 'root';
$password = 'your_password';
\`\`\`

## Database Schema Overview

### Core Tables
- **users** - All system users (admin, teachers, students, parents)
- **classes** - Class information (grade, section, academic year)
- **subjects** - Subject catalog
- **class_subjects** - Maps subjects to classes with assigned teachers

### Academic Tables
- **enrollments** - Student-class relationships
- **assignments** - Assignment information
- **assignment_submissions** - Student submissions and grades
- **tests** - Test/exam information
- **test_results** - Student test scores

### Tracking Tables
- **attendance** - Daily attendance records
- **messages** - Internal messaging system
- **announcements** - System-wide announcements
- **parent_student** - Parent-child relationships

## Security Notes

1. All passwords are hashed using PHP's `password_hash()` with bcrypt
2. Foreign key constraints maintain referential integrity
3. Indexes are added for performance optimization
4. Unique constraints prevent duplicate data
5. ON DELETE CASCADE ensures clean data removal

## Maintenance

### Backup Database
\`\`\`bash
mysqldump -u root -p school_management > backup.sql
\`\`\`

### Restore Database
\`\`\`bash
mysql -u root -p school_management < backup.sql
\`\`\`

### Reset Database
\`\`\`bash
mysql -u root -p -e "DROP DATABASE school_management;"
cat scripts/*.sql | mysql -u root -p
