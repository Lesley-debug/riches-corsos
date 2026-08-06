<?php
require_once __DIR__ . '/../inc/paths.php';

// Load .env if credentials not already in environment
if (getenv('GOOGLE_CLIENT_ID') === false) {
    $locations = [
        dirname($_SERVER['DOCUMENT_ROOT'] ?? ''),
        __DIR__ . '/../',
    ];
    foreach ($locations as $dir) {
        $path = rtrim($dir, '/') . '/.env';
        if ($path && file_exists($path) && is_readable($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                [$key, $value] = explode('=', $line, 2);
                putenv(trim($key) . '=' . trim($value));
            }
            break;
        }
    }
}

define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID')     ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI',  'https://' . $_SERVER['HTTP_HOST'] . site_url('/account/google-callback.php'));

define('FACEBOOK_APP_ID',      '');
define('FACEBOOK_APP_SECRET',  '');
define('FACEBOOK_REDIRECT_URI','');
