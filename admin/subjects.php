<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pageTitle = 'Manage Subjects';
$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_subject'])) {
        $id = $_POST['id'] ?? null;
        $subjectCode = trim($_POST['subject_code']);
        $subjectName = trim($_POST['subject_name']);
        $maxMarks = intval($_POST['max_marks']);
        $passMarks = intval($_POST['pass_marks']);
        $description = trim($_POST['description']);
        $status = $_POST['status'] ?? 'active';
        
        // Validation
        if (empty($subjectCode) || empty($subjectName)) {
            $error = 'Please fill in all required fields.';
        } elseif ($maxMarks <= 0) {
            $error = 'Maximum marks must be greater than 0.';
        } elseif ($passMarks < 0 || $passMarks > $maxMarks) {
            $error = 'Pass marks must be between 0 and maximum marks.';
        } else {
            try {
                if ($id) {
                    // Update existing subject
                    executeQuery(
                        "UPDATE subjects SET subject_code = ?, subject_name = ?, max_marks = ?, pass_marks = ?, description = ?, status = ? WHERE id = ?",
                        [$subjectCode, $subjectName, $maxMarks, $passMarks, $description, $status, $id]
                    );
                    $success = 'Subject updated successfully.';
                } else {
                    // Create new subject
                    executeQuery(
                        "INSERT INTO subjects (subject_code, subject_name, max_marks, pass_marks, description, status) VALUES (?, ?, ?, ?, ?, ?)",
                        [$subjectCode, $subjectName, $maxMarks, $passMarks, $description, $status]
                    );
                    $success = 'Subject created successfully.';
                }
                
                setFlashMessage('success', $success);
                header("Location: subjects.php");
                exit();
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = 'Subject code already exists.';
                } else {
                    $error = 'An error occurred. Please try again.';
                }
            }
        }
    }
}

// Handle delete action
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        executeQuery("DELETE FROM subjects WHERE id = ?", [$_GET['id']]);
        setFlashMessage('success', 'Subject deleted successfully.');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Cannot delete subject. It may have associated marks records.');
    }
    header("Location: subjects.php");
    exit();
}

// Get subject for editing
$editSubject = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editSubject = fetchOne("SELECT * FROM subjects WHERE id = ?", [$_GET['id']]);
    if (!$editSubject) {
        setFlashMessage('error', 'Subject not found.');
        header("Location: subjects.php");
        exit();
    }
}

// Get all subjects
$subjects = fetchAll("SELECT * FROM subjects ORDER BY created_at DESC");

include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-book me-2"></i>Manage Subjects
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
                    <?php echo $action === 'add' ? 'Add New Subject' : 'Edit Subject'; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?php echo $editSubject['id'] ?? ''; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subject Code *</label>
                            <input type="text" class="form-control" name="subject_code" required 
                                   value="<?php echo htmlspecialchars($editSubject['subject_code'] ?? ''); ?>"
                                   placeholder="e.g., MATH101">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subject Name *</label>
                            <input type="text" class="form-control" name="subject_name" required 
                                   value="<?php echo htmlspecialchars($editSubject['subject_name'] ?? ''); ?>"
                                   placeholder="e.g., Mathematics">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maximum Marks *</label>
                            <input type="number" class="form-control" name="max_marks" required min="1"
                                   value="<?php echo $editSubject['max_marks'] ?? '100'; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Marks *</label>
                            <input type="number" class="form-control" name="pass_marks" required min="0"
                                   value="<?php echo $editSubject['pass_marks'] ?? '40'; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($editSubject['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" <?php echo ($editSubject['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($editSubject['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="subjects.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" name="save_subject" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Subjects List -->
<div class="row">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-book me-2"></i>All Subjects</h5>
                <div>
                    <input type="text" class="form-control form-control-sm d-inline-block w-auto me-2 table-search" 
                           data-table="subjectsTable" placeholder="Search subjects...">
                    <a href="subjects.php?action=add" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Add Subject
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($subjects)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-state-icon"></i>
                        <h4>No Subjects Found</h4>
                        <p class="text-muted">Start by adding your first subject.</p>
                        <a href="subjects.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add Subject
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="subjectsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">Max Marks</th>
                                    <th class="text-center">Pass Marks</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td><?php echo $subject['id']; ?></td>
                                    <td><code><?php echo htmlspecialchars($subject['subject_code']); ?></code></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td class="text-center"><?php echo $subject['max_marks']; ?></td>
                                    <td class="text-center"><?php echo $subject['pass_marks']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $subject['status']; ?>">
                                            <?php echo ucfirst($subject['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="subjects.php?action=edit&id=<?php echo $subject['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="subjects.php?action=delete&id=<?php echo $subject['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger btn-delete" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this subject?')">
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
