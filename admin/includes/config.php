<?php
require_once __DIR__ . '/../../inc/paths.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim(site_url('/'), '/'));
}

if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', site_url('/admin'));
}
