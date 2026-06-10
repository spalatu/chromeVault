<?php
require_once 'functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $colors_json = $_POST['colors'] ?? '[]';
        $colors = json_decode($colors_json, true) ?: [];
        $file_last_modified = $_POST['last_modified'] ?? time();
        
        if (empty($title)) {
             echo json_encode(['success' => false, 'message' => 'Title is required']);
             exit;
        }

        $dest_path = '';
        $ext = 'jpg'; // Default extension
        
        if (isset($_POST['unsplash_url']) && !empty($_POST['unsplash_url'])) {
            // Download from Unsplash
            $url = $_POST['unsplash_url'];
            $new_filename = uniqid('img_') . '.jpg';
            $upload_dir = __DIR__ . '/../data/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $dest_path = $upload_dir . $new_filename;
            $img_content = file_get_contents($url);
            if ($img_content === false || !file_put_contents($dest_path, $img_content)) {
                echo json_encode(['success' => false, 'message' => 'Failed to download from Unsplash']);
                exit;
            }
        } else {
            // Local file upload
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                 echo json_encode(['success' => false, 'message' => 'Image upload failed']);
                 exit;
            }

            $file = $_FILES['image'];
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mime_type = mime_content_type($file['tmp_name']);
            
            if (!in_array($mime_type, $allowed_mimes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, WebP are allowed.']);
                exit;
            }
            
            $upload_dir = __DIR__ . '/../data/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('img_') . '.' . $ext;
            $dest_path = $upload_dir . $new_filename;
            
            if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
                 echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
                 exit;
            }
        }
        
        // Extract metadata
        $size_bytes = filesize($dest_path);
        $size_kb = round($size_bytes / 1024, 2);
        $size_str = $size_kb > 1024 ? round($size_kb / 1024, 2) . ' MB' : $size_kb . ' KB';
        
        $img_info = getimagesize($dest_path);
        $dimensions = $img_info ? $img_info[0] . ' × ' . $img_info[1] : 'Unknown';
        
        $type_str = strtoupper($ext);
        if ($type_str === 'JPEG') $type_str = 'JPG';
        
        $now = date('Y/m/d H:i');
        $created_date = date('Y/m/d H:i', $file_last_modified / 1000); // From JS lastModified (ms)
        
        $items = read_json('items.json');
        
        $new_item = [
            'id' => uniqid(),
            'user_id' => $_SESSION['user_id'],
            'title' => htmlspecialchars($title),
            'url' => 'data/uploads/' . $new_filename,
            'dimensions' => $dimensions,
            'size' => $size_str,
            'type' => $type_str,
            'date_imported' => $now,
            'date_created' => $created_date,
            'date_modified' => $created_date,
            'rating' => 0,
            'notes' => '',
            'colors' => $colors,
            'tags' => ['Design'],
            'folders' => ['Wrenbridge - Visual Journal']
        ];

        array_unshift($items, $new_item);
        write_json('items.json', $items);
        
        echo json_encode(['success' => true, 'item' => $new_item]);
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $items = read_json('items.json');
        $new_items = [];
        $deleted = false;
        
        foreach ($items as $item) {
            if ($item['id'] === $id && $item['user_id'] === $_SESSION['user_id']) {
                $deleted = true;
                // Delete physical file
                $filepath = __DIR__ . '/../' . $item['url'];
                if (file_exists($filepath) && strpos($item['url'], 'data/uploads/') === 0) {
                    unlink($filepath);
                }
                continue; // Skip the item to delete
            }
            $new_items[] = $item;
        }
        
        if ($deleted) {
            write_json('items.json', $new_items);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found or unauthorized']);
        }
        exit;
    }

    if ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        
        $items = read_json('items.json');
        $found = false;
        
        foreach ($items as &$item) {
            if ($item['id'] === $id && $item['user_id'] === $_SESSION['user_id']) {
                if ($title !== '') $item['title'] = htmlspecialchars($title);
                if ($notes !== '') $item['notes'] = htmlspecialchars($notes);
                $item['date_modified'] = date('Y/m/d H:i');
                $found = true;
                break;
            }
        }
        
        if ($found) {
            write_json('items.json', $items);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found or unauthorized']);
        }
        exit;
    }
    if ($action === 'trash') {
        $id = $_POST['id'] ?? '';
        $trashed = ($_POST['trashed'] ?? '0') === '1';
        $items = read_json('items.json');
        $found = false;
        foreach ($items as &$item) {
            if ($item['id'] === $id && $item['user_id'] === $_SESSION['user_id']) {
                $item['trashed'] = $trashed;
                $found = true;
                break;
            }
        }
        if ($found) {
            write_json('items.json', $items);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
        exit;
    }

    if ($action === 'empty_trash') {
        $items = read_json('items.json');
        $new_items = [];
        foreach ($items as $item) {
            if (!empty($item['trashed']) && $item['trashed'] === true && $item['user_id'] === $_SESSION['user_id']) {
                $filepath = __DIR__ . '/../' . $item['url'];
                if (file_exists($filepath) && strpos($item['url'], 'data/uploads/') === 0) {
                    unlink($filepath);
                }
                continue;
            }
            $new_items[] = $item;
        }
        write_json('items.json', $new_items);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'edit_rating') {
        $id = $_POST['id'] ?? '';
        $rating = intval($_POST['rating'] ?? 0);
        $items = read_json('items.json');
        $found = false;
        foreach ($items as &$item) {
            if ($item['id'] === $id && $item['user_id'] === $_SESSION['user_id']) {
                $item['rating'] = $rating;
                $found = true;
                break;
            }
        }
        if ($found) {
            write_json('items.json', $items);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    if ($action === 'edit_tags') {
        $id = $_POST['id'] ?? '';
        $tags_json = $_POST['tags'] ?? '[]';
        $tags = json_decode($tags_json, true) ?: [];
        $items = read_json('items.json');
        $found = false;
        foreach ($items as &$item) {
            if ($item['id'] === $id && $item['user_id'] === $_SESSION['user_id']) {
                $item['tags'] = $tags;
                $found = true;
                break;
            }
        }
        if ($found) {
            write_json('items.json', $items);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    if ($action === 'edit_folders') {
        $id = $_POST['id'] ?? '';
        $folders_json = $_POST['folders'] ?? '[]';
        $folders = json_decode($folders_json, true) ?: [];
        $items = read_json('items.json');
        $found = false;
        foreach ($items as &$item) {
            if ($item['id'] === $id && $item['user_id'] === $_SESSION['user_id']) {
                $item['folders'] = $folders;
                $found = true;
                break;
            }
        }
        if ($found) {
            write_json('items.json', $items);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'list') {
        $items = read_json('items.json');
        $user_items = [];
        
        foreach ($items as $item) {
            if ($item['user_id'] === $_SESSION['user_id']) {
                $user_items[] = $item;
            }
        }
        
        echo json_encode(['success' => true, 'items' => $user_items]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
?>
