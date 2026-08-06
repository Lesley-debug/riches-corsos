<?php
require_once __DIR__ . '/../../inc/security.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';

    $allowedStatuses = ['pending', 'paid', 'completed', 'cancelled'];
    if (in_array($status, $allowedStatuses, true) && $orderId > 0) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
    }
}

header('Location: index.php');
exit;
