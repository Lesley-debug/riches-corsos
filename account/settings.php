<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . site_url('/account/login.php'));
    exit;
}

require_once __DIR__ . '/../admin/includes/db.php';

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Password must be at least 8 characters';
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPassword, $userId);
                $stmt->execute();
                $success = 'Password changed successfully';
            } else {
                $error = 'Current password is incorrect';
            }
        }
    }
}

require_once __DIR__ . '/../template/header.php';
?>

<div class="account-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Account Settings</h1>
            <p>Manage your account preferences</p>
        </div>

        <div class="settings-container">
            <div class="settings-card">
                <h2>Change Password</h2>
                
                <?php if ($error): ?>
                    <div class="alert-error"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert-success"><?= htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" class="settings-form">
                    <?= csrfField(); ?>
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn-save">Change Password</button>
                </form>
            </div>
            
            <div class="settings-card">
                <h2>Account Actions</h2>
                <div class="action-list">
                    <a href="<?= $basePath; ?>/account/dashboard.php" class="action-link">
                        <span>← Back to Dashboard</span>
                    </a>
                    <a href="<?= $basePath; ?>/account/logout.php" class="action-link danger">
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../template/footer.php'; ?>
