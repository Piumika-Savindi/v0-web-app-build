# Troubleshooting Guide

## Apache Error - Page Not Found

If you're seeing an Apache error page, here are the solutions:

### 1. Check Your URL
Make sure you're accessing the correct URL:
- **Correct:** `http://localhost/v0-web-app-build/`
- **Correct:** `http://localhost/v0-web-app-build/index.php`
- **Wrong:** `http://localhost/` (unless you placed files in htdocs root)

### 2. Verify File Location
Your files should be in: `C:\xampp\htdocs\v0-web-app-build\`

If you placed them elsewhere, adjust the URL accordingly.

### 3. Check Apache Configuration
1. Open XAMPP Control Panel
2. Make sure Apache is running (green "Running" status)
3. If not running, click "Start" button

### 4. Database Setup
Before using the system, set up the database:

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "SQL" tab
3. Run these scripts in order:
   - `scripts/00_create_database.sql`
   - `scripts/01_create_tables.sql`
   - `scripts/02_seed_data.sql`

### 5. Test Database Connection
Access: `http://localhost/v0-web-app-build/test-connection.php`

This will show if your database is properly configured.

### 6. Common Issues

#### Issue: "Invalid parameter number" SQL error
**Solution:** This has been fixed in the latest version. Make sure you're using the updated files.

#### Issue: Links/buttons not working
**Solution:** All paths have been updated to use relative paths. Clear your browser cache and try again.

#### Issue: Cannot login
**Solution:** 
1. Make sure database is set up (see step 4)
2. Check test-connection.php for database connectivity
3. Use demo credentials:
   - Admin: username `admin`, password `password`
   - Teacher: username `teacher1`, password `password`
   - Student: username `student1`, password `password`
   - Parent: username `parent1`, password `password`

### 7. File Permissions
If on Linux/Mac, ensure proper permissions:
\`\`\`bash
chmod 755 -R /path/to/v0-web-app-build
chmod 644 /path/to/v0-web-app-build/*.php
\`\`\`

## Getting Help

If you still have issues:
1. Check Apache error logs: `xampp/apache/logs/error.log`
2. Check PHP error logs
3. Enable error reporting in `config/database.php`
