<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$itemId = $input['id'] ?? null;

if ($itemId && isset($_SESSION['cart'])) {
  $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($itemId) {
    return $item['id'] != $itemId;
  });
  $_SESSION['cart'] = array_values($_SESSION['cart']);
}

$cartCount = 0;
$totalAmount = 0;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $item) {
    $qty = max(1, (int)($item['qty'] ?? 1));
    $price = (float)($item['price'] ?? 0);
    $cartCount += $qty;
    $totalAmount += $price * $qty;
  }
}

echo json_encode([
  'cartCount' => $cartCount,
  'totalAmount' => $totalAmount,
  'cart' => $_SESSION['cart'] ?? []
]);
