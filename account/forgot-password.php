<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';
require_once __DIR__ . '/../admin/includes/db.php';
require_once __DIR__ . '/../inc/email.php';

$message = '';
$error = '';
$step = 'email'; // email -> sent | reset -> (via token link) | done

// Self-installing table for reset tokens.
$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token_hash VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (token_hash)
)");

$tokenTTL = 30 * 60; // 30 minutes

function findValidReset($conn, $token) {
    if (!$token) return null;
    $tokenHash = hash('sha256', $token);
    $stmt = $conn->prepare("SELECT id, email, expires_at, used FROM password_resets WHERE token_hash = ? LIMIT 1");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row || $row['used'] || strtotime($row['expires_at']) < time()) {
        return null;
    }
    return $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    if (isset($_POST['email']) && !isset($_POST['token'])) {
        // Step 1: request a reset link
        $email = trim($_POST['email'] ?? '');

        if ($email) {
            $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $expiresAt = date('Y-m-d H:i:s', time() + $tokenTTL);

                $insert = $conn->prepare("INSERT INTO password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $user['email'], $tokenHash, $expiresAt);
                $insert->execute();

                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                $resetLink = $scheme . $_SERVER['HTTP_HOST'] . site_url('/account/forgot-password.php') . '?token=' . urlencode($token);

                $html = "<h2>Reset your password</h2>
                    <p>We received a request to reset the password for your Riches Corsos account.</p>
                    <p><a href=\"{$resetLink}\" class=\"button\">Reset Password</a></p>
                    <p>This link expires in 30 minutes. If you didn't request this, you can safely ignore this email &mdash; your password will not change.</p>";

                sendEmail($user['email'], $user['name'] ?? '', 'Reset your Riches Corsos password', $html);
            }

            // Always show the same message, whether or not the email exists,
            // so this page can't be used to check which emails are registered.
            $step = 'sent';
        } else {
            $error = 'Please enter your email address';
        }
    } elseif (isset($_POST['new_password']) && isset($_POST['token'])) {
        // Step 2: set the new password (only reachable with a valid, unused, unexpired token)
        $resetRow = findValidReset($conn, $_POST['token']);

        if (!$resetRow) {
            $error = 'This reset link is invalid or has expired. Please request a new one.';
            $step = 'email';
        } else {
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($newPassword !== $confirmPassword) {
                $error = 'Passwords do not match';
                $step = 'reset';
            } elseif (strlen($newPassword) < 8) {
                $error = 'Password must be at least 8 characters';
                $step = 'reset';
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $update->bind_param("ss", $hashedPassword, $resetRow['email']);
                $update->execute();

                $markUsed = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
                $markUsed->bind_param("i", $resetRow['id']);
                $markUsed->execute();

                header('Location: ' . site_url('/account/login.php?reset=success'));
                exit;
            }
        }

        if ($step === 'reset') {
            // Keep the token around so the re-rendered form can still submit.
            $_GET['token'] = $_POST['token'];
        }
    }
} elseif (isset($_GET['token'])) {
    // Someone followed the emailed link.
    $resetRow = findValidReset($conn, $_GET['token']);
    if ($resetRow) {
        $step = 'reset';
    } else {
        $error = 'This reset link is invalid or has expired. Please request a new one.';
        $step = 'email';
    }
}

$currentToken = $_POST['token'] ?? $_GET['token'] ?? '';

require_once __DIR__ . '/../template/header.php';
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1><?php
                if ($step === 'sent') echo 'Check Your Email';
                elseif ($step === 'reset') echo 'Reset Password';
                else echo 'Forgot Password?';
            ?></h1>
            <p class="auth-subtitle"><?php
                if ($step === 'sent') echo 'If an account exists for that email, we\'ve sent a link to reset your password.';
                elseif ($step === 'reset') echo 'Enter your new password';
                else echo 'Enter your email and we\'ll send you a reset link';
            ?></p>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert-success"><?= htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($step === 'email'): ?>
            <form method="POST" class="auth-form">
                <?= csrfField(); ?>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@email.com" required>
                </div>

                <button type="submit" class="btn-auth">Send Reset Link</button>
            </form>
            <?php elseif ($step === 'reset'): ?>
            <form method="POST" class="auth-form">
                <?= csrfField(); ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($currentToken); ?>">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="••••••••" required minlength="8">
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required minlength="8">
                </div>

                <button type="submit" class="btn-auth">Reset Password</button>
            </form>
            <?php endif; // 'sent' step shows no form, just the message ?>

            <p class="auth-footer">
                Remember your password? <a href="<?= $basePath; ?>/account/login.php">Sign in</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>
