<?php
/**
 * news-carousel/index.php
 * Página principal con carrusel de noticias usando Swiper
 * Requiere PHP 8.0+
 */

// ──────────────────────────────────────────────
// 1. OBTENER NOTICIAS DESDE LA API (PHP interno)
// ──────────────────────────────────────────────
// En producción sustituye la URL por tu endpoint real, p.ej.:
//   $apiUrl = 'https://tu-api.com/api/news?limit=6';
// Aquí cargamos el archivo local directamente para evitar una petición HTTP extra.
$newsData = [];
$error = null;

try {
    require_once __DIR__ . '/news.php';
    $newsData = getNews(limit: 6);
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Fallback: carga el JSON del array inline si el fichero no está disponible
if (empty($newsData)) {
    $newsData = [
        ['id'=>1,'title'=>'IBEC recibe financiación récord','slug'=>'ibec-financiacion','category'=>'Institucional','excerpt'=>'El IBEC obtiene 12 millones de euros para proyectos de nanomedicina.','image'=>'https://picsum.photos/seed/n1/800/450','date'=>'2025-07-10','url'=>'#'],
        ['id'=>2,'title'=>'IA detecta enfermedades raras con 94% de precisión','slug'=>'ia-enfermedades','category'=>'Tecnología','excerpt'=>'Modelo entrenado con 2 millones de casos clínicos anonimizados.','image'=>'https://picsum.photos/seed/n2/800/450','date'=>'2025-07-08','url'=>'#'],
        ['id'=>3,'title'=>'CSIC abre convocatoria para investigadores 2025','slug'=>'csic-convocatoria','category'=>'Convocatorias','excerpt'=>'320 plazas en centros de toda España con contrato financiado.','image'=>'https://picsum.photos/seed/n3/800/450','date'=>'2025-07-05','url'=>'#'],
        ['id'=>4,'title'=>'Congreso Internacional de Neurociencias en Barcelona','slug'=>'congreso-neuro','category'=>'Eventos','excerpt'=>'4.000 especialistas de 60 países debaten los últimos avances.','image'=>'https://picsum.photos/seed/n4/800/450','date'=>'2025-07-02','url'=>'#'],
        ['id'=>5,'title'=>'España lidera producción solar fotovoltaica en Europa','slug'=>'solar-espana','category'=>'Sostenibilidad','excerpt'=>'Superamos a Alemania y Francia con 28 GW instalados.','image'=>'https://picsum.photos/seed/n5/800/450','date'=>'2025-06-28','url'=>'#'],
        ['id'=>6,'title'=>'Startup capta 40M€ para escalar tecnología de agua','slug'=>'startup-agua','category'=>'Innovación','excerpt'=>'Aquanoa Technology se expande a 12 países con fondos europeos.','image'=>'https://picsum.photos/seed/n6/800/450','date'=>'2025-06-24','url'=>'#'],
    ];
}

/**
 * Helper: formatea fecha ISO → "10 jul. 2025"
 */
function formatDate(string $isoDate): string {
    $months = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    [$y, $m, $d] = explode('-', $isoDate);
    return (int)$d . ' ' . $months[(int)$m - 1] . '. ' . $y;
}

/**
 * Helper: devuelve color de badge por categoría
 */
function categoryColor(string $cat): string {
    return match (strtolower($cat)) {
        'institucional'  => '#2563EB',
        'tecnología'     => '#7C3AED',
        'convocatorias'  => '#059669',
        'eventos'        => '#D97706',
        'sostenibilidad' => '#16A34A',
        'innovación'     => '#DB2777',
        default          => '#64748B',
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias — Carrusel con Swiper + PHP</title>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        /* ── Reset & base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:         #0d0f14;
            --surface:    #161921;
            --border:     rgba(255,255,255,.07);
            --text:       #e8eaf0;
            --muted:      #8892a4;
            --accent:     #f0c040;
            --radius:     12px;
            --font-head:  'Playfair Display', Georgia, serif;
            --font-body:  'DM Sans', sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            font-weight: 300;
            min-height: 100vh;
            padding: 60px 0 80px;
        }

        /* ── Section header ── */
        .section-header {
            max-width: 1200px;
            margin: 0 auto 40px;
            padding: 0 24px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
        }

        .section-header__eyebrow {
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .section-header__title {
            font-family: var(--font-head);
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 900;
            line-height: 1.08;
            color: #fff;
        }

        .section-header__title span {
            color: var(--accent);
        }

        .section-header__nav {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        /* ── Swiper container ── */
        .news-swiper-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
        }

        .swiper {
            overflow: visible;
            padding-bottom: 48px !important;
        }

        /* ── News card ── */
        .news-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
            cursor: pointer;
        }

        .news-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
            border-color: rgba(240,192,64,.25);
        }

        .news-card__img-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 16/9;
        }

        .news-card__img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .5s ease;
        }

        .news-card:hover .news-card__img-wrap img {
            transform: scale(1.05);
        }

        .news-card__badge {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .65rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #fff;
        }

        .news-card__body {
            padding: 20px 20px 24px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .news-card__date {
            font-size: .7rem;
            color: var(--muted);
            letter-spacing: .04em;
            margin-bottom: 10px;
        }

        .news-card__title {
            font-family: var(--font-head);
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.3;
            color: #fff;
            margin-bottom: 10px;
        }

        .news-card__excerpt {
            font-size: .85rem;
            color: var(--muted);
            line-height: 1.65;
            flex: 1;
        }

        .news-card__cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            font-size: .78rem;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--accent);
            text-decoration: none;
            transition: gap .2s;
        }

        .news-card__cta:hover { gap: 10px; }

        .news-card__cta svg {
            width: 14px;
            height: 14px;
            transition: transform .2s;
        }

        .news-card__cta:hover svg { transform: translateX(3px); }

        /* ── Nav buttons ── */
        .swiper-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s, border-color .2s, color .2s;
        }

        .swiper-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: #000;
        }

        .swiper-btn svg { width: 18px; height: 18px; }

        /* ── Pagination ── */
        .swiper-pagination {
            bottom: 0 !important;
        }

        .swiper-pagination-bullet {
            background: var(--muted);
            opacity: .4;
        }

        .swiper-pagination-bullet-active {
            background: var(--accent);
            opacity: 1;
            width: 20px;
            border-radius: 4px;
        }

        /* ── Error state ── */
        .error-box {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: #2d1212;
            border: 1px solid #7f1d1d;
            border-radius: var(--radius);
            color: #fca5a5;
            font-size: .9rem;
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .section-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<section>
    <div class="section-header">
        <div>
            <p class="section-header__eyebrow">Últimas publicaciones</p>
            <h2 class="section-header__title">Noticias <span>&amp; Actualidad</span></h2>
        </div>
        <div class="section-header__nav">
            <button class="swiper-btn" id="btn-prev" aria-label="Anterior">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button class="swiper-btn" id="btn-next" aria-label="Siguiente">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="news-swiper-wrap">

        <?php if ($error): ?>
            <div class="error-box">⚠️ Error al cargar noticias: <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="swiper" id="newsSwiper">
            <div class="swiper-wrapper">

                <?php foreach ($newsData as $item): ?>
                    <?php
                        $badgeColor = categoryColor($item['category']);
                        $dateStr    = formatDate($item['date']);
                        $safeTitle  = htmlspecialchars($item['title']);
                        $safeExcerpt= htmlspecialchars($item['excerpt']);
                        $safeCat    = htmlspecialchars($item['category']);
                        $safeUrl    = htmlspecialchars($item['url']);
                        $safeImg    = htmlspecialchars($item['image']);
                    ?>
                    <div class="swiper-slide">
                        <article class="news-card" onclick="window.location='<?= $safeUrl ?>'">
                            <div class="news-card__img-wrap">
                                <img
                                    src="<?= $safeImg ?>"
                                    alt="<?= $safeTitle ?>"
                                    loading="lazy"
                                    width="800"
                                    height="450"
                                >
                                <span
                                    class="news-card__badge"
                                    style="background-color:<?= $badgeColor ?>"
                                ><?= $safeCat ?></span>
                            </div>
                            <div class="news-card__body">
                                <time class="news-card__date"><?= $dateStr ?></time>
                                <h3 class="news-card__title"><?= $safeTitle ?></h3>
                                <p class="news-card__excerpt"><?= $safeExcerpt ?></p>
                                <a href="<?= $safeUrl ?>" class="news-card__cta" onclick="event.stopPropagation()">
                                    Leer más
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4-4 4M3 12h18"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>

            </div><!-- /.swiper-wrapper -->

            <div class="swiper-pagination"></div>
        </div><!-- /.swiper -->

    </div><!-- /.news-swiper-wrap -->
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
const swiper = new Swiper('#newsSwiper', {
    // Layout
    slidesPerView: 1.1,
    spaceBetween: 20,
    centeredSlides: false,

    // Breakpoints responsivos
    breakpoints: {
        560: { slidesPerView: 1.8, spaceBetween: 20 },
        768: { slidesPerView: 2.2, spaceBetween: 24 },
        1024: { slidesPerView: 3,   spaceBetween: 28 },
        1200: { slidesPerView: 3.2, spaceBetween: 28 },
    },

    // Navegación externa
    navigation: {
        prevEl: '#btn-prev',
        nextEl: '#btn-next',
    },

    // Paginación
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },

    // Opciones adicionales
    grabCursor: true,
    loop: false,
    a11y: {
        prevSlideMessage: 'Noticia anterior',
        nextSlideMessage: 'Noticia siguiente',
    },
});
</script>

</body>
</html>
