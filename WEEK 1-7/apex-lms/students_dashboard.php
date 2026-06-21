<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/connection.php';

// Block non-administrators
if ($_SESSION['role'] !== 'Admin') {
    header("Location: views/catalog.php?error=insufficient_clearance_level");
    exit();
}

$feedbackMessage = "";
$feedbackClass = "";

// --- CREATE & UPDATE OPERATIONS ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveStudent'])) {
    $studentNum = htmlspecialchars(trim($_POST['studentNumber']));
    $fullName   = htmlspecialchars(trim($_POST['fullName']));
    $email      = htmlspecialchars(trim($_POST['email']));
    $course     = htmlspecialchars(trim($_POST['course']));
    $editId     = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;

    if (!empty($studentNum) && !empty($fullName) && !empty($email)) {
        try {
            if ($editId > 0) {
                // UPDATE OPERATION
                $sql = "UPDATE students SET student_number = :snum, full_name = :fname, email = :email, course = :course WHERE student_id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':snum' => $studentNum, ':fname' => $fullName, ':email' => $email, ':course' => $course, ':id' => $editId]);
                $feedbackMessage = "Student profile updated successfully.";
            } else {
                // CREATE OPERATION
                $sql = "INSERT INTO students (student_number, full_name, email, course) VALUES (:snum, :fname, :email, :course)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':snum' => $studentNum, ':fname' => $fullName, ':email' => $email, ':course' => $course]);
                $feedbackMessage = "New student logged securely into tracking database.";
            }
            $feedbackClass = "success";
        } catch (\PDOException $e) {
            $feedbackMessage = "Database Exception: Target value violation or duplicate registration parameter code.";
            $feedbackClass = "error";
        }
    }
}

// --- DELETE OPERATION ---
if (isset($_GET['action']) && $_GET['action'] === 'deleteStudent' && isset($_GET['id'])) {
    $targetId = (int)$_GET['id'];
    try {
        $deleteSql = "DELETE FROM students WHERE student_id = :id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute([':id' => $targetId]);
        $feedbackMessage = "Student profile removed from active directory tracking loops.";
        $feedbackClass = "success";
    } catch (\PDOException $e) {
        $feedbackMessage = "Data integrity constraint block prevents record purging.";
        $feedbackClass = "error";
    }
}

// --- FETCH FOR EDITING ---
$editData = null;
if (isset($_GET['action']) && $_GET['action'] === 'editStudent' && isset($_GET['id'])) {
    $searchId = (int)$_GET['id'];
    $fetchStmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :id LIMIT 1");
    $fetchStmt->execute([':id' => $searchId]);
    $editData = $fetchStmt->fetch();
}

// --- READ OPERATION ---
$studentsResultSet = $pdo->query("SELECT * FROM students ORDER BY student_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex LMS - Student Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; display:flex; background:#f4f6f9; }
        .sidebar { width:260px; background:#1A2B4C; color:white; min-height:100vh; padding:20px; box-sizing:border-box; }
        .sidebar h2 { margin-top:0; font-size:20px; border-bottom:1px solid #334466; padding-bottom:10px; }
        .sidebar a { color:#ccc; display:block; padding:12px; text-decoration:none; font-weight:bold; border-radius:4px; }
        .sidebar a.active { background:#008080; color:white; }
        .main-workspace { flex:1; padding:30px; box-sizing:border-box; }
        .data-table-container { background:white; padding:20px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #eee; }
        th { background:#f8f9fa; color:#1A2B4C; }
        .btn-danger { background:#dc3545; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:12px; margin-left:5px; }
        .btn-edit { background:#008080; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:12px; }
        .feedback { padding:15px; border-radius:4px; margin-bottom:20px; font-weight:bold; }
        .feedback.success { background:#D4EDDA; color:#155724; border:1px solid #C3E6CB; }
        .feedback.error { background:#F8D7DA; color:#721C24; border:1px solid #F5C6CB; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Apex Admin</h2>
        <nav>
            <a href="index.php">Book Inventory</a>
            <a href="students_dashboard.php" class="active">Student Records</a>
            <a href="employees_dashboard.php">Employee System</a>
            <a href="login.php" style="color:#ff6b6b; margin-top:40px;">Logout</a>
        </nav>
    </div>

    <div class="main-workspace">
        <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
            <div>
                <h1 style="color:#1A2B4C; margin:0;">Student System Portal</h1>
                <small style="color:#666;">Practical Task 1 Matrix</small>
            </div>
            <div style="background:#1A2B4C; color:white; padding:10px 20px; border-radius:4px; font-weight:bold;">
                Admin Terminal
            </div>
        </header>

        <?php if (!empty($feedbackMessage)): ?>
            <div class="feedback <?php echo $feedbackClass; ?>">
                Current Status: <?php echo $feedbackMessage; ?>
            </div>
        <?php endif; ?>

        <section class="data-table-container">
            <h3 style="color:#1A2B4C; margin-top:0;"><?php echo $editData ? 'Edit Student Entry' : 'Register New Student'; ?></h3>
            <form action="students_dashboard.php" method="POST" style="display:grid; grid-template-columns: repeat(4, 1fr) auto; gap:10px; margin-top:15px;">
                <?php if($editData): ?>
                    <input type="hidden" name="student_id" value="<?php echo $editData['student_id']; ?>">
                <?php endif; ?>
                <input type="text" name="studentNumber" placeholder="Student Number (e.g. STU-101)" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['student_number']) : ''; ?>">
                <input type="text" name="fullName" placeholder="Full Academic Name" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['full_name']) : ''; ?>">
                <input type="email" name="email" placeholder="Communication Email" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['email']) : ''; ?>">
                <input type="text" name="course" placeholder="Assigned Course" required style="padding:8px; border:1px solid #ccc; border-radius:4px;" value="<?php echo $editData ? htmlspecialchars($editData['course']) : ''; ?>">
                <button type="submit" name="saveStudent" style="background:#008080; color:white; border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer;">
                    <?php echo $editData ? 'Update Asset' : 'Commit Asset'; ?>
                </button>
            </form>
            <?php if($editData): ?>
                <a href="students_dashboard.php" style="display:inline-block; margin-top:10px; font-size:13px; color:#666;">Cancel Edit Action</a>
            <?php endif; ?>
        </section>

        <section class="data-table-container">
            <h3 style="color:#1A2B4C; margin-top:0;">Active Registered Student Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Reference</th>
                        <th>Student Number</th>
                        <th>Full Name</th>
                        <th>Email Routing</th>
                        <th>Course Module</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($studentsResultSet) === 0): ?>
                        <tr><td colspan="6" style="text-align:center; color:#999;">No real-time student registry records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($studentsResultSet as $student): ?>
                            <tr>
                                <td><code>REG-<?php echo $student['student_id']; ?></code></td>
                                <td style="font-weight:bold; color:#1A2B4C;"><?php echo htmlspecialchars($student['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><code><?php echo htmlspecialchars($student['email']); ?></code></td>
                                <td><?php echo htmlspecialchars($student['course']); ?></td>
                                <td>
                                    <a href="students_dashboard.php?action=editStudent&id=<?php echo $student['student_id']; ?>" class="btn-edit">Edit</a>
                                    <a href="students_dashboard.php?action=deleteStudent&id=<?php echo $student['student_id']; ?>" class="btn-danger" onclick="return confirm('Purge this record completely?');">Purge</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>