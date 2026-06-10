<?php
require_once 'functions.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$file = __DIR__ . '/../data/collections.json';
if (!file_exists($file)) file_put_contents($file, '[]');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $collections = json_decode(file_get_contents($file), true) ?: [];
    $user_collections = array_filter($collections, function($c) {
        return $c['user_id'] === $_SESSION['user_id'];
    });
    echo json_encode(['success' => true, 'folders' => array_values($user_collections)]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $collections = json_decode(file_get_contents($file), true) ?: [];

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Name required']);
            exit;
        }
        $new_col = [
            'id' => uniqid('col_'),
            'user_id' => $_SESSION['user_id'],
            'name' => htmlspecialchars($name)
        ];
        $collections[] = $new_col;
        file_put_contents($file, json_encode($collections, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'folder' => $new_col]);
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $new_collections = [];
        $deleted = false;
        foreach ($collections as $c) {
            if ($c['id'] === $id && $c['user_id'] === $_SESSION['user_id']) {
                $deleted = true;
                continue;
            }
            $new_collections[] = $c;
        }
        if ($deleted) {
            file_put_contents($file, json_encode($new_collections, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Folder not found']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
