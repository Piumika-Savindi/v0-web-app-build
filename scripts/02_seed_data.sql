-- Seed Data for School Management System

USE school_management;

-- Insert Admin User
INSERT INTO users (email, username, password, first_name, last_name, role) VALUES
('admin@school.edu', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'admin');
-- Password is 'password' (hashed with bcrypt)

-- Insert Teachers
INSERT INTO users (email, username, password, first_name, last_name, role) VALUES
('teacher1@school.edu', 'teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', 'teacher'),
('teacher2@school.edu', 'teacher2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Johnson', 'teacher'),
('teacher3@school.edu', 'teacher3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michael', 'Brown', 'teacher');

-- Insert Students
INSERT INTO users (email, username, password, first_name, last_name, role) VALUES
('student1@school.edu', 'student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma', 'Davis', 'student'),
('student2@school.edu', 'student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Oliver', 'Wilson', 'student'),
('student3@school.edu', 'student3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sophia', 'Martinez', 'student');

-- Insert Parents
INSERT INTO users (email, username, password, first_name, last_name, role) VALUES
('parent1@school.edu', 'parent1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert', 'Davis', 'parent'),
('parent2@school.edu', 'parent2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Linda', 'Wilson', 'parent');

-- Insert Classes
INSERT INTO classes (name, grade_level, academic_year) VALUES
('Grade 10 - A', 10, '2024-2025'),
('Grade 10 - B', 10, '2024-2025'),
('Grade 11 - A', 11, '2024-2025');

-- Insert Subjects
INSERT INTO subjects (name, code, description) VALUES
('Mathematics', 'MATH101', 'Advanced Mathematics'),
('Science', 'SCI101', 'General Science'),
('English', 'ENG101', 'English Language and Literature'),
('History', 'HIST101', 'World History');

-- Link Parents to Students
INSERT INTO parent_student_links (parent_id, student_id, relationship) VALUES
(8, 5, 'Father'),
(9, 6, 'Mother');

-- Enroll Students in Classes
INSERT INTO student_enrollments (student_id, class_id, enrollment_date) VALUES
(5, 1, '2024-09-01'),
(6, 1, '2024-09-01'),
(7, 2, '2024-09-01');

-- Assign Teachers to Classes
INSERT INTO teacher_assignments (teacher_id, class_id, subject_id) VALUES
(2, 1, 1),
(3, 1, 2),
(4, 2, 3);

-- Insert Sample Announcement
INSERT INTO announcements (title, content, created_by, target_role) VALUES
('Welcome to New Academic Year', 'Welcome everyone to the 2024-2025 academic year! We look forward to a successful year ahead.', 1, 'all');
