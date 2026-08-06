<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}
requireValidCSRF();

$id = (int)($_POST['id'] ?? 0);

// Remove relations
$stmt = $conn->prepare("DELETE FROM post_tags WHERE tag_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Delete tag
$stmt = $conn->prepare("DELETE FROM tags WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit();
