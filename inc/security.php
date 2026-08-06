<?php
// Security bootstrap: session hardening, security headers, CSRF + sanitization helpers.
// Safe to require_once from any entry point, in any order relative to session_start().
//
// NOTE: for the cookie flags below to actually take effect on the session cookie,
// this file should be required BEFORE session_start() is called. If session_start()
// already ran earlier in the request, we still start/keep the session and provide
// CSRF protection, we just can't retroactively change that cookie's flags.

if (session_status() === PHP_SESSION_NONE) {
    // Must be set before the session starts to affect the session cookie.
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 1 : 0);
    ini_set('session.cookie_samesite', 'Lax'); // Lax (not Strict) so OAuth redirects back from Google/Facebook keep the session
    session_start();
}

// Don't leak errors to visitors; still log them.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);

// Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://accounts.google.com https://connect.facebook.net https://cdn.tiny.cloud https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data: https:; frame-src https://accounts.google.com https://www.facebook.com; connect-src 'self' https://cdn.tiny.cloud;");
}

// CSRF Token Generation
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

// CSRF Token Validation
if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('csrfField')) {
    function csrfField()
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

// Call at the top of any POST handler. Kills the request with a 400 on failure.
if (!function_exists('requireValidCSRF')) {
    function requireValidCSRF()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            die('Security check failed. Please go back, refresh the page, and try again.');
        }
    }
}

// Input Sanitization
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data)
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}