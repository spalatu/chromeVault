<?php
session_start();

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data_dir = __DIR__ . '/../data/';

// Load language
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ro';

function get_translations() {
    global $data_dir;
    $lang_file = $data_dir . 'lang.json';
    if (file_exists($lang_file)) {
        $json = file_get_contents($lang_file);
        return json_decode($json, true);
    }
    return [];
}

$translations = get_translations();

function t($key) {
    global $translations, $current_lang;
    if (isset($translations[$current_lang][$key])) {
        return $translations[$current_lang][$key];
    }
    // Fallback
    if (isset($translations['en'][$key])) {
        return $translations['en'][$key];
    }
    return $key;
}

function read_json($filename) {
    global $data_dir;
    $filepath = $data_dir . $filename;
    if (file_exists($filepath)) {
        $json = file_get_contents($filepath);
        $data = json_decode($json, true);
        return $data ? $data : [];
    }
    return [];
}

function write_json($filename, $data) {
    global $data_dir;
    $filepath = $data_dir . $filename;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($filepath, $json);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>
