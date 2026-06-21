<?php
require_once __DIR__ . '/includes/auth_check.php';
// Restrict access strictly to the Lecturer role context
restrictAccessTo(['Lecturer']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex LMS - Lecturer Assignment Control Hub</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f4f6f9; color:#333; }
        .dashboard-container { max-width: 1100px; margin: 40px auto; padding: 20px; }
        .header-bar { background: #9b59b6; color: white; padding: 25px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; }
        .card-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px; }
        .metric-card { background: white; padding: 25px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #9b59b6; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <header class="header-bar">
            <div>
                <h1 style="margin:0;">Lecturer Academic Interface Workspace</h1>
                <small>Welcome back, Professor <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong> (ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?>)</small>
            </div>
            <a href="login.php?action=terminateSession" style="color: white; font-weight: bold; text-decoration: none; background: rgba(0,0,0,0.2); padding: 10px 15px; border-radius: 4px;">Exit Workspace</a>
        </header>

        <section class="card-grid">
            <div class="metric-card">
                <h3>Assignment Requirements</h3>
                <p style="font-size:28px; font-weight:bold; color:#9b59b6; margin:10px 0;">BIT3208</p>
                <small style="color:#777;">Advanced Web Design Modules</small>
            </div>
            <div class="metric-card">
                <h3>Pending Submissions</h3>
                <p style="font-size:28px; font-weight:bold; color:#2ecc71; margin:10px 0;">42 Pending</p>
                <small style="color:#777;">Awaiting Code Evaluation Loops</small>
            </div>
            <div class="metric-card">
                <h3>Active Students Logged</h3>
                <p style="font-size:28px; font-weight:bold; color:#e67e22; margin:10px 0;">18 Registered</p>
                <small style="color:#777;">Practical Task Systems Verification</small>
            </div>
        </section>

        <div style="background:white; padding:30px; border-radius:6px; margin-top:30px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
            <h2 style="color:#1A2B4C; margin-top:0;">Evaluation Parameters Engine Matrix</h2>
            <p>This module provides verified access to student grading metrics, database script uploads, and CRUD portfolio tracking parameters.</p>
            <div style="border-left: 4px solid #9b59b6; padding:15px; background:#f9f9f9; font-family:monospace;">
                Access Clearance Authorization Verified: TRUE <br>
                Security Mapping Mode: LECTURER_ISOLATION_NODE
            </div>
        </div>
    </div>

</body>
</html>