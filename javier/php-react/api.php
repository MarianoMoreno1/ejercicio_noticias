<?php
/**
 * API endpoint – devuelve las noticias como JSON.
 * React (en el browser) llama a este archivo vía fetch('api.php').
 * PHP hace la petición a la API externa y devuelve los datos normalizados.
 */

// Decimos al browser que lo que devolvemos es JSON, no HTML
header('Content-Type: application/json; charset=utf-8');
// CORS: permite que cualquier origen (el browser sirviendo React) llame a esta API.
// En producción reemplazar * con el dominio concreto.
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
