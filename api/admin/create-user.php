<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$firstName = sanitize($_POST['first_name'] ?? '');
$lastName = sanitize($_POST['last_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = sanitize($_POST['role'] ?? '');

if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!in_array($role, ['admin', 'teacher', 'student', 'parent'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

try {
    // Check if email or username exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
    $stmt->execute(['email' => $email, 'username' => $username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email or username already exists']);
        exit;
    }
    
    // Insert user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("
        INSERT INTO users (email, username, password, first_name, last_name, role)
        VALUES (:email, :username, :password, :first_name, :last_name, :role)
    ");
    $stmt->execute([
        'email' => $email,
        'username' => $username,
        'password' => $hashedPassword,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'role' => $role
    ]);
    
    echo json_encode(['success' => true, 'message' => 'User created successfully']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
