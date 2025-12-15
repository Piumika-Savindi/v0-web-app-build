<?php
require_once '../../config/init.php';
$required_role = 'teacher';
require_once '../../components/protect.php';

$user = getCurrentUser();
$assignmentId = intval($_GET['assignment_id'] ?? 0);

// Get assignment details
$stmt = $db->prepare("
    SELECT a.*, c.name as class_name, s.name as subject_name
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN subjects s ON a.subject_id = s.id
    WHERE a.id = :id AND a.teacher_id = :teacher_id
");
$stmt->execute(['id' => $assignmentId, 'teacher_id' => $user['id']]);
$assignment = $stmt->fetch();

if (!$assignment) {
    header('Location: assignments.php');
    exit;
}

// Get submissions
$stmt = $db->prepare("
    SELECT asub.*, u.first_name, u.last_name, u.email
    FROM assignment_submissions asub
    JOIN users u ON asub.student_id = u.id
    WHERE asub.assignment_id = :assignment_id
    ORDER BY asub.submitted_at DESC
");
$stmt->execute(['assignment_id' => $assignmentId]);
$submissions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions - School Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="min-h-screen bg-[#0a0a0a]">
    <?php include '../../components/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <!-- Fixed back link to use relative path -->
            <a href="assignments.php" class="text-blue-400 hover:text-blue-300 mb-4 inline-block">← Back to Assignments</a>
            <h1 class="text-3xl font-bold text-white mb-2"><?php echo htmlspecialchars($assignment['title']); ?></h1>
            <p class="text-gray-400"><?php echo htmlspecialchars($assignment['class_name'] . ' - ' . $assignment['subject_name']); ?></p>
        </div>
        
        <!-- Submissions List -->
        <div class="space-y-4">
            <?php if (empty($submissions)): ?>
                <div class="card text-center py-8">
                    <p class="text-gray-400">No submissions yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($submissions as $submission): ?>
                <div class="card">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-1">
                                <?php echo htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-400"><?php echo htmlspecialchars($submission['email']); ?></p>
                            <p class="text-sm text-gray-400 mt-2">
                                Submitted: <?php echo date('M d, Y h:i A', strtotime($submission['submitted_at'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <?php if ($submission['marks_obtained'] !== null): ?>
                                <div class="text-2xl font-bold text-green-400">
                                    <?php echo $submission['marks_obtained']; ?>/<?php echo $assignment['total_marks']; ?>
                                </div>
                            <?php else: ?>
                                <button onclick="showGradeModal(<?php echo $submission['id']; ?>, <?php echo $assignment['total_marks']; ?>)" 
                                        class="btn-primary">
                                    Grade Submission
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="bg-black/50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-300 whitespace-pre-wrap"><?php echo htmlspecialchars($submission['submission_text'] ?? 'No text submitted'); ?></p>
                    </div>
                    
                    <?php if ($submission['feedback']): ?>
                    <div class="bg-blue-900/20 border border-blue-800 rounded-lg p-4">
                        <p class="text-sm font-medium text-blue-300 mb-1">Feedback:</p>
                        <p class="text-sm text-gray-300"><?php echo htmlspecialchars($submission['feedback']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Grade Modal -->
    <div id="gradeModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-bold text-white mb-4">Grade Submission</h2>
            <form id="gradeForm">
                <input type="hidden" name="submission_id" id="submissionId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Marks Obtained</label>
                    <input type="number" name="marks_obtained" id="marksInput" required min="0" 
                           class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Out of <span id="totalMarks"></span> marks</p>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Feedback</label>
                    <textarea name="feedback" rows="4" 
                              class="w-full px-4 py-2 rounded-lg bg-black border border-gray-700 text-white focus:border-blue-500 focus:outline-none"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 btn-primary">Submit Grade</button>
                    <button type="button" onclick="hideGradeModal()" 
                            class="flex-1 px-4 py-2 text-gray-300 border border-gray-700 rounded-lg hover:border-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showGradeModal(submissionId, totalMarks) {
            document.getElementById('submissionId').value = submissionId;
            document.getElementById('totalMarks').textContent = totalMarks;
            document.getElementById('marksInput').max = totalMarks;
            document.getElementById('gradeModal').classList.remove('hidden');
        }
        
        function hideGradeModal() {
            document.getElementById('gradeModal').classList.add('hidden');
            document.getElementById('gradeForm').reset();
        }
        
        document.getElementById('gradeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../../api/teacher/grade-submission.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Submission graded successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to grade submission');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    </script>
</body>
</html>
