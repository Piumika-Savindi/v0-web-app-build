# School Management System - Setup Instructions

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server with PHP support
- Web browser

## Installation Steps

### 1. Database Setup

First, you need to create the database and tables. Follow these steps in order:

#### Option A: Using MySQL Command Line
\`\`\`bash
# Login to MySQL
mysql -u root -p

# Run the scripts in order
source scripts/00_create_database.sql
source scripts/01_create_tables.sql
source scripts/02_seed_data.sql
\`\`\`

#### Option B: Using phpMyAdmin
1. Open phpMyAdmin in your browser
2. Click on "SQL" tab
3. Copy and paste the contents of `scripts/00_create_database.sql` and click "Go"
4. Select the `school_management` database from the left sidebar
5. Click on "SQL" tab again
6. Copy and paste the contents of `scripts/01_create_tables.sql` and click "Go"
7. Copy and paste the contents of `scripts/02_seed_data.sql` and click "Go"

### 2. Configure Database Connection

Edit `config/database.php` if your MySQL settings are different:

\`\`\`php
define('DB_HOST', 'localhost');     // Your MySQL host
define('DB_NAME', 'school_management'); // Database name
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password
\`\`\`

### 3. Test Database Connection

Open your browser and navigate to:
\`\`\`
http://localhost/your-project-folder/test-connection.php
\`\`\`

This will verify that:
- Database connection is working
- Tables are created
- Seed data is loaded

### 4. Access the System

Navigate to:
\`\`\`
http://localhost/your-project-folder/
\`\`\`

## Demo Login Credentials

### Admin Account
- Username: `admin`
- Password: `password`

### Teacher Account
- Username: `teacher1`
- Password: `password`

### Student Account
- Username: `student1`
- Password: `password`

### Parent Account
- Username: `parent1`
- Password: `password`

## Troubleshooting

### Error: "An error occurred"
1. Run `test-connection.php` to check database connectivity
2. Make sure all SQL scripts have been executed in order
3. Check that the `users` table has records
4. Verify database credentials in `config/database.php`

### Error: "Unknown database"
- Run `scripts/00_create_database.sql` to create the database

### Error: "Table doesn't exist"
- Run `scripts/01_create_tables.sql` to create tables

### Error: "Invalid credentials"
- Make sure you've run `scripts/02_seed_data.sql` to create demo users
- Passwords for all demo accounts are: `password`

## File Structure

\`\`\`
project/
├── api/                    # API endpoints
│   ├── auth/              # Authentication APIs
│   ├── admin/             # Admin APIs
│   ├── teacher/           # Teacher APIs
│   ├── student/           # Student APIs
│   ├── messages/          # Messaging APIs
│   └── announcements/     # Announcements APIs
├── config/                # Configuration files
│   ├── database.php       # Database connection
│   └── init.php          # Initialization
├── components/            # Reusable components
│   ├── header.php        # Navigation header
│   └── protect.php       # Route protection
├── pages/                # Application pages
│   ├── admin/           # Admin panel pages
│   ├── teacher/         # Teacher panel pages
│   ├── student/         # Student panel pages
│   └── parent/          # Parent panel pages
├── scripts/             # Database scripts
│   ├── 00_create_database.sql
│   ├── 01_create_tables.sql
│   └── 02_seed_data.sql
├── assets/              # Static assets
│   └── css/
├── index.php           # Login page
├── register.php        # Registration page
└── test-connection.php # Database test
\`\`\`

## Security Notes

1. Change default passwords immediately in production
2. Update database credentials in `config/database.php`
3. Never commit database credentials to version control
4. Use HTTPS in production
5. Keep PHP and MySQL updated

## Support

If you encounter any issues:
1. Check `test-connection.php` output
2. Verify all SQL scripts were executed successfully
3. Check PHP error logs
4. Ensure proper file permissions
