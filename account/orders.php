<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . site_url('/account/login.php'));
    exit;
}

require_once __DIR__ . '/../admin/includes/db.php';

$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email'];

// Get orders by customer email (since orders table uses customer_email, not user_id)
$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_email = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$ordersResult = $stmt->get_result();

require_once __DIR__ . '/../template/header.php';
?>

<div class="account-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>My Orders</h1>
            <p>View your transaction history</p>
        </div>

        <div class="orders-container">
            <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
                <?php while ($order = $ordersResult->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <h3>Order #<?= htmlspecialchars($order['id']); ?></h3>
                                <p class="order-date"><?= date('M d, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div>
                                <span class="order-status status-<?= htmlspecialchars($order['status']); ?>">
                                    <?= ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="order-body">
                            <div class="order-info">
                                <span>Total Amount:</span>
                                <strong>$<?= number_format($order['total'], 2); ?></strong>
                            </div>
                            <div class="order-info">
                                <span>Customer:</span>
                                <strong><?= htmlspecialchars($order['customer_name']); ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-orders">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M9 3v18M15 3v18"/>
                    </svg>
                    <h3>No Orders Yet</h3>
                    <p>Start shopping for your perfect puppy!</p>
                    <a href="<?= $basePath; ?>/shop/shop.php" class="btn-shop">Browse Puppies</a>
                </div>
            <?php endif; ?>
        </div>

        <a href="<?= $basePath; ?>/account/dashboard.php" class="btn-back">← Back to Dashboard</a>
    </div>
</div>


<?php require_once __DIR__ . '/../template/footer.php'; ?>
