<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';
require_once __DIR__ . '/../admin/includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($name && $email && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters';
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Email already registered';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    // Send welcome email
                    require_once __DIR__ . '/../inc/email.php';
                    
                    $escapedName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                    $shopUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . site_url('/shop/shop.php');
                    $message = "
                        <h2>Welcome, {$escapedName}!</h2>
                        <p>We're thrilled to have you join our family of puppy lovers. Your account has been successfully created and you're now ready to explore our collection of adorable, healthy puppies.</p>
                        
                        <div class='info-box'>
                            <h3>What you can do now:</h3>
                            <ul>
                                <li>Browse our available puppies</li>
                                <li>Save your favorites to your wishlist</li>
                                <li>Place orders securely</li>
                                <li>Track your orders in your dashboard</li>
                            </ul>
                        </div>
                        
                        <p style='text-align: center;'>
                            <a href='$shopUrl' class='button'>Start Shopping</a>
                        </p>
                        
                        <p>If you have any questions, feel free to reach out to us anytime.</p>
                        <p>Best regards,<br><strong>The Riches Corsos Team</strong></p>
                    ";
                    
                    sendEmail($email, $name, $subject, $message);
                    
                    header('Location: ' . site_url('/account/login.php?registered=1'));
                    exit;
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
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
            <h1>Create Account</h1>
            <p class="auth-subtitle">Join us today</p>
            
            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert-success"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <?= csrfField(); ?>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@email.com" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn-auth">Create Account</button>
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
                Already have an account? <a href="<?= $basePath; ?>/account/login.php">Sign in</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>
