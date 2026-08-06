<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();

    $title = $_POST['title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    // ensure unique slug by appending numeric suffix if collision
    $baseSlug = $slug;
    $suffix = 1;
    while (true) {
        $check = $conn->prepare('SELECT id FROM posts WHERE slug = ?');
        $check->bind_param('s', $slug);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            break;
        }
        $slug = $baseSlug . '-' . $suffix;
        $suffix++;
    }

    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $category_id = $_POST['category_id'];
    $tags = $_POST['tags'] ?? [];

    // Image Upload
    $image = '';
    if (!empty($_FILES['featured_image']['name'])) {
        // Path relative to your project root
        $uploadDir = __DIR__ . '/../../../uploads/'; // go up three levels to richescorsos/uploads
        $filename = time() . '_' . basename($_FILES['featured_image']['name']);
        $image = 'uploads/' . $filename; // store relative path in DB

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create folder if missing
        }

        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
            die('Failed to upload image.');
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO posts (title, slug, excerpt, content, featured_image, status, category_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssi", $title, $slug, $excerpt, $content, $image, $status, $category_id);
    $stmt->execute();

    $post_id = $stmt->insert_id;

    // Insert tags
    foreach ($tags as $tag_id) {
        $tag_stmt = $conn->prepare("
            INSERT INTO post_tags (post_id, tag_id)
            VALUES (?, ?)
        ");
        $tag_stmt->bind_param("ii", $post_id, $tag_id);
        $tag_stmt->execute();
    }

    header("Location: index.php");
    exit();
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");

// Fetch tags
$tags = $conn->query("SELECT * FROM tags");

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Create Blog Post</h1>
    </div>

    <div class="admin-form">

    <form method="POST" enctype="multipart/form-data" id="postForm">
        <?= csrfField(); ?>

        <div class="form-grid">

            <div class="form-main">

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Excerpt</label>
                    <textarea name="excerpt" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" id="editor"></textarea>
                </div>

            </div>

            <div class="form-sidebar">

                <div class="form-box">
                    <label>Status</label>
                    <select name="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>

                <div class="form-box">
                    <label>Category</label>
                    <select name="category_id" required>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id']; ?>">
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-box">
                    <label>Tags</label>
                    <select name="tags[]" multiple id="tagSelect">
                        <?php while($tag = $tags->fetch_assoc()): ?>
                            <option value="<?= $tag['id']; ?>">
                                <?= htmlspecialchars($tag['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-box">
                    <label>Featured Image</label>
                    <input type="file" name="featured_image" id="imageInput">
                    <img id="imagePreview" style="display:none; margin-top:10px; border-radius:10px; max-width:100%;">
                </div>

                <button type="submit" class="primary-btn">Publish Post</button>

            </div>

        </div>

    </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    // Simple textarea - no TinyMCE needed
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

