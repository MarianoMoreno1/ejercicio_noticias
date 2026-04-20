<?php

/**
 * Fuente de datos de noticias.
 * Obtiene las noticias desde la API externa y las normaliza en un array limpio.
 */
function getNews(): array {
    // stream_context_create configura cómo se hace la petición HTTP:
    // las credenciales van en headers personalizados (user/pass), no en la URL
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

    // @ suprime el warning de PHP si la petición falla (red caída, timeout…).
    // El false final de retorno lo manejamos nosotros con el if de abajo.
    $response = @file_get_contents('https://apiapp.scimarina.com/v1/iNews/0/0/0/spa', false, $context);

    if (!$response) {
        return [];
    }

    // json_decode con true devuelve array asociativo en vez de objeto stdClass
    $data = json_decode($response, true);
    if (!is_array($data)) {
        return [];
    }

    $news = [];
    foreach ($data as $i => $item) {
        // Preferimos el resumen corto; si no existe usamos el texto largo
        // strip_tags quita HTML del texto, mb_strimwidth corta a 160 caracteres respetando UTF-8
        $summary = $item['rfShortTextSpa'] ?? null;
        if (!$summary) {
            $raw     = $item['rfTextSpa'] ?? '';
            $summary = mb_strimwidth(strip_tags($raw), 0, 160, '…');
        }

        // Si la noticia no trae imagen usamos picsum (placeholder) con seed fija
        // para que la misma noticia siempre muestre la misma imagen de placeholder
        $image = $item['rfPathImage'] ?? null;
        if (!$image) {
            $image = 'https://picsum.photos/seed/' . ($i + 1) . '/400/250';
        }

        // Normalizamos los campos de la API a nombres simples y consistentes
        $news[] = [
            'id'      => $item['idNews'],
            'title'   => $item['rfTitleSpa'] ?? $item['rfTitle'] ?? 'Sin título',
            'summary' => $summary,
            'image'   => $image,
        ];
    }

    return $news;
}
