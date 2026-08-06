<?php
require_once __DIR__ . '/../../inc/security.php';
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin — Riches Corsos</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo1.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
    <style>
      .admin-mobile-bar { display: flex !important; background: #111827 !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; height: 56px !important; z-index: 9999 !important; align-items: center !important; gap: 14px !important; padding: 0 16px !important; }
      .admin-hamburger { display: flex !important; flex-direction: column !important; justify-content: center !important; gap: 5px !important; width: 44px !important; height: 44px !important; background: none !important; border: none !important; cursor: pointer !important; padding: 4px !important; }
      .admin-hamburger span { display: block !important; width: 26px !important; height: 3px !important; background: #ffffff !important; border-radius: 2px !important; transition: transform 0.25s, opacity 0.25s !important; }
      .admin-mobile-title { color: #c19a6b !important; font-weight: 700 !important; font-size: 1rem !important; }
      /* X animation when sidebar is open */
      body.sidebar-open .admin-hamburger span:nth-child(1) { transform: translateY(8px) rotate(45deg) !important; }
      body.sidebar-open .admin-hamburger span:nth-child(2) { opacity: 0 !important; }
      body.sidebar-open .admin-hamburger span:nth-child(3) { transform: translateY(-8px) rotate(-45deg) !important; }
      /* Push content below mobile bar */
      @media (max-width: 992px) { .admin-content { padding-top: 72px !important; } }
      @media (min-width: 993px) { .admin-mobile-bar { display: none !important; } }
    </style>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
</head>
<body>

<!-- Mobile top bar -->
<div class="admin-mobile-bar">
    <button class="admin-hamburger" id="adminHamburger" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>
    <span class="admin-mobile-title">Riches Corsos</span>
</div>
<div class="admin-sidebar-overlay" id="adminOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('adminHamburger');
    const overlay = document.getElementById('adminOverlay');
    const sidebar = document.querySelector('.admin-sidebar');
    function toggle(open) {
        sidebar?.classList.toggle('show', open);
        overlay?.classList.toggle('show', open);
        document.body.classList.toggle('sidebar-open', open);
    }
    btn?.addEventListener('click', () => toggle(!sidebar?.classList.contains('show')));
    overlay?.addEventListener('click', () => toggle(false));
});
</script>
