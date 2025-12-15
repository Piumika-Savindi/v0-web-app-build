<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$code = sanitize($_POST['code'] ?? '');
$description = sanitize($_POST['description'] ?? '');

if (empty($name) || empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Name and code are required']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO subjects (name, code, description) VALUES (:name, :code, :description)");
    $stmt->execute(['name' => $name, 'code' => $code, 'description' => $description]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
