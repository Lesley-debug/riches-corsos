<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

session_unset();
session_regenerate_id(true);
session_destroy();

setcookie('user_email', '', time() - 3600, '/');

header('Location: ' . site_url('/account/login.php'));
exit;
