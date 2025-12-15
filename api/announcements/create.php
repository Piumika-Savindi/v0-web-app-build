<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !in_array($user['role'] ?? '', ['admin', 'teacher'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$title = sanitize($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$targetRole = sanitize($_POST['target_role'] ?? 'all');

if (empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Title and content are required']);
    exit;
}

if (!in_array($targetRole, ['all', 'teacher', 'student', 'parent'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid target role']);
    exit;
}

try {
    $stmt = $db->prepare("
        INSERT INTO announcements (title, content, created_by, target_role)
        VALUES (:title, :content, :created_by, :target_role)
    ");
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'created_by' => $user['id'],
        'target_role' => $targetRole
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
