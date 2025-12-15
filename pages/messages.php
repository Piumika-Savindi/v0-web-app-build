<?php
require_once '../config/init.php';
require_once '../components/protect.php';

$user = getCurrentUser();

$stmt = $db->prepare("
    SELECT m.*, 
           sender.first_name as sender_first, sender.last_name as sender_last, sender.role as sender_role,
           recipient.first_name as recipient_first, recipient.last_name as recipient_last, recipient.role as recipient_role
    FROM messages m
    JOIN users sender ON m.sender_id = sender.id
    JOIN users recipient ON m.recipient_id = recipient.id
    WHERE m.sender_id = :user_id1 OR m.recipient_id = :user_id2
    ORDER BY m.sent_at DESC
");
$stmt->execute(['user_id1' => $user['id'], 'user_id2' => $user['id']]);
$messages = $stmt->fetchAll();

// Get users who can be messaged (role-specific)
$allowedRoles = [];
switch($user['role']) {
    case 'admin':
        $allowedRoles = ['teacher', 'student', 'parent', 'admin'];
        break;
    case 'teacher':
        $allowedRoles = ['admin', 'teacher', 'student', 'parent'];
        break;
    case 'student':
        $allowedRoles = ['admin', 'teacher'];
        break;
    case 'parent':
        $allowedRoles = ['admin', 'teacher'];
        break;
}

$placeholders = str_repeat('?,', count($allowedRoles) - 1) . '?';
$stmt = $db->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE role IN ($placeholders) AND id != ? AND is_active = 1 ORDER BY first_name, last_name");
$stmt->execute(array_merge($allowedRoles, [$user['id']]));
$availableUsers = $stmt->fetchAll();

// Mark messages as read
$stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE recipient_id = :user_id");
$stmt->execute(['user_id' => $user['id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Messages</h1>
                <p class="text-gray-400">Communicate with teachers, students, and parents</p>
            </div>
            <button onclick="showComposeModal()" class="btn-primary">
                Compose Message
            </button>
        </div>
        
        <!-- Messages List -->
        <div class="card">
            <?php if (empty($messages)): ?>
                <p class="text-gray-400 text-center py-12">No messages yet</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($messages as $message): 
                        $isReceived = $message['recipient_id'] === $user['id'];
                        $otherPerson = $isReceived ? 
                            $message['sender_first'] . ' ' . $message['sender_last'] : 
                            $message['recipient_first'] . ' ' . $message['recipient_last'];
                        $otherRole = $isReceived ? $message['sender_role'] : $message['recipient_role'];
                    ?>
                    <div class="p-4 bg-black/50 rounded-lg hover:bg-black/70 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                    <?php echo strtoupper(substr($otherPerson, 0, 1)); ?>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">
                                        <?php echo htmlspecialchars($otherPerson); ?>
                                        <span class="ml-2 text-xs px-2 py-1 rounded-full bg-gray-800 text-gray-400 capitalize">
                                            <?php echo htmlspecialchars($otherRole); ?>
                                        </span>
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        <?php echo date('M d, Y h:i A', strtotime($message['sent_at'])); ?>
                                        <?php if ($isReceived): ?>
                                            <span class="ml-2 text-green-400">Received</span>
                                        <?php else: ?>
                                            <span class="ml-2 text-blue-400">Sent</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($message['subject']): ?>
                        <h4 class="font-medium text-white mb-2"><?php echo htmlspecialchars($message['subject']); ?></h4>
                        <?php endif; ?>
                        
                        <p class="text-sm text-gray-300"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                        
                        <?php if ($isReceived): ?>
                        <button onclick="replyTo(<?php echo $message['sender_id']; ?>, '<?php echo htmlspecialchars($otherPerson, ENT_QUOTES); ?>', 'Re: <?php echo htmlspecialchars($message['subject'] ?? 'No Subject', ENT_QUOTES); ?>')" 
                                class="mt-3 text-sm text-blue-400 hover:text-blue-300">
                            Reply
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Compose Modal -->
    <div id="composeModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-2xl w-full">
            <h2 class="text-xl font-bold text-white mb-4">Compose Message</h2>
            <form id="composeForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Recipient</label>
                    <select name="recipient_id" id="recipientSelect" required 
                            class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                        <option value="">Select Recipient</option>
                        <?php foreach ($availableUsers as $availableUser): ?>
                        <option value="<?php echo $availableUser['id']; ?>">
                            <?php echo htmlspecialchars($availableUser['first_name'] . ' ' . $availableUser['last_name'] . ' (' . ucfirst($availableUser['role']) . ')'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Subject</label>
                    <input type="text" name="subject" id="subjectInput" 
                           class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Message</label>
                    <textarea name="message" rows="6" required 
                              class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 btn-primary">Send Message</button>
                    <button type="button" onclick="hideComposeModal()" 
                            class="flex-1 px-4 py-2 text-gray-300 border border-gray-700 rounded-lg hover:border-gray-600">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showComposeModal() {
            document.getElementById('composeModal').classList.remove('hidden');
        }
        
        function hideComposeModal() {
            document.getElementById('composeModal').classList.add('hidden');
            document.getElementById('composeForm').reset();
        }
        
        function replyTo(recipientId, recipientName, subject) {
            showComposeModal();
            document.getElementById('recipientSelect').value = recipientId;
            document.getElementById('subjectInput').value = subject;
        }
        
        document.getElementById('composeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../api/messages/send.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Message sent successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to send message');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    </script>
</body>
</html>
