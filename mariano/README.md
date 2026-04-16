# 📰 News Carousel — PHP + Swiper

Carrusel de noticias construido con **PHP 8+** y **Swiper 11**.  
Sin dependencias de Composer. Listo para servidor Apache/Nginx con PHP.

---

## Estructura de archivos

```
news-carousel/
├── index.php          ← Página principal con el carrusel
├── .htaccess          ← URLs limpias (Apache)
├── news.php           ← Endpoint JSON y lógica de noticias
└── detail.php         ← Página de detalle (/news/[slug])
```

---

## Uso rápido

```bash
# Requiere PHP 8.0+ y Apache/Nginx con mod_rewrite
php -S localhost:8000
# Abre http://localhost:8000
```

---

## API de noticias (`news.php`)

Devuelve JSON. Acepta parámetros GET opcionales:

| Parámetro  | Tipo   | Descripción                          |
|------------|--------|--------------------------------------|
| `category` | string | Filtra por categoría (case-insensitive) |
| `limit`    | int    | Número máximo de resultados          |

**Ejemplos:**
```
GET /api/news
GET /api/news?limit=3
GET /api/news?category=Tecnología
```

**Respuesta:**
```json
{
  "success": true,
  "total": 6,
  "data": [
    {
      "id": 1,
      "title": "IBEC recibe financiación récord...",
      "slug": "ibec-financiacion-record",
      "category": "Institucional",
      "excerpt": "El IBEC obtiene 12 millones...",
      "image": "https://...",
      "date": "2025-07-10",
      "url": "/news/ibec-financiacion-record"
    }
  ]
}
```

---

## Conectar a una API externa

En `index.php`, localiza el bloque de carga de datos y sustituye:

```php
// Antes (archivo local):
$raw = file_get_contents(__DIR__ . '/news.php');

// Después (API externa):
$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$raw = file_get_contents('https://tu-api.com/api/news?limit=6', false, $ctx);
```

---

## Conectar a base de datos (MySQL / MariaDB)

Sustituye el array `$news` en `api/news.php` por una consulta PDO:

```php
$pdo = new PDO('mysql:host=localhost;dbname=tu_bd;charset=utf8mb4', 'user', 'pass');
$stmt = $pdo->query('SELECT * FROM noticias ORDER BY fecha DESC LIMIT 6');
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

---

## Páginas de detalle

Con `.htaccess` activo, las URLs limpias funcionan automáticamente:

```
/news/ibec-financiacion-record  →  news/detail.php?slug=ibec-financiacion-record
```

En producción, carga el contenido de cada noticia desde tu BD usando el slug como clave.

---

## Swiper — configuración del carrusel

El carrusel está configurado en `index.php` al final del archivo:

```js
const swiper = new Swiper('#newsSwiper', {
    slidesPerView: 1.1,
    spaceBetween: 20,
    breakpoints: {
        560:  { slidesPerView: 1.8 },
        768:  { slidesPerView: 2.2 },
        1024: { slidesPerView: 3   },
    },
    navigation: { prevEl: '#btn-prev', nextEl: '#btn-next' },
    pagination:  { el: '.swiper-pagination', clickable: true },
    grabCursor: true,
    loop: false,
});
```

Consulta la [documentación oficial de Swiper](https://swiperjs.com/swiper-api) para opciones avanzadas (autoplay, efectos, etc.).

---

## Requisitos

- PHP 8.0+
- Apache con `mod_rewrite` habilitado, o Nginx con `try_files`
- Acceso a internet (CDN de Swiper y Google Fonts)
