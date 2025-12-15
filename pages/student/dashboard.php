<?php
require_once '../../config/init.php';
$required_role = 'student';
require_once '../../components/protect.php';

$user = getCurrentUser();

// Get pending assignments
$stmt = $db->prepare("
    SELECT a.*, c.name as class_name, s.name as subject_name,
           asub.id as submission_id, asub.marks_obtained
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    JOIN student_enrollments se ON c.id = se.class_id
    LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = :student_id
    WHERE se.student_id = :student_id AND a.deadline >= NOW()
    ORDER BY a.deadline ASC
    LIMIT 5
");
$stmt->execute(['student_id' => $user['id']]);
$pendingAssignments = $stmt->fetchAll();

// Get recent grades
$stmt = $db->prepare("
    SELECT a.title, a.total_marks, asub.marks_obtained, asub.graded_at,
           c.name as class_name, s.name as subject_name
    FROM assignment_submissions asub
    JOIN assignments a ON asub.assignment_id = a.id
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    WHERE asub.student_id = :student_id AND asub.marks_obtained IS NOT NULL
    ORDER BY asub.graded_at DESC
    LIMIT 5
");
$stmt->execute(['student_id' => $user['id']]);
$recentGrades = $stmt->fetchAll();

// Get attendance summary
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent
    FROM attendance
    WHERE student_id = :student_id AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$stmt->execute(['student_id' => $user['id']]);
$attendance = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></h1>
            <p class="text-gray-400">Track your assignments, grades, and attendance</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="card">
                <div class="text-blue-500 text-3xl mb-4">📝</div>
                <div class="text-3xl font-bold text-white mb-2"><?php echo count($pendingAssignments); ?></div>
                <p class="text-sm text-gray-400">Pending Assignments</p>
            </div>
            
            <div class="card">
                <div class="text-green-500 text-3xl mb-4">✓</div>
                <div class="text-3xl font-bold text-white mb-2">
                    <?php echo $attendance['total'] > 0 ? round(($attendance['present'] / $attendance['total']) * 100) : 0; ?>%
                </div>
                <p class="text-sm text-gray-400">Attendance (Last 30 days)</p>
            </div>
            
            <div class="card">
                <div class="text-yellow-500 text-3xl mb-4">⭐</div>
                <div class="text-3xl font-bold text-white mb-2"><?php echo count($recentGrades); ?></div>
                <p class="text-sm text-gray-400">Recent Grades</p>
            </div>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-6">
            <!-- Pending Assignments -->
            <div class="card">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Pending Assignments</h2>
                    <!-- Fixed navigation link to be relative -->
                    <a href="assignments.php" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
                </div>
                
                <?php if (empty($pendingAssignments)): ?>
                    <p class="text-gray-400 text-center py-8">No pending assignments</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($pendingAssignments as $assignment): ?>
                        <div class="p-4 bg-black/50 rounded-lg">
                            <h3 class="font-semibold text-white mb-2"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                            <p class="text-sm text-gray-400 mb-2"><?php echo htmlspecialchars($assignment['class_name'] . ' - ' . $assignment['subject_name']); ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    Due: <?php echo date('M d, Y', strtotime($assignment['deadline'])); ?>
                                </span>
                                <?php if (!$assignment['submission_id']): ?>
                                    <!-- Fixed navigation link to be relative -->
                                    <a href="submit-assignment.php?id=<?php echo $assignment['id']; ?>" 
                                       class="text-sm text-blue-400 hover:text-blue-300">
                                        Submit
                                    </a>
                                <?php else: ?>
                                    <span class="text-xs text-green-400">Submitted</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Recent Grades -->
            <div class="card">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Recent Grades</h2>
                    <!-- Fixed navigation link to be relative -->
                    <a href="grades.php" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
                </div>
                
                <?php if (empty($recentGrades)): ?>
                    <p class="text-gray-400 text-center py-8">No grades yet</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentGrades as $grade): ?>
                        <div class="p-4 bg-black/50 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-white mb-2"><?php echo htmlspecialchars($grade['title']); ?></h3>
                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($grade['class_name'] . ' - ' . $grade['subject_name']); ?></p>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-blue-400">
                                        <?php echo $grade['marks_obtained']; ?>/<?php echo $grade['total_marks']; ?>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <?php echo round(($grade['marks_obtained'] / $grade['total_marks']) * 100, 1); ?>%
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
