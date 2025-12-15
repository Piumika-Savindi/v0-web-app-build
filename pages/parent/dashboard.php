<?php
require_once '../../config/init.php';
$required_role = 'parent';
require_once '../../components/protect.php';

$user = getCurrentUser();

// Get linked children
$stmt = $db->prepare("
    SELECT u.id, u.first_name, u.last_name, u.email, psl.relationship
    FROM parent_student_links psl
    JOIN users u ON psl.student_id = u.id
    WHERE psl.parent_id = :parent_id
");
$stmt->execute(['parent_id' => $user['id']]);
$children = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Parent Dashboard</h1>
            <p class="text-gray-400">Monitor your children's academic progress</p>
        </div>
        
        <!-- Children Overview -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($children as $child): ?>
            <?php
                // Get child's stats
                $stmt = $db->prepare("
                    SELECT 
                        COUNT(DISTINCT a.id) as pending_assignments,
                        COUNT(DISTINCT CASE WHEN asub.marks_obtained IS NOT NULL THEN asub.id END) as graded_assignments,
                        AVG(CASE WHEN asub.marks_obtained IS NOT NULL THEN (asub.marks_obtained / a.total_marks * 100) END) as avg_grade
                    FROM assignments a
                    JOIN student_enrollments se ON a.class_id = se.class_id
                    LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = :student_id
                    WHERE se.student_id = :student_id AND a.deadline >= NOW()
                ");
                $stmt->execute(['student_id' => $child['id']]);
                $stats = $stmt->fetch();
                
                // Get attendance
                $stmt = $db->prepare("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
                    FROM attendance
                    WHERE student_id = :student_id AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ");
                $stmt->execute(['student_id' => $child['id']]);
                $attendance = $stmt->fetch();
                $attendanceRate = $attendance['total'] > 0 ? round(($attendance['present'] / $attendance['total']) * 100) : 0;
            ?>
            <div class="card">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-1">
                            <?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?>
                        </h3>
                        <p class="text-sm text-gray-400 capitalize"><?php echo htmlspecialchars($child['relationship']); ?></p>
                    </div>
                </div>
                
                <div class="space-y-3 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-400">Pending Assignments</span>
                        <span class="text-white font-semibold"><?php echo $stats['pending_assignments']; ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-400">Attendance (30 days)</span>
                        <span class="text-white font-semibold"><?php echo $attendanceRate; ?>%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-400">Average Grade</span>
                        <span class="text-white font-semibold">
                            <?php echo $stats['avg_grade'] ? round($stats['avg_grade'], 1) . '%' : 'N/A'; ?>
                        </span>
                    </div>
                </div>
                
                <a href="child-details.php?student_id=<?php echo $child['id']; ?>" 
                   class="block text-center btn-primary">
                    View Details
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
