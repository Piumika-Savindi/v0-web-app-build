<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('teacher')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$title = sanitize($_POST['title'] ?? '');
$description = sanitize($_POST['description'] ?? '');
$classId = intval($_POST['class_id'] ?? 0);
$subjectId = intval($_POST['subject_id'] ?? 0);
$deadline = $_POST['deadline'] ?? '';
$totalMarks = intval($_POST['total_marks'] ?? 100);

if (empty($title) || empty($description) || !$classId || !$subjectId || empty($deadline)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $stmt = $db->prepare("
        INSERT INTO assignments (title, description, teacher_id, class_id, subject_id, deadline, total_marks)
        VALUES (:title, :description, :teacher_id, :class_id, :subject_id, :deadline, :total_marks)
    ");
    $stmt->execute([
        'title' => $title,
        'description' => $description,
        'teacher_id' => $user['id'],
        'class_id' => $classId,
        'subject_id' => $subjectId,
        'deadline' => $deadline,
        'total_marks' => $totalMarks
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
