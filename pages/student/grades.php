<?php
require_once '../../config/init.php';
$required_role = 'student';
require_once '../../components/protect.php';

$user = getCurrentUser();

// Get all graded assignments
$stmt = $db->prepare("
    SELECT a.title, a.total_marks, asub.marks_obtained, asub.feedback, asub.graded_at,
           c.name as class_name, s.name as subject_name
    FROM assignment_submissions asub
    JOIN assignments a ON asub.assignment_id = a.id
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    WHERE asub.student_id = :student_id AND asub.marks_obtained IS NOT NULL
    ORDER BY asub.graded_at DESC
");
$stmt->execute(['student_id' => $user['id']]);
$grades = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">My Grades</h1>
            <p class="text-gray-400">View your graded assignments and feedback</p>
        </div>
        
        <?php if (empty($grades)): ?>
            <div class="card text-center py-12">
                <p class="text-gray-400">No grades available yet</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($grades as $grade): ?>
                <div class="card">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-2"><?php echo htmlspecialchars($grade['title']); ?></h3>
                            <p class="text-sm text-gray-400"><?php echo htmlspecialchars($grade['class_name'] . ' - ' . $grade['subject_name']); ?></p>
                            <p class="text-xs text-gray-500 mt-1">
                                Graded on: <?php echo date('M d, Y h:i A', strtotime($grade['graded_at'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-400 mb-1">
                                <?php echo $grade['marks_obtained']; ?>/<?php echo $grade['total_marks']; ?>
                            </div>
                            <p class="text-sm text-gray-400">
                                <?php echo round(($grade['marks_obtained'] / $grade['total_marks']) * 100, 1); ?>%
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($grade['feedback']): ?>
                    <div class="bg-blue-900/20 border border-blue-800 rounded-lg p-4">
                        <p class="text-sm font-medium text-blue-300 mb-2">Teacher Feedback:</p>
                        <p class="text-sm text-gray-300"><?php echo htmlspecialchars($grade['feedback']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
