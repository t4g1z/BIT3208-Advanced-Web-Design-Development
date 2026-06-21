<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/connection.php';

$systemFeedbackMessage = "";
$feedbackStatusClass = "";

// INTERCEPT POST DATA PAYLOADS
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- PIPELINE A: ACCOUNT CREATION / REGISTRATION ---
    if (isset($_POST['executeRegistration'])) {
        $regCardId   = htmlspecialchars(trim($_POST['regLibraryCardId']));
        $regFullName = htmlspecialchars(trim($_POST['regFullName']));
        $regRole     = htmlspecialchars(trim($_POST['regUserRole'] ?? 'Student'));
        $regPassword = trim($_POST['regUserPassword']);
        
        if (empty($regCardId) || empty($regFullName) || empty($regPassword)) {
            $systemFeedbackMessage = "Registration Blocked: Missing mandatory properties.";
            $feedbackStatusClass = "error";
        } else {
            try {
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = :id");
                $checkStmt->execute([':id' => $regCardId]);
                
                if ($checkStmt->fetchColumn() > 0) {
                    $systemFeedbackMessage = "Registration Blocked: User identity reference code already logged.";
                    $feedbackStatusClass = "error";
                } else {
                    // SECURE ENCRYPTION hashing transformation processing
                    $hashedSecret = password_hash($regPassword, PASSWORD_DEFAULT);
                    
                    $insertSql = "INSERT INTO users (user_id, full_name, role, password) VALUES (:id, :name, :role, :pass)";
                    $pdo->prepare($insertSql)->execute([
                        ':id'   => $regCardId,
                        ':name' => $regFullName,
                        ':role' => $regRole,
                        ':pass' => $hashedSecret
                    ]);
                    
                    $systemFeedbackMessage = "Account set created successfully! Advance to authentication terminal.";
                    $feedbackStatusClass = "success";
                }
            } catch (\PDOException $e) {
                $systemFeedbackMessage = "Infrastructure Exception Trace Fault: Account initialization breakdown.";
                $feedbackStatusClass = "error";
            }
        }
    }
    
    // --- PIPELINE B: AUTHENTICATION CHECK / LOGIN ---
    if (isset($_POST['executeLoginHandshake'])) {
        $userId   = htmlspecialchars(trim($_POST['libraryCardId']));
        $password = trim($_POST['userPassword']);
        
        if (!empty($userId) && !empty($password)) {
            $fetchSql = "SELECT * FROM users WHERE user_id = :id LIMIT 1";
            $stmt = $pdo->prepare($fetchSql);
            $stmt->execute([':id' => $userId]);
            $userDataRecord = $stmt->fetch();
            
            if ($userDataRecord && password_verify($password, $userDataRecord['password'])) {
                // Initialize State Management Variable Arrays
                $_SESSION['user_id']   = $userDataRecord['user_id'];
                $_SESSION['full_name'] = $userDataRecord['full_name'];
                $_SESSION['role']      = $userDataRecord['role'];
                
                // MULTI-USER ROLE ROUTER REDIRECTION ENGINE
                switch ($_SESSION['role']) {
                    case 'Admin':
                        header("Location: index.php");
                        break;
                    case 'Lecturer':
                        header("Location: lecturers_dashboard.php");
                        break;
                    case 'Student':
                    default:
                        header("Location: views/catalog.php");
                        break;
                }
                exit();
            } else {
                $systemFeedbackMessage = "Authentication Failed: Mismatched identifier keys or invalid credentials.";
                $feedbackStatusClass = "error";
            }
        }
    }
}

// LOGOUT TRIGGER VIA GET INTERCEPTION
if (isset($_GET['action']) && $_GET['action'] === 'terminateSession') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    $systemFeedbackMessage = "Session terminated safely. Identity parameters wiped clean.";
    $feedbackStatusClass = "success";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apex LMS - Secure Gateway Access Terminal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-container { display: flex; width: 850px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .auth-panel { flex: 1; padding: 40px; box-sizing: border-box; }
        .panel-left { background: #1A2B4C; color: white; display: flex; flex-direction: column; justify-content: center; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; color: #555; font-size: 13px; font-weight: bold; }
        .input-group input, .input-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-action { background: #008080; color: white; border: none; padding: 12px; width: 100%; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .feedback { padding: 12px; border-radius: 4px; margin-bottom: 25px; font-size: 13px; font-weight: bold; text-align: center; }
        .feedback.success { background: #D4EDDA; color: #155724; border: 1px solid #C3E6CB; }
        .feedback.error { background: #F8D7DA; color: #721C24; border: 1px solid #F5C6CB; }
    </style>
</head>
<body>

    <div style="width: 100%; max-width: 850px; margin: 20px;">
        <?php if (!empty($systemFeedbackMessage)): ?>
            <div class="feedback <?php echo $feedbackStatusClass; ?>"><?php echo $systemFeedbackMessage; ?></div>
        <?php endif; ?>

        <div class="auth-container">
            <div class="auth-panel panel-left">
                <h2 style="margin-top:0; color:#008080;">Secure Identity Sign-In</h2>
                <p style="font-size:14px; color:#ccc;">Input credentials to securely interface with your assigned access tier.</p>
                
                <form id="portalLoginForm" action="login.php" method="POST" style="margin-top:20px;">
                    <div class="input-group">
                        <input type="text" id="libraryCardId" name="libraryCardId" placeholder="User ID Account Reference" required style="background:rgba(255,255,255,0.1); border:1px solid #334466; color:white;">
                    </div>
                    <div class="input-group">
                        <input type="password" id="userPassword" name="userPassword" placeholder="Account Password" required style="background:rgba(255,255,255,0.1); border:1px solid #334466; color:white;">
                    </div>
                    <button type="submit" name="executeLoginHandshake" class="btn-action">Authorize Token</button>
                </form>
            </div>

            <div class="auth-panel">
                <h2 style="margin-top:0; color:#1A2B4C;">Register System Profile</h2>
                <p style="font-size:14px; color:#666;">Set up custom account nodes across the three global server roles.</p>
                
                <form id="portalRegisterForm" action="login.php" method="POST" style="margin-top:20px;">
                    <div class="input-group">
                        <label>Select Authorization Target Tier Role</label>
                        <select name="regUserRole" id="regUserRole" required>
                            <option value="Student">Student Portal User</option>
                            <option value="Lecturer">Lecturer Assignment Portal</option>
                            <option value="Admin">Administrator Matrix Node</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Unique System Reference ID</label>
                        <input type="text" id="regLibraryCardId" name="regLibraryCardId" placeholder="e.g. STUD-990, LECT-124" required>
                    </div>
                    <div class="input-group">
                        <label>Full Structural Legal Name</label>
                        <input type="text" id="regFullName" name="regFullName" placeholder="Academic Legal Name" required>
                    </div>
                    <div class="input-group">
                        <label>Cryptographic Access Key Password</label>
                        <input type="password" id="regUserPassword" name="regUserPassword" placeholder="Minimum 6 characters required" required>
                    </div>
                    <button type="submit" name="executeRegistration" class="btn-action" style="background:#1A2B4C;">Initialize Node Credentials</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("portalRegisterForm").addEventListener("submit", function(e) {
                const pass = document.getElementById("regUserPassword").value;
                if (pass.length < 6) {
                    e.preventDefault();
                    alert("Security Policy Block: Passwords must contain at least 6 characters!");
                }
            });
        });
    </script>
</body>
</html>