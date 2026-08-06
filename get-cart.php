<?php
session_start();
header('Content-Type: application/json');

$cart = $_SESSION['cart'] ?? [];
$totalItems = 0;
$totalAmount = 0;

foreach ($cart as $item) {
    $qty = max(1, (int)($item['qty'] ?? 1));
    $price = (float)($item['price'] ?? 0);
    $totalItems += $qty;
    $totalAmount += $price * $qty;
}

echo json_encode([
    'cart' => array_values($cart),
    'cartCount' => $totalItems,
    'totalAmount' => $totalAmount
]);
