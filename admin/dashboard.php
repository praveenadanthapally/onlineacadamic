<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pageTitle = 'Admin Dashboard';

// Get statistics
$totalStudents = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'student'")['count'];
$activeStudents = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'student' AND status = 'active'")['count'];
$totalSubjects = fetchOne("SELECT COUNT(*) as count FROM subjects")['count'];
$activeSubjects = fetchOne("SELECT COUNT(*) as count FROM subjects WHERE status = 'active'")['count'];
$totalMarks = fetchOne("SELECT COUNT(*) as count FROM marks")['count'];

// Get recent students
$recentStudents = fetchAll("SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC LIMIT 5");

// Get recent marks entries
$recentMarks = fetchAll(
    "SELECT m.*, u.full_name as student_name, s.subject_name 
     FROM marks m 
     JOIN users u ON m.student_id = u.id 
     JOIN subjects s ON m.subject_id = s.id 
     ORDER BY m.created_at DESC LIMIT 5"
);

include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
        </h2>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Total Students</p>
                    <h3 class="stat-number"><?php echo $totalStudents; ?></h3>
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i><?php echo $activeStudents; ?> Active</small>
                </div>
                <i class="bi bi-people stat-icon text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Total Subjects</p>
                    <h3 class="stat-number"><?php echo $totalSubjects; ?></h3>
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i><?php echo $activeSubjects; ?> Active</small>
                </div>
                <i class="bi bi-book stat-icon text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Marks Entries</p>
                    <h3 class="stat-number"><?php echo $totalMarks; ?></h3>
                    <small class="text-muted">Total records</small>
                </div>
                <i class="bi bi-clipboard-data stat-icon text-info"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Quick Actions</p>
                    <div class="mt-2">
                        <a href="students.php?action=add" class="btn btn-sm btn-outline-primary mb-1 d-block">
                            <i class="bi bi-plus-lg me-1"></i>Add Student
                        </a>
                        <a href="marks.php?action=add" class="btn btn-sm btn-outline-success d-block">
                            <i class="bi bi-plus-lg me-1"></i>Add Marks
                        </a>
                    </div>
                </div>
                <i class="bi bi-lightning stat-icon text-warning"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Students -->
    <div class="col-md-6 mb-4">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Recent Students</h5>
                <a href="students.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentStudents)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No students found
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Roll No</th>
                                    <th>Class</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentStudents as $student): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($student['full_name']); ?>
                                        </div>
                                    </td>
                                    <td><code><?php echo htmlspecialchars($student['roll_number']); ?></code></td>
                                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $student['status']; ?>">
                                            <?php echo ucfirst($student['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Marks -->
    <div class="col-md-6 mb-4">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Recent Marks Entries</h5>
                <a href="marks.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentMarks)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No marks entries found
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th class="text-center">Marks</th>
                                    <th class="text-center">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMarks as $mark): 
                                    $grade = calculateGrade($mark['marks_obtained'], $mark['max_marks'] ?? 100);
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($mark['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($mark['subject_name']); ?></td>
                                    <td class="text-center fw-bold"><?php echo $mark['marks_obtained']; ?></td>
                                    <td class="text-center">
                                        <span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $grade)); ?>">
                                            <?php echo $grade; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="students.php" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-people fs-3 d-block mb-2"></i>
                            Manage Students
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="subjects.php" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-book fs-3 d-block mb-2"></i>
                            Manage Subjects
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="marks.php" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-clipboard-data fs-3 d-block mb-2"></i>
                            Manage Marks
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="marks.php?action=add" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-plus-circle fs-3 d-block mb-2"></i>
                            Add New Marks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
