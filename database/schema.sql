-- Online Academic System Database Schema

CREATE DATABASE IF NOT EXISTS online_academic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE online_academic;

-- Users table (for both students and admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    roll_number VARCHAR(20) UNIQUE,
    class VARCHAR(20),
    phone VARCHAR(15),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_roll_number (roll_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    max_marks INT DEFAULT 100,
    pass_marks INT DEFAULT 40,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_subject_code (subject_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Marks table
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    marks_obtained INT NOT NULL,
    exam_type ENUM('midterm', 'final', 'quiz', 'assignment') DEFAULT 'final',
    exam_date DATE,
    remarks VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_mark (student_id, subject_id, exam_type, exam_date),
    INDEX idx_student (student_id),
    INDEX idx_subject (subject_id),
    INDEX idx_exam_type (exam_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, full_name, role, status) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@school.edu', 'System Administrator', 'admin', 'active');

-- Insert sample subjects
INSERT INTO subjects (subject_code, subject_name, max_marks, pass_marks, description) VALUES
('MATH101', 'Mathematics', 100, 40, 'Basic Mathematics'),
('SCI101', 'Science', 100, 40, 'General Science'),
('ENG101', 'English', 100, 40, 'English Language'),
('HIS101', 'History', 100, 40, 'World History'),
('GEO101', 'Geography', 100, 40, 'Physical Geography'),
('COMP101', 'Computer Science', 100, 40, 'Introduction to Computers');

-- Insert sample student (password: student123)
INSERT INTO users (username, password, email, full_name, role, roll_number, class, phone, status) 
VALUES ('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student1@school.edu', 'John Doe', 'student', 'R001', '10th Grade', '9876543210', 'active');

-- Insert sample marks
INSERT INTO marks (student_id, subject_id, marks_obtained, exam_type, exam_date, remarks, created_by) 
SELECT 
    (SELECT id FROM users WHERE roll_number = 'R001'),
    id,
    FLOOR(60 + RAND() * 40),
    'final',
    '2024-03-15',
    'Good performance',
    (SELECT id FROM users WHERE username = 'admin')
FROM subjects;
