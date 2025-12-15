<?php
require_once '../../config/init.php';
$required_role = 'teacher';
require_once '../../components/protect.php';

$user = getCurrentUser();

// Get teacher's classes
$stmt = $db->prepare("
    SELECT DISTINCT c.id, c.name, s.id as subject_id, s.name as subject_name
    FROM teacher_assignments ta
    JOIN classes c ON ta.class_id = c.id
    JOIN subjects s ON ta.subject_id = s.id
    WHERE ta.teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $user['id']]);
$classes = $stmt->fetchAll();

// Get all assignments
$stmt = $db->prepare("
    SELECT a.*, c.name as class_name, s.name as subject_name,
           COUNT(DISTINCT asub.id) as submission_count
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id
    WHERE a.teacher_id = :teacher_id
    GROUP BY a.id
    ORDER BY a.deadline DESC
");
$stmt->execute(['teacher_id' => $user['id']]);
$assignments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Assignments</h1>
                <p class="text-gray-400">Create and manage assignments for your classes</p>
            </div>
            <button onclick="showCreateModal()" class="btn-primary">Create Assignment</button>
        </div>
        
        <!-- Assignments List -->
        <div class="space-y-4">
            <?php foreach ($assignments as $assignment): ?>
            <div class="card">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-white mb-2"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                        <p class="text-sm text-gray-400 mb-4"><?php echo htmlspecialchars($assignment['description']); ?></p>
                        <div class="flex items-center gap-6 text-sm text-gray-400">
                            <span><?php echo htmlspecialchars($assignment['class_name']); ?></span>
                            <span><?php echo htmlspecialchars($assignment['subject_name']); ?></span>
                            <span>Due: <?php echo date('M d, Y h:i A', strtotime($assignment['deadline'])); ?></span>
                            <span><?php echo $assignment['submission_count']; ?> submissions</span>
                        </div>
                    </div>
                    <a href="../../pages/teacher/view-submissions.php?assignment_id=<?php echo $assignment['id']; ?>" 
                       class="btn-primary">
                        View Submissions
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Create Assignment Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-white mb-4">Create New Assignment</h2>
            <form id="createForm" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Title</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
                    <textarea name="description" rows="4" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none"></textarea>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Class</label>
                        <select name="class_id" id="classSelect" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" data-subject="<?php echo $class['subject_id']; ?>">
                                <?php echo htmlspecialchars($class['name'] . ' - ' . $class['subject_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Deadline</label>
                        <input type="datetime-local" name="deadline" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Total Marks</label>
                    <input type="number" name="total_marks" value="100" required class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                </div>
                
                <input type="hidden" name="subject_id" id="subjectIdInput">
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 btn-primary">Create Assignment</button>
                    <button type="button" onclick="hideCreateModal()" class="flex-1 px-4 py-2 text-gray-300 border border-gray-700 rounded-lg hover:border-gray-600">Cancel</button>
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
        
        // Update subject_id when class is selected
        document.getElementById('classSelect').addEventListener('change', function() {
            const subjectId = this.options[this.selectedIndex].dataset.subject;
            document.getElementById('subjectIdInput').value = subjectId;
        });
        
        document.getElementById('createForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../../api/teacher/create-assignment.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Assignment created successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to create assignment');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    </script>
</body>
</html>
