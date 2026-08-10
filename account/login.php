<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';
require_once __DIR__ . '/../admin/includes/db.php';

$error = '';
$success = '';
$info = '';

if (isset($_GET['registered'])) {
    $success = 'Account created successfully! Please sign in.';
}

if (isset($_GET['reset'])) {
    $success = 'Password reset successfully! Please sign in with your new password.';
}

if (isset($_GET['redirect'])) {
    if ($_GET['redirect'] === 'cart') {
        $info = 'Please sign in to add items to your cart.';
    } elseif ($_GET['redirect'] === 'wishlist') {
        $info = 'Please sign in to add items to your wishlist.';
    }
}

// Rate limiting: 10 failed attempts locks the IP for 15 minutes.
$conn->query("CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempts INT NOT NULL DEFAULT 1,
    last_attempt DATETIME NOT NULL,
    UNIQUE KEY ip_unique (ip_address)
)");

$ip           = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$lockoutSecs  = 15 * 60;
$maxAttempts  = 10;
$lockedOut    = false;

$stmt = $conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
$stmt->bind_param("s", $ip);
$stmt->execute();
$attemptRow = $stmt->get_result()->fetch_assoc();

if ($attemptRow && $attemptRow['attempts'] >= $maxAttempts) {
    $elapsed = time() - strtotime($attemptRow['last_attempt']);
    if ($elapsed < $lockoutSecs) {
        $lockedOut   = true;
        $minsLeft    = ceil(($lockoutSecs - $elapsed) / 60);
        $error       = "Too many failed attempts. Try again in {$minsLeft} minute(s).";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($lockedOut) {
        // error already set above, fall through to re-render form
    } elseif ($email && $password) {
        $stmt = $conn->prepare("SELECT id, email, password, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                // Success: clear lockout record for this IP
                $del = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                $del->bind_param("s", $ip);
                $del->execute();

                session_regenerate_id(true);
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name']  = $user['name'];

                if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                    setcookie('user_email', $email, time() + (86400 * 30), '/', '', true, true);
                }

                $redirectUrl = $_SESSION['redirect_after_login'] ?? site_url('/index.php');
                unset($_SESSION['redirect_after_login']);
                $_SESSION['just_logged_in'] = true;

                header('Location: ' . $redirectUrl);
                exit;
            } else {
                // Failed: increment attempt counter
                $upsert = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt)
                    VALUES (?, 1, NOW())
                    ON DUPLICATE KEY UPDATE
                        attempts = IF(last_attempt < DATE_SUB(NOW(), INTERVAL ? SECOND), 1, attempts + 1),
                        last_attempt = NOW()");
                $upsert->bind_param("si", $ip, $lockoutSecs);
                $upsert->execute();
                $error = 'Invalid email or password';
            }
        } else {
            $upsert = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt)
                VALUES (?, 1, NOW())
                ON DUPLICATE KEY UPDATE
                    attempts = IF(last_attempt < DATE_SUB(NOW(), INTERVAL ? SECOND), 1, attempts + 1),
                    last_attempt = NOW()");
            $upsert->bind_param("si", $ip, $lockoutSecs);
            $upsert->execute();
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}

require_once __DIR__ . '/../template/header.php';
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your account</p>
            
            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert-success"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($info): ?>
                <div class="alert-info"><?= htmlspecialchars($info); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <?= csrfField(); ?>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@email.com" required <?= $lockedOut ? 'disabled' : ''; ?>>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required <?= $lockedOut ? 'disabled' : ''; ?>>
                </div>
                
                <div class="form-options">
                    <label class="remember-checkbox">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="<?= $basePath; ?>/account/forgot-password.php" class="forgot-link">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn-auth" <?= $lockedOut ? 'disabled' : ''; ?>>Sign In</button>
            </form>
            
            <div class="divider"><span>or continue with</span></div>
            
            <div class="social-auth">
                <a href="<?php 
                    require_once __DIR__ . '/oauth-config.php';
                    $oauthState = bin2hex(random_bytes(16));
                    $_SESSION['oauth_state'] = $oauthState;
                    $googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                        'client_id' => GOOGLE_CLIENT_ID,
                        'redirect_uri' => GOOGLE_REDIRECT_URI,
                        'response_type' => 'code',
                        'scope' => 'email profile',
                        'access_type' => 'online',
                        'state' => $oauthState,
                    ]);
                    echo $googleAuthUrl;
                ?>" class="btn-social btn-google">
                    <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Google
                </a>
            </div>
            
            <p class="auth-footer">
                Don't have an account? <a href="<?= $basePath; ?>/account/register.php">Sign up</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>
