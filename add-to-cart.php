<?php
require_once __DIR__ . '/inc/security.php';
require_once __DIR__ . '/inc/paths.php';

// Lightweight CSRF guard for GET-based cart add: must originate from this site.
$host = parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST) ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$refererHost = parse_url($referer, PHP_URL_HOST) ?? '';
if ($host && $referer && $refererHost !== $host) {
    http_response_code(403);
    exit('Forbidden');
}

$wantsJson = strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '', 'XMLHttpRequest') === 0
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

$sendJson = static function (array $payload, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
};

$cartPayload = static function (): array {
    $cart = $_SESSION['cart'] ?? [];
    $cartCount = 0;
    $totalAmount = 0.0;

    foreach ($cart as $item) {
        $qty = max(1, (int)($item['qty'] ?? 1));
        $price = (float)($item['price'] ?? 0);
        $cartCount += $qty;
        $totalAmount += $price * $qty;
    }

    return [
        'cart' => array_values($cart),
        'cartCount' => $cartCount,
        'totalAmount' => $totalAmount,
    ];
};

if (!isset($_SESSION['user_id'])) {
    $redirectUrl = site_url('/account/login.php?redirect=cart');
    $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? site_url('/shop/shop.php');

    if ($wantsJson) {
        $sendJson([
            'success' => false,
            'loginRequired' => true,
            'redirectUrl' => $redirectUrl,
            'message' => 'Please sign in to add items to your cart.',
        ], 401);
    }

    header('Location: ' . $redirectUrl);
    exit;
}

require_once __DIR__ . '/admin/includes/db.php';

if (!isset($_GET['id']) || (int)$_GET['id'] <= 0) {
    if ($wantsJson) {
        $sendJson([
            'success' => false,
            'message' => 'Invalid puppy selected.',
        ], 400);
    }

    header('Location: ' . site_url('/index.php'));
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT id, name, price, featured_image FROM puppies WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    if ($wantsJson) {
        $sendJson([
            'success' => false,
            'message' => 'Puppy not found.',
        ], 404);
    }

    header('Location: ' . site_url('/index.php'));
    exit;
}

$puppy = $result->fetch_assoc();
$stmt->close();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as &$cartItem) {
    if ((int)($cartItem['id'] ?? 0) === $id) {
        $cartItem['qty'] = max(1, (int)($cartItem['qty'] ?? 1)) + 1;
        $found = true;
        break;
    }
}
unset($cartItem);

if (!$found) {
    $_SESSION['cart'][] = [
        'id'    => (int)$puppy['id'],
        'name'  => $puppy['name'],
        'price' => (float)$puppy['price'],
        'image' => $puppy['featured_image'],
        'qty'   => 1,
    ];
}

$_SESSION['success_message'] = "Added to cart successfully.";

if ($wantsJson) {
    $sendJson(array_merge([
        'success' => true,
        'message' => 'Added to cart successfully.',
    ], $cartPayload()));
}

header('Location: ' . site_url('/pages/cart.php'));
exit;
