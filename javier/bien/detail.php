<?php
require_once __DIR__ . '/news.php';

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$news = null;

if ($id > 0) {
    $all = getNews();
    foreach ($all as $item) {
        if ((int) $item['id'] === $id) {
            $news = $item;
            break;
        }
    }
}

if (!$news) {
    http_response_code(404);
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
            --bg:        #0d0f14;
            --surface:   #161921;
            --border:    rgba(255,255,255,.07);
            --text:      #e8eaf0;
            --muted:     #8892a4;
            --accent:    #f0c040;
            --font-head: 'Playfair Display', Georgia, serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            font-weight: 300;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 32px 24px 0;
            font-size: .8rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--accent);
            text-decoration: none;
        }

        .back-link:hover { text-decoration: underline; }

        .article-wrap { max-width: 780px; margin: 0 auto; padding: 40px 24px 100px; }

        .article-title {
            font-family: var(--font-head);
            font-size: clamp(1.8rem, 5vw, 3rem);
            font-weight: 900;
            line-height: 1.1;
            color: #fff;
            margin-bottom: 32px;
        }

        .article-img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 36px;
            display: block;
        }

        .article-body {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #c9cdd8;
        }

        .article-body p  { margin-bottom: 1.2em; }
        .article-body h2 {
            font-family: var(--font-head);
            font-size: 1.4rem;
            color: #fff;
            margin: 2em 0 .6em;
        }
        .article-body ul     { padding-left: 1.4em; margin-bottom: 1.2em; }
        .article-body li     { margin-bottom: .4em; }
        .article-body strong { color: var(--text); font-weight: 500; }

        .not-found {
            text-align: center;
            padding: 120px 24px;
            font-family: var(--font-head);
            font-size: 1.4rem;
            color: var(--muted);
        }
    </style>
</head>
<body>

<a href="index.php" class="back-link">← Volver a noticias</a>

<?php if ($news): ?>
    <article class="article-wrap">
        <h1 class="article-title"><?= htmlspecialchars($news['title']) ?></h1>
        <img class="article-img"
             src="<?= htmlspecialchars($news['image']) ?>"
             alt="<?= htmlspecialchars($news['title']) ?>">
        <div class="article-body">
            <?php
                $content = $news['content'];
                if (strip_tags($content) === '') {
                    echo '<p>' . htmlspecialchars($news['summary']) . '</p>';
                } else {
                    echo $content;
                }
            ?>
        </div>
    </article>
<?php else: ?>
    <div class="not-found">
        Noticia no encontrada.
    </div>
<?php endif; ?>

</body>
</html>
