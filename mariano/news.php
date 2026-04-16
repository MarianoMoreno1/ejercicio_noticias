<?php

function getNews(?string $category = null, ?int $limit = null): array {
$news = [
    [
        'id' => 1,
        'title' => 'IBEC recibe financiación récord para investigación en biotecnología',
        'slug' => 'ibec-recibe-financiacion-record',
        'category' => 'Institucional',
        'excerpt' => 'El Instituto de Bioingeniería de Cataluña obtiene 12 millones de euros para proyectos de nanomedicina y terapia génica.',
        'image' => 'https://picsum.photos/seed/news1/800/450',
        'date' => '2025-07-10',
        'url' => '/news/ibec-recibe-financiacion-record',
    ],
    [
        'id' => 2,
        'title' => 'Nueva plataforma de IA detecta enfermedades raras con 94% de precisión',
        'slug' => 'ia-detecta-enfermedades-raras',
        'category' => 'Tecnología',
        'excerpt' => 'Investigadores desarrollan un modelo de aprendizaje profundo entrenado con más de 2 millones de casos clínicos anonimizados.',
        'image' => 'https://picsum.photos/seed/news2/800/450',
        'date' => '2025-07-08',
        'url' => '/news/ia-detecta-enfermedades-raras',
    ],
    [
        'id' => 3,
        'title' => 'El CSIC abre convocatoria para jóvenes investigadores 2025',
        'slug' => 'csic-convocatoria-jovenes-investigadores',
        'category' => 'Convocatorias',
        'excerpt' => 'Se ofertan 320 plazas de investigador predoctoral en centros de toda España con contrato y formación financiada.',
        'image' => 'https://picsum.photos/seed/news3/800/450',
        'date' => '2025-07-05',
        'url' => '/news/csic-convocatoria-jovenes-investigadores',
    ],
    [
        'id' => 4,
        'title' => 'Congreso Internacional de Neurociencias llega a Barcelona en octubre',
        'slug' => 'congreso-neurociencias-barcelona',
        'category' => 'Eventos',
        'excerpt' => 'Más de 4.000 especialistas de 60 países se darán cita en el Palau de Congressos para debatir los últimos avances en neurología.',
        'image' => 'https://picsum.photos/seed/news4/800/450',
        'date' => '2025-07-02',
        'url' => '/news/congreso-neurociencias-barcelona',
    ],
    [
        'id' => 5,
        'title' => 'España lidera en Europa la producción de energía solar fotovoltaica',
        'slug' => 'espana-lidera-energia-solar',
        'category' => 'Sostenibilidad',
        'excerpt' => 'Con más de 28 GW instalados, España supera a Alemania y Francia en capacidad solar, cubriendo el 18% de la demanda nacional.',
        'image' => 'https://picsum.photos/seed/news5/800/450',
        'date' => '2025-06-28',
        'url' => '/news/espana-lidera-energia-solar',
    ],
    [
        'id' => 6,
        'title' => 'Startup española recauda 40M€ en Serie B para escalar su tecnología de agua',
        'slug' => 'startup-agua-serie-b',
        'category' => 'Innovación',
        'excerpt' => 'Aquanoa Technology capta inversión liderada por fondos europeos para expandir su sistema de purificación sostenible a 12 países.',
        'image' => 'https://picsum.photos/seed/news6/800/450',
        'date' => '2025-06-24',
        'url' => '/news/startup-agua-serie-b',
    ],
];

    if ($category) {
        $news = array_values(array_filter($news, fn($n) => strtolower($n['category']) === strtolower($category)));
    }
    if ($limit) {
        $news = array_slice($news, 0, $limit);
    }
    return $news;
}

// Solo responde como API si se llama directamente (no desde include/require)
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    $category = $_GET['category'] ?? null;
    $limit    = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
    $data     = getNews($category, $limit);
    echo json_encode(['success' => true, 'total' => count($data), 'data' => $data]);
}
