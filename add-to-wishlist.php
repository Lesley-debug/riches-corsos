<?php
require_once __DIR__ . '/inc/security.php';
require_once __DIR__ . '/inc/paths.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode([
    'success' => false,
    'loginRequired' => true,
    'redirectUrl' => site_url('/account/login.php?redirect=wishlist'),
    'message' => 'Please sign in to save items to your wishlist.'
  ]);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input) || !isset($input['id']) || (int)$input['id'] <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid data']);
  exit;
}

if (!isset($_SESSION['wishlist'])) {
  $_SESSION['wishlist'] = [];
}

$puppyId = (int)$input['id'];
$alreadySaved = false;

foreach ($_SESSION['wishlist'] as $item) {
  if ((int)($item['id'] ?? 0) === $puppyId) {
    $alreadySaved = true;
    break;
  }
}

if (!$alreadySaved) {
  $_SESSION['wishlist'][] = [
    'id' => $puppyId,
    'name' => trim((string)($input['name'] ?? '')),
    'price' => (float)($input['price'] ?? 0),
    'image' => $input['image'] ?? ''
  ];
}

echo json_encode([
  'success' => true,
  'alreadySaved' => $alreadySaved,
  'wishlistCount' => count($_SESSION['wishlist'])
]);
