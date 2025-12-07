<?php
session_start();

require_once __DIR__ . '/../public/db.php'; 
require_once __DIR__ . '/../classes/Admin.php'; 

if (!isset($_SESSION['admin_id'])) {
    header("Location: home.php");
    exit();
}

$employees = [];
$attendance = [];
$db_error = null;

if (isset($conn) && !$conn->connect_error) {
    $admin = new Admin($conn); 
    $employees = $admin->getAllEmployees();
    $attendance = $admin->getAllAttendance();
} else {
    $db_error = "Error: Database connection failed.";
    error_log($db_error);
}

require_once __DIR__ . '/../includes/admin_header.php'; 
?>

<?php if (isset($db_error)): ?>
        <div class="alert alert-danger" role="alert"><?= $db_error ?></div>
<?php endif; ?>
    
<div id="generalResponse" class="response-message"></div>

<div class="row mb-5">
    <div class="col-md-6 mb-3 mb-md-0">
        <div class="dashboard-card">
            <p class="card-title">Total Employees Registered</p>
            <div class="card-value"><?= count($employees) ?></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dashboard-card">
            <p class="card-title">Total Attendance Records</p>
            <div class="card-value"><?= count($attendance) ?></div>
        </div>
    </div>
</div>
    
<ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees-tab-pane" type="button" role="tab" aria-controls="employees-tab-pane" aria-selected="true">Employee Management</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance-tab-pane" type="button" role="tab" aria-controls="attendance-tab-pane" aria-selected="false">Attendance Records</button>
    </li>
</ul>

<div class="tab-content" id="adminTabsContent">
    
    <div class="tab-pane fade show active" id="employees-tab-pane" role="tabpanel" aria-labelledby="employees-tab" tabindex="0">
        <h3>Registered Employees</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="employeeTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees)): ?>
                        <?php foreach($employees as $e): ?>
                        <tr data-employee-id="<?= $e['employee_id'] ?>">
                            <td><?= $e['employee_id'] ?></td>
                            <td><?= htmlspecialchars($e['first_name'].' '.$e['last_name']) ?></td>
                            <td><?= htmlspecialchars($e['department_name']) ?></td>
                            <td><?= htmlspecialchars($e['email']) ?></td>
                            <td><?= htmlspecialchars($e['contact_number']) ?></td>
                            <td>
                                <button 
                                    class="btn btn-sm btn-danger delete-employee-btn" 
                                    data-id="<?= $e['employee_id'] ?>"
                                >
                                    Fire/Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-secondary">No employee data found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="attendance-tab-pane" role="tabpanel" aria-labelledby="attendance-tab" tabindex="0">
        <h3>Daily Time Logs</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr><th>Date</th><th>Name</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Remarks</th></tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance)): ?>
                        <?php foreach($attendance as $a): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i:s', strtotime($a['created_at'])) ?></td>
                            <td><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></td>
                            <td><?= $a['check_in'] ?></td>
                            <td><?= $a['check_out'] ?: '---' ?></td>
                            <td><?= $a['status'] ?></td>
                            <td><?= $a['remarks'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-secondary">No attendance data found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/../includes/admin_footer.php'; 
?>
