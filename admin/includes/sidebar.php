<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$isActive = static function (string $needle) use ($currentPath): string {
    return str_contains($currentPath, $needle) ? 'active' : '';
};
?>

<aside class="admin-sidebar">

    <div class="admin-sidebar-logo">
        <span>RichesCorsors</span>
    </div>

    <a href="<?= ADMIN_URL ?>/dashboard.php" class="<?= $isActive('/admin/dashboard.php'); ?>">
        Dashboard
    </a>

    <h4>Shop</h4>
    <a href="<?= ADMIN_URL ?>/shop/index.php"  class="<?= $isActive('/admin/shop/index.php'); ?>">All Puppies</a>
    <a href="<?= ADMIN_URL ?>/shop/create.php" class="<?= $isActive('/admin/shop/create.php'); ?>">Add Puppy</a>

    <h4>Blog</h4>
    <a href="<?= ADMIN_URL ?>/blog/posts/index.php"      class="<?= $isActive('/admin/blog/posts/index.php'); ?>">All Posts</a>
    <a href="<?= ADMIN_URL ?>/blog/posts/create.php"     class="<?= $isActive('/admin/blog/posts/create.php'); ?>">Add Post</a>
    <a href="<?= ADMIN_URL ?>/blog/categories/index.php" class="<?= $isActive('/admin/blog/categories/'); ?>">Categories</a>
    <a href="<?= ADMIN_URL ?>/blog/tags/index.php"       class="<?= $isActive('/admin/blog/tags/'); ?>">Tags</a>

    <h4>Orders</h4>
    <a href="<?= ADMIN_URL ?>/orders/index.php" class="<?= $isActive('/admin/orders/'); ?>">All Orders</a>

    <h4>Users</h4>
    <a class="is-disabled" href="#" aria-disabled="true">All Users (Soon)</a>

    <h4>Settings</h4>
    <a class="is-disabled" href="#" aria-disabled="true">Settings (Soon)</a>

    <a href="<?= ADMIN_URL ?>/logout.php" class="sidebar-logout" style="margin-top:auto;">Logout</a>

</aside>
