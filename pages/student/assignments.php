<?php
require_once '../../config/init.php';
$required_role = 'student';
require_once '../../components/protect.php';

$user = getCurrentUser();

// Get all assignments
$stmt = $db->prepare("
    SELECT a.*, c.name as class_name, s.name as subject_name,
           asub.id as submission_id, asub.submitted_at, asub.marks_obtained
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    JOIN student_enrollments se ON c.id = se.class_id
    LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = :student_id
    WHERE se.student_id = :student_id
    ORDER BY a.deadline DESC
");
$stmt->execute(['student_id' => $user['id']]);
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">My Assignments</h1>
            <p class="text-gray-400">View and submit your assignments</p>
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
                            <span>Total Marks: <?php echo $assignment['total_marks']; ?></span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <?php if ($assignment['submission_id']): ?>
                            <span class="px-3 py-1 text-sm rounded-full bg-green-900/30 text-green-300">
                                Submitted
                            </span>
                            <?php if ($assignment['marks_obtained'] !== null): ?>
                                <span class="text-lg font-bold text-blue-400">
                                    <?php echo $assignment['marks_obtained']; ?>/<?php echo $assignment['total_marks']; ?>
                                </span>
                            <?php endif; ?>
                        <?php elseif (strtotime($assignment['deadline']) < time()): ?>
                            <span class="px-3 py-1 text-sm rounded-full bg-red-900/30 text-red-300">
                                Overdue
                            </span>
                        <?php else: ?>
                            <a href="/pages/student/submit-assignment.php?id=<?php echo $assignment['id']; ?>" 
                               class="btn-primary">
                                Submit Assignment
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
