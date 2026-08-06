<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $basePath . '/pages/checkout.php';
    header('Location: ' . $basePath . '/account/login.php?redirect=checkout');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: ' . $basePath . '/pages/cart.php');
    exit;
}

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += (float)$item['price'] * (int)$item['qty'];
}

$shipping = 50.00;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;

$normalizeImage = static fn($path): string => normalize_site_url($path);

require __DIR__ . '/../template/header.php';
?>

<div class="checkout-page">
    <div class="container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <div class="checkout-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-label">Information</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">2</span>
                    <span class="step-label">Payment</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span class="step-label">Confirmation</span>
                </div>
            </div>
        </div>

        <div class="checkout-layout">
            <div class="checkout-form-section">
                <form id="checkoutForm" method="POST" action="<?= $basePath; ?>/process-checkout.php">
                    <?= csrfField(); ?>

                    <!-- Contact Information -->
                    <div class="form-section">
                        <h2>Contact Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email Address *</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input type="tel" name="phone" placeholder="+1 (555) 000-0000" required>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="form-section">
                        <h2>Shipping Address</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Street Address *</label>
                            <input type="text" name="address" placeholder="123 Main Street" required>
                        </div>
                        <div class="form-group">
                            <label>Apartment, suite, etc. (optional)</label>
                            <input type="text" name="address2" placeholder="Apt 4B">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" name="city" required>
                            </div>
                            <div class="form-group">
                                <label>State *</label>
                                <select name="state" required>
                                    <option value="">Select State</option>
                                    <option value="CA">California</option>
                                    <option value="NY">New York</option>
                                    <option value="TX">Texas</option>
                                    <option value="FL">Florida</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>ZIP Code *</label>
                                <input type="text" name="zip" placeholder="12345" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h2>Payment Method</h2>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card" checked>
                                <div class="payment-card">
                                    <svg width="40" height="28" viewBox="0 0 40 28">
                                        <rect width="40" height="28" rx="4" fill="#1a1f2e" />
                                        <rect x="4" y="8" width="32" height="4" fill="#fff" />
                                    </svg>
                                    <span>Credit / Debit Card</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="paypal">
                                <div class="payment-card">
                                    <svg width="40" height="28" viewBox="0 0 40 28">
                                        <rect width="40" height="28" rx="4" fill="#0070ba" /><text x="20" y="18" fill="#fff" font-size="10" text-anchor="middle" font-weight="bold">PayPal</text>
                                    </svg>
                                    <span>PayPal</span>
                                </div>
                            </label>
                        </div>

                        <div id="cardFields">
                            <div class="form-group">
                                <label>Card Number *</label>
                                <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Expiry Date *</label>
                                    <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label>CVV *</label>
                                    <input type="text" name="card_cvv" placeholder="123" maxlength="4">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="form-section">
                        <h2>Order Notes (Optional)</h2>
                        <div class="form-group">
                            <textarea name="notes" rows="4" placeholder="Any special instructions for delivery..."></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-place-order">Place Order - $<?= number_format($total, 2); ?></button>
                </form>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="checkout-summary-section">
                <div class="summary-card">
                    <h2>Order Summary</h2>

                    <div class="summary-items">
                        <?php foreach ($cart as $item): ?>
                            <div class="summary-item">
                                <img src="<?= htmlspecialchars($normalizeImage($item['image'])); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                                <div class="summary-item-details">
                                    <h4><?= htmlspecialchars($item['name']); ?></h4>
                                    <p>Qty: <?= (int)$item['qty']; ?></p>
                                </div>
                                <span class="summary-item-price">$<?= number_format((float)$item['price'] * (int)$item['qty'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?= number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>$<?= number_format($shipping, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (8%)</span>
                        <span>$<?= number_format($tax, 2); ?></span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span>$<?= number_format($total, 2); ?></span>
                    </div>
                </div>

                <div class="security-badges">
                    <div class="security-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                        <span>Secure SSL Encryption</span>
                    </div>
                    <div class="security-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        <span>100% Money Back Guarantee</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>
