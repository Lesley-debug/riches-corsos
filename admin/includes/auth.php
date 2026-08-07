<?php
// auth.php — admin session guard.
// security.php (which starts the session with hardened flags) is always
// required before this file, so we never call session_start() here.

if (session_status() === PHP_SESSION_NONE) {
    // Fallback: if somehow security.php wasn't loaded first, start safely.
    session_start();
}

// Check if logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: " . ADMIN_URL . "/login.php");
    exit();
}

// Session timeout: 2 hours of inactivity
$timeout = 2 * 60 * 60;

if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];
    if ($elapsed > $timeout) {
        session_unset();
        session_destroy();
        header("Location: " . ADMIN_URL . "/login.php?timeout=1");
        exit();
    }
}

$_SESSION['last_activity'] = time();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");