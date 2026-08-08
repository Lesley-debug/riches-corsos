<?php

require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = (int)($_GET['id'] ?? 0);

$post_stmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$post_stmt->bind_param("i", $id);
$post_stmt->execute();
$post = $post_stmt->get_result()->fetch_assoc();

$categories = $conn->query("SELECT * FROM categories");
$tags = $conn->query("SELECT * FROM tags");

$post_tags = [];
$tag_stmt = $conn->prepare("SELECT tag_id FROM post_tags WHERE post_id=?");
$tag_stmt->bind_param("i", $id);
$tag_stmt->execute();
$result = $tag_stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $post_tags[] = $row['tag_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $title = $_POST['title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $category_id = $_POST['category_id'];
    $tags_input = $_POST['tags'] ?? [];

    $stmt = $conn->prepare("
        UPDATE posts SET
        title=?, slug=?, excerpt=?, content=?, status=?, category_id=?
        WHERE id=?
    ");
    $stmt->bind_param("sssssii", $title, $slug, $excerpt, $content, $status, $category_id, $id);
    $stmt->execute();

    // Resync tags
    $resync = $conn->prepare("DELETE FROM post_tags WHERE post_id=?");
    $resync->bind_param("i", $id);
    $resync->execute();

    foreach ($tags_input as $tag_id) {
        $insert = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?,?)");
        $insert->bind_param("ii", $id, $tag_id);
        $insert->execute();
    }

    header("Location: index.php");
    exit();
}

// Only include page chrome after all POST handling is done
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Edit Blog Post</h1>
    </div>

    <div class="admin-form">

        <form method="POST" id="postForm">
            <?= csrfField(); ?>

            <div class="form-grid">

                <div class="form-main">

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($post['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Excerpt</label>
                        <textarea name="excerpt" rows="3"><?= htmlspecialchars($post['excerpt']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="content" id="editor"><?= htmlspecialchars($post['content']); ?></textarea>
                    </div>

                </div>

                <div class="form-sidebar">

                    <div class="form-box">
                        <label>Status</label>
                        <select name="status">
                            <option value="draft" <?= $post['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?= $post['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>

                    <div class="form-box">
                        <label>Category</label>
                        <select name="category_id">
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['id']; ?>"
                                    <?= $cat['id'] == $post['category_id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-box">
                        <label>Tags</label>
                        <select name="tags[]" multiple id="tagSelect">
                            <?php while ($tag = $tags->fetch_assoc()): ?>
                                <option value="<?= $tag['id']; ?>"
                                    <?= in_array($tag['id'], $post_tags) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($tag['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="primary-btn">Update Post</button>

                </div>

            </div>

        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    // Rich content editor
    document.getElementById('editor').style.minHeight = '400px';

    // Tom Select for Tags
    new TomSelect("#tagSelect", {
        plugins: ['remove_button'],
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        }
    });
</script>