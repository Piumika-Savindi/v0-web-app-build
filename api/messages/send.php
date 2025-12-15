<?php
require_once '../../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$recipientId = intval($_POST['recipient_id'] ?? 0);
$subject = sanitize($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$recipientId || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Recipient and message are required']);
    exit;
}

// Verify recipient exists
$stmt = $db->prepare("SELECT id FROM users WHERE id = :id AND is_active = 1");
$stmt->execute(['id' => $recipientId]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipient']);
    exit;
}

try {
    $stmt = $db->prepare("
        INSERT INTO messages (sender_id, recipient_id, subject, message)
        VALUES (:sender_id, :recipient_id, :subject, :message)
    ");
    $stmt->execute([
        'sender_id' => $user['id'],
        'recipient_id' => $recipientId,
        'subject' => $subject,
        'message' => $message
    ]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
