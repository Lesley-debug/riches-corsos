<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tags WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tag = $stmt->get_result()->fetch_assoc();

if(!$tag){
    die("Tag not found.");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    requireValidCSRF();

    $name = trim($_POST['name']);
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    $slug = trim($slug, '-');

    // Check duplicate slug except current tag
    $check = $conn->prepare("SELECT id FROM tags WHERE slug=? AND id!=?");
    $check->bind_param("si", $slug, $id);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        $error = "Another tag with this slug already exists.";
    } else {

        $update = $conn->prepare("UPDATE tags SET name=?, slug=? WHERE id=?");
        $update->bind_param("ssi", $name, $slug, $id);
        $update->execute();

        header("Location: index.php");
        exit();
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Edit Tag</h1>
    </div>

    <div class="admin-form">
        <?php if(isset($error)): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:20px;"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrfField(); ?>
            <div class="form-group">
                <label>Tag Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($tag['name']); ?>" required>
            </div>
            <button type="submit" class="btn-primary">Update Tag</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>