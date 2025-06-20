<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['API_KEY'] ?? null;
$prompt = $_POST['prompt'] ?? 'test';
$saveDir = '../images';

if (!$prompt) {
    echo json_encode(['success' => false, 'error' => 'Kein Prompt angegeben.']);
    exit;
}

$data = [
    'model' => 'gpt-image-1',
    'prompt' => $prompt,
    'n' => 1,
    'size' => '1024x1024',
    'quality' => 'medium'
];

$ch = curl_init('https://api.openai.com/v1/images/generations');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
if (!isset($result['data'][0]['b64_json'])) {
    echo json_encode(['success' => false, 'error' => $result['error']['message'] ?? 'Unbekannter Fehler']);
    exit;
}

$base64 = $result['data'][0]['b64_json'];
$imageData = base64_decode($base64);
$filenamePng = uniqid('img_') . '.png';
$filepathPng = "$saveDir/$filenamePng";
file_put_contents($filepathPng, $imageData);

// PNG zu JPEG konvertieren
$filenameJpeg = str_replace('.png', '.jpg', $filenamePng);
$filepathJpeg = "$saveDir/$filenameJpeg";

$image = imagecreatefrompng($filepathPng);
if ($image === false) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Laden des PNG-Bildes.']);
    exit;
}

// Hintergrund weiß setzen (für transparente PNGs)
$background = imagecreatetruecolor(imagesx($image), imagesy($image));
$white = imagecolorallocate($background, 25, 25, 25);
imagefill($background, 0, 0, $white);
imagecopy($background, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

imagejpeg($background, $filepathJpeg, 90); // Qualität: 90
imagedestroy($image);
imagedestroy($background);


echo json_encode(['success' => true, 'filename' => $filenameJpeg]);