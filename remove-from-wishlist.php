<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false]);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$index = $input['index'] ?? null;

if ($index !== null && isset($_SESSION['wishlist'][$index])) {
  array_splice($_SESSION['wishlist'], $index, 1);
  $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
}

echo json_encode([
  'success' => true,
  'wishlistCount' => count($_SESSION['wishlist'] ?? [])
]);
