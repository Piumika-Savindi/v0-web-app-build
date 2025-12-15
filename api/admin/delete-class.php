<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$class_id = $data['class_id'] ?? null;

if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID is required']);
    exit;
}

try {
    $stmt = $db->prepare("DELETE FROM classes WHERE id = :class_id");
    $stmt->execute(['class_id' => $class_id]);
    
    echo json_encode(['success' => true, 'message' => 'Class deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete class']);
}
?>
