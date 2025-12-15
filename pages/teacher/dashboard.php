<?php
require_once '../../config/init.php';
$required_role = 'teacher';
require_once '../../components/protect.php';

$user = getCurrentUser();

// Get teacher's assignments
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
    LIMIT 5
");
$stmt->execute(['teacher_id' => $user['id']]);
$recentAssignments = $stmt->fetchAll();

// Get teacher's classes
$stmt = $db->prepare("
    SELECT DISTINCT c.*, s.name as subject_name
    FROM teacher_assignments ta
    JOIN classes c ON ta.class_id = c.id
    JOIN subjects s ON ta.subject_id = s.id
    WHERE ta.teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $user['id']]);
$classes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></h1>
            <p class="text-gray-400">Manage your classes, assignments, and student progress</p>
        </div>
        
        <!-- My Classes -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-white mb-4">My Classes</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($classes as $class): ?>
                <div class="card">
                    <h3 class="text-lg font-bold text-white mb-2"><?php echo htmlspecialchars($class['name']); ?></h3>
                    <p class="text-sm text-gray-400 mb-4"><?php echo htmlspecialchars($class['subject_name']); ?></p>
                    <div class="flex gap-2">
                        <a href="/pages/teacher/attendance.php?class_id=<?php echo $class['id']; ?>" class="text-sm text-blue-400 hover:text-blue-300">
                            Mark Attendance
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Recent Assignments -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">Recent Assignments</h2>
                <a href="/pages/teacher/assignments.php" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
            </div>
            
            <?php if (empty($recentAssignments)): ?>
                <p class="text-gray-400 text-center py-8">No assignments yet. Create your first assignment!</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Title</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Class</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Subject</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Deadline</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Submissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAssignments as $assignment): ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50">
                            <td class="py-3 px-4 text-sm text-white"><?php echo htmlspecialchars($assignment['title']); ?></td>
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo htmlspecialchars($assignment['class_name']); ?></td>
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo htmlspecialchars($assignment['subject_name']); ?></td>
                            <td class="py-3 px-4 text-sm text-gray-400">
                                <?php echo date('M d, Y', strtotime($assignment['deadline'])); ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-400"><?php echo $assignment['submission_count']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
