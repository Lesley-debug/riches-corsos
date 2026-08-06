<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$timeoutMessage = isset($_GET['timeout']) ? 'Session expired. Please login again.' : '';
$lockedMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // Self-installing brute-force lockout table: 5 failed attempts locks that IP out for 15 minutes.
    $conn->query("CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        attempts INT NOT NULL DEFAULT 1,
        last_attempt DATETIME NOT NULL,
        UNIQUE KEY ip_unique (ip_address)
    )");

    $lockoutSeconds = 15 * 60;
    $maxAttempts = 5;

    $stmt = $conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $attemptRow = $stmt->get_result()->fetch_assoc();

    $isLocked = false;
    if ($attemptRow && $attemptRow['attempts'] >= $maxAttempts) {
        $secondsSince = time() - strtotime($attemptRow['last_attempt']);
        if ($secondsSince < $lockoutSeconds) {
            $isLocked = true;
            $minutesLeft = ceil(($lockoutSeconds - $secondsSince) / 60);
            $lockedMessage = "Too many failed attempts. Try again in {$minutesLeft} minute(s).";
        }
    }

    if ($isLocked) {
        // fall through, form re-renders with lockout message
    } elseif (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM admin WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if (($user = $result->fetch_assoc()) && password_verify($password, $user['password'])) {

            // Success: clear any lockout record for this IP.
            $del = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
            $del->bind_param("s", $ip);
            $del->execute();

            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['last_activity'] = time();

            header("Location: dashboard.php");
            exit();
        } else {
            // Failed: record/increment the attempt for this IP.
            $upsert = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt)
                VALUES (?, 1, NOW())
                ON DUPLICATE KEY UPDATE
                    attempts = IF(last_attempt < DATE_SUB(NOW(), INTERVAL ? SECOND), 1, attempts + 1),
                    last_attempt = NOW()");
            $upsert->bind_param("si", $ip, $lockoutSeconds);
            $upsert->execute();

            $error = "Invalid credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — RichesCorsors</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-login.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <p class="subtitle">Enter your credentials to access the dashboard</p>
        
        <?php if($timeoutMessage): ?>
            <div class="error"><?= htmlspecialchars($timeoutMessage); ?></div>
        <?php endif; ?>

        <?php if($lockedMessage): ?>
            <div class="error"><?= htmlspecialchars($lockedMessage); ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <?= csrfField(); ?>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="admin@example.com" <?= $lockedMessage ? 'disabled' : ''; ?>>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••" <?= $lockedMessage ? 'disabled' : ''; ?>>
            </div>
            <button type="submit" <?= $lockedMessage ? 'disabled' : ''; ?>>Sign In</button>
        </form>
    </div>
</body>
</html>

