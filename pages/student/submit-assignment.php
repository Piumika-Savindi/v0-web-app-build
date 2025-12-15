<?php
require_once '../../config/init.php';
$required_role = 'student';
require_once '../../components/protect.php';

$user = getCurrentUser();
$assignmentId = intval($_GET['id'] ?? 0);

// Get assignment details
$stmt = $db->prepare("
    SELECT a.*, c.name as class_name, s.name as subject_name
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    JOIN student_enrollments se ON c.id = se.class_id
    WHERE a.id = :id AND se.student_id = :student_id
");
$stmt->execute(['id' => $assignmentId, 'student_id' => $user['id']]);
$assignment = $stmt->fetch();

if (!$assignment) {
    header('Location: assignments.php');
    exit;
}

// Check if already submitted
$stmt = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id = :assignment_id AND student_id = :student_id");
$stmt->execute(['assignment_id' => $assignmentId, 'student_id' => $user['id']]);
if ($stmt->fetch()) {
    header('Location: assignments.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Fixed back link to use relative path -->
            <a href="assignments.php" class="text-blue-400 hover:text-blue-300 mb-4 inline-block">← Back to Assignments</a>
            
            <div class="card mb-6">
                <h1 class="text-2xl font-bold text-white mb-4"><?php echo htmlspecialchars($assignment['title']); ?></h1>
                <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($assignment['description']); ?></p>
                <div class="flex items-center gap-6 text-sm text-gray-400">
                    <span><?php echo htmlspecialchars($assignment['class_name']); ?></span>
                    <span><?php echo htmlspecialchars($assignment['subject_name']); ?></span>
                    <span>Due: <?php echo date('M d, Y h:i A', strtotime($assignment['deadline'])); ?></span>
                    <span>Total Marks: <?php echo $assignment['total_marks']; ?></span>
                </div>
            </div>
            
            <div class="card">
                <h2 class="text-xl font-bold text-white mb-6">Submit Your Work</h2>
                <form id="submitForm">
                    <input type="hidden" name="assignment_id" value="<?php echo $assignmentId; ?>">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Your Submission</label>
                        <textarea name="submission_text" rows="10" required 
                                  placeholder="Type or paste your assignment text here..."
                                  class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full">
                        Submit Assignment
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('submitForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to submit this assignment? You cannot modify it after submission.')) {
                return;
            }
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../../api/student/submit-assignment.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Assignment submitted successfully');
                    window.location.href = 'assignments.php';
                } else {
                    alert(data.message || 'Failed to submit assignment');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    </script>
</body>
</html>
