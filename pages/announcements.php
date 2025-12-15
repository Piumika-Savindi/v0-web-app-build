<?php
require_once '../config/init.php';
require_once '../components/protect.php';

$user = getCurrentUser();

// Get announcements relevant to user's role
$stmt = $db->prepare("
    SELECT a.*, u.first_name, u.last_name, c.name as class_name
    FROM announcements a
    JOIN users u ON a.created_by = u.id
    LEFT JOIN classes c ON a.class_id = c.id
    WHERE a.target_role IN ('all', :role)
    ORDER BY a.created_at DESC
");
$stmt->execute(['role' => $user['role']]);
$announcements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Announcements</h1>
                <p class="text-gray-400">Stay updated with school news and updates</p>
            </div>
            <?php if ($user['role'] === 'admin' || $user['role'] === 'teacher'): ?>
            <button onclick="showCreateModal()" class="btn-primary">
                Create Announcement
            </button>
            <?php endif; ?>
        </div>
        
        <!-- Announcements List -->
        <div class="space-y-4">
            <?php if (empty($announcements)): ?>
                <div class="card text-center py-12">
                    <p class="text-gray-400">No announcements yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                <div class="card">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                            <div class="flex items-center gap-4 text-sm text-gray-400 mb-4">
                                <span>
                                    By <?php echo htmlspecialchars($announcement['first_name'] . ' ' . $announcement['last_name']); ?>
                                </span>
                                <span><?php echo date('M d, Y h:i A', strtotime($announcement['created_at'])); ?></span>
                                <?php if ($announcement['class_name']): ?>
                                    <span class="px-2 py-1 rounded-full bg-blue-900/30 text-blue-300">
                                        <?php echo htmlspecialchars($announcement['class_name']); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="px-2 py-1 rounded-full bg-gray-800 text-gray-300 capitalize">
                                    For: <?php echo $announcement['target_role'] === 'all' ? 'Everyone' : ucfirst($announcement['target_role']) . 's'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-300 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($announcement['content']); ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($user['role'] === 'admin' || $user['role'] === 'teacher'): ?>
    <!-- Create Announcement Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-2xl w-full">
            <h2 class="text-xl font-bold text-white mb-4">Create Announcement</h2>
            <form id="createForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Title</label>
                    <input type="text" name="title" required 
                           class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Target Audience</label>
                    <select name="target_role" required 
                            class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                        <option value="all">Everyone</option>
                        <option value="teacher">Teachers Only</option>
                        <option value="student">Students Only</option>
                        <option value="parent">Parents Only</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Content</label>
                    <textarea name="content" rows="6" required 
                              class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 btn-primary">Create Announcement</button>
                    <button type="button" onclick="hideCreateModal()" 
                            class="flex-1 px-4 py-2 text-gray-300 border border-gray-700 rounded-lg hover:border-gray-600">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }
        
        function hideCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.getElementById('createForm').reset();
        }
        
        document.getElementById('createForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../api/announcements/create.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Announcement created successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to create announcement');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
