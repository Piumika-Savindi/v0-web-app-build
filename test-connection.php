<?php
// Database connection test
$host = 'localhost';
$dbname = 'school_management';
$username = 'root';
$password = '';

echo "<h1>Database Connection Test</h1>";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test if users table exists
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✓ Users table exists with $count records</p>";
    
    // Test if admin user exists
    $stmt = $conn->prepare("SELECT username, role FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'>✓ Admin user exists (username: admin, role: {$admin['role']})</p>";
    } else {
        echo "<p style='color: red;'>✗ Admin user not found. Please run the seed data script.</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Connection failed: " . $e->getMessage() . "</p>";
    
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<p style='color: orange;'>Please create the database first by running scripts/00_create_database.sql</p>";
    }
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 {
        color: #333;
    }
    p {
        font-size: 16px;
        padding: 10px;
        background: white;
        border-radius: 5px;
        margin: 10px 0;
    }
</style>
