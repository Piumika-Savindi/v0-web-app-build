<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$email = sanitize($_POST['email'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$first_name = sanitize($_POST['first_name'] ?? '');
$last_name = sanitize($_POST['last_name'] ?? '');
$role = sanitize($_POST['role'] ?? '');

// Validate required fields
if (empty($email) || empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate username length
if (strlen($username) < 3) {
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

// Validate passwords match
if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

// Validate role (admin cannot be registered through public registration)
$allowed_roles = ['student', 'teacher', 'parent'];
if (!in_array($role, $allowed_roles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Check if username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already taken']);
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $db->prepare("
        INSERT INTO users (email, username, password, first_name, last_name, role, is_active) 
        VALUES (:email, :username, :password, :first_name, :last_name, :role, 1)
    ");
    
    $stmt->execute([
        'email' => $email,
        'username' => $username,
        'password' => $hashed_password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'role' => $role
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Redirecting to login...'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
?>
