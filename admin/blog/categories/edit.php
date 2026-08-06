<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) {
    die("Category not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    $slug = trim($slug, '-');

    if ($name === '') {
        $error = "Category name is required.";
    } else {
        $check = $conn->prepare("SELECT id FROM categories WHERE slug=? AND id!=?");
        $check->bind_param("si", $slug, $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Another category with this slug already exists.";
        } else {
            $update = $conn->prepare("UPDATE categories SET name=?, slug=? WHERE id=?");
            $update->bind_param("ssi", $name, $slug, $id);
            $update->execute();
            $update->close();

            header("Location: index.php");
            exit();
        }

        $check->close();
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Edit Category</h1>
    </div>

    <div class="admin-form">
        <?php if (isset($error)): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:20px;"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrfField(); ?>
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>
            </div>
            <button type="submit" class="btn-primary">Update Category</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
