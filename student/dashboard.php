<?php
require_once __DIR__ . '/../includes/functions.php';
requireStudent();

$pageTitle = 'My Dashboard';
$user = getCurrentUser();
$marks = getStudentMarks($user['id']);

// Calculate statistics
$totalSubjects = count($marks);
$totalObtained = 0;
$totalMax = 0;
$passedSubjects = 0;

foreach ($marks as $mark) {
    $totalObtained += $mark['marks_obtained'];
    $totalMax += $mark['max_marks'];
    if (isPassed($mark['marks_obtained'], $mark['pass_marks'])) {
        $passedSubjects++;
    }
}

$overallPercentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
$overallGrade = calculateGrade($totalObtained, $totalMax);

include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-speedometer2 me-2"></i>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>
        </h2>
    </div>
</div>

<!-- Student Info Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-1 text-muted"><small>Roll Number</small></p>
                        <p class="fw-bold"><?php echo htmlspecialchars($user['roll_number'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1 text-muted"><small>Class</small></p>
                        <p class="fw-bold"><?php echo htmlspecialchars($user['class'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1 text-muted"><small>Email</small></p>
                        <p class="fw-bold"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1 text-muted"><small>Phone</small></p>
                        <p class="fw-bold"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Total Subjects</p>
                    <h3 class="stat-number"><?php echo $totalSubjects; ?></h3>
                </div>
                <i class="bi bi-book stat-icon text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Passed</p>
                    <h3 class="stat-number"><?php echo $passedSubjects; ?></h3>
                </div>
                <i class="bi bi-check-circle stat-icon text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Percentage</p>
                    <h3 class="stat-number"><?php echo $overallPercentage; ?>%</h3>
                </div>
                <i class="bi bi-percent stat-icon text-info"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="stat-label mb-1">Grade</p>
                    <h3 class="stat-number"><?php echo $overallGrade; ?></h3>
                </div>
                <i class="bi bi-award stat-icon text-warning"></i>
            </div>
        </div>
    </div>
</div>

<!-- Marks Table -->
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Academic Scores</h5>
                <button class="btn btn-outline-primary btn-sm btn-print">
                    <i class="bi bi-printer me-1"></i>Print Marksheet
                </button>
            </div>
            <div class="card-body p-0">
                <?php if (empty($marks)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-state-icon"></i>
                        <h4>No Marks Available</h4>
                        <p class="text-muted">Your marks have not been entered yet. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="marksTable">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">Max Marks</th>
                                    <th class="text-center">Pass Marks</th>
                                    <th class="text-center">Obtained</th>
                                    <th class="text-center">Percentage</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($marks as $mark): 
                                    $percentage = round(($mark['marks_obtained'] / $mark['max_marks']) * 100, 2);
                                    $grade = calculateGrade($mark['marks_obtained'], $mark['max_marks']);
                                    $passed = isPassed($mark['marks_obtained'], $mark['pass_marks']);
                                ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($mark['subject_code']); ?></code></td>
                                    <td><?php echo htmlspecialchars($mark['subject_name']); ?></td>
                                    <td class="text-center"><?php echo $mark['max_marks']; ?></td>
                                    <td class="text-center"><?php echo $mark['pass_marks']; ?></td>
                                    <td class="text-center fw-bold"><?php echo $mark['marks_obtained']; ?></td>
                                    <td class="text-center"><?php echo $percentage; ?>%</td>
                                    <td class="text-center">
                                        <span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $grade)); ?>">
                                            <?php echo $grade; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($passed): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Pass</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-lg me-1"></i>Fail</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr class="table-primary fw-bold">
                                    <td colspan="4" class="text-end">Overall Total:</td>
                                    <td class="text-center"><?php echo $totalObtained; ?> / <?php echo $totalMax; ?></td>
                                    <td class="text-center"><?php echo $overallPercentage; ?>%</td>
                                    <td class="text-center">
                                        <span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $overallGrade)); ?>">
                                            <?php echo $overallGrade; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($passedSubjects === $totalSubjects && $totalSubjects > 0): ?>
                                            <span class="badge bg-success">Passed All</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><?php echo $passedSubjects; ?>/<?php echo $totalSubjects; ?> Passed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Print Marksheet Section (Hidden by default, shown when printing) -->
<div class="d-none d-print-block mt-4">
    <div class="marksheet">
        <div class="marksheet-header">
            <h2>ONLINE ACADEMIC SYSTEM</h2>
            <h4>STUDENT MARKSHEET</h4>
        </div>
        <div class="marksheet-info">
            <div class="row">
                <div class="col-6">
                    <label>Student Name:</label>
                    <p><?php echo htmlspecialchars($user['full_name']); ?></p>
                </div>
                <div class="col-6">
                    <label>Roll Number:</label>
                    <p><?php echo htmlspecialchars($user['roll_number']); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label>Class:</label>
                    <p><?php echo htmlspecialchars($user['class']); ?></p>
                </div>
                <div class="col-6">
                    <label>Date:</label>
                    <p><?php echo date('F d, Y'); ?></p>
                </div>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th class="text-center">Max Marks</th>
                    <th class="text-center">Obtained</th>
                    <th class="text-center">Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($marks as $mark): 
                    $grade = calculateGrade($mark['marks_obtained'], $mark['max_marks']);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($mark['subject_name']); ?></td>
                    <td class="text-center"><?php echo $mark['max_marks']; ?></td>
                    <td class="text-center"><?php echo $mark['marks_obtained']; ?></td>
                    <td class="text-center"><?php echo $grade; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td>Total</td>
                    <td class="text-center"><?php echo $totalMax; ?></td>
                    <td class="text-center"><?php echo $totalObtained; ?></td>
                    <td class="text-center"><?php echo $overallGrade; ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="text-center mt-4">
            <p class="mb-0"><strong>Overall Percentage:</strong> <?php echo $overallPercentage; ?>%</p>
            <p class="mb-0"><strong>Result:</strong> 
                <?php if ($passedSubjects === $totalSubjects && $totalSubjects > 0): ?>
                    <span class="text-success">PASSED</span>
                <?php else: ?>
                    <span class="text-danger">NEEDS IMPROVEMENT</span>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
