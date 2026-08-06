<?php
require_once __DIR__ . '/../../inc/security.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

include __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}
requireValidCSRF();

if (isset($_POST['id'])) {

    $id = intval($_POST['id']);

    // Get images securely
    $stmt = $conn->prepare("SELECT featured_image, gallery FROM puppies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {

        // Delete featured image
        if (!empty($row['featured_image'])) {
            $filePath = app_path($row['featured_image']);
            if (file_exists($filePath)) unlink($filePath);
        }

        // Delete gallery images
        if (!empty($row['gallery'])) {
            $gallery = json_decode($row['gallery'], true);
            if (is_array($gallery)) {
                foreach ($gallery as $img) {
                    $filePath = app_path($img);
                    if (file_exists($filePath)) unlink($filePath);
                }
            }
        }

        // Delete record
        $deleteStmt = $conn->prepare("DELETE FROM puppies WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }

    header("Location: index.php");
    exit();
}
