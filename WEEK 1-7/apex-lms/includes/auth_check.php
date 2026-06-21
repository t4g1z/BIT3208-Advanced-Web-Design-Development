<?php
/**
 * Apex LMS - Global Multi-Role Session Access Gatekeeper Firewall
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verify general authentication layer presence
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    session_destroy();
    header("Location: /apex-lms/login.php?error=unauthorized_access_restriction");
    exit();
}

/**
 * Custom function to enforce specific role access restrictions on target files
 * @param array $permittedRoles List of roles allowed to view the page
 */
function restrictAccessTo(array $permittedRoles) {
    if (!in_array($_SESSION['role'], $permittedRoles)) {
        // Log unauthorized vector attempt internally and route to standard safe catalog interface
        header("Location: /apex-lms/views/catalog.php?error=insufficient_clearance_level");
        exit();
    }
}
?>