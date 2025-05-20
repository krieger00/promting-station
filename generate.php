<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['API_KEY'] ?? null;
$prompt = $_POST['prompt'] ?? '';
$saveDir = 'images';

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
$filename = uniqid('img_') . '.png';
file_put_contents("$saveDir/$filename", $imageData);

echo json_encode(['success' => true, 'filename' => $filename]);