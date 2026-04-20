<?php
/**
 * API endpoint – devuelve las noticias como JSON.
 * En desarrollo, Vite hace de proxy: fetch('/api.php') llega aquí desde localhost:8003.
 * En producción, React y PHP estarían en el mismo servidor y no haría falta proxy.
 */

// Decimos al browser que lo que devolvemos es JSON, no HTML
header('Content-Type: application/json; charset=utf-8');
// CORS: permite que Vite (localhost:5173) llame a este endpoint (localhost:8003)
header('Access-Control-Allow-Origin: *');

// Las credenciales van en headers personalizados, no en la URL
$context = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'header'  => "user: UAM\r\npass: 8LXm2wAiV1OgQ7458\r\n",
        'timeout' => 10,
    ],
    'ssl' => [
        // La API usa HTTPS pero su certificado no pasa verificación estándar.
        // Desactivamos verify_peer para evitar el error SSL en local.
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

// @ suprime el warning de PHP si falla la red; manejamos el false con el if de abajo
$response = @file_get_contents('https://apiapp.scimarina.com/v1/iNews/0/0/0/spa', false, $context);

if (!$response) {
    // 502 Bad Gateway: nosotros somos el proxy, la API de arriba falló
    http_response_code(502);
    echo json_encode(['error' => 'No se pudo conectar con la API de noticias.']);
    exit;
}

// true = array asociativo en vez de objeto stdClass
$data = json_decode($response, true);
if (!is_array($data)) {
    http_response_code(502);
    echo json_encode(['error' => 'Respuesta inválida de la API.']);
    exit;
}

$news = [];
foreach ($data as $i => $item) {
    // Preferimos el resumen corto; si no existe recortamos el texto largo a 160 chars
    $summary = $item['rfShortTextSpa'] ?? null;
    if (!$summary) {
        $raw     = $item['rfTextSpa'] ?? '';
        $summary = mb_strimwidth(strip_tags($raw), 0, 160, '…');
    }

    // Fallback a picsum con seed fija para que la imagen placeholder sea consistente
    $image = $item['rfPathImage'] ?? null;
    if (!$image) {
        $image = 'https://picsum.photos/seed/' . ($i + 1) . '/400/250';
    }

    // Normalizamos nombres de campo de la API a claves simples para React
    $news[] = [
        'id'      => $item['idNews'],
        'title'   => $item['rfTitleSpa'] ?? $item['rfTitle'] ?? 'Sin título',
        'summary' => $summary,
        'image'   => $image,
    ];
}

// JSON_UNESCAPED_UNICODE evita \uXXXX en tildes y ñ
echo json_encode($news, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
