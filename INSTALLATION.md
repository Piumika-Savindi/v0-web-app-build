# Installation Guide

## Step-by-Step Installation

### Step 1: Copy Files
1. Copy all project files to: `C:\xampp\htdocs\v0-web-app-build\`
2. Make sure the folder structure is intact:
   \`\`\`
   v0-web-app-build/
   ├── index.php
   ├── register.php
   ├── api/
   ├── config/
   ├── components/
   ├── pages/
   ├── scripts/
   └── assets/
   \`\`\`

### Step 2: Start XAMPP
1. Open XAMPP Control Panel
2. Start Apache server
3. Start MySQL server
4. Both should show green "Running" status

### Step 3: Create Database
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click "SQL" tab at the top
3. Copy and paste the content from `scripts/00_create_database.sql`
4. Click "Go" button
5. Repeat for `scripts/01_create_tables.sql`
6. Repeat for `scripts/02_seed_data.sql`

### Step 4: Configure Database Connection
1. Open `config/database.php` in a text editor
2. Update credentials if needed (default should work for XAMPP):
   \`\`\`php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'school_management');
   \`\`\`

### Step 5: Test Installation
1. Open browser
2. Go to: `http://localhost/v0-web-app-build/test-connection.php`
3. You should see "Database connection successful"
4. Check if all tables are listed

### Step 6: Access the System
1. Go to: `http://localhost/v0-web-app-build/`
2. Use demo credentials to login:
   - **Admin:** username `admin`, password `password`
   - **Teacher:** username `teacher1`, password `password`
   - **Student:** username `student1`, password `password`
   - **Parent:** username `parent1`, password `password`

## Common URLs

- **Home/Login:** `http://localhost/v0-web-app-build/`
- **Register:** `http://localhost/v0-web-app-build/register.php`
- **Test Connection:** `http://localhost/v0-web-app-build/test-connection.php`
- **phpMyAdmin:** `http://localhost/phpmyadmin`

## Next Steps

After successful installation:
1. Login with admin account
2. Create new users via Admin Panel
3. Set up classes and subjects
4. Assign teachers to subjects
5. Enroll students in classes
6. Start creating assignments and tests

## Security Note

**Important:** After installation, delete or move `test-connection.php` to prevent unauthorized access to database information.
