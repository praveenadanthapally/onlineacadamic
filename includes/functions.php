<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is student
 * @return bool
 */
function isStudent() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to access this page.";
        header("Location: /login.php");
        exit();
    }
}

/**
 * Require admin access
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = "You don't have permission to access this page.";
        header("Location: /student/dashboard.php");
        exit();
    }
}

/**
 * Require student access
 */
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        $_SESSION['error'] = "You don't have permission to access this page.";
        header("Location: /admin/dashboard.php");
        exit();
    }
}

/**
 * Redirect based on role
 */
function redirectBasedOnRole() {
    if (isAdmin()) {
        header("Location: /admin/dashboard.php");
    } else {
        header("Location: /student/dashboard.php");
    }
    exit();
}

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ][$flash['type']] ?? 'alert-info';
        
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Sanitize input
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get current user data
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * Calculate grade based on marks
 * @param int $marks
 * @param int $maxMarks
 * @return string
 */
function calculateGrade($marks, $maxMarks = 100) {
    $percentage = ($marks / $maxMarks) * 100;
    
    if ($percentage >= 90) return 'A+';
    if ($percentage >= 80) return 'A';
    if ($percentage >= 70) return 'B+';
    if ($percentage >= 60) return 'B';
    if ($percentage >= 50) return 'C';
    if ($percentage >= 40) return 'D';
    return 'F';
}

/**
 * Check if student passed
 * @param int $marks
 * @param int $passMarks
 * @return bool
 */
function isPassed($marks, $passMarks) {
    return $marks >= $passMarks;
}

/**
 * Format date
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get student marks with subject details
 * @param int $studentId
 * @return array
 */
function getStudentMarks($studentId) {
    return fetchAll(
        "SELECT m.*, s.subject_name, s.subject_code, s.max_marks, s.pass_marks 
         FROM marks m 
         JOIN subjects s ON m.subject_id = s.id 
         WHERE m.student_id = ? 
         ORDER BY s.subject_name",
        [$studentId]
    );
}

/**
 * Get all students
 * @return array
 */
function getAllStudents() {
    return fetchAll("SELECT * FROM users WHERE role = 'student' ORDER BY full_name");
}

/**
 * Get all subjects
 * @return array
 */
function getAllSubjects() {
    return fetchAll("SELECT * FROM subjects WHERE status = 'active' ORDER BY subject_name");
}

/**
 * Get student by roll number
 * @param string $rollNumber
 * @return array|false
 */
function getStudentByRollNumber($rollNumber) {
    return fetchOne("SELECT * FROM users WHERE roll_number = ? AND role = 'student'", [$rollNumber]);
}
