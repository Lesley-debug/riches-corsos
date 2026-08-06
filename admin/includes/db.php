<?php

// Load .env — check one level above public_html first (production),
// then fall back to the project root (local dev).
function loadEnvFile(): void {
    $locations = [
        dirname($_SERVER['DOCUMENT_ROOT'] ?? ''),  // one level above public_html (Hostinger)
        __DIR__ . '/../../',                        // project root (local dev)
    ];
    foreach ($locations as $dir) {
        $path = rtrim($dir, '/') . '/.env';
        if ($path && file_exists($path) && is_readable($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                [$key, $value] = explode('=', $line, 2);
                putenv(trim($key) . '=' . trim($value));
            }
            return;
        }
    }
}

if (getenv('DB_HOST') === false) {
    loadEnvFile();
}

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'riches_corsos';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    error_log('DB connection failed: ' . $conn->connect_error);
    // Don't die() — let pages render gracefully without DB sections
    $conn = null;
}
