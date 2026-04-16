<?php
/**
 * news-carousel/news/[slug].php
 * Página de detalle de una noticia
 *
 * Uso real con .htaccess o router: /news/ibec-financiacion
 * En este ejemplo accede via: /news/detail.php?slug=ibec-financiacion
 */

$slug = htmlspecialchars($_GET['slug'] ?? '');

// ── Base de noticias (en producción: DB o API) ──────────────────
$allNews = [
    'ibec-recibe-financiacion-record' => [
        'title'    => 'IBEC recibe financiación récord para investigación en biotecnología',
        'category' => 'Institucional',
        'date'     => '2025-07-10',
        'image'    => 'https://picsum.photos/seed/n1/1200/600',
        'content'  => '<p>El Instituto de Bioingeniería de Cataluña (IBEC) ha recibido una financiación sin precedentes de <strong>12 millones de euros</strong> destinados a impulsar proyectos de nanomedicina y terapia génica durante los próximos cuatro años.</p>
<p>Esta inversión, procedente de fondos europeos del programa Horizon Europe y de la Generalitat de Catalunya, permitirá al IBEC reforzar su liderazgo en el desarrollo de tratamientos personalizados para enfermedades crónicas y oncológicas.</p>
<p>La directora del centro, Dra. Elena Martí, ha destacado que "este apoyo nos coloca en una posición privilegiada para trasladar nuestros hallazgos del laboratorio a la clínica en plazos mucho más cortos".</p>
<h2>Líneas de investigación prioritarias</h2>
<ul>
    <li>Sistemas de nanopartículas para entrega dirigida de fármacos</li>
    <li>Edición génica mediante CRISPR-Cas9 en tejido tumoral</li>
    <li>Bioimpresión 3D de tejidos vasculares</li>
</ul>
<p>Los primeros ensayos clínicos están previstos para el segundo trimestre de 2026.</p>',
    ],
    'ia-detecta-enfermedades-raras' => [
        'title'    => 'Nueva plataforma de IA detecta enfermedades raras con 94% de precisión',
        'category' => 'Tecnología',
        'date'     => '2025-07-08',
        'image'    => 'https://picsum.photos/seed/n2/1200/600',
        'content'  => '<p>Un equipo de investigadores del Centro Nacional de Análisis Genómico ha desarrollado <strong>RareScan</strong>, una plataforma de inteligencia artificial capaz de identificar patrones asociados a más de 7.000 enfermedades raras con una tasa de acierto del 94%.</p>
<p>El modelo ha sido entrenado con más de dos millones de historiales clínicos anonimizados y utiliza arquitecturas transformer adaptadas al dominio biomédico.</p>
<p>El sistema reduce el tiempo medio de diagnóstico de estas patologías de 5 años —la media actual en España— a menos de 3 meses.</p>',
    ],
];

$news = $allNews[$slug] ?? null;

if (!$news) {
    http_response_code(404);
}

function formatDate(string $isoDate): string {
    $months = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    [$y, $m, $d] = explode('-', $isoDate);
    return (int)$d . ' de ' . $months[(int)$m - 1] . ' de ' . $y;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $news ? htmlspecialchars($news['title']) : '404 — Noticia no encontrada' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0d0f14; --surface: #161921; --border: rgba(255,255,255,.07);
            --text: #e8eaf0; --muted: #8892a4; --accent: #f0c040;
            --font-head: 'Playfair Display', Georgia, serif;
            --font-body: 'DM Sans', sans-serif;
        }
        body { background: var(--bg); color: var(--text); font-family: var(--font-body); font-weight: 300; }

        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 32px 24px 0;
            font-size: .8rem; letter-spacing: .06em; text-transform: uppercase;
            color: var(--accent); text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }

        .article-wrap { max-width: 780px; margin: 0 auto; padding: 40px 24px 100px; }

        .article-meta { display: flex; gap: 12px; align-items: center; margin-bottom: 20px; }
        .article-cat {
            background: var(--accent); color: #000;
            font-size: .65rem; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; padding: 3px 10px; border-radius: 999px;
        }
        .article-date { font-size: .78rem; color: var(--muted); }

        .article-title {
            font-family: var(--font-head); font-size: clamp(1.8rem, 5vw, 3rem);
            font-weight: 900; line-height: 1.1; color: #fff; margin-bottom: 32px;
        }

        .article-img { width: 100%; border-radius: 10px; margin-bottom: 36px; display: block; }

        .article-body { font-size: 1.05rem; line-height: 1.8; color: #c9cdd8; }
        .article-body p { margin-bottom: 1.2em; }
        .article-body h2 {
            font-family: var(--font-head); font-size: 1.4rem; color: #fff;
            margin: 2em 0 .6em;
        }
        .article-body ul { padding-left: 1.4em; margin-bottom: 1.2em; }
        .article-body li { margin-bottom: .4em; }
        .article-body strong { color: var(--text); font-weight: 500; }

        .not-found {
            text-align: center; padding: 120px 24px;
            font-family: var(--font-head); font-size: 1.4rem; color: var(--muted);
        }
        .not-found span { display: block; font-size: 5rem; margin-bottom: 16px; }
    </style>
</head>
<body>

<a href="index.php" class="back-link">← Volver a noticias</a>

<?php if ($news): ?>
    <article class="article-wrap">
        <div class="article-meta">
            <span class="article-cat"><?= htmlspecialchars($news['category']) ?></span>
            <time class="article-date"><?= formatDate($news['date']) ?></time>
        </div>
        <h1 class="article-title"><?= htmlspecialchars($news['title']) ?></h1>
        <img class="article-img" src="<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
        <div class="article-body"><?= $news['content'] /* HTML controlado — en producción sanitiza si viene de usuarios */ ?></div>
    </article>
<?php else: ?>
    <div class="not-found">
        <span>🔍</span>
        Noticia no encontrada. El slug <code><?= $slug ?></code> no existe.
    </div>
<?php endif; ?>

</body>
</html>
