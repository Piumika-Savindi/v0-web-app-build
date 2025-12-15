<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_POST['user_id'] ?? null;
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$role = $_POST['role'] ?? '';

if (!$user_id || !$first_name || !$last_name || !$email || !$username || !$role) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    // Check if email/username already exists for other users
    $stmt = $db->prepare("SELECT id FROM users WHERE (email = :email OR username = :username) AND id != :user_id");
    $stmt->execute(['email' => $email, 'username' => $username, 'user_id' => $user_id]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email or username already exists']);
        exit;
    }
    
    $stmt = $db->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, username = :username, role = :role WHERE id = :user_id");
    $stmt->execute([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'username' => $username,
        'role' => $role,
        'user_id' => $user_id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to update user']);
}
?>
