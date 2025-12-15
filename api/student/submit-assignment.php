<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('student')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$assignmentId = intval($_POST['assignment_id'] ?? 0);
$submissionText = trim($_POST['submission_text'] ?? '');

if (!$assignmentId || empty($submissionText)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    // Check if assignment exists and student is enrolled
    $stmt = $db->prepare("
        SELECT a.id 
        FROM assignments a
        JOIN student_enrollments se ON a.class_id = se.class_id
        WHERE a.id = :assignment_id AND se.student_id = :student_id
    ");
    $stmt->execute(['assignment_id' => $assignmentId, 'student_id' => $user['id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Assignment not found']);
        exit;
    }
    
    // Check if already submitted
    $stmt = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id = :assignment_id AND student_id = :student_id");
    $stmt->execute(['assignment_id' => $assignmentId, 'student_id' => $user['id']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Assignment already submitted']);
        exit;
    }
    
    // Insert submission
    $stmt = $db->prepare("
        INSERT INTO assignment_submissions (assignment_id, student_id, submission_text)
        VALUES (:assignment_id, :student_id, :submission_text)
    ");
    $stmt->execute([
        'assignment_id' => $assignmentId,
        'student_id' => $user['id'],
        'submission_text' => $submissionText
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
