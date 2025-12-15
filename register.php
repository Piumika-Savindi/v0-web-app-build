<?php require_once 'config/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - School Management System</title>
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
    
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold mb-3">
                    <span class="gradient-text">Create Account</span>
                </h1>
                <p class="text-gray-400">Join the School Management System</p>
            </div>
            
            <!-- Registration Form -->
            <div class="card">
                <form id="registerForm" method="POST" action="api/auth/register.php">
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" for="first_name">First Name</label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                                required
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2" for="last_name">Last Name</label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                                required
                            >
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                            required
                        >
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" for="username">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                            required
                            minlength="3"
                        >
                        <p class="text-xs text-gray-500 mt-1">Minimum 3 characters</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                            required
                            minlength="6"
                        >
                        <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" for="confirm_password">Confirm Password</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                            required
                        >
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2" for="role">Register As</label>
                        <select 
                            id="role" 
                            name="role" 
                            class="w-full px-4 py-3 rounded-lg bg-black border border-gray-700 focus:border-blue-500 focus:outline-none transition-colors"
                            required
                        >
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="parent">Parent</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Admin accounts must be created by an existing admin</p>
                    </div>
                    
                    <div id="errorMessage" class="mb-4 p-3 rounded-lg bg-red-900/30 border border-red-500 text-red-300 text-sm hidden"></div>
                    <div id="successMessage" class="mb-4 p-3 rounded-lg bg-green-900/30 border border-green-500 text-green-300 text-sm hidden"></div>
                    
                    <button type="submit" class="w-full btn-primary">
                        Create Account
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-400">
                        Already have an account? 
                        <a href="index.php" class="text-blue-400 hover:text-blue-300 font-medium">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorDiv = document.getElementById('errorMessage');
            const successDiv = document.getElementById('successMessage');
            
            // Hide previous messages
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            
            // Validate passwords match
            if (password !== confirmPassword) {
                errorDiv.textContent = 'Passwords do not match';
                errorDiv.classList.remove('hidden');
                return;
            }
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    successDiv.textContent = data.message;
                    successDiv.classList.remove('hidden');
                    
                    // Reset form
                    this.reset();
                    
                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    errorDiv.textContent = data.message || 'Registration failed';
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
