<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$gradeLevel = intval($_POST['grade_level'] ?? 0);
$academicYear = sanitize($_POST['academic_year'] ?? '');

if (empty($name) || $gradeLevel < 1 || empty($academicYear)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO classes (name, grade_level, academic_year) VALUES (:name, :grade_level, :academic_year)");
    $stmt->execute(['name' => $name, 'grade_level' => $gradeLevel, 'academic_year' => $academicYear]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
