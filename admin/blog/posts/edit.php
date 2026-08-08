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

    // Keep existing image unless a new one is uploaded
    $featured_image = $post['featured_image'] ?? '';

    // Image Upload
    if (!empty($_FILES['featured_image']['name']) && is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
        $uploadDir = __DIR__ . '/../../../uploads/'; // go up three levels to richescorsos/uploads
        $filename = time() . '_' . basename($_FILES['featured_image']['name']);
        $featured_image = 'uploads/' . $filename; // store relative path in DB

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create folder if missing
        }

        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
            die('Failed to upload image.');
        }
    }

    $stmt = $conn->prepare("
        UPDATE posts SET
        title=?, slug=?, excerpt=?, content=?, status=?, category_id=?, featured_image=?
        WHERE id=?
    ");
    $stmt->bind_param("sssssisi", $title, $slug, $excerpt, $content, $status, $category_id, $featured_image, $id);
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

        <form method="POST" id="postForm" enctype="multipart/form-data">
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

                    <div class="form-box">
                        <label>Featured Image</label>
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?= htmlspecialchars(normalize_site_url($post['featured_image'])); ?>"
                                alt="Current featured image"
                                style="width:100%; max-height:180px; object-fit:cover; border-radius:10px; margin-bottom:10px; border:2px solid var(--adm-line);">
                        <?php endif; ?>
                        <input type="file" name="featured_image" id="imageInput" accept="image/*">
                        <img id="imagePreview" style="display:none; margin-top:10px; border-radius:10px; max-width:100%; max-height:200px; object-fit:cover;">
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

    // Image Preview
    document.getElementById('imageInput').addEventListener('change', function(e){
        const reader = new FileReader();
        reader.onload = function(){
            const preview = document.getElementById('imagePreview');
            preview.src = reader.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    });

</script>