<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    requireValidCSRF();

    $name = trim($_POST['name']);

    // Generate slug
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    $slug = trim($slug, '-');

    // Check duplicate slug
    $check = $conn->prepare("SELECT id FROM tags WHERE slug=?");
    $check->bind_param("s", $slug);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        $error = "Tag slug already exists.";
    } else {

        $stmt = $conn->prepare("INSERT INTO tags (name, slug) VALUES (?,?)");
        $stmt->bind_param("ss", $name, $slug);
        $stmt->execute();

        header("Location: index.php");
        exit();
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Create Tag</h1>
    </div>

    <div class="admin-form">
        <?php if(isset($error)): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:20px;"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrfField(); ?>
            <div class="form-group">
                <label>Tag Name</label>
                <input type="text" name="name" required>
            </div>
            <button type="submit" class="btn-primary">Create Tag</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>