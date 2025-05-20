<?php
header('Content-Type: application/json');

$dir = 'images/';
$allowed = ['png', 'jpg', 'jpeg', 'webp'];
$files = [];

foreach (scandir($dir) as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        $files[] = $dir . $file;
    }
}

echo json_encode($files);