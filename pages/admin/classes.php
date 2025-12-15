<?php
require_once '../../config/init.php';
$required_role = 'admin';
require_once '../../components/protect.php';

// Get all classes with student count
$stmt = $db->query("
    SELECT c.*, COUNT(se.id) as student_count 
    FROM classes c
    LEFT JOIN student_enrollments se ON c.id = se.class_id
    GROUP BY c.id
    ORDER BY c.grade_level, c.name
");
$classes = $stmt->fetchAll();

// Get all subjects
$stmt = $db->query("SELECT * FROM subjects ORDER BY name");
$subjects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Class & Subject Management</h1>
                <p class="text-gray-400">Manage classes and subjects</p>
            </div>
            <div class="flex gap-3">
                <button onclick="showCreateClassModal()" class="btn-primary">Create Class</button>
                <button onclick="showCreateSubjectModal()" class="btn-primary">Create Subject</button>
            </div>
        </div>
        
        <!-- Classes Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($classes as $class): ?>
            <div class="card">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-1"><?php echo htmlspecialchars($class['name']); ?></h3>
                        <p class="text-sm text-gray-400">Grade <?php echo $class['grade_level']; ?> • <?php echo $class['academic_year']; ?></p>
                    </div>
                </div>
                <div class="text-sm text-gray-400">
                    <?php echo $class['student_count']; ?> Students
                </div>
                <!-- Added delete button for classes -->
                <button onclick="deleteClass(<?php echo $class['id']; ?>)" 
                        class="text-sm text-red-400 hover:text-red-300">
                    Delete
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Subjects Table -->
        <div class="card">
            <h2 class="text-xl font-bold text-white mb-6">Subjects</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Code</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Name</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Description</th>
                            <!-- Added actions column -->
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50">
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo htmlspecialchars($subject['code']); ?></td>
                            <td class="py-3 px-4 text-sm text-white"><?php echo htmlspecialchars($subject['name']); ?></td>
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo htmlspecialchars($subject['description']); ?></td>
                            <td class="py-3 px-4">
                                <!-- Added delete button for subjects -->
                                <button onclick="deleteSubject(<?php echo $subject['id']; ?>)" 
                                        class="text-sm text-red-400 hover:text-red-300">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Create Class Modal -->
    <div id="createClassModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-bold text-white mb-4">Create New Class</h2>
            <form id="createClassForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Class Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Grade Level</label>
                    <input type="number" name="grade_level" required min="1" max="13" class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Academic Year</label>
                    <input type="text" name="academic_year" required placeholder="2024-2025" class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 btn-primary">Create Class</button>
                    <button type="button" onclick="hideModal('createClassModal')" class="flex-1 px-4 py-2 text-gray-300 border border-gray-700 rounded-lg hover:border-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Create Subject Modal -->
    <div id="createSubjectModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-bold text-white mb-4">Create New Subject</h2>
            <form id="createSubjectForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Subject Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Subject Code</label>
                    <input type="text" name="code" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 btn-primary">Create Subject</button>
                    <button type="button" onclick="hideModal('createSubjectModal')" class="flex-1 px-4 py-2 text-gray-300 border border-gray-700 rounded-lg hover:border-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showCreateClassModal() {
            document.getElementById('createClassModal').classList.remove('hidden');
        }
        
        function showCreateSubjectModal() {
            document.getElementById('createSubjectModal').classList.remove('hidden');
        }
        
        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        
        document.getElementById('createClassForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../../api/admin/create-class.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Class created successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to create class');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
        
        document.getElementById('createSubjectForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../../api/admin/create-subject.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Subject created successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to create subject');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
        
        async function deleteClass(classId) {
            if (!confirm('Are you sure you want to delete this class? This will also remove all related data.')) return;
            
            try {
                const response = await fetch('../../api/admin/delete-class.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({class_id: classId})
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Class deleted successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete class');
                }
            } catch (error) {
                alert('An error occurred');
            }
        }
        
        async function deleteSubject(subjectId) {
            if (!confirm('Are you sure you want to delete this subject?')) return;
            
            try {
                const response = await fetch('../../api/admin/delete-subject.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({subject_id: subjectId})
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Subject deleted successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete subject');
                }
            } catch (error) {
                alert('An error occurred');
            }
        }
    </script>
</body>
</html>
