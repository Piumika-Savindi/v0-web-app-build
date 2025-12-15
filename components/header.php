<?php
if (!isset($_SESSION)) {
    session_start();
}
$user = getCurrentUser();

$currentPath = $_SERVER['PHP_SELF'];
$depth = substr_count($currentPath, '/') - 1;
$basePath = str_repeat('../', $depth);
?>
<nav class="bg-[#1a1a1a] border-b border-[#2a2a2a] sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="<?php echo $basePath; ?>index.php" class="text-xl font-bold gradient-text">School MS</a>
                
                <?php if ($user): ?>
                <div class="hidden md:flex items-center gap-6">
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="<?php echo $basePath; ?>pages/admin/dashboard.php" class="text-gray-300 hover:text-white transition-colors">Dashboard</a>
                        <a href="<?php echo $basePath; ?>pages/admin/users.php" class="text-gray-300 hover:text-white transition-colors">Users</a>
                        <a href="<?php echo $basePath; ?>pages/admin/classes.php" class="text-gray-300 hover:text-white transition-colors">Classes</a>
                        <a href="<?php echo $basePath; ?>pages/messages.php" class="text-gray-300 hover:text-white transition-colors">Messages</a>
                        <a href="<?php echo $basePath; ?>pages/announcements.php" class="text-gray-300 hover:text-white transition-colors">Announcements</a>
                    <?php elseif ($user['role'] === 'teacher'): ?>
                        <a href="<?php echo $basePath; ?>pages/teacher/dashboard.php" class="text-gray-300 hover:text-white transition-colors">Dashboard</a>
                        <a href="<?php echo $basePath; ?>pages/teacher/assignments.php" class="text-gray-300 hover:text-white transition-colors">Assignments</a>
                        <a href="<?php echo $basePath; ?>pages/teacher/attendance.php" class="text-gray-300 hover:text-white transition-colors">Attendance</a>
                        <a href="<?php echo $basePath; ?>pages/messages.php" class="text-gray-300 hover:text-white transition-colors">Messages</a>
                        <a href="<?php echo $basePath; ?>pages/announcements.php" class="text-gray-300 hover:text-white transition-colors">Announcements</a>
                    <?php elseif ($user['role'] === 'student'): ?>
                        <a href="<?php echo $basePath; ?>pages/student/dashboard.php" class="text-gray-300 hover:text-white transition-colors">Dashboard</a>
                        <a href="<?php echo $basePath; ?>pages/student/assignments.php" class="text-gray-300 hover:text-white transition-colors">Assignments</a>
                        <a href="<?php echo $basePath; ?>pages/student/grades.php" class="text-gray-300 hover:text-white transition-colors">Grades</a>
                        <a href="<?php echo $basePath; ?>pages/messages.php" class="text-gray-300 hover:text-white transition-colors">Messages</a>
                        <a href="<?php echo $basePath; ?>pages/announcements.php" class="text-gray-300 hover:text-white transition-colors">Announcements</a>
                    <?php elseif ($user['role'] === 'parent'): ?>
                        <a href="<?php echo $basePath; ?>pages/parent/dashboard.php" class="text-gray-300 hover:text-white transition-colors">Dashboard</a>
                        <a href="<?php echo $basePath; ?>pages/messages.php" class="text-gray-300 hover:text-white transition-colors">Messages</a>
                        <a href="<?php echo $basePath; ?>pages/announcements.php" class="text-gray-300 hover:text-white transition-colors">Announcements</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($user): ?>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                    </div>
                    <div class="hidden md:block">
                        <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                        <div class="text-xs text-gray-400 capitalize"><?php echo htmlspecialchars($user['role']); ?></div>
                    </div>
                </div>
                <a href="<?php echo $basePath; ?>api/auth/logout.php" class="px-4 py-2 text-sm text-gray-300 hover:text-white border border-gray-700 rounded-lg hover:border-gray-600 transition-colors">
                    Logout
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
