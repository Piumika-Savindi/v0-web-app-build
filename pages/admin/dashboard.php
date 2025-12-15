<?php
require_once '../../config/init.php';
$required_role = 'admin';
require_once '../../components/protect.php';

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher' AND is_active = 1");
$teacherCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'student' AND is_active = 1");
$studentCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'parent' AND is_active = 1");
$parentCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM classes");
$classCount = $stmt->fetch()['count'];

// Get recent users
$stmt = $db->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$recentUsers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
            <p class="text-gray-400">Manage users, classes, and system configuration</p>
        </div>
        
        <!-- Quick Access -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-white mb-4">Quick Access</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="users.php" class="card hover:border-blue-500/50 transition-all cursor-pointer">
                    <div class="text-3xl mb-3">👥</div>
                    <h3 class="text-lg font-semibold text-white mb-1">Manage Users</h3>
                    <p class="text-sm text-gray-400">Add and manage all users</p>
                </a>
                
                <a href="classes.php" class="card hover:border-blue-500/50 transition-all cursor-pointer">
                    <div class="text-3xl mb-3">🏫</div>
                    <h3 class="text-lg font-semibold text-white mb-1">Manage Classes</h3>
                    <p class="text-sm text-gray-400">Organize classes and subjects</p>
                </a>
                
                <a href="../messages.php" class="card hover:border-blue-500/50 transition-all cursor-pointer">
                    <div class="text-3xl mb-3">💬</div>
                    <h3 class="text-lg font-semibold text-white mb-1">Messages</h3>
                    <p class="text-sm text-gray-400">Communicate with staff</p>
                </a>
                
                <a href="../announcements.php" class="card hover:border-blue-500/50 transition-all cursor-pointer">
                    <div class="text-3xl mb-3">📢</div>
                    <h3 class="text-lg font-semibold text-white mb-1">Announcements</h3>
                    <p class="text-sm text-gray-400">Post system-wide notices</p>
                </a>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-2xl">👨‍🏫</div>
                    <div class="text-2xl font-bold text-white"><?php echo $teacherCount; ?></div>
                </div>
                <h3 class="text-sm font-medium text-gray-400">Total Teachers</h3>
            </div>
            
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-2xl">🎓</div>
                    <div class="text-2xl font-bold text-white"><?php echo $studentCount; ?></div>
                </div>
                <h3 class="text-sm font-medium text-gray-400">Total Students</h3>
            </div>
            
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-2xl">👪</div>
                    <div class="text-2xl font-bold text-white"><?php echo $parentCount; ?></div>
                </div>
                <h3 class="text-sm font-medium text-gray-400">Total Parents</h3>
            </div>
            
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-2xl">🏫</div>
                    <div class="text-2xl font-bold text-white"><?php echo $classCount; ?></div>
                </div>
                <h3 class="text-sm font-medium text-gray-400">Total Classes</h3>
            </div>
        </div>
        
        <!-- Recent Users -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">Recent Users</h2>
                <a href="../../pages/admin/users.php" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Name</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Email</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Role</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50">
                            <td class="py-3 px-4 text-sm text-white">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-900/30 text-blue-300 capitalize">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-400">
                                <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
