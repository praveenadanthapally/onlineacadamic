<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pageTitle = 'Manage Marks';
$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Get all students and subjects for dropdowns
$students = getAllStudents();
$subjects = getAllSubjects();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_mark'])) {
        $id = $_POST['id'] ?? null;
        $studentId = intval($_POST['student_id']);
        $subjectId = intval($_POST['subject_id']);
        $marksObtained = intval($_POST['marks_obtained']);
        $examType = $_POST['exam_type'];
        $examDate = $_POST['exam_date'];
        $remarks = trim($_POST['remarks']);
        
        // Get max marks for the subject
        $subject = fetchOne("SELECT max_marks FROM subjects WHERE id = ?", [$subjectId]);
        $maxMarks = $subject ? $subject['max_marks'] : 100;
        
        // Validation
        if (empty($studentId) || empty($subjectId)) {
            $error = 'Please select both student and subject.';
        } elseif ($marksObtained < 0 || $marksObtained > $maxMarks) {
            $error = "Marks must be between 0 and {$maxMarks}.";
        } else {
            try {
                if ($id) {
                    // Update existing mark
                    executeQuery(
                        "UPDATE marks SET student_id = ?, subject_id = ?, marks_obtained = ?, exam_type = ?, exam_date = ?, remarks = ? WHERE id = ?",
                        [$studentId, $subjectId, $marksObtained, $examType, $examDate, $remarks, $id]
                    );
                    $success = 'Marks updated successfully.';
                } else {
                    // Create new mark
                    executeQuery(
                        "INSERT INTO marks (student_id, subject_id, marks_obtained, exam_type, exam_date, remarks, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [$studentId, $subjectId, $marksObtained, $examType, $examDate, $remarks, $_SESSION['user_id']]
                    );
                    $success = 'Marks added successfully.';
                }
                
                setFlashMessage('success', $success);
                header("Location: marks.php");
                exit();
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = 'Marks already exist for this student, subject, exam type, and date.';
                } else {
                    $error = 'An error occurred. Please try again.';
                }
            }
        }
    }
}

// Handle delete action
if ($action === 'delete' && isset($_GET['id'])) {
    executeQuery("DELETE FROM marks WHERE id = ?", [$_GET['id']]);
    setFlashMessage('success', 'Marks deleted successfully.');
    header("Location: marks.php");
    exit();
}

// Get mark for editing
$editMark = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editMark = fetchOne("SELECT * FROM marks WHERE id = ?", [$_GET['id']]);
    if (!$editMark) {
        setFlashMessage('error', 'Marks record not found.');
        header("Location: marks.php");
        exit();
    }
}

// Get all marks with student and subject details
$marks = fetchAll(
    "SELECT m.*, u.full_name as student_name, u.roll_number, s.subject_name, s.subject_code, s.max_marks, s.pass_marks 
     FROM marks m 
     JOIN users u ON m.student_id = u.id 
     JOIN subjects s ON m.subject_id = s.id 
     ORDER BY m.created_at DESC"
);

include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-clipboard-data me-2"></i>Manage Marks
        </h2>
    </div>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<!-- Add/Edit Form -->
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $action === 'add' ? 'plus-lg' : 'pencil'; ?> me-2"></i>
                    <?php echo $action === 'add' ? 'Add New Marks' : 'Edit Marks'; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?php echo $editMark['id'] ?? ''; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student *</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>" 
                                    <?php echo ($editMark['student_id'] ?? '') == $student['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($student['full_name'] . ' (' . $student['roll_number'] . ')'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subject *</label>
                            <select class="form-select" name="subject_id" required>
                                <option value="">Select Subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>" 
                                    data-max-marks="<?php echo $subject['max_marks']; ?>"
                                    <?php echo ($editMark['subject_id'] ?? '') == $subject['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subject['subject_name'] . ' (' . $subject['subject_code'] . ')'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Marks Obtained *</label>
                            <input type="number" class="form-control marks-input" name="marks_obtained" required 
                                   min="0" id="marksObtained"
                                   value="<?php echo $editMark['marks_obtained'] ?? ''; ?>">
                            <small class="text-muted">Max marks: <span id="maxMarksDisplay">100</span></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Exam Type *</label>
                            <select class="form-select" name="exam_type" required>
                                <option value="midterm" <?php echo ($editMark['exam_type'] ?? '') === 'midterm' ? 'selected' : ''; ?>>Midterm</option>
                                <option value="final" <?php echo ($editMark['exam_type'] ?? 'final') === 'final' ? 'selected' : ''; ?>>Final</option>
                                <option value="quiz" <?php echo ($editMark['exam_type'] ?? '') === 'quiz' ? 'selected' : ''; ?>>Quiz</option>
                                <option value="assignment" <?php echo ($editMark['exam_type'] ?? '') === 'assignment' ? 'selected' : ''; ?>>Assignment</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Exam Date *</label>
                            <input type="date" class="form-control" name="exam_date" required 
                                   value="<?php echo $editMark['exam_date'] ?? date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="2"><?php echo htmlspecialchars($editMark['remarks'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="marks.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" name="save_mark" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Marks
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Update max marks display when subject changes
document.querySelector('select[name="subject_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const maxMarks = selectedOption.getAttribute('data-max-marks') || 100;
    document.getElementById('maxMarksDisplay').textContent = maxMarks;
    document.getElementById('marksObtained').setAttribute('max', maxMarks);
});
</script>

<?php else: ?>
<!-- Marks List -->
<div class="row">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>All Marks</h5>
                <div>
                    <input type="text" class="form-control form-control-sm d-inline-block w-auto me-2 table-search" 
                           data-table="marksTable" placeholder="Search marks...">
                    <a href="marks.php?action=add" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Add Marks
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($marks)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-state-icon"></i>
                        <h4>No Marks Found</h4>
                        <p class="text-muted">Start by adding marks for students.</p>
                        <a href="marks.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add Marks
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="marksTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th class="text-center">Marks</th>
                                    <th class="text-center">Grade</th>
                                    <th>Exam Type</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($marks as $mark): 
                                    $grade = calculateGrade($mark['marks_obtained'], $mark['max_marks']);
                                    $passed = isPassed($mark['marks_obtained'], $mark['pass_marks']);
                                ?>
                                <tr>
                                    <td><?php echo $mark['id']; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($mark['student_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($mark['roll_number']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($mark['subject_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($mark['subject_code']); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $mark['marks_obtained']; ?> / <?php echo $mark['max_marks']; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $grade)); ?>">
                                            <?php echo $grade; ?>
                                        </span>
                                    </td>
                                    <td><?php echo ucfirst($mark['exam_type']); ?></td>
                                    <td><?php echo formatDate($mark['exam_date']); ?></td>
                                    <td class="text-center">
                                        <a href="marks.php?action=edit&id=<?php echo $mark['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="marks.php?action=delete&id=<?php echo $mark['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger btn-delete" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete these marks?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
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
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
