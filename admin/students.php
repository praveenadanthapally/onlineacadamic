<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pageTitle = 'Manage Students';
$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_student'])) {
        $id = $_POST['id'] ?? null;
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $fullName = trim($_POST['full_name']);
        $rollNumber = trim($_POST['roll_number']);
        $class = trim($_POST['class']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $status = $_POST['status'] ?? 'active';
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($fullName) || empty($rollNumber)) {
            $error = 'Please fill in all required fields.';
        } elseif (!isValidEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                if ($id) {
                    // Update existing student
                    $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, roll_number = ?, class = ?, phone = ?, address = ?, status = ? WHERE id = ? AND role = 'student'";
                    $params = [$username, $email, $fullName, $rollNumber, $class, $phone, $address, $status, $id];
                    
                    // Update password if provided
                    if (!empty($password)) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, roll_number = ?, class = ?, phone = ?, address = ?, status = ?, password = ? WHERE id = ? AND role = 'student'";
                        $params = [$username, $email, $fullName, $rollNumber, $class, $phone, $address, $status, $hashedPassword, $id];
                    }
                    
                    executeQuery($sql, $params);
                    $success = 'Student updated successfully.';
                } else {
                    // Create new student
                    if (empty($password)) {
                        $password = 'student123'; // Default password
                    }
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    executeQuery(
                        "INSERT INTO users (username, password, email, full_name, role, roll_number, class, phone, address, status) VALUES (?, ?, ?, ?, 'student', ?, ?, ?, ?, ?)",
                        [$username, $hashedPassword, $email, $fullName, $rollNumber, $class, $phone, $address, $status]
                    );
                    $success = 'Student created successfully. Default password: ' . $password;
                }
                
                if (empty($error)) {
                    setFlashMessage('success', $success);
                    header("Location: students.php");
                    exit();
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = 'Username, email, or roll number already exists.';
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
        executeQuery("DELETE FROM users WHERE id = ? AND role = 'student'", [$_GET['id']]);
        setFlashMessage('success', 'Student deleted successfully.');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Cannot delete student. They may have associated marks records.');
    }
    header("Location: students.php");
    exit();
}

// Get student for editing
$editStudent = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editStudent = fetchOne("SELECT * FROM users WHERE id = ? AND role = 'student'", [$_GET['id']]);
    if (!$editStudent) {
        setFlashMessage('error', 'Student not found.');
        header("Location: students.php");
        exit();
    }
}

// Get all students
$students = fetchAll("SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC");

include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-people me-2"></i>Manage Students
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
                    <?php echo $action === 'add' ? 'Add New Student' : 'Edit Student'; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?php echo $editStudent['id'] ?? ''; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" required 
                                   value="<?php echo htmlspecialchars($editStudent['username'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required 
                                   value="<?php echo htmlspecialchars($editStudent['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" required 
                                   value="<?php echo htmlspecialchars($editStudent['full_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Roll Number *</label>
                            <input type="text" class="form-control" name="roll_number" required 
                                   value="<?php echo htmlspecialchars($editStudent['roll_number'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class</label>
                            <input type="text" class="form-control" name="class" 
                                   value="<?php echo htmlspecialchars($editStudent['class'] ?? ''); ?>"
                                   placeholder="e.g., 10th Grade">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?php echo htmlspecialchars($editStudent['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($editStudent['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <?php echo $action === 'edit' ? '(Leave blank to keep current)' : '(Leave blank for default: student123)'; ?></label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo ($editStudent['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($editStudent['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="students.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" name="save_student" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Students List -->
<div class="row">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>All Students</h5>
                <div>
                    <input type="text" class="form-control form-control-sm d-inline-block w-auto me-2 table-search" 
                           data-table="studentsTable" placeholder="Search students...">
                    <a href="students.php?action=add" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Add Student
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($students)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-state-icon"></i>
                        <h4>No Students Found</h4>
                        <p class="text-muted">Start by adding your first student.</p>
                        <a href="students.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add Student
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Roll Number</th>
                                    <th>Class</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($student['full_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['username']); ?></td>
                                    <td><code><?php echo htmlspecialchars($student['roll_number']); ?></code></td>
                                    <td><?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $student['status']; ?>">
                                            <?php echo ucfirst($student['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="students.php?action=edit&id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="students.php?action=delete&id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger btn-delete" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this student?')">
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
