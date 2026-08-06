<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . site_url('/account/login.php'));
    exit;
}

require_once __DIR__ . '/../admin/includes/db.php';

$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

// Get orders count
$ordersCount = 0;
try {
    $ordersStmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE customer_email = ?");
    if ($ordersStmt) {
        $ordersStmt->bind_param("s", $userEmail);
        $ordersStmt->execute();
        $ordersResult = $ordersStmt->get_result();
        $ordersCount = (int)($ordersResult->fetch_assoc()['count'] ?? 0);
        $ordersStmt->close();
    }
} catch (Throwable $e) {
    // Orders table may not exist until the first checkout.
    $ordersCount = 0;
}

// Get wishlist
$wishlist = $_SESSION['wishlist'] ?? [];

// Get cart
$cart = $_SESSION['cart'] ?? [];
$cartCount = array_sum(array_column($cart, 'qty'));

$normalizeImagePath = static fn($path): string => normalize_site_url($path);

require_once __DIR__ . '/../template/header.php';
?>

<div class="account-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>My Account</h1>
            <p>Welcome back, <?= htmlspecialchars($userName); ?>!</p>
        </div>

        <div class="dashboard-grid">
            <!-- Profile Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Profile</h2>
                    <a href="<?= $basePath; ?>/account/edit-profile.php" class="btn-edit">Edit</a>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($userName, 0, 1)); ?>
                        </div>
                        <div>
                            <p class="profile-name"><?= htmlspecialchars($userName); ?></p>
                            <p class="profile-email"><?= htmlspecialchars($userEmail); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>My Orders</h2>
                    <a href="<?= $basePath; ?>/account/orders.php" class="btn-view">View All</a>
                </div>
                <div class="card-body">
                    <div class="stat-box">
                        <div class="stat-number"><?= $ordersCount; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
            </div>

            <!-- Wishlist Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Wishlist</h2>
                    <span class="badge-count"><?= count($wishlist); ?> items</span>
                </div>
                <div class="card-body">
                    <?php if (empty($wishlist)): ?>
                        <p class="empty-state">No items in wishlist</p>
                    <?php else: ?>
                        <div class="wishlist-preview">
                            <?php foreach (array_slice($wishlist, 0, 3) as $item): ?>
                                <div class="wishlist-item">
                                    <img src="<?= htmlspecialchars($normalizeImagePath($item['image'])); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                                    <div>
                                        <p class="item-name"><?= htmlspecialchars($item['name']); ?></p>
                                        <p class="item-price">$<?= number_format($item['price'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($wishlist) > 3): ?>
                            <a href="<?= $basePath; ?>/account/wishlist.php" class="btn-view-more">View All (<?= count($wishlist); ?>)</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cart Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Shopping Cart</h2>
                    <span class="badge-count"><?= $cartCount; ?> items</span>
                </div>
                <div class="card-body">
                    <?php if (empty($cart)): ?>
                        <p class="empty-state">Your cart is empty</p>
                    <?php else: ?>
                        <?php
                        $cartTotal = 0;
                        foreach ($cart as $item) {
                            $cartTotal += $item['price'] * $item['qty'];
                        }
                        ?>
                        <div class="stat-box">
                            <div class="stat-number">$<?= number_format($cartTotal, 2); ?></div>
                            <div class="stat-label">Cart Total</div>
                        </div>
                        <a href="<?= $basePath; ?>/pages/cart.php" class="btn-view-cart">View Cart</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="card-body">
                    <div class="action-buttons">
                        <a href="<?= $basePath; ?>/shop/shop.php" class="action-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 21s-7-4.35-10-9a6 6 0 0 1 10-6 6 6 0 0 1 10 6c-3 4.65-10 9-10 9z" />
                            </svg>
                            Browse Puppies
                        </a>
                        <a href="<?= $basePath; ?>/account/orders.php" class="action-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M9 3v18M15 3v18"/>
                            </svg>
                            Order History
                        </a>
                        <a href="<?= $basePath; ?>/account/settings.php" class="action-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 1v6m0 6v6M1 12h6m6 0h6"/>
                            </svg>
                            Settings
                        </a>
                        <a href="<?= $basePath; ?>/account/logout.php" class="action-btn logout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>
