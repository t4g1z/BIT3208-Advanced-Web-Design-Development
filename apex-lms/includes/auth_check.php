<?php
/**
 * Apex LMS - Global Session Access Validation Gatekeeper
 * Intercepts unauthenticated browser trace vectors securely
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Block entry routes if verification parameters are absent
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    session_destroy();
    header("Location: /apex-lms/login.php?error=unauthorized_access_restriction");
    exit();
}
?>