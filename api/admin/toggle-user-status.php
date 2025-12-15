<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? 0;
$isActive = $data['is_active'] ?? false;

try {
    $stmt = $db->prepare("UPDATE users SET is_active = :is_active WHERE id = :id");
    $stmt->execute(['is_active' => $isActive ? 1 : 0, 'id' => $userId]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
