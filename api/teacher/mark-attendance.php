<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('teacher')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$classId = intval($_POST['class_id'] ?? 0);
$subjectId = intval($_POST['subject_id'] ?? 0);
$date = $_POST['date'] ?? '';
$attendance = $_POST['attendance'] ?? [];

if (!$classId || !$subjectId || empty($date) || empty($attendance)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $db->beginTransaction();
    
    foreach ($attendance as $studentId => $status) {
        $stmt = $db->prepare("
            INSERT INTO attendance (student_id, class_id, subject_id, date, status, marked_by)
            VALUES (:student_id, :class_id, :subject_id, :date, :status, :marked_by)
            ON DUPLICATE KEY UPDATE status = :status, marked_by = :marked_by
        ");
        $stmt->execute([
            'student_id' => intval($studentId),
            'class_id' => $classId,
            'subject_id' => $subjectId,
            'date' => $date,
            'status' => $status,
            'marked_by' => $user['id']
        ]);
    }
    
    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
