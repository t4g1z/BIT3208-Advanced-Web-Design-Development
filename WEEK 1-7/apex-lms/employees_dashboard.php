<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/connection.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: views/catalog.php?error=insufficient_clearance_level");
    exit();
}

$feedbackMessage = "";
$feedbackClass = "";

// --- CREATE & UPDATE OPERATIONS ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveEmployee'])) {
    $payrollNo  = htmlspecialchars(trim($_POST['payrollNumber']));
    $fullName   = htmlspecialchars(trim($_POST['fullName']));
    $department = htmlspecialchars(trim($_POST['department']));
    $position   = htmlspecialchars(trim($_POST['position']));
    $salary     = (float)$_POST['salary'];
    $editId     = isset($_POST['employee_id']) ? (int)$_POST['employee_id'] : 0;

    if (!empty($payrollNo) && !empty($fullName) && $salary > 0) {
        try {
            if ($editId > 0) {
                $sql = "UPDATE employees SET payroll_number = :pnum, full_name = :fname, department = :dept, position = :pos, salary = :sal WHERE employee_id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':pnum' => $payrollNo, ':fname' => $fullName, ':dept' => $department, ':pos' => $position, ':sal' => $salary, ':id' => $editId]);
                $feedbackMessage = "Employee master file updated.";
            } else {
                $sql = "INSERT INTO employees (payroll_number, full_name, department, position, salary) VALUES (:pnum, :fname, :dept, :pos, :sal)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':pnum' => $payrollNo, ':fname' => $fullName, ':dept' => $department, ':pos' => $position, ':sal' => $salary]);
                $feedbackMessage = "Employee registry transaction set complete.";
            }
            $feedbackClass = "success";
        } catch (\PDOException $e) {
            $feedbackMessage = "Data Integrity Exception: Structural constraints block transaction.";
            $feedbackClass = "error";
        }
    }
}

// --- DELETE OPERATION ---
if (isset($_GET['action']) && $_GET['action'] === 'deleteEmployee' && isset($_GET['id'])) {
    $targetId = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM employees WHERE employee_id = :id")->execute([':id' => $targetId]);
        $feedbackMessage = "Record purged safely from corporate infrastructure matrix.";
        $feedbackClass = "success";
    } catch (\PDOException $e) {
        $feedbackMessage = "Cascade tracking rules blocked the operation execution.";
        $feedbackClass = "error";
    }
}

// --- BONUS: SEARCH FUNCTIONALITY ---
$searchEngineKeyword = isset($_GET['employeeSearchKey']) ? "%" . trim($_GET['employeeSearchKey']) . "%" : "";
if (!empty($searchEngineKeyword)) {
    $readQuery = "SELECT * FROM employees WHERE full_name LIKE :term OR payroll_number LIKE :term OR department LIKE :term ORDER BY employee_id DESC";
    $readStmt = $pdo->prepare($readQuery);
    $readStmt->execute([':term' => $searchEngineKeyword]);
    $employeesResultSet = $readStmt->fetchAll();
} else {
    $employeesResultSet = $pdo->query("SELECT * FROM employees ORDER BY employee_id DESC")->fetchAll();
}

// Fetch context for active single row update checks
$editData = null;
if (isset($_GET['action']) && $_GET['action'] === 'editEmployee' && isset($_GET['id'])) {
    $searchId = (int)$_GET['id'];
    $fetchStmt = $pdo->prepare("SELECT * FROM employees WHERE employee_id = :id LIMIT 1");
    $fetchStmt->execute([':id' => $searchId]);
    $editData = $fetchStmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apex LMS - Employee Corporate Matrix</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; display:flex; background:#f4f6f9; flex-wrap: wrap; }
        .sidebar { width:260px; background:#1A2B4C; color:white; min-height:100vh; padding:20px; box-sizing:border-box; }
        .sidebar h2 { margin-top:0; font-size:20px; border-bottom:1px solid #334466; padding-bottom:10px; }
        .sidebar a { color:#ccc; display:block; padding:12px; text-decoration:none; font-weight:bold; border-radius:4px; }
        .sidebar a.active { background:#008080; color:white; }
        .main-workspace { flex:1; padding:30px; box-sizing:border-box; min-width: 320px; }
        .data-table-container { background:white; padding:20px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-top:15px; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #eee; }
        th { background:#f8f9fa; color:#1A2B4C; }
        .btn-danger { background:#dc3545; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:12px; }
        .btn-edit { background:#008080; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:12px; margin-right:5px; }
        .feedback { padding:15px; border-radius:4px; margin-bottom:20px; font-weight:bold; }
        .feedback.success { background:#D4EDDA; color:#155724; border:1px solid #C3E6CB; }
        .feedback.error { background:#F8D7DA; color:#721C24; border:1px solid #F5C6CB; }
        @media(max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Apex Corporate</h2>
        <nav>
            <a href="index.php">Book Inventory</a>
            <a href="students_dashboard.php">Student Records</a>
            <a href="employees_dashboard.php" class="active">Employee System</a>
            <a href="login.php" style="color:#ff6b6b; margin-top:40px;">Logout</a>
        </nav>
    </div>

    <div class="main-workspace">
        <header style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:25px;">
            <div>
                <h1 style="color:#1A2B4C; margin:0;">Employee Records System</h1>
                <small style="color:#666;">Practical Task 3 Challenge Node</small>
            </div>
            <form method="GET" action="employees_dashboard.php" style="display:flex; gap:5px;">
                <input type="text" name="employeeSearchKey" placeholder="Search parameters..." style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo htmlspecialchars($_GET['employeeSearchKey'] ?? ''); ?>">
                <button type="submit" style="background:#1A2B4C; color:white; border:none; padding:8px 12px; border-radius:4px; cursor:pointer;">Query</button>
            </form>
        </header>

        <?php if (!empty($feedbackMessage)): ?>
            <div class="feedback <?php echo $feedbackClass; ?>">
                System Notification: <?php echo $feedbackMessage; ?>
            </div>
        <?php endif; ?>

        <section class="data-table-container">
            <h3 style="color:#1A2B4C; margin-top:0;"><?php echo $editData ? 'Modify Core Employee Parameters' : 'Register New Employee Resource'; ?></h3>
            <form id="employeeForm" action="employees_dashboard.php" method="POST">
                <?php if($editData): ?>
                    <input type="hidden" name="employee_id" value="<?php echo $editData['employee_id']; ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <input type="text" id="payrollNumber" name="payrollNumber" placeholder="Payroll ID (e.g. EMP-998)" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['payroll_number']) : ''; ?>">
                    <input type="text" id="fullName" name="fullName" placeholder="Full Legal Name" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['full_name']) : ''; ?>">
                    <input type="text" id="department" name="department" placeholder="Department Sector" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['department']) : ''; ?>">
                    <input type="text" id="position" name="position" placeholder="Role Assignment Title" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['position']) : ''; ?>">
                    <input type="number" id="salary" name="salary" placeholder="Salary Rate" step="0.01" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['salary']) : ''; ?>">
                </div>
                <button type="submit" name="saveEmployee" style="background:#008080; color:white; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer; margin-top:15px; width:100%;">
                    <?php echo $editData ? 'Commit Upstream Engine Variable State' : 'Submit Asset File Record'; ?>
                </button>
            </form>
        </section>

        <section class="data-table-container" style="overflow-x:auto;">
            <h3 style="color:#1A2B4C; margin-top:0;">Corporate Infrastructure Directories</h3>
            <table>
                <thead>
                    <tr>
                        <th>Payroll Reference ID</th>
                        <th>Employee Target Name</th>
                        <th>Department Sector</th>
                        <th>Assigned Title</th>
                        <th>Salary Scale</th>
                        <th>System Actions Mapping</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($employeesResultSet) === 0): ?>
                        <tr><td colspan="6" style="text-align:center; color:#999;">No dynamic search records parsed. System loop matches empty.</td></tr>
                    <?php else: ?>
                        <?php foreach ($employeesResultSet as $emp): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($emp['payroll_number']); ?></code></td>
                                <td style="font-weight:bold; color:#1A2B4C;"><?php echo htmlspecialchars($emp['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($emp['department']); ?></td>
                                <td><span style="background:#eee; padding:3px 6px; border-radius:3px; font-size:12px;"><?php echo htmlspecialchars($emp['position']); ?></span></td>
                                <td><strong>$<?php echo number_format($emp['salary'], 2); ?></strong></td>
                                <td>
                                    <a href="employees_dashboard.php?action=editEmployee&id=<?php echo $emp['employee_id']; ?>" class="btn-edit">Edit</a>
                                    <a href="employees_dashboard.php?action=deleteEmployee&id=<?php echo $emp['employee_id']; ?>" class="btn-danger" onclick="return confirm('Purge this employee structural reference track?');">Purge</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
            const salaryInput = document.getElementById('salary').value;
            const nameInput = document.getElementById('fullName').value.trim();

            if (parseFloat(salaryInput) <= 0) {
                e.preventDefault();
                alert("Validation Fault: Remuneration tracking variables must register higher than zero values.");
            }
            if (nameInput.length < 3) {
                e.preventDefault();
                alert("Validation Fault: Identified name arrays must meet a 3-character threshold length ruleset.");
            }
        });
    </script>
</body>
</html>