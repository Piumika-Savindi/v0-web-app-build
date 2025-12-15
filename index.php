<?php require_once 'config/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --background: #0a0a0a;
            --surface: #1a1a1a;
            --border: #2a2a2a;
            --text: #e5e5e5;
            --text-muted: #a3a3a3;
        }
        
        body {
            background-color: var(--background);
            color: var(--text);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            transition: transform 0.2s, border-color 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
    </style>
</head>
<body class="min-h-screen">
    <?php if (isLoggedIn()): 
        $user = getCurrentUser();
        switch($user['role']) {
            case 'admin':
                redirect('pages/admin/dashboard.php');
                break;
            case 'teacher':
                redirect('pages/teacher/dashboard.php');
                break;
            case 'student':
                redirect('pages/student/dashboard.php');
                break;
            case 'parent':
                redirect('pages/parent/dashboard.php');
                break;
        }
    endif; ?>
    
    <!-- Hero Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-16">
                <h1 class="text-6xl font-bold mb-6">
                    <span class="gradient-text">School Management</span><br>
                    <span class="text-white">System</span>
                </h1>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                    A comprehensive platform for managing academic operations, assignments, assessments, and communication between teachers, students, and parents.
                </p>
            </div>
            
            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                <div class="card">
                    <div class="text-blue-500 text-3xl mb-4">👤</div>
                    <h3 class="text-lg font-semibold mb-2">Admin Panel</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Manage users, classes, subjects and system configuration</p>
                </div>
                
                <div class="card">
                    <div class="text-blue-500 text-3xl mb-4">📚</div>
                    <h3 class="text-lg font-semibold mb-2">Teacher Tools</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Create assignments, grade work, and track student progress</p>
                </div>
                
                <div class="card">
                    <div class="text-blue-500 text-3xl mb-4">🎓</div>
                    <h3 class="text-lg font-semibold mb-2">Student Access</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Submit work, take tests, and view grades and materials</p>
                </div>
                
                <div class="card">
                    <div class="text-blue-500 text-3xl mb-4">👨‍👩‍👧‍👦</div>
                    <h3 class="text-lg font-semibold mb-2">Parent Portal</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Monitor child's attendance, grades, and communicate with teachers</p>
                </div>
            </div>
            
            <!-- Login Section -->
            <div class="max-w-md mx-auto">
                <div class="card">
                    <h2 class="text-2xl font-bold mb-6 text-center">Sign In</h2>
                    <form id="loginForm" method="POST" action="api/auth/login.php">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2" for="username">Username or Email</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                                required
                            >
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-2" for="password">Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                                required
                            >
                        </div>
                        
                        <div id="errorMessage" class="mb-4 p-3 rounded-lg bg-red-900/30 border border-red-500 text-red-300 text-sm hidden"></div>
                        
                        <button type="submit" class="w-full btn-primary">
                            Sign In
                        </button>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <a href="#" class="text-sm text-blue-400 hover:text-blue-300">Forgot password?</a>
                    </div>
                </div>
                
                <!-- Demo Credentials -->
                <div class="mt-6 p-4 rounded-lg bg-gray-900/50 border border-gray-800">
                    <p class="text-xs text-gray-400 mb-2 font-semibold">Demo Credentials:</p>
                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                        <div>
                            <span class="font-medium text-gray-300">Admin:</span> admin
                        </div>
                        <div>
                            <span class="font-medium text-gray-300">Teacher:</span> teacher1
                        </div>
                        <div>
                            <span class="font-medium text-gray-300">Student:</span> student1
                        </div>
                        <div>
                            <span class="font-medium text-gray-300">Parent:</span> parent1
                        </div>
                        <div class="col-span-2 mt-1">
                            <span class="font-medium text-gray-300">Password for all:</span> password
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const errorDiv = document.getElementById('errorMessage');
            
            try {
                const response = await fetch('api/auth/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorDiv.textContent = data.message || 'Login failed';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
