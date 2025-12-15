<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$subject_id = $data['subject_id'] ?? null;

if (!$subject_id) {
    echo json_encode(['success' => false, 'message' => 'Subject ID is required']);
    exit;
}

try {
    $stmt = $db->prepare("DELETE FROM subjects WHERE id = :subject_id");
    $stmt->execute(['subject_id' => $subject_id]);
    
    echo json_encode(['success' => true, 'message' => 'Subject deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete subject']);
}
?>
