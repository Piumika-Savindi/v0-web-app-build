<?php
require_once '../../config/init.php';
$required_role = 'parent';
require_once '../../components/protect.php';

$user = getCurrentUser();
$studentId = intval($_GET['student_id'] ?? 0);

// Verify parent-child link
$stmt = $db->prepare("
    SELECT u.id, u.first_name, u.last_name, u.email, psl.relationship
    FROM parent_student_links psl
    JOIN users u ON psl.student_id = u.id
    WHERE psl.parent_id = :parent_id AND psl.student_id = :student_id
");
$stmt->execute(['parent_id' => $user['id'], 'student_id' => $studentId]);
$child = $stmt->fetch();

if (!$child) {
    header('Location: dashboard.php');
    exit;
}

// Get recent grades
$stmt = $db->prepare("
    SELECT a.title, a.total_marks, asub.marks_obtained, asub.feedback, asub.graded_at,
           c.name as class_name, s.name as subject_name
    FROM assignment_submissions asub
    JOIN assignments a ON asub.assignment_id = a.id
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    WHERE asub.student_id = :student_id AND asub.marks_obtained IS NOT NULL
    ORDER BY asub.graded_at DESC
    LIMIT 10
");
$stmt->execute(['student_id' => $studentId]);
$grades = $stmt->fetchAll();

// Get attendance records
$stmt = $db->prepare("
    SELECT att.date, att.status, c.name as class_name, s.name as subject_name
    FROM attendance att
    JOIN classes c ON att.class_id = c.id
    JOIN subjects s ON att.subject_id = s.id
    WHERE att.student_id = :student_id
    ORDER BY att.date DESC
    LIMIT 20
");
$stmt->execute(['student_id' => $studentId]);
$attendanceRecords = $stmt->fetchAll();

// Get upcoming assignments
$stmt = $db->prepare("
    SELECT a.*, c.name as class_name, s.name as subject_name,
           asub.id as submission_id
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    JOIN student_enrollments se ON c.id = se.class_id
    LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = :student_id
    WHERE se.student_id = :student_id AND a.deadline >= NOW()
    ORDER BY a.deadline ASC
    LIMIT 5
");
$stmt->execute(['student_id' => $studentId]);
$upcomingAssignments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($child['first_name']); ?>'s Details - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <!-- Fixed back link to use relative path -->
        <a href="dashboard.php" class="text-blue-400 hover:text-blue-300 mb-4 inline-block">← Back to Dashboard</a>
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">
                <?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?>
            </h1>
            <p class="text-gray-400"><?php echo htmlspecialchars($child['email']); ?></p>
        </div>
        
        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-800">
            <nav class="flex gap-6">
                <button onclick="showTab('grades')" id="tab-grades" class="tab-btn py-3 px-1 border-b-2 border-blue-500 text-blue-400 font-medium">
                    Grades
                </button>
                <button onclick="showTab('attendance')" id="tab-attendance" class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-400 hover:text-white">
                    Attendance
                </button>
                <button onclick="showTab('assignments')" id="tab-assignments" class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-400 hover:text-white">
                    Upcoming Assignments
                </button>
            </nav>
        </div>
        
        <!-- Grades Tab -->
        <div id="content-grades" class="tab-content">
            <div class="card">
                <h2 class="text-xl font-bold text-white mb-6">Recent Grades</h2>
                <?php if (empty($grades)): ?>
                    <p class="text-gray-400 text-center py-8">No grades available yet</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($grades as $grade): ?>
                        <div class="p-4 bg-black/50 rounded-lg">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-white mb-1"><?php echo htmlspecialchars($grade['title']); ?></h3>
                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($grade['class_name'] . ' - ' . $grade['subject_name']); ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <?php echo date('M d, Y', strtotime($grade['graded_at'])); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-400">
                                        <?php echo $grade['marks_obtained']; ?>/<?php echo $grade['total_marks']; ?>
                                    </div>
                                    <p class="text-sm text-gray-400">
                                        <?php echo round(($grade['marks_obtained'] / $grade['total_marks']) * 100, 1); ?>%
                                    </p>
                                </div>
                            </div>
                            <?php if ($grade['feedback']): ?>
                            <div class="bg-blue-900/20 border border-blue-800 rounded-lg p-3 mt-3">
                                <p class="text-xs font-medium text-blue-300 mb-1">Teacher Feedback:</p>
                                <p class="text-sm text-gray-300"><?php echo htmlspecialchars($grade['feedback']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Attendance Tab -->
        <div id="content-attendance" class="tab-content hidden">
            <div class="card">
                <h2 class="text-xl font-bold text-white mb-6">Attendance Records</h2>
                <?php if (empty($attendanceRecords)): ?>
                    <p class="text-gray-400 text-center py-8">No attendance records yet</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-800">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Date</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Class</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Subject</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendanceRecords as $record): ?>
                                <tr class="border-b border-gray-800">
                                    <td class="py-3 px-4 text-sm text-white">
                                        <?php echo date('M d, Y', strtotime($record['date'])); ?>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-400">
                                        <?php echo htmlspecialchars($record['class_name']); ?>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-400">
                                        <?php echo htmlspecialchars($record['subject_name']); ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php
                                        $statusColors = [
                                            'present' => 'bg-green-900/30 text-green-300',
                                            'absent' => 'bg-red-900/30 text-red-300',
                                            'late' => 'bg-yellow-900/30 text-yellow-300',
                                            'excused' => 'bg-blue-900/30 text-blue-300'
                                        ];
                                        $color = $statusColors[$record['status']] ?? 'bg-gray-900/30 text-gray-300';
                                        ?>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $color; ?> capitalize">
                                            <?php echo htmlspecialchars($record['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Assignments Tab -->
        <div id="content-assignments" class="tab-content hidden">
            <div class="card">
                <h2 class="text-xl font-bold text-white mb-6">Upcoming Assignments</h2>
                <?php if (empty($upcomingAssignments)): ?>
                    <p class="text-gray-400 text-center py-8">No upcoming assignments</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($upcomingAssignments as $assignment): ?>
                        <div class="p-4 bg-black/50 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-white mb-2"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                    <p class="text-sm text-gray-400 mb-2"><?php echo htmlspecialchars($assignment['description']); ?></p>
                                    <div class="flex items-center gap-4 text-sm text-gray-400">
                                        <span><?php echo htmlspecialchars($assignment['class_name']); ?></span>
                                        <span><?php echo htmlspecialchars($assignment['subject_name']); ?></span>
                                        <span>Due: <?php echo date('M d, Y', strtotime($assignment['deadline'])); ?></span>
                                    </div>
                                </div>
                                <?php if ($assignment['submission_id']): ?>
                                    <span class="px-3 py-1 text-sm rounded-full bg-green-900/30 text-green-300">
                                        Submitted
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 text-sm rounded-full bg-yellow-900/30 text-yellow-300">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tab) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-400');
                el.classList.add('border-transparent', 'text-gray-400');
            });
            
            // Show selected content
            document.getElementById('content-' + tab).classList.remove('hidden');
            const btn = document.getElementById('tab-' + tab);
            btn.classList.remove('border-transparent', 'text-gray-400');
            btn.classList.add('border-blue-500', 'text-blue-400');
        }
    </script>
</body>
</html>
