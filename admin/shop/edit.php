<?php

require_once __DIR__ . '/../../inc/security.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id'] ?? $_POST['id']);


/* ===============================
   FETCH EXISTING POST
================================= */
$stmt = $conn->prepare("SELECT * FROM puppies WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    header("Location: index.php");
    exit();
}

/* ===============================
   UPDATE POST
================================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    requireValidCSRF();

    $title = trim($_POST['title']);
    $price = floatval($_POST['price']);
    $name = trim($_POST['name']);
    $breed = trim($_POST['breed']);
    $age = trim($_POST['age']);
    $sex = trim($_POST['sex']);
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_breed = trim($_POST['parent_breed'] ?? '');
    $parent_info = trim($_POST['parent_info'] ?? '');
    $status = trim($_POST['status']);
    $status = ucfirst(strtolower($status));
    if (!in_array($status, ['Available', 'Reserved', 'Sold', 'Draft'], true)) {
        $status = 'Available';
    }
    $category = trim($_POST['category']);
    $category = ucfirst(strtolower($category));
    if (!in_array($category, ['Puppies', 'Featured'], true)) {
        $category = 'Puppies';
    }
    $description = trim($_POST['description']);

    $featuredPath = $post['featured_image'];

    /* === Replace Featured Image === */
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (in_array($mimeType, $allowedTypes)) {
            if (!empty($post['featured_image'])) {
                $oldPath = app_path($post['featured_image']);
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $uniqueName = bin2hex(random_bytes(16)) . '.' . $extension;
            $featuredPath = 'uploads/' . $uniqueName;
            move_uploaded_file($_FILES['image']['tmp_name'], app_path($featuredPath));
        }
    }

    $parentImagePath = $post['parent_image'] ?? '';
    if (!empty($_FILES['parent_image']['name']) && $_FILES['parent_image']['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['parent_image']['tmp_name']);
        finfo_close($finfo);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($mimeType, $allowedTypes)) {
            if (!empty($post['parent_image'])) {
                $oldPath = app_path($post['parent_image']);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $extension = strtolower(pathinfo($_FILES['parent_image']['name'], PATHINFO_EXTENSION));
            $uniqueName = bin2hex(random_bytes(16)) . '.' . $extension;
            $parentImagePath = 'uploads/' . $uniqueName;
            move_uploaded_file($_FILES['parent_image']['tmp_name'], app_path($parentImagePath));
        }
    }

    /* === Handle Gallery === */
    $gallery = json_decode($post['gallery'], true);
    if (!is_array($gallery)) $gallery = [];

    // Delete selected gallery images
    if (!empty($_POST['delete_gallery'])) {
        foreach ($_POST['delete_gallery'] as $deleteImg) {

            $filePath = app_path($deleteImg);
            if (file_exists($filePath)) unlink($filePath);

            $gallery = array_filter($gallery, function ($img) use ($deleteImg) {
                return $img !== $deleteImg;
            });
        }
    }

    // Add new gallery images
    if (!empty($_FILES['gallery']['name'][0])) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        foreach ($_FILES['gallery']['name'] as $key => $nameFile) {

            if ($_FILES['gallery']['error'][$key] !== UPLOAD_ERR_OK) continue;

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['gallery']['tmp_name'][$key]);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) continue;

            $extension = strtolower(pathinfo($nameFile, PATHINFO_EXTENSION));
            $uniqueName = bin2hex(random_bytes(16)) . '.' . $extension;
            $path = 'uploads/' . $uniqueName;

            if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], app_path($path))) {
                $gallery[] = $path;
            }
        }
    }

    $galleryJSON = json_encode(array_values($gallery));

    /* === UPDATE DATABASE === */
    $update = $conn->prepare("UPDATE puppies SET
        title=?, price=?, name=?, breed=?, age=?, sex=?, parent_name=?, parent_breed=?, parent_info=?, parent_image=?,
        status=?, category=?, description=?,
        featured_image=?, gallery=?
        WHERE id=?");

    $update->bind_param(
        "sdsssssssssssssi",
        $title,
        $price,
        $name,
        $breed,
        $age,
        $sex,
        $parent_name,
        $parent_breed,
        $parent_info,
        $parentImagePath,
        $status,
        $category,
        $description,
        $featuredPath,
        $galleryJSON,
        $id
    );

    if (!$update->execute()) {
        die('Update failed: ' . $update->error);
    }
    $update->close();

    header("Location: index.php");
    exit();
}
?>
<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<main class="admin-content">

    <h1>Edit Puppy</h1>

    <form method="POST" action="edit.php?id=<?= (int)$post['id']; ?>" enctype="multipart/form-data" class="admin-form">
        <?= csrfField(); ?>

        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']); ?>">
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="text" name="price" value="<?= $post['price']; ?>">
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($post['name']); ?>">
        </div>

        <div class="form-group">
            <label>Breed</label>
            <input type="text" name="breed" value="<?= htmlspecialchars($post['breed']); ?>">
        </div>

        <div class="form-group">
            <label>Age</label>
            <input type="text" name="age" value="<?= htmlspecialchars($post['age']); ?>">
        </div>

        <div class="form-group">
            <label>Sex</label>
            <select name="sex">
                <option <?= $post['sex'] == "Male" ? "selected" : "" ?>>Male</option>
                <option <?= $post['sex'] == "Female" ? "selected" : "" ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option <?= $post['status'] == "Available" ? "selected" : "" ?>>Available</option>
                <option <?= $post['status'] == "Reserved" ? "selected" : "" ?>>Reserved</option>
                <option <?= $post['status'] == "Sold" ? "selected" : "" ?>>Sold</option>
                <option <?= $post['status'] == "Draft" ? "selected" : "" ?>>Draft</option>
            </select>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category">
                <option <?= $post['category'] == "Puppies" ? "selected" : "" ?>>Puppies</option>
                <option <?= $post['category'] == "Featured" ? "selected" : "" ?>>Featured</option>
            </select>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5"><?= htmlspecialchars($post['description']); ?></textarea>
        </div>

        <div class="form-section">
            <h3>Parent Information</h3>
            <div class="form-group">
                <label>Parent Name</label>
                <input type="text" name="parent_name" value="<?= htmlspecialchars($post['parent_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Parent Breed</label>
                <input type="text" name="parent_breed" value="<?= htmlspecialchars($post['parent_breed'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Parent Image</label>
                <?php if (!empty($post['parent_image'])): ?>
                    <img src="<?= htmlspecialchars(normalize_site_url($post['parent_image'])); ?>" width="120" style="display:block;margin-bottom:8px;border-radius:8px;"><br>
                <?php endif; ?>
                <input type="file" name="parent_image" accept="image/*">
            </div>
            <div class="form-group">
                <label>Parent Details</label>
                <textarea name="parent_info" rows="4"><?= htmlspecialchars($post['parent_info'] ?? ''); ?></textarea>
            </div>
        </div>

        <hr>

        <h3>Featured Image</h3>

        <?php if (!empty($post['featured_image'])): ?>
            <img src="<?= htmlspecialchars(normalize_site_url($post['featured_image'])); ?>" width="120"><br><br>
        <?php endif; ?>

        <input type="file" name="image">

        <hr>

        <h3>Gallery Images</h3>

        <?php
        $gallery = json_decode($post['gallery'], true);
        if (is_array($gallery)) :
            foreach ($gallery as $img) :
        ?>

                <div style="display:inline-block;margin:10px;text-align:center;">
                    <img src="<?= htmlspecialchars(normalize_site_url($img)); ?>" width="100"><br>
                    <label>
                        <input type="checkbox" name="delete_gallery[]" value="<?= htmlspecialchars($img); ?>">
                        Delete
                    </label>
                </div>

        <?php endforeach;
        endif; ?>

        <br><br>
        <label>Add More Images</label>
        <input type="file" name="gallery[]" multiple>

        <br><br>
        <button type="submit" class="btn-primary">Update Puppy</button>

    </form>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>