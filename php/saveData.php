<?php
/**
 * saveData.php — ChromeVault
 * Handles image upload form submission (via both file upload and external URL).
 * Extracts dominant palette, appends metadata to JSON, and redirects to library.
 */

// Start session and include guards/utilities
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Only allow POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../upload.php");
    exit;
}

$userId = $_SESSION['user_id'];
$title = isset($_POST['title']) ? trim($_POST['title']) : 'Untitled';
$collection = isset($_POST['collection']) ? trim($_POST['collection']) : 'General';
$tagsRaw = isset($_POST['tags']) ? trim($_POST['tags']) : '';
$paletteRaw = isset($_POST['palette']) ? $_POST['palette'] : '[]';
$sourceType = isset($_POST['source_type']) ? $_POST['source_type'] : 'file'; // 'file' or 'url'

// Parse tags into clean array
$tags = [];
if ($tagsRaw !== '') {
    $tags = array_map('trim', explode(',', $tagsRaw));
    $tags = array_filter($tags); // remove empty elements
}

// Decode palette array
$palette = json_decode($paletteRaw, true);
if (!is_array($palette)) {
    $palette = ['#8e8e93', '#c7c7cc', '#aeaeb2', '#d1d1d6', '#e5e5ea']; // default grayscale fallback
}

$imagePath = '';
$error = '';

if ($sourceType === 'file') {
    // ── Handle Local File Upload ─────────────────────────────────────────────
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileSize = $_FILES['image_file']['size'];
        $fileType = $_FILES['image_file']['type'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate completely unique filename to avoid overwrites
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../images/';
            
            // Create images directory if it doesn't exist
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $destPath = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagePath = 'images/' . $newFileName;
            } else {
                $error = 'There was an error moving the uploaded file.';
            }
        } else {
            $error = 'Upload failed. Allowed extensions: ' . implode(', ', $allowedExtensions);
        }
    } else {
        $error = 'Please select a valid image file to upload.';
    }
} else if ($sourceType === 'url') {
    // ── Handle Remote Image URL ──────────────────────────────────────────────
    $imageUrl = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
    
    if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        // Simply store the remote URL directly
        $imagePath = $imageUrl;
    } else {
        $error = 'Please enter a valid image URL.';
    }
} else {
    $error = 'Invalid upload mode.';
}

// ── Check if any error occurred ──────────────────────────────────────────────
if ($error !== '') {
    $_SESSION['upload_error'] = $error;
    header("Location: ../upload.php");
    exit;
}

// ── Read and Update images.json ──────────────────────────────────────────────
$imagesPath = __DIR__ . '/../data/images.json';
$images = readJSON($imagesPath);

$newImage = [
    'id' => uniqid('image_'),
    'user_id' => $userId,
    'title' => $title,
    'collection' => $collection === '' ? 'General' : $collection,
    'tags' => array_values($tags),
    'palette' => $palette,
    'image_path' => $imagePath,
    'created_at' => date('c') // ISO 8601 format
];

$images[] = $newImage;
writeJSON($imagesPath, $images);

// ── Read and Update collections.json ─────────────────────────────────────────
$collectionsPath = __DIR__ . '/../data/collections.json';
$collections = readJSON($collectionsPath);

// Make sure to add the collection to collections.json for quick lookup
$colName = $collection === '' ? 'General' : $collection;
$colExists = false;
foreach ($collections as $col) {
    if (isset($col['name']) && strtolower($col['name']) === strtolower($colName)) {
        $colExists = true;
        break;
    }
}

if (!$colExists) {
    $collections[] = [
        'id' => uniqid('col_'),
        'user_id' => $userId,
        'name' => $colName
    ];
    writeJSON($collectionsPath, $collections);
}

// Success redirection
$_SESSION['upload_success'] = 'Image successfully added to Vault!';
header("Location: ../gallery.php");
exit;
