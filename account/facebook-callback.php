<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../admin/includes/db.php';
require_once __DIR__ . '/oauth-config.php';

if (!isset($_GET['code'])) {
    header('Location: ' . site_url('/account/login.php'));
    exit;
}

// Validate OAuth state to prevent CSRF
if (empty($_GET['state']) || empty($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], $_GET['state'])) {
    header('Location: ' . site_url('/account/login.php?error=state_mismatch'));
    exit;
}
unset($_SESSION['oauth_state']);

$code = $_GET['code'];

// Exchange code for access token
$tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token';
$tokenData = [
    'code' => $code,
    'client_id' => FACEBOOK_APP_ID,
    'client_secret' => FACEBOOK_APP_SECRET,
    'redirect_uri' => FACEBOOK_REDIRECT_URI
];

$ch = curl_init($tokenUrl . '?' . http_build_query($tokenData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$tokenInfo = json_decode($response, true);

if (!isset($tokenInfo['access_token'])) {
    header('Location: ' . site_url('/account/login.php?error=oauth'));
    exit;
}

// Get user info
$userInfoUrl = 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $tokenInfo['access_token'];
$ch = curl_init($userInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$userInfoResponse = curl_exec($ch);
curl_close($ch);

$userInfo = json_decode($userInfoResponse, true);

if (!isset($userInfo['email'])) {
    header('Location: ' . site_url('/account/login.php?error=oauth'));
    exit;
}

// Check if user exists
$email = $userInfo['email'];
$name = $userInfo['name'] ?? $userInfo['email'];
$facebookId = $userInfo['id'];

$stmt = $conn->prepare("SELECT id, email, name FROM users WHERE email = ? OR (provider = 'facebook' AND provider_id = ?)");
$stmt->bind_param("ss", $email, $facebookId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User exists, log them in
    $user = $result->fetch_assoc();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
} else {
    // Create new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, provider, provider_id, created_at) VALUES (?, ?, 'facebook', ?, NOW())");
    $stmt->bind_param("sss", $name, $email, $facebookId);
    $stmt->execute();

    session_regenerate_id(true);
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
}

$redirectUrl = $_SESSION['redirect_after_login'] ?? site_url('/index.php');
unset($_SESSION['redirect_after_login']);

header('Location: ' . $redirectUrl);
exit;
