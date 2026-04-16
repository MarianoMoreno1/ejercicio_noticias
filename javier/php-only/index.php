<?php
// PHP corre en el SERVIDOR antes de mandar nada al browser.
// Carga las noticias desde data.php (array o BD) y las guarda en $news.
require_once 'data.php';
$news  = getNews();
$total = count($news); // cuántas noticias hay en total
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias – PHP Only</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<section class="carousel-section">
    <h2 class="carousel-title">Últimas Noticias</h2>

    <div class="carousel-wrapper">

        <!-- Botón izquierdo. carousel.js lo activa/desactiva según la posición -->
        <button class="carousel-btn carousel-btn--prev" id="prevBtn" aria-label="Anterior">&#8249;</button>

        <!-- El viewport recorta el track con overflow:hidden. Solo se ven 6 cards -->
        <div class="carousel-viewport">

            <!-- El track contiene TODAS las cards y se mueve con translateX.
                 data-total le pasa el total de noticias a carousel.js sin JS extra -->
            <div class="carousel-track" id="carouselTrack"
                 data-total="<?= $total ?>">

                <?php foreach ($news as $item): ?>
                <!-- PHP genera un <article> por cada noticia del array.
                     htmlspecialchars evita XSS por si los datos vienen de BD -->
                <article class="news-card">
                    <div class="news-card__img-wrap">
                        <img
                            src="<?= htmlspecialchars($item['image']) ?>"
                            alt="<?= htmlspecialchars($item['title']) ?>"
                            loading="lazy"
                        >
                    </div>
                    <div class="news-card__body">
                        <h3 class="news-card__title"><?= htmlspecialchars($item['title']) ?></h3>
                        <p class="news-card__summary"><?= htmlspecialchars($item['summary']) ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
                <!-- PHP termina aquí. A partir de este punto todo es browser -->

            </div>
        </div>

        <!-- Botón derecho -->
        <button class="carousel-btn carousel-btn--next" id="nextBtn" aria-label="Siguiente">&#8250;</button>
    </div>

    <!-- Los dots se generan dinámicamente en carousel.js, no en PHP,
         porque dependen del ancho de pantalla (cuántas cards entran) -->
    <div class="carousel-dots" id="carouselDots" aria-label="Indicadores"></div>
</section>

<!-- carousel.js se carga AL FINAL del body para que cuando se ejecute
     el DOM ya esté completo y pueda encontrar los elementos por id -->
<script src="carousel.js"></script>
</body>
</html>
