<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$assignment_id = $data['assignment_id'] ?? null;

if (!$assignment_id) {
    echo json_encode(['success' => false, 'message' => 'Assignment ID is required']);
    exit;
}

try {
    // Verify this assignment belongs to the teacher
    $stmt = $db->prepare("SELECT id FROM assignments WHERE id = :assignment_id AND teacher_id = :teacher_id");
    $stmt->execute(['assignment_id' => $assignment_id, 'teacher_id' => $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Assignment not found or unauthorized']);
        exit;
    }
    
    $stmt = $db->prepare("DELETE FROM assignments WHERE id = :assignment_id");
    $stmt->execute(['assignment_id' => $assignment_id]);
    
    echo json_encode(['success' => true, 'message' => 'Assignment deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete assignment']);
}
?>
