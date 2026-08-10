<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . site_url('/account/login.php'));
    exit;
}

$wishlist = $_SESSION['wishlist'] ?? [];

$normalizeImagePath = static fn($path): string => normalize_site_url($path);

require_once __DIR__ . '/../template/header.php';
?>

<div class="account-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>My Wishlist</h1>
            <p><?= count($wishlist); ?> saved puppies</p>
        </div>

        <div class="wishlist-grid">
            <?php if (empty($wishlist)): ?>
                <div class="empty-wishlist">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 21s-7-4.35-10-9a6 6 0 0 1 10-6 6 6 0 0 1 10 6c-3 4.65-10 9-10 9z" />
                    </svg>
                    <h3>Your Wishlist is Empty</h3>
                    <p>Save your favorite puppies here!</p>
                    <a href="<?= $basePath; ?>/shop/shop.php" class="btn-shop">Browse Puppies</a>
                </div>
            <?php else: ?>
                <?php foreach ($wishlist as $index => $item): ?>
                    <div class="wishlist-card">
                        <img src="<?= htmlspecialchars($normalizeImagePath($item['image'])); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                        <div class="wishlist-card-body">
                            <h3><?= htmlspecialchars($item['name']); ?></h3>
                            <p class="wishlist-price">$<?= number_format($item['price'], 2); ?></p>
                            <div class="wishlist-actions">
                                <a href="<?= $basePath; ?>/add-to-cart.php?id=<?= htmlspecialchars($item['id']); ?>" class="btn-add-cart">Add to Cart</a>
                                <button class="btn-remove" data-index="<?= $index; ?>">Remove</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="<?= $basePath; ?>/account/dashboard.php" class="btn-back">← Back to Dashboard</a>
    </div>
</div>



<script src="<?= $basePath; ?>/assets/js/wishlist.min.js" defer></script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>
