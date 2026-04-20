<?php
/**
 * API endpoint – devuelve las noticias como JSON.
 * Fuente: API externa scimarina.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // ajustar en producción

$context = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'header'  => "user: UAM\r\npass: 8LXm2wAiV1OgQ7458\r\n",
        'timeout' => 10,
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

$response = @file_get_contents('https://apiapp.scimarina.com/v1/iNews/0/0/0/spa', false, $context);

if (!$response) {
    http_response_code(502);
    echo json_encode(['error' => 'No se pudo conectar con la API de noticias.']);
    exit;
}

$data = json_decode($response, true);
if (!is_array($data)) {
    http_response_code(502);
    echo json_encode(['error' => 'Respuesta inválida de la API.']);
    exit;
}

$news = [];
foreach ($data as $i => $item) {
    $summary = $item['rfShortTextSpa'] ?? null;
    if (!$summary) {
        $raw     = $item['rfTextSpa'] ?? '';
        $summary = mb_strimwidth(strip_tags($raw), 0, 160, '…');
    }

    $image = $item['rfPathImage'] ?? null;
    if (!$image) {
        $image = 'https://picsum.photos/seed/' . ($i + 1) . '/400/250';
    }

    $news[] = [
        'id'      => $item['idNews'],
        'title'   => $item['rfTitleSpa'] ?? $item['rfTitle'] ?? 'Sin título',
        'summary' => $summary,
        'image'   => $image,
    ];
}

echo json_encode($news, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
