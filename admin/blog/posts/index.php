<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$stmt = $conn->prepare("
    SELECT posts.*, categories.name AS category_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    ORDER BY posts.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>All Blog Posts</h1>
        <a href="create.php" class="btn-add">+ Create New Post</a>
    </div>

    <div class="table-wrapper">
        <form method="POST" action="delete.php" id="bulkForm">
            <?= csrfField(); ?>
            <div class="bulk-actions">
                <button type="submit" name="bulk_action" value="selected" class="action-btn delete-btn" onclick="return confirmBulk('selected')">Delete Selected</button>
                <button type="submit" name="bulk_action" value="all" class="action-btn delete-btn" onclick="return confirmBulk('all')">Delete All</button>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= (int)$row['id']; ?>"></td>
                            <td>
                                <?php if (!empty($row['featured_image'])): ?>
                                    <img src="<?= htmlspecialchars(normalize_site_url($row['featured_image'])); ?>" class="table-thumb">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td class="post-title">
                                <?= htmlspecialchars($row['title']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?>
                            </td>

                            <td>
                                <span class="badge <?= strtolower($row['status']); ?>">
                                    <?= ucfirst($row['status']); ?>
                                </span>
                            </td>

                            <td>
                                <?= date('M d, Y', strtotime($row['created_at'])); ?>
                            </td>

                            <td class="table-actions">
                                <a href="edit.php?id=<?= $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <button type="submit" name="bulk_action" value="selected" class="action-btn delete-btn"
                                    onclick="document.querySelectorAll('input[name=\'ids[]\']').forEach(c=>c.checked=false); this.closest('tr').querySelector('input[type=checkbox]').checked=true; return confirmBulk('selected')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </form>
    </div>
</main>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('input[name="ids[]"]').forEach(c => c.checked = this.checked);
    });

    function confirmBulk(type) {
        const checked = document.querySelectorAll('input[name="ids[]"]:checked').length;
        if (type === 'all') return confirm('Delete ALL posts? This cannot be undone.');
        if (checked === 0) {
            alert('Please select at least one post.');
            return false;
        }
        return confirm('Delete ' + checked + ' selected post(s)?');
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>