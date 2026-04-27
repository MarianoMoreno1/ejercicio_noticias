<?php
$newsData = [];
$error    = null;

try {
    require_once __DIR__ . '/news.php';
    $newsData = getNews();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0d0f14;
            --surface:   #161921;
            --border:    rgba(255,255,255,.07);
            --text:      #e8eaf0;
            --muted:     #8892a4;
            --accent:    #f0c040;
            --radius:    12px;
            --font-head: 'Playfair Display', Georgia, serif;
            --font-body: 'DM Sans', sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            font-weight: 300;
            padding: 24px 0 40px;
            overflow-x: hidden;
        }

        .section-header {
            max-width: 900px;
            margin: 0 auto 16px;
            padding: 0 20px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
        }

        .section-header__eyebrow {
            font-size: .65rem;
            font-weight: 500;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .section-header__title {
            font-family: var(--font-head);
            font-size: clamp(1.1rem, 2.5vw, 1.6rem);
            font-weight: 900;
            line-height: 1.1;
            color: #fff;
        }

        .section-header__title span { color: var(--accent); }

        .section-header__nav { display: flex; gap: 8px; flex-shrink: 0; }

        .news-swiper-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .swiper {
            overflow: visible;
            padding-bottom: 28px !important;
        }

        .swiper-wrapper { align-items: stretch; }

        .swiper-slide { height: auto; }

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
            text-decoration: none;
            color: inherit;
        }

        .news-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
            border-color: rgba(240,192,64,.25);
        }

        .news-card__img-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 2/1;
        }

        .news-card__img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .5s ease;
        }

        .news-card:hover .news-card__img-wrap img { transform: scale(1.05); }

        .news-card__body {
            padding: 12px 14px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .news-card__title {
            font-family: var(--font-head);
            font-size: .95rem;
            font-weight: 700;
            line-height: 1.3;
            color: #fff;
            margin-bottom: 6px;
        }

        .news-card__excerpt {
            font-size: .78rem;
            color: var(--muted);
            line-height: 1.5;
            flex: 1;
        }

        .news-card__cta {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--accent);
            transition: gap .2s;
        }

        .news-card__cta svg { width: 14px; height: 14px; transition: transform .2s; }
        .news-card:hover .news-card__cta { gap: 10px; }
        .news-card:hover .news-card__cta svg { transform: translateX(3px); }

        .swiper-btn {
            width: 36px;
            height: 36px;
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

        .swiper-btn:hover { background: var(--accent); border-color: var(--accent); color: #000; }
        .swiper-btn svg { width: 18px; height: 18px; }

        .swiper-pagination { bottom: 0 !important; }
        .swiper-pagination-bullet { background: var(--muted); opacity: .4; }
        .swiper-pagination-bullet-active {
            background: var(--accent);
            opacity: 1;
            width: 20px;
            border-radius: 4px;
        }

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
            <div class="error-box">Error al cargar noticias: <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($newsData) && !$error): ?>
            <div class="error-box">No se pudieron cargar las noticias. Comprueba la conexión a la API.</div>
        <?php endif; ?>

        <div class="swiper" id="newsSwiper">
            <div class="swiper-wrapper">

                <?php foreach ($newsData as $item): ?>
                    <?php
                        $safeId      = (int) $item['id'];
                        $safeTitle   = htmlspecialchars($item['title']);
                        $safeExcerpt = htmlspecialchars($item['summary']);
                        $safeImg     = htmlspecialchars($item['image']);
                    ?>
                    <div class="swiper-slide">
                        <a class="news-card" href="detail.php?id=<?= $safeId ?>">
                            <div class="news-card__img-wrap">
                                <img
                                    src="<?= $safeImg ?>"
                                    alt="<?= $safeTitle ?>"
                                    loading="lazy"
                                    width="800"
                                    height="450"
                                >
                            </div>
                            <div class="news-card__body">
                                <h3 class="news-card__title"><?= $safeTitle ?></h3>
                                <p class="news-card__excerpt"><?= $safeExcerpt ?></p>
                                <span class="news-card__cta">
                                    Leer más
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4-4 4M3 12h18"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="swiper-pagination"></div>
        </div>

    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
new Swiper('#newsSwiper', {
    slidesPerView: 1.2,
    spaceBetween: 14,
    breakpoints: {
        480:  { slidesPerView: 2,   spaceBetween: 14 },
        700:  { slidesPerView: 3,   spaceBetween: 16 },
        960:  { slidesPerView: 4,   spaceBetween: 16 },
    },
    navigation: { prevEl: '#btn-prev', nextEl: '#btn-next' },
    pagination: { el: '.swiper-pagination', clickable: true },
    grabCursor: true,
    loop: false,
    a11y: { prevSlideMessage: 'Noticia anterior', nextSlideMessage: 'Noticia siguiente' },
});
</script>

</body>
</html>
