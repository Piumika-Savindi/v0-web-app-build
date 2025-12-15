<?php
require_once '../../config/init.php';
$required_role = 'admin';
require_once '../../components/protect.php';

// Get all users
$stmt = $db->query("SELECT id, first_name, last_name, email, username, role, is_active, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">User Management</h1>
                <p class="text-gray-400">Manage all system users</p>
            </div>
            <button onclick="showCreateUserModal()" class="btn-primary">
                Create New User
            </button>
        </div>
        
        <!-- Users Table -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">ID</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Name</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Email</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Username</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Role</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50">
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo $user['id']; ?></td>
                            <td class="py-3 px-4 text-sm text-white">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-900/30 text-blue-300 capitalize">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $user['is_active'] ? 'bg-green-900/30 text-green-300' : 'bg-red-900/30 text-red-300'; ?>">
                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <button onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active']; ?>)" 
                                        class="text-sm text-blue-400 hover:text-blue-300 mr-3">
                                    <?php echo $user['is_active'] ? 'Disable' : 'Enable'; ?>
                                </button>
                                <button onclick="editUser(<?php echo $user['id']; ?>)" 
                                        class="text-sm text-yellow-400 hover:text-yellow-300">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Create User Modal -->
    <div id="createUserModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-bold text-white mb-4">Create New User</h2>
            <form id="createUserForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">First Name</label>
                    <input type="text" name="first_name" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Last Name</label>
                    <input type="text" name="last_name" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Username</label>
                    <input type="text" name="username" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Role</label>
                    <select name="role" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 btn-primary">Create User</button>
                    <button type="button" onclick="hideCreateUserModal()" class="flex-1 px-4 py-2 text-gray-300 border border-gray-700 rounded-lg hover:border-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showCreateUserModal() {
            document.getElementById('createUserModal').classList.remove('hidden');
        }
        
        function hideCreateUserModal() {
            document.getElementById('createUserModal').classList.add('hidden');
            document.getElementById('createUserForm').reset();
        }
        
        document.getElementById('createUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../../api/admin/create-user.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('User created successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to create user');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
        
        async function toggleUserStatus(userId, currentStatus) {
            if (!confirm('Are you sure you want to change this user\'s status?')) return;
            
            try {
                const response = await fetch('../../api/admin/toggle-user-status.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({user_id: userId, is_active: !currentStatus})
                });
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update status');
                }
            } catch (error) {
                alert('An error occurred');
            }
        }
    </script>
</body>
</html>
