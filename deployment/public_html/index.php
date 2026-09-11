<?php

/**
 * Hostinger deployment bootstrap.
 *
 * This file lives at public_html/index.php.
 * It forwards all requests into the Laravel application's public/index.php
 * which lives at public_html/riches-corsos/public/index.php.
 *
 * DO NOT put this file inside the Laravel project itself — it belongs
 * one level up, directly inside public_html/.
 */

// Absolute path to the Laravel project's public directory.
// Adjust 'riches-corsos' if you upload the project under a different folder name.
define('LARAVEL_PUBLIC_PATH', __DIR__ . '/riches-corsos/public');

// Make Laravel's public folder look like the real document root.
$_SERVER['DOCUMENT_ROOT']  = LARAVEL_PUBLIC_PATH;
$_SERVER['SCRIPT_FILENAME'] = LARAVEL_PUBLIC_PATH . '/index.php';

// Hand off to Laravel.
chdir(LARAVEL_PUBLIC_PATH);
require LARAVEL_PUBLIC_PATH . '/index.php';
