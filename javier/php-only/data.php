<?php

/**
 * Fuente de datos de noticias.
 * Obtiene las noticias desde la API externa usando cURL.
 */
function getNews(): array {
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
        return [];
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return [];
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

    return $news;
}
