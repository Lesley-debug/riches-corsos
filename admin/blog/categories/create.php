<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if($_POST){
    requireValidCSRF();

    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?,?)");
    $stmt->bind_param("ss", $name, $slug);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Create Category</h1>
    </div>

    <div class="admin-form">
        <form method="POST">
            <?= csrfField(); ?>
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" required>
            </div>
            <button type="submit" class="btn-primary">Create Category</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>