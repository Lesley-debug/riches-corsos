<?php
require_once __DIR__ . '/../inc/security.php';

// Log all errors to the server log only (never display to the browser mid-OAuth-flow)
error_reporting(E_ALL);
error_log("Google callback started");

require_once __DIR__ . '/../admin/includes/db.php';
require_once __DIR__ . '/oauth-config.php';

if (!isset($_GET['code'])) {
    error_log("No code received");
    header('Location: ' . site_url('/account/login.php?error=no_code'));
    exit;
}

// Validate OAuth state to prevent CSRF
if (empty($_GET['state']) || empty($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], $_GET['state'])) {
    error_log("OAuth state mismatch");
    header('Location: ' . site_url('/account/login.php?error=state_mismatch'));
    exit;
}
unset($_SESSION['oauth_state']);

$code = $_GET['code'];
error_log("Code received: " . substr($code, 0, 20) . "...");

// Exchange code for access token
$tokenUrl = 'https://oauth2.googleapis.com/token';
$tokenData = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

error_log("Token response code: " . $httpCode);
error_log("Token response: " . $response);

$tokenInfo = json_decode($response, true);

if (!isset($tokenInfo['access_token'])) {
    error_log("No access token received");
    header('Location: ' . site_url('/account/login.php?error=oauth_token'));
    exit;
}

error_log("Access token received");

// Get user info
$userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init($userInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenInfo['access_token']]);
$userInfoResponse = curl_exec($ch);
curl_close($ch);

error_log("User info response: " . $userInfoResponse);

$userInfo = json_decode($userInfoResponse, true);

if (!isset($userInfo['email'])) {
    error_log("No email in user info");
    header('Location: ' . site_url('/account/login.php?error=oauth_email'));
    exit;
}

// Check if user exists
$email = $userInfo['email'];
$name = $userInfo['name'] ?? $userInfo['email'];
$googleId = $userInfo['id'];

error_log("User email: " . $email);
error_log("User name: " . $name);

$stmt = $conn->prepare("SELECT id, email, name FROM users WHERE email = ? OR (provider = 'google' AND provider_id = ?)");
$stmt->bind_param("ss", $email, $googleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User exists, log them in
    error_log("User exists, logging in");
    $user = $result->fetch_assoc();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
} else {
    // Create new user
    error_log("Creating new user");
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, provider, provider_id, created_at) VALUES (?, ?, '', 'google', ?, NOW())");
    $stmt->bind_param("sss", $name, $email, $googleId);
    
    if (!$stmt->execute()) {
        error_log("Failed to create user: " . $stmt->error);
        header('Location: ' . site_url('/account/login.php?error=db_error'));
        exit;
    }
    
    session_regenerate_id(true);
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    error_log("User created with ID: " . $conn->insert_id);
}

$redirectUrl = $_SESSION['redirect_after_login'] ?? site_url('/account/dashboard.php');
unset($_SESSION['redirect_after_login']);

error_log("Redirecting to: " . $redirectUrl);
header('Location: ' . $redirectUrl);
exit;
