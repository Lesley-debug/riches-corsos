<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . site_url('/account/login.php'));
    exit;
}

require_once __DIR__ . '/../admin/includes/db.php';

$userId    = (int)$_SESSION['user_id'];
$userName  = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

// Member since
$memberSince = '';
$userStmt = $conn->prepare("SELECT created_at FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userRow = $userStmt->get_result()->fetch_assoc();
if ($userRow) {
    $memberSince = date('F Y', strtotime($userRow['created_at']));
}

// Orders
$ordersCount  = 0;
$latestOrder  = null;
try {
    $oStmt = $conn->prepare("SELECT * FROM orders WHERE customer_email = ? ORDER BY created_at DESC");
    $oStmt->bind_param("s", $userEmail);
    $oStmt->execute();
    $oResult     = $oStmt->get_result();
    $ordersCount = $oResult->num_rows;
    $latestOrder = $oResult->fetch_assoc();
} catch (Throwable $e) {}

// Wishlist & Cart
$wishlist  = $_SESSION['wishlist'] ?? [];
$cart      = $_SESSION['cart'] ?? [];
$cartCount = array_sum(array_column($cart, 'qty'));
$cartTotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));

$normalizeImagePath = static fn($path): string => normalize_site_url($path);
$initials = strtoupper(substr($userName, 0, 1));

require_once __DIR__ . '/../template/header.php';
?>

<div class="user-dashboard">

    <!-- Hero Banner -->
    <div class="dash-hero">
        <div class="dash-hero-inner">
            <div class="dash-avatar"><?= $initials ?></div>
            <div class="dash-hero-text">
                <h1>Welcome back, <?= htmlspecialchars(explode(' ', $userName)[0]); ?>!</h1>
                <p><?= htmlspecialchars($userEmail); ?> &nbsp;·&nbsp; Member since <?= $memberSince; ?></p>
            </div>
            <a href="<?= $basePath; ?>/account/settings.php" class="dash-hero-settings">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Settings
            </a>
        </div>
    </div>

    <div class="dash-body">

        <!-- Stat Cards -->
        <div class="dash-stats">
            <div class="dash-stat-card">
                <div class="dash-stat-icon orders">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <div class="dash-stat-info">
                    <span class="dash-stat-num"><?= $ordersCount ?></span>
                    <span class="dash-stat-label">Total Orders</span>
                </div>
                <a href="<?= $basePath; ?>/account/orders.php" class="dash-stat-link">View →</a>
            </div>

            <div class="dash-stat-card">
                <div class="dash-stat-icon wishlist">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </div>
                <div class="dash-stat-info">
                    <span class="dash-stat-num"><?= count($wishlist) ?></span>
                    <span class="dash-stat-label">Wishlist Items</span>
                </div>
                <a href="<?= $basePath; ?>/account/wishlist.php" class="dash-stat-link">View →</a>
            </div>

            <div class="dash-stat-card">
                <div class="dash-stat-icon cart">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </div>
                <div class="dash-stat-info">
                    <span class="dash-stat-num">$<?= number_format($cartTotal, 0) ?></span>
                    <span class="dash-stat-label"><?= $cartCount ?> item<?= $cartCount !== 1 ? 's' : '' ?> in Cart</span>
                </div>
                <a href="<?= $basePath; ?>/pages/cart.php" class="dash-stat-link">View →</a>
            </div>
        </div>

        <div class="dash-main">

            <!-- Left Column -->
            <div class="dash-left">

                <!-- Latest Order -->
                <div class="dash-card">
                    <div class="dash-card-head">
                        <h2>Latest Order</h2>
                        <a href="<?= $basePath; ?>/account/orders.php">View All</a>
                    </div>
                    <div class="dash-card-body">
                        <?php if ($latestOrder): ?>
                            <div class="latest-order">
                                <div class="latest-order-row">
                                    <span>Order #<?= $latestOrder['id'] ?></span>
                                    <span class="order-status-badge status-<?= strtolower($latestOrder['status']) ?>"><?= ucfirst($latestOrder['status']) ?></span>
                                </div>
                                <div class="latest-order-row muted">
                                    <span><?= date('M d, Y', strtotime($latestOrder['created_at'])) ?></span>
                                    <span class="order-total">$<?= number_format($latestOrder['total'], 2) ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="dash-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                                <p>No orders yet</p>
                                <a href="<?= $basePath; ?>/shop/shop.php">Browse Puppies</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Wishlist Preview -->
                <div class="dash-card">
                    <div class="dash-card-head">
                        <h2>Wishlist <span class="dash-badge"><?= count($wishlist) ?></span></h2>
                        <a href="<?= $basePath; ?>/account/wishlist.php">View All</a>
                    </div>
                    <div class="dash-card-body">
                        <?php if (empty($wishlist)): ?>
                            <div class="dash-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                <p>Your wishlist is empty</p>
                                <a href="<?= $basePath; ?>/shop/shop.php">Browse Puppies</a>
                            </div>
                        <?php else: ?>
                            <div class="dash-wishlist-list">
                                <?php foreach (array_slice($wishlist, 0, 4) as $item): ?>
                                    <div class="dash-wishlist-item">
                                        <img src="<?= htmlspecialchars($normalizeImagePath($item['image'])); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                                        <div class="dash-wishlist-info">
                                            <p><?= htmlspecialchars($item['name']); ?></p>
                                            <span>$<?= number_format($item['price'], 2); ?></span>
                                        </div>
                                        <a href="<?= $basePath; ?>/shop/product.php?id=<?= (int)$item['id']; ?>" class="dash-wishlist-view">View</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <div class="dash-right">

                <!-- Quick Actions -->
                <div class="dash-card">
                    <div class="dash-card-head"><h2>Quick Actions</h2></div>
                    <div class="dash-card-body">
                        <div class="dash-actions">
                            <a href="<?= $basePath; ?>/shop/shop.php" class="dash-action">
                                <div class="dash-action-icon browse">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                </div>
                                <span>Browse Puppies</span>
                            </a>
                            <a href="<?= $basePath; ?>/account/orders.php" class="dash-action">
                                <div class="dash-action-icon orders">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                </div>
                                <span>My Orders</span>
                            </a>
                            <a href="<?= $basePath; ?>/account/wishlist.php" class="dash-action">
                                <div class="dash-action-icon wishlist">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                </div>
                                <span>Wishlist</span>
                            </a>
                            <a href="<?= $basePath; ?>/pages/cart.php" class="dash-action">
                                <div class="dash-action-icon cart">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                </div>
                                <span>Cart</span>
                            </a>
                            <a href="<?= $basePath; ?>/account/settings.php" class="dash-action">
                                <div class="dash-action-icon settings">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                </div>
                                <span>Settings</span>
                            </a>
                            <a href="<?= $basePath; ?>/account/logout.php" class="dash-action logout">
                                <div class="dash-action-icon logout">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                </div>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="dash-card">
                    <div class="dash-card-head">
                        <h2>Profile</h2>
                        <a href="<?= $basePath; ?>/account/settings.php">Edit</a>
                    </div>
                    <div class="dash-card-body">
                        <div class="dash-profile">
                            <div class="dash-profile-avatar"><?= $initials ?></div>
                            <div>
                                <p class="dash-profile-name"><?= htmlspecialchars($userName) ?></p>
                                <p class="dash-profile-email"><?= htmlspecialchars($userEmail) ?></p>
                                <p class="dash-profile-since">Member since <?= $memberSince ?></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>
