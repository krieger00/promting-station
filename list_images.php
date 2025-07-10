<?php
echo "this function is deprecated. Load image.json to get the available images.";
/*
header('Content-Type: application/json');

$dir = 'images/';
$allowed = ['jpg', 'jpeg', 'webp'];
$files = [];

foreach (scandir($dir) as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        $files[] = $dir . $file;
    }
}

echo json_encode($files);*/