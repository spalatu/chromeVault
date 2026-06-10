<?php
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password) || strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $users = read_json('users.json');
        
        // Check if email exists
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                echo json_encode(['success' => false, 'message' => t('error_register')]);
                exit;
            }
        }

        $new_user = [
            'id' => uniqid(),
            'name' => htmlspecialchars($name),
            'email' => htmlspecialchars($email),
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $users[] = $new_user;
        write_json('users.json', $users);

        echo json_encode(['success' => true, 'message' => t('success_register')]);
        exit;
    }

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $users = read_json('users.json');
        
        foreach ($users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                echo json_encode(['success' => true, 'message' => t('success_login')]);
                exit;
            }
        }

        echo json_encode(['success' => false, 'message' => t('error_login')]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
?>
