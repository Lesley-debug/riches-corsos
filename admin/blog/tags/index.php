<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$tags = $conn->query("SELECT * FROM tags ORDER BY name ASC");
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Tags</h1>
        <a href="create.php" class="btn-add">+ Add New Tag</a>
    </div>

    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($tag = $tags->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($tag['name']); ?></strong></td>
                    <td><?= htmlspecialchars($tag['slug']); ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $tag['id']; ?>" class="action-btn edit-btn">Edit</a>
                        <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this tag?')">
                            <?= csrfField(); ?>
                            <input type="hidden" name="id" value="<?= $tag['id']; ?>">
                            <button type="submit" class="action-btn delete-btn" style="background:none;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>