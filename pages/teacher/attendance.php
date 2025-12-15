<?php
require_once '../../config/init.php';
$required_role = 'teacher';
require_once '../../components/protect.php';

$user = getCurrentUser();
$classId = intval($_GET['class_id'] ?? 0);

// Get class details
$stmt = $db->prepare("
    SELECT c.*, s.id as subject_id, s.name as subject_name
    FROM teacher_assignments ta
    JOIN classes c ON ta.class_id = c.id
    JOIN subjects s ON ta.subject_id = s.id
    WHERE ta.teacher_id = :teacher_id AND c.id = :class_id
    LIMIT 1
");
$stmt->execute(['teacher_id' => $user['id'], 'class_id' => $classId]);
$class = $stmt->fetch();

if (!$class) {
    redirect('/pages/teacher/dashboard.php');
}

// Get students in class
$stmt = $db->prepare("
    SELECT u.id, u.first_name, u.last_name, u.email
    FROM student_enrollments se
    JOIN users u ON se.student_id = u.id
    WHERE se.class_id = :class_id AND u.is_active = 1
    ORDER BY u.last_name, u.first_name
");
$stmt->execute(['class_id' => $classId]);
$students = $stmt->fetchAll();

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="/pages/teacher/dashboard.php" class="text-blue-400 hover:text-blue-300 mb-4 inline-block">← Back to Dashboard</a>
            <h1 class="text-3xl font-bold text-white mb-2">Mark Attendance</h1>
            <p class="text-gray-400"><?php echo htmlspecialchars($class['name'] . ' - ' . $class['subject_name']); ?></p>
            <p class="text-gray-400">Date: <?php echo date('F d, Y'); ?></p>
        </div>
        
        <div class="card">
            <form id="attendanceForm">
                <input type="hidden" name="class_id" value="<?php echo $classId; ?>">
                <input type="hidden" name="subject_id" value="<?php echo $class['subject_id']; ?>">
                <input type="hidden" name="date" value="<?php echo $today; ?>">
                
                <div class="space-y-3">
                    <?php foreach ($students as $student): ?>
                    <div class="flex items-center justify-between p-4 bg-black/50 rounded-lg">
                        <div>
                            <p class="text-white font-medium">
                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                            </p>
                            <p class="text-sm text-gray-400"><?php echo htmlspecialchars($student['email']); ?></p>
                        </div>
                        <div class="flex gap-2">
                            <label class="flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer bg-green-900/30 text-green-300 border border-green-700">
                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="present" required class="w-4 h-4">
                                <span class="text-sm">Present</span>
                            </label>
                            <label class="flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer bg-red-900/30 text-red-300 border border-red-700">
                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="absent" class="w-4 h-4">
                                <span class="text-sm">Absent</span>
                            </label>
                            <label class="flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer bg-yellow-900/30 text-yellow-300 border border-yellow-700">
                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="late" class="w-4 h-4">
                                <span class="text-sm">Late</span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="btn-primary w-full">Submit Attendance</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('attendanceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/api/teacher/mark-attendance.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Attendance marked successfully');
                    window.location.href = '/pages/teacher/dashboard.php';
                } else {
                    alert(data.message || 'Failed to mark attendance');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    </script>
</body>
</html>
