<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('teacher')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$submissionId = intval($_POST['submission_id'] ?? 0);
$marksObtained = intval($_POST['marks_obtained'] ?? 0);
$feedback = sanitize($_POST['feedback'] ?? '');

if (!$submissionId) {
    echo json_encode(['success' => false, 'message' => 'Invalid submission']);
    exit;
}

try {
    $stmt = $db->prepare("
        UPDATE assignment_submissions 
        SET marks_obtained = :marks_obtained, feedback = :feedback, graded_at = NOW()
        WHERE id = :id
    ");
    $stmt->execute([
        'marks_obtained' => $marksObtained,
        'feedback' => $feedback,
        'id' => $submissionId
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
