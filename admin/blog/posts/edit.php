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
?>

<h2>Edit Post</h2>

<form method="POST">
    <?= csrfField(); ?>

    Title:<br>
    <input type="text" name="title" value="<?= htmlspecialchars($post['title']); ?>"><br><br>

    Excerpt:<br>
    <textarea name="excerpt"><?= htmlspecialchars($post['excerpt']); ?></textarea><br><br>

    Content:<br>
    <textarea name="content" rows="8"><?= htmlspecialchars($post['content']); ?></textarea><br><br>

    Category:<br>
    <select name="category_id">
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['id']; ?>"
                <?= $cat['id'] == $post['category_id'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($cat['name']); ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    Tags:<br>
    <?php while ($tag = $tags->fetch_assoc()): ?>
        <label>
            <input type="checkbox" name="tags[]" value="<?= $tag['id']; ?>"
                <?= in_array($tag['id'], $post_tags) ? 'checked' : ''; ?>>
            <?= htmlspecialchars($tag['name']); ?>
        </label><br>
    <?php endwhile; ?>

    <br>
    Status:<br>
    <select name="status">
        <option value="draft" <?= $post['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
        <option value="published" <?= $post['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
    </select><br><br>

    <button type="submit">Update</button>
</form>
