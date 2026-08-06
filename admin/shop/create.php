<?php
require_once __DIR__ . '/../../inc/security.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
include __DIR__ . '/../includes/db.php';

function createThumbnail($sourcePath, $thumbPath, $thumbWidth = 300)
{
    if (!file_exists($sourcePath)) return false;

    $imageData = file_get_contents($sourcePath);
    if ($imageData === false) return false;

    $sourceImage = imagecreatefromstring($imageData);
    if (!$sourceImage) return false;

    // --- EXIF rotation ---
    $mime = mime_content_type($sourcePath);
    if (function_exists('exif_read_data') && $mime === 'image/jpeg') {
        $exif = @exif_read_data($sourcePath);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
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

    $width  = imagesx($sourceImage);
    $height = imagesy($sourceImage);
    if ($width == 0 || $height == 0) {
        imagedestroy($sourceImage);
        return false;
    }

    $thumbHeight = (int) floor($height * ($thumbWidth / $width));
    $thumbnail   = imagecreatetruecolor($thumbWidth, $thumbHeight);

    // Preserve transparency for PNG/WebP
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

    switch ($mime) {
        case 'image/png':
            imagepng($thumbnail, $thumbPath, 8);
            break;
        case 'image/webp':
            imagewebp($thumbnail, $thumbPath, 85);
            break;
        default:
            imagejpeg($thumbnail, $thumbPath, 85);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($thumbnail);

    return true;
}



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

    // --- Featured Image ---
    $featuredPath = "";
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // 1️⃣ File size limit
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['image']['size'] > $maxSize) {
            die("Image too large. Max allowed size is 5MB.");
        }
        if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $uploadDir = app_path('uploads') . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (in_array($mimeType, $allowedTypes)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $uniqueName = bin2hex(random_bytes(16)) . '.' . strtolower($extension);

            $featuredPath = "uploads/" . $uniqueName;
            move_uploaded_file($_FILES['image']['tmp_name'], app_path($featuredPath));
        }
    }

    // --- Gallery Images & Thumbnails ---
    $galleryPaths = [];
    $thumbnailPaths = [];

    if (!empty($_FILES['gallery']['name'][0])) {
        $thumbDir = app_path('uploads/thumbnails') . '/';
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        foreach ($_FILES['gallery']['name'] as $key => $nameFile) {

            if ($_FILES['gallery']['error'][$key] !== UPLOAD_ERR_OK) continue;

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['gallery']['tmp_name'][$key]);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) continue;

            $extension = strtolower(pathinfo($nameFile, PATHINFO_EXTENSION));
            $uniqueName = bin2hex(random_bytes(16)) . '.' . $extension;
            $originalPath = "uploads/" . $uniqueName;
            $thumbPath = "uploads/thumbnails/" . $uniqueName;

            $fullOriginalPath = app_path($originalPath);
            $fullThumbPath = app_path($thumbPath);

            if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $fullOriginalPath)) {
                createThumbnail($fullOriginalPath, $fullThumbPath, 300);
                $galleryPaths[] = $originalPath;
                $thumbnailPaths[] = $thumbPath;
            }
        }
    }

    $galleryJSON = json_encode($galleryPaths);
    $thumbnailJSON = json_encode($thumbnailPaths);

    $stmt = $conn->prepare("INSERT INTO puppies 
        (title, price, name, breed, age, sex, parent_name, parent_breed, parent_info, vaccination_status, potty_trained, 
        registration_papers, health_certificate, status, category, description, 
        featured_image, gallery, thumbnails) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sdsssssssssssssssss",
        $title,
        $price,
        $name,
        $breed,
        $age,
        $sex,
        $parent_name,
        $parent_breed,
        $parent_info,
        $vaccination_status,
        $potty_trained,
        $registration_papers,
        $health_certificate,
        $status,
        $category,
        $description,
        $featuredPath,
        $galleryJSON,
        $thumbnailJSON
    );

    if (!$stmt->execute()) {
        die('Failed to create puppy: ' . $stmt->error);
    }
    $stmt->close();

    echo "<script>window.location='index.php';</script>";
}
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Add New Puppy</h1>
    </div>

    <form class="admin-form" method="POST" enctype="multipart/form-data">
        <?= csrfField(); ?>
        <!-- Title -->
        <div class="form-group">
            <label>Puppy Title</label>
            <input type="text" name="title" placeholder="Leo (Male Teacup Maltese Puppy)">
        </div>

        <!-- Price -->
        <div class="form-group">
            <label>Price ($)</label>
            <input type="text" name="price" placeholder="$0.00">
        </div>

        <!-- Puppy Details -->
        <div class="form-section">
            <h3 class="section-title">Puppy Details</h3>
            <div class="form-row">
                <div class="form-group"><label>Name</label><input type="text" name="name" placeholder="Leo"></div>
                <div class="form-group"><label>Breed</label><input type="text" name="breed" placeholder="Maltese"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Age</label><input type="text" name="age" placeholder="10 Weeks"></div>
                <div class="form-group"><label>Sex</label>
                    <select name="sex">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Vaccination Status</label><input type="text" name="vaccination_status"></div>
                <div class="form-group"><label>Potty Trained</label>
                    <select name="potty_trained">
                        <option>Yes</option>
                        <option>No</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Registration Papers</label>
                    <select name="registration_papers">
                        <option>Available</option>
                        <option>Not Available</option>
                    </select>
                </div>
                <div class="form-group"><label>Health Certificate / Vet Record</label>
                    <select name="health_certificate">
                        <option>Available</option>
                        <option>Not Available</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Parent Information</h3>
            <div class="form-row">
                <div class="form-group"><label>Parent Name</label><input type="text" name="parent_name" placeholder="Example: Zeus & Luna"></div>
                <div class="form-group"><label>Parent Breed</label><input type="text" name="parent_breed" placeholder="Cane Corso"></div>
            </div>
            <div class="form-group">
                <label>Parent Details</label>
                <textarea name="parent_info" rows="4" placeholder="Share health, temperament, and lineage notes about the parents..."></textarea>
            </div>
        </div>

        <!-- Status & Category -->
        <div class="form-row">
            <div class="form-group"><label>Status</label>
                <select name="status">
                    <option>Available</option>
                    <option>Reserved</option>
                    <option>Sold</option>
                    <option>Draft</option>
                </select>
            </div>
            <div class="form-group"><label>Category</label>
                <select name="category">
                    <option>Puppies</option>
                    <option>Featured</option>
                </select>
            </div>
        </div>

        <!-- Featured Image -->
        <div class="form-group">
            <label>Featured Image</label>
            <input type="file" id="featuredImageInput" name="image" accept="image/*" required>
            <div id="featuredPreview" class="image-preview"></div>
        </div>

        <!-- Gallery Images -->
        <div class="form-group">
            <label>Puppy Gallery Images</label>
            <input type="file" id="galleryInput" name="gallery[]" multiple accept="image/*">
            <div id="galleryPreview" class="image-preview"></div>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label>Detailed Description</label>
            <textarea name="description" rows="6" placeholder="Write something about Leo..."></textarea>
        </div>

        <button type="submit" class="btn-primary">Publish Puppy</button>
    </form>
</main>

<script>
    // utilities for creating image previews
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

    // featured image preview
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

    // gallery images preview
    const galleryInput = document.getElementById('galleryInput');
    const galleryPreview = document.getElementById('galleryPreview');
    if (galleryInput) {
        galleryInput.addEventListener('change', function() {
            galleryPreview.innerHTML = '';
            Array.from(this.files).forEach(file => {
                galleryPreview.appendChild(createImagePreview(file));
            });
        });
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>