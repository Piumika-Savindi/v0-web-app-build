# School Management System

A comprehensive school management system built with HTML, Tailwind CSS, JavaScript, PHP, and MySQL.

## Features

### User Roles
- **Admin**: Manage users, classes, subjects, and system configuration
- **Teacher**: Create assignments, grade work, mark attendance, manage tests
- **Student**: Submit assignments, take tests, view grades and attendance
- **Parent**: Monitor children's academic progress, grades, and attendance

### Core Functionality
- **Authentication**: Secure login with password hashing and role-based access control
- **Assignment Management**: Create, submit, and grade assignments
- **Testing System**: Create MCQ and written tests with automatic/manual grading
- **Attendance Tracking**: Mark and view attendance records
- **Grade Management**: View detailed grade reports and feedback
- **Communication**: Internal messaging system between users
- **Announcements**: System-wide and role-specific announcements
- **Learning Materials**: Upload and access course materials

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server

### Setup Instructions

1. **Database Setup**
   - Create a MySQL database named `school_management`
   - Update database credentials in `config/database.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'school_management');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```

2. **Import Database Schema**
   - Run the SQL scripts in the following order:
     ```bash
     mysql -u your_username -p school_management < scripts/01_create_tables.sql
     mysql -u your_username -p school_management < scripts/02_seed_data.sql
     ```

3. **Configure Web Server**
   - Point your web server document root to the project directory
   - Ensure PHP has write permissions for the `uploads/` directory (if file uploads are needed)

4. **Access the System**
   - Navigate to `http://localhost/` (or your configured domain)
   - Use the demo credentials to log in

## Demo Credentials

| Role    | Username  | Password  |
|---------|-----------|-----------|
| Admin   | admin     | password  |
| Teacher | teacher1  | password  |
| Student | student1  | password  |
| Parent  | parent1   | password  |

## Project Structure

```
school-management/
├── api/                    # API endpoints
│   ├── admin/             # Admin-specific APIs
│   ├── announcements/     # Announcement APIs
│   ├── auth/              # Authentication APIs
│   ├── messages/          # Messaging APIs
│   ├── student/           # Student-specific APIs
│   └── teacher/           # Teacher-specific APIs
├── assets/
│   └── css/
│       └── style.css      # Global styles
├── components/            # Reusable PHP components
│   ├── header.php         # Navigation header
│   └── protect.php        # Authentication guard
├── config/                # Configuration files
│   ├── database.php       # Database connection
│   └── init.php           # Initialization script
├── pages/                 # Application pages
│   ├── admin/             # Admin panel pages
│   ├── parent/            # Parent portal pages
│   ├── student/           # Student panel pages
│   ├── teacher/           # Teacher panel pages
│   ├── announcements.php  # Announcements page
│   └── messages.php       # Messaging page
├── scripts/               # SQL scripts
│   ├── 01_create_tables.sql
│   └── 02_seed_data.sql
├── index.php              # Landing/login page
└── README.md
```

## Security Features

- Password hashing using bcrypt
- Role-based access control (RBAC)
- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- Session-based authentication
- CSRF protection ready (can be implemented)

## Technology Stack

- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Authentication**: Session-based with password hashing

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is open-source and available for educational purposes.

## Support

For issues and questions, please refer to the project documentation or contact the development team.
