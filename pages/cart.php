<?php
require_once __DIR__ . '/../inc/security.php';
require __DIR__ . '/../template/header.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;

foreach ($cart as $item) {
    $total += (float)$item['price'] * (int)$item['qty'];
}

$normalizeImage = static fn($path): string => normalize_site_url($path);
?>

<div class="cart-page">
    <div class="container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <p><?= count($cart); ?> item(s) in your cart</p>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert-success"><?= htmlspecialchars($_SESSION['success_message']); ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (!empty($cart)): ?>
            <div class="cart-layout">
                <div class="cart-items-section">
                    <?php foreach ($cart as $item): ?>
                        <div class="cart-item-card">
                            <img src="<?= htmlspecialchars($normalizeImage($item['image'] ?? '')); ?>" alt="<?= htmlspecialchars($item['name'] ?? 'Puppy'); ?>" class="cart-item-image">
                            <div class="cart-item-details">
                                <h3><?= htmlspecialchars($item['name'] ?? 'Puppy'); ?></h3>
                                <p class="cart-item-price">$<?= number_format((float)($item['price'] ?? 0), 2); ?></p>
                                <div class="cart-item-qty">
                                    <label>Quantity:</label>
                                    <span><?= (int)($item['qty'] ?? 1); ?></span>
                                </div>
                            </div>
                            <div class="cart-item-actions">
                                <div class="cart-item-total">$<?= number_format(((float)($item['price'] ?? 0) * (int)($item['qty'] ?? 1)), 2); ?></div>
                                <button class="btn-remove" data-id="<?= htmlspecialchars($item['id']); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary-section">
                    <div class="summary-card">
                        <h2>Order Summary</h2>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$<?= number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span>$<?= number_format($total, 2); ?></span>
                        </div>
                        <button class="btn-checkout" onclick="window.location.href='<?= $basePath; ?>/pages/checkout.php'">Proceed to Checkout</button>
                        <a href="<?= $basePath; ?>/shop/shop.php" class="btn-continue">Continue Shopping</a>
                    </div>

                    <div class="trust-badges">
                        <div class="trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <span>Secure Payment</span>
                        </div>
                        <div class="trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <span>Safe Delivery</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="20" cy="21" r="1" />
                    <path d="M1 1h4l2.6 13h11.4" />
                </svg>
                <h2>Your cart is empty</h2>
                <p>Add some puppies to get started!</p>
                <a href="<?= $basePath; ?>/shop/shop.php" class="btn-shop-now">Browse Puppies</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= $basePath; ?>/assets/js/cart.js" defer></script>

<?php require __DIR__ . '/../template/footer.php'; ?>
