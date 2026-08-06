<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';
$orderNumber = $_GET['order'] ?? '';

if (empty($orderNumber)) {
    header('Location: ' . site_url('/index.php'));
    exit;
}

require __DIR__ . '/../template/header.php';
?>

<div class="success-page">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
            </div>
            <h1>Order Placed Successfully!</h1>
            <p class="order-number">Order #<?= htmlspecialchars($orderNumber); ?></p>
            <p class="success-message">Thank you for your purchase! We've received your order and will process it shortly. You'll receive a confirmation email with tracking details.</p>

            <div class="success-actions">
                <a href="<?= $basePath; ?>/account/orders.php" class="btn-view-orders">View My Orders</a>
                <a href="<?= $basePath; ?>/shop/shop.php" class="btn-continue-shopping">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>


<?php require __DIR__ . '/../template/footer.php'; ?>
