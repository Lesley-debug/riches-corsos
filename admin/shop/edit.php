<?php
require_once __DIR__ . '/../../inc/security.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!function_exists('puppyStoredPathIsLocal')) {
    function puppyStoredPathIsLocal(?string $path): bool
    {
        $path = str_replace('\\', '/', trim((string)$path));

        return $path !== '' && !preg_match('#^https?://#i', $path) && !str_contains($path, '..');
    }
}

if (!function_exists('puppyDeleteStoredFile')) {
    function puppyDeleteStoredFile(?string $path): void
    {
        if (!puppyStoredPathIsLocal($path)) {
            return;
        }

        $appRoot = realpath(app_path('')) ?: app_path('');
        $filePath = realpath(app_path((string)$path));

        if ($filePath === false) {
            return;
        }

        $normalizedRoot = str_replace('\\', '/', rtrim($appRoot, '/'));
        $normalizedFile = str_replace('\\', '/', $filePath);

        if (str_starts_with($normalizedFile, $normalizedRoot . '/') && is_file($filePath)) {
            unlink($filePath);
        }
    }
}

if (!function_exists('puppyUploadedImagePath')) {
    function puppyUploadedImagePath(array $file): ?string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if (empty($file['name']) || $error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error !== UPLOAD_ERR_OK) {
            return null;
        }

        $maxSize = 5 * 1024 * 1024;
        if (($file['size'] ?? 0) > $maxSize) {
            die('Image too large. Max allowed size is 5MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return null;
        }

        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($extensionMap[$mimeType])) {
            return null;
        }

        $uploadDir = app_path('uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $extension = $extensionMap[$mimeType];
        }

        $path = 'uploads/' . bin2hex(random_bytes(16)) . '.' . $extension;

        return move_uploaded_file($file['tmp_name'], app_path($path)) ? $path : null;
    }
}

if (!function_exists('puppyCreateThumbnail')) {
    function puppyCreateThumbnail(string $sourcePath, string $thumbPath, int $thumbWidth = 300): bool
    {
        if (!function_exists('imagecreatefromstring') || !is_file($sourcePath)) {
            return false;
        }

        $imageData = file_get_contents($sourcePath);
        if ($imageData === false) {
            return false;
        }

        $sourceImage = imagecreatefromstring($imageData);
        if (!$sourceImage) {
            return false;
        }

        $mime = mime_content_type($sourcePath);
        if (function_exists('exif_read_data') && $mime === 'image/jpeg') {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                switch ((int)$exif['Orientation']) {
                    case 3:
                        $sourceImage = imagerotate($sourceImage, 180, 0);
                        break;
                    case 6:
                        $sourceImage = imagerotate($sourceImage, -90, 0);
                        break;
                    case 8:
                        $sourceImage = imagerotate($sourceImage, 90, 0);
                        break;
                }
            }
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        if ($width <= 0 || $height <= 0) {
            imagedestroy($sourceImage);
            return false;
        }

        $thumbHeight = max(1, (int)floor($height * ($thumbWidth / $width)));
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);

        imagecopyresampled(
            $thumbnail,
            $sourceImage,
            0,
            0,
            0,
            0,
            $thumbWidth,
            $thumbHeight,
            $width,
            $height
        );

        $thumbDir = dirname($thumbPath);
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        $saved = match ($mime) {
            'image/png' => imagepng($thumbnail, $thumbPath, 8),
            'image/webp' => imagewebp($thumbnail, $thumbPath, 85),
            default => imagejpeg($thumbnail, $thumbPath, 85),
        };

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return (bool)$saved;
    }
}

if (!function_exists('puppyThumbnailPathFor')) {
    function puppyThumbnailPathFor(string $imagePath): string
    {
        return 'uploads/thumbnails/' . basename(str_replace('\\', '/', $imagePath));
    }
}

if (!function_exists('puppySyncGalleryThumbnails')) {
    function puppySyncGalleryThumbnails(array $gallery, array $existingThumbnails): array
    {
        $synced = [];

        foreach ($gallery as $imagePath) {
            $imagePath = str_replace('\\', '/', trim((string)$imagePath));
            if ($imagePath === '') {
                continue;
            }

            $thumbnailPath = puppyThumbnailPathFor($imagePath);
            $imageBaseName = basename($imagePath);

            foreach ($existingThumbnails as $existingThumbnail) {
                $existingThumbnail = str_replace('\\', '/', trim((string)$existingThumbnail));
                if (basename($existingThumbnail) === $imageBaseName && is_file(app_path($existingThumbnail))) {
                    $thumbnailPath = $existingThumbnail;
                    break;
                }
            }

            if (!is_file(app_path($thumbnailPath)) && puppyStoredPathIsLocal($imagePath)) {
                puppyCreateThumbnail(app_path($imagePath), app_path($thumbnailPath));
            }

            $synced[] = is_file(app_path($thumbnailPath)) ? $thumbnailPath : $imagePath;
        }

        return $synced;
    }
}

if (!function_exists('puppySelected')) {
    function puppySelected(?string $current, string $option): string
    {
        return strcasecmp(trim((string)$current), $option) === 0 ? ' selected' : '';
    }
}
// NOTE: header.php and sidebar.php are NOT included here anymore.
// They used to be included at the top, which printed HTML before we
// knew whether this was a POST (update) request. Once ANY output is
// sent to the browser, PHP can no longer send new HTTP headers - so
// header("Location: index.php") later on would silently fail, and
// you'd get a blank/half-rendered page instead of a redirect.
// Fix: only include the page chrome AFTER all POST handling (and any
// possible redirect) is fully done. See bottom of the POST block and
// just above the HTML form below.

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

    $title = trim($_POST['title'] ?? '');
    $price = isset($_POST['price']) ? floatval(str_replace(['$', ','], '', $_POST['price'])) : 0;
    $name = trim($_POST['name'] ?? '');
    $breed = trim($_POST['breed'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $sex = trim($_POST['sex'] ?? '');
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_breed = trim($_POST['parent_breed'] ?? '');
    $parent_info = trim($_POST['parent_info'] ?? '');
    $vaccination_status = trim($_POST['vaccination_status'] ?? '');
    $potty_trained = trim($_POST['potty_trained'] ?? '');
    $registration_papers = trim($_POST['registration_papers'] ?? '');
    $health_certificate = trim($_POST['health_certificate'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $status = ucfirst(strtolower($status));
    if (!in_array($status, ['Available', 'Reserved', 'Sold', 'Draft'], true)) {
        $status = 'Available';
    }
    $category = trim($_POST['category'] ?? '');
    $category = ucfirst(strtolower($category));
    if (!in_array($category, ['Puppies', 'Featured'], true)) {
        $category = 'Puppies';
    }
    $description = trim($_POST['description'] ?? '');

    $featuredPath = $post['featured_image'] ?? '';

    /* === Replace Featured Image === */
    $newFeaturedPath = puppyUploadedImagePath($_FILES['image'] ?? []);
    if ($newFeaturedPath !== null) {
        puppyDeleteStoredFile($featuredPath);
        $featuredPath = $newFeaturedPath;
    }

    /* === Replace Parent Image === */
    $parentImagePath = $post['parent_image'] ?? '';
    $newParentImagePath = puppyUploadedImagePath($_FILES['parent_image'] ?? []);
    if ($newParentImagePath !== null) {
        puppyDeleteStoredFile($parentImagePath);
        $parentImagePath = $newParentImagePath;
    }

    /* === Handle Gallery === */
    $gallery = json_decode((string)($post['gallery'] ?? '[]'), true);
    if (!is_array($gallery)) $gallery = [];
    $thumbnails = json_decode((string)($post['thumbnails'] ?? '[]'), true);
    if (!is_array($thumbnails)) $thumbnails = [];

    // Delete selected gallery images
    if (!empty($_POST['delete_gallery']) && is_array($_POST['delete_gallery'])) {
        foreach ($_POST['delete_gallery'] as $deleteImg) {
            $deleteImg = str_replace('\\', '/', trim((string)$deleteImg));

            if (!in_array($deleteImg, $gallery, true)) {
                continue;
            }

            $galleryIndex = array_search($deleteImg, $gallery, true);
            $thumbCandidates = [puppyThumbnailPathFor($deleteImg)];
            if ($galleryIndex !== false && isset($thumbnails[$galleryIndex])) {
                $thumbCandidates[] = $thumbnails[$galleryIndex];
            }
            foreach ($thumbnails as $thumbnail) {
                if (basename(str_replace('\\', '/', (string)$thumbnail)) === basename($deleteImg)) {
                    $thumbCandidates[] = $thumbnail;
                }
            }

            puppyDeleteStoredFile($deleteImg);
            foreach (array_unique($thumbCandidates) as $thumbnail) {
                puppyDeleteStoredFile($thumbnail);
            }

            $gallery = array_filter($gallery, function ($img) use ($deleteImg) {
                return $img !== $deleteImg;
            });
            $thumbnails = array_filter($thumbnails, function ($thumbnail) use ($deleteImg) {
                return basename(str_replace('\\', '/', (string)$thumbnail)) !== basename($deleteImg);
            });
        }
    }

    // Add new gallery images
    if (!empty($_FILES['gallery']['name'][0]) && is_array($_FILES['gallery']['name'])) {
        foreach ($_FILES['gallery']['name'] as $key => $nameFile) {
            $path = puppyUploadedImagePath([
                'name' => $nameFile,
                'type' => $_FILES['gallery']['type'][$key] ?? '',
                'tmp_name' => $_FILES['gallery']['tmp_name'][$key] ?? '',
                'error' => $_FILES['gallery']['error'][$key] ?? UPLOAD_ERR_NO_FILE,
                'size' => $_FILES['gallery']['size'][$key] ?? 0,
            ]);

            if ($path !== null) {
                $gallery[] = $path;
            }
        }
    }

    $gallery = array_values($gallery);
    $thumbnailPaths = puppySyncGalleryThumbnails($gallery, array_values($thumbnails));
    $galleryJSON = json_encode($gallery, JSON_UNESCAPED_SLASHES) ?: '[]';
    $thumbnailJSON = json_encode($thumbnailPaths, JSON_UNESCAPED_SLASHES) ?: '[]';

    /* === UPDATE DATABASE === */
    $update = $conn->prepare("UPDATE puppies SET
        title=?, price=?, name=?, breed=?, age=?, sex=?, parent_name=?, parent_breed=?, parent_info=?, parent_image=?,
        vaccination_status=?, potty_trained=?, registration_papers=?, health_certificate=?,
        status=?, category=?, description=?,
        featured_image=?, gallery=?, thumbnails=?
        WHERE id=?");

    // Safety check: if the query has a typo or a column doesn't exist,
    // prepare() returns false instead of a statement object. Calling
    // bind_param() on false would fatal-error and (with display_errors
    // off) show a blank page with no clue why. This makes it loud instead.
    if (!$update) {
        die('Prepare failed: ' . $conn->error);
    }

    $types = 'sd' . str_repeat('s', 18) . 'i';
    $update->bind_param(
        $types,
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
        $vaccination_status,
        $potty_trained,
        $registration_papers,
        $health_certificate,
        $status,
        $category,
        $description,
        $featuredPath,
        $galleryJSON,
        $thumbnailJSON,
        $id
    );

    if (!$update->execute()) {
        die('Update failed: ' . $update->error);
    }
    $update->close();

    header("Location: index.php");
    exit();
}

/* ===============================
   ONLY NOW include page chrome.
   By this point, a POST request has either redirected and exited above,
   or this is a plain GET request loading the edit form - either way,
   nothing has been echoed yet, so header() calls above were always safe.
================================= */
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<main class="admin-content">

    <h1>Edit Puppy</h1>

    <form method="POST" action="edit.php?id=<?= (int)$post['id']; ?>" enctype="multipart/form-data" class="admin-form">
        <?= csrfField(); ?>
        <input type="hidden" name="id" value="<?= (int)$post['id']; ?>">

        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="text" name="price" value="<?= htmlspecialchars((string)($post['price'] ?? '')); ?>">
        </div>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($post['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Breed</label>
            <input type="text" name="breed" value="<?= htmlspecialchars($post['breed'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Age</label>
            <input type="text" name="age" value="<?= htmlspecialchars($post['age'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Sex</label>
            <select name="sex">
                <option value="Male"<?= puppySelected($post['sex'] ?? '', 'Male'); ?>>Male</option>
                <option value="Female"<?= puppySelected($post['sex'] ?? '', 'Female'); ?>>Female</option>
            </select>
        </div>

        <div class="form-section">
            <h3>Puppy Health & Papers</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Vaccination Status</label>
                    <input type="text" name="vaccination_status" value="<?= htmlspecialchars($post['vaccination_status'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Potty Trained</label>
                    <select name="potty_trained">
                        <option value="Yes"<?= puppySelected($post['potty_trained'] ?? '', 'Yes'); ?>>Yes</option>
                        <option value="No"<?= puppySelected($post['potty_trained'] ?? '', 'No'); ?>>No</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Registration Papers</label>
                    <select name="registration_papers">
                        <option value="Available"<?= puppySelected($post['registration_papers'] ?? '', 'Available'); ?>>Available</option>
                        <option value="Not Available"<?= puppySelected($post['registration_papers'] ?? '', 'Not Available'); ?>>Not Available</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Health Certificate / Vet Record</label>
                    <select name="health_certificate">
                        <option value="Available"<?= puppySelected($post['health_certificate'] ?? '', 'Available'); ?>>Available</option>
                        <option value="Not Available"<?= puppySelected($post['health_certificate'] ?? '', 'Not Available'); ?>>Not Available</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="Available"<?= puppySelected($post['status'] ?? '', 'Available'); ?>>Available</option>
                <option value="Reserved"<?= puppySelected($post['status'] ?? '', 'Reserved'); ?>>Reserved</option>
                <option value="Sold"<?= puppySelected($post['status'] ?? '', 'Sold'); ?>>Sold</option>
                <option value="Draft"<?= puppySelected($post['status'] ?? '', 'Draft'); ?>>Draft</option>
            </select>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category">
                <option value="Puppies"<?= puppySelected($post['category'] ?? '', 'Puppies'); ?>>Puppies</option>
                <option value="Featured"<?= puppySelected($post['category'] ?? '', 'Featured'); ?>>Featured</option>
            </select>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5"><?= htmlspecialchars($post['description'] ?? ''); ?></textarea>
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
                <input type="file" id="parentImageInput" name="parent_image" accept="image/*">
                <div id="parentPreview" class="image-preview"></div>
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

        <input type="file" id="featuredImageInput" name="image" accept="image/*">
        <div id="featuredPreview" class="image-preview"></div>

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
                        <input type="checkbox" name="delete_gallery[]" value="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>">
                        Delete
                    </label>
                </div>

        <?php endforeach;
        endif; ?>

        <br><br>
        <label>Add More Images</label>
        <input type="file" id="galleryInput" name="gallery[]" multiple accept="image/*">
        <div id="galleryPreview" class="image-preview"></div>

        <br><br>
        <button type="submit" class="btn-primary">Update Puppy</button>

    </form>

</main>

<script>
    function createImagePreview(file) {
        const reader = new FileReader();
        const img = document.createElement('img');
        img.className = 'preview-thumb';
        reader.onload = function(e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
        return img;
    }

    const featuredInput = document.getElementById('featuredImageInput');
    const featuredPreview = document.getElementById('featuredPreview');
    if (featuredInput) {
        featuredInput.addEventListener('change', function() {
            featuredPreview.innerHTML = '';
            if (this.files && this.files[0]) {
                featuredPreview.appendChild(createImagePreview(this.files[0]));
            }
        });
    }

    const galleryInput = document.getElementById('galleryInput');
    const galleryPreview = document.getElementById('galleryPreview');
    if (galleryInput) {
        galleryInput.addEventListener('change', function() {
            galleryPreview.innerHTML = '';
            Array.from(this.files || []).forEach(file => {
                galleryPreview.appendChild(createImagePreview(file));
            });
        });
    }

    const parentInput = document.getElementById('parentImageInput');
    const parentPreview = document.getElementById('parentPreview');
    if (parentInput) {
        parentInput.addEventListener('change', function() {
            parentPreview.innerHTML = '';
            if (this.files && this.files[0]) {
                parentPreview.appendChild(createImagePreview(this.files[0]));
            }
        });
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
