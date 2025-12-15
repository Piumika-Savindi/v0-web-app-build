<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    if (!isset($db)) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Find user by username or email
    $stmt = $db->prepare("
        SELECT id, email, username, password, first_name, last_name, role, is_active 
        FROM users 
        WHERE (username = :username OR email = :email) AND is_active = 1
    ");
    $stmt->execute([
        'username' => $username,
        'email' => $username
    ]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_data'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'username' => $user['username'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'role' => $user['role']
    ];
    
    $redirects = [
        'admin' => 'pages/admin/dashboard.php',
        'teacher' => 'pages/teacher/dashboard.php',
        'student' => 'pages/student/dashboard.php',
        'parent' => 'pages/parent/dashboard.php'
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect' => $redirects[$user['role']]
    ]);
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
