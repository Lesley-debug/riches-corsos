<?php
require_once __DIR__ . '/inc/security.php';
require_once __DIR__ . '/inc/paths.php';
require_once __DIR__ . '/admin/includes/db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: ' . site_url('/pages/cart.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $userId = $_SESSION['user_id'];
    $cart = $_SESSION['cart'];
    
    // Get user info
    $userName = $_SESSION['user_name'] ?? '';
    $userEmail = $_SESSION['user_email'] ?? '';
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += (float)$item['price'] * (int)$item['qty'];
    }
    $shipping = 50.00;
    $tax = $subtotal * 0.08;
    $total = $subtotal + $shipping + $tax;
    
    // Generate order number
    $orderNumber = 'ORD-' . strtoupper(uniqid());
    
    // Get form data
    $email          = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone          = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
    $firstName      = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $lastName       = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $address        = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
    $address2       = htmlspecialchars(trim($_POST['address2'] ?? ''), ENT_QUOTES, 'UTF-8');
    $city           = htmlspecialchars(trim($_POST['city'] ?? ''), ENT_QUOTES, 'UTF-8');
    $state          = htmlspecialchars(trim($_POST['state'] ?? ''), ENT_QUOTES, 'UTF-8');
    $zip            = htmlspecialchars(trim($_POST['zip'] ?? ''), ENT_QUOTES, 'UTF-8');
    $paymentMethod  = htmlspecialchars(trim($_POST['payment_method'] ?? 'card'), ENT_QUOTES, 'UTF-8');
    $notes          = htmlspecialchars(trim($_POST['notes'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    // Create orders table if not exists (match existing schema)
    $conn->query("CREATE TABLE IF NOT EXISTS `orders` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_name` varchar(255) DEFAULT NULL,
        `customer_email` varchar(255) DEFAULT NULL,
        `total` decimal(10,2) DEFAULT NULL,
        `status` enum('pending','paid','completed','cancelled') DEFAULT 'pending',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Prepare customer info
    $customerName = trim($firstName . ' ' . $lastName);
    if (empty($customerName)) {
        $customerName = $userName;
    }
    if (empty($email)) {
        $email = $userEmail;
    }
    
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, total, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("ssd", $customerName, $email, $total);
    
    if ($stmt->execute()) {
        $orderId = $conn->insert_id;
        
        // Send email to customer
        require_once __DIR__ . '/inc/email.php';
        
        $escapedCustomerName = htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8');
        $customerSubject = "Order Confirmation #$orderId - Riches Corsos";
        $ordersUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . site_url('/account/orders.php');
        $customerMessage = "
            <h2>Thank you for your order, {$escapedCustomerName}! 🎉</h2>
            <p>Your order has been successfully placed and is being processed.</p>
            
            <div class='info-box'>
                <h3>Order Details</h3>
                <p><strong>Order ID:</strong> #$orderId</p>
                <p><strong>Total:</strong> $" . number_format($total, 2) . "</p>
                <p><strong>Shipping Address:</strong><br>
                $address<br>";
        if ($address2) $customerMessage .= "$address2<br>";
        $customerMessage .= "$city, $state $zip</p>
            </div>
            
            <h3>What's Next?</h3>
            <p>We'll contact you shortly with payment instructions. Once payment is confirmed, we'll prepare your puppy for delivery.</p>
            
            <p style='text-align: center;'>
                <a href='$ordersUrl' class='button'>Track Your Order</a>
            </p>
            
            <p>If you have any questions, feel free to reach out.</p>
            <p>Best regards,<br><strong>The Riches Corsos Team</strong></p>
        ";
        
        sendEmail($email, $customerName, $customerSubject, $customerMessage);
        
        $adminEmail = getenv('MAIL_CONTACT_TO') ?: getenv('MAIL_USERNAME') ?: 'barbarapettra@gmail.com';
        $adminSubject = "New Order #$orderId - " . htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8');
        $adminMessage = '
            <h2>New Order Received</h2>
            <div class="info-box">
                <p><strong>Order ID:</strong> #' . $orderId . '</p>
                <p><strong>Customer:</strong> ' . htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Phone:</strong> ' . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Total:</strong> $' . number_format($total, 2) . '</p>
                <p><strong>Address:</strong> ' . htmlspecialchars($address . ($address2 ? ', ' . $address2 : '') . ', ' . $city . ', ' . $state . ' ' . $zip, ENT_QUOTES, 'UTF-8') . '</p>
                ' . ($notes ? '<p><strong>Notes:</strong> ' . htmlspecialchars($notes, ENT_QUOTES, 'UTF-8') . '</p>' : '') . '
            </div>';
        sendEmail($adminEmail, 'Admin', $adminSubject, $adminMessage);
        
        // Clear cart
        unset($_SESSION['cart']);
        
        // Redirect to success page
        $_SESSION['success_message'] = "Order placed successfully! Order #$orderId";
        header('Location: ' . site_url('/pages/order-success.php?order=' . $orderId));
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to place order. Please try again.";
        header('Location: ' . site_url('/pages/checkout.php'));
        exit;
    }
}

header('Location: ' . site_url('/pages/checkout.php'));
exit;
