<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias – PHP + React</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Contenedor vacío. React va a llenar este div cuando cargue.
     PHP no pone nada aquí — solo sirve el HTML al browser -->
<div id="root"></div>

<!-- Las tres librerías llegan desde internet (CDN), no están instaladas localmente.
     React:    la librería principal que maneja componentes y virtual DOM
     ReactDOM: la parte de React que escribe en el DOM real del browser
     Babel:    transpila JSX (la sintaxis tipo HTML dentro de JS) a JS puro -->
<script src="https://unpkg.com/react@18/umd/react.development.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- type="text/babel" hace que el browser NO ejecute esto directamente.
     Babel intercepta el contenido, lo convierte a JS normal, y ahí sí se ejecuta.
     Sin ese type, el browser vería JSX y rompería porque no lo entiende -->
<script type="text/babel">

// Desestructuramos los hooks de React que vamos a usar:
// useState  → para guardar datos que pueden cambiar (índice, noticias, loading)
// useEffect → para ejecutar código cuando algo cambia (fetch, resize, slide)
// useRef    → para apuntar a un elemento del DOM sin causar re-render
const { useState, useEffect, useRef } = React;

// Cuántas cards se muestran en pantalla grande (se sobreescribe en mobile)
const VISIBLE_DEFAULT = 6;


/* ── COMPONENTE NewsCard ────────────────────────────────────────────────────
   Recibe las props de UNA noticia y devuelve su HTML.
   No tiene estado propio — solo muestra lo que le pasan.
   JSX parece HTML pero es JS: className en vez de class, {} para expresiones.
────────────────────────────────────────────────────────────────────────── */
function NewsCard({ title, summary, image }) {
    return (
        <article className="news-card">
            <div className="news-card__img-wrap">
                {/* loading="lazy" = el browser no descarga la imagen hasta que
                    esté cerca del viewport. Mejora velocidad de carga inicial */}
                <img src={image} alt={title} loading="lazy" />
            </div>
            <div className="news-card__body">
                <h3 className="news-card__title">{title}</h3>
                <p className="news-card__summary">{summary}</p>
            </div>
        </article>
    );
}


/* ── COMPONENTE NewsCarousel ────────────────────────────────────────────────
   Recibe el array de noticias y cuántas deben verse a la vez.
   Maneja toda la lógica de navegación: índice actual, slide, dots, swipe.
────────────────────────────────────────────────────────────────────────── */
function NewsCarousel({ news, visible = VISIBLE_DEFAULT }) {
    // index = qué card está primera a la izquierda en este momento (empieza en 0)
    const [index, setIndex] = useState(0);

    // trackRef apunta al div .carousel-track del DOM para moverlo con translateX
    const trackRef = useRef(null);

    const total    = news.length;
    // maxIndex = hasta dónde podemos scrollear sin dejar espacio vacío a la derecha
    const maxIndex = Math.max(0, total - visible);

    /* Cada vez que index cambia, movemos el track físicamente en el DOM.
       offsetWidth da el ancho real en px de la primera card.
       Multiplicamos por index para saber cuántos px hay que moverse */
    useEffect(() => {
        if (!trackRef.current || total === 0) return;
        const cardWidth = trackRef.current.children[0]?.offsetWidth ?? 0;
        trackRef.current.style.transform = `translateX(-${index * cardWidth}px)`;
    }, [index, total]); // se ejecuta cuando index o total cambian

    /* Al redimensionar la ventana el ancho de las cards cambia.
       Ajustamos el índice para que no quede fuera de rango */
    useEffect(() => {
        const handleResize = () => {
            setIndex(prev => Math.min(prev, Math.max(0, total - visible)));
        };
        window.addEventListener('resize', handleResize);
        // la función de retorno limpia el listener cuando el componente se desmonta
        return () => window.removeEventListener('resize', handleResize);
    }, [total, visible]);

    /* Soporte táctil: guardamos X al inicio del toque.
       Al soltar, si el dedo se movió más de 50px cambiamos de card */
    const touchStart = useRef(0);
    const onTouchStart = (e) => { touchStart.current = e.touches[0].clientX; };
    const onTouchEnd   = (e) => {
        const delta = touchStart.current - e.changedTouches[0].clientX;
        if (Math.abs(delta) > 50) {
            if (delta > 0) setIndex(i => Math.min(i + 1, maxIndex)); // swipe izquierda → siguiente
            else           setIndex(i => Math.max(i - 1, 0));        // swipe derecha  → anterior
        }
    };

    // Cuántos dots mostrar = cuántas "páginas" hay
    const pages      = Math.ceil(total / visible);
    // En qué página estamos = posición del dot activo
    const activePage = Math.floor(index / visible);

    // Si no hay noticias no renderizamos nada
    if (total === 0) return null;

    return (
        <section>
            <div className="carousel-wrapper">

                {/* Botón anterior: disabled cuando ya estamos al inicio */}
                <button
                    className="carousel-btn carousel-btn--prev"
                    aria-label="Anterior"
                    disabled={index === 0}
                    onClick={() => setIndex(i => Math.max(i - 1, 0))}
                >
                    &#8249;
                </button>

                {/* El viewport recorta el track con overflow:hidden en CSS */}
                <div className="carousel-viewport">
                    <div
                        className="carousel-track"
                        ref={trackRef}          // ← React guarda referencia a este div
                        onTouchStart={onTouchStart}
                        onTouchEnd={onTouchEnd}
                    >
                        {/* Por cada noticia del array, React crea un NewsCard.
                            key es obligatorio en listas — React lo usa internamente
                            para saber qué card cambió sin re-renderizar todas */}
                        {news.map(item => (
                            <NewsCard
                                key={item.id}
                                title={item.title}
                                summary={item.summary}
                                image={item.image}
                            />
                        ))}
                    </div>
                </div>

                {/* Botón siguiente: disabled cuando llegamos al final */}
                <button
                    className="carousel-btn carousel-btn--next"
                    aria-label="Siguiente"
                    disabled={index >= maxIndex}
                    onClick={() => setIndex(i => Math.min(i + 1, maxIndex))}
                >
                    &#8250;
                </button>
            </div>

            {/* Dots: Array.from crea un array de N elementos para mapear N botones */}
            <div className="carousel-dots" aria-label="Indicadores">
                {Array.from({ length: pages }, (_, i) => (
                    <button
                        key={i}
                        className={`carousel-dot${i === activePage ? ' active' : ''}`}
                        aria-label={`Página ${i + 1}`}
                        // Al hacer click en un dot, saltamos al inicio de esa página
                        onClick={() => setIndex(Math.min(i * visible, maxIndex))}
                    />
                ))}
            </div>
        </section>
    );
}


/* ── HOOK useResponsiveVisible ──────────────────────────────────────────────
   Un hook es una función que empieza con "use" y puede usar otros hooks.
   Este devuelve cuántas cards deben verse según el ancho de pantalla actual.
   Cuando la ventana cambia de tamaño, el valor se actualiza automáticamente
   y React re-renderiza los componentes que lo usan.
────────────────────────────────────────────────────────────────────────── */
function useResponsiveVisible() {
    const getVisible = () => {
        const w = window.innerWidth;
        if (w <= 420)  return 1;
        if (w <= 720)  return 2;
        if (w <= 1100) return 4;
        return VISIBLE_DEFAULT;
    };

    // visible empieza con el valor actual de la pantalla
    const [visible, setVisible] = useState(getVisible);

    useEffect(() => {
        let timer;
        // debounce: esperamos 150ms antes de actualizar para no disparar
        // demasiados re-renders mientras el usuario arrastra el borde de la ventana
        const handler = () => {
            clearTimeout(timer);
            timer = setTimeout(() => setVisible(getVisible()), 150);
        };
        window.addEventListener('resize', handler);
        return () => window.removeEventListener('resize', handler);
    }, []);

    return visible;
}


/* ── COMPONENTE App ─────────────────────────────────────────────────────────
   Componente raíz. Se encarga de:
   1. Pedir las noticias a api.php
   2. Mostrar estado de carga o error
   3. Pasar las noticias al carrusel cuando están listas
────────────────────────────────────────────────────────────────────────── */
function App() {
    const [news,    setNews]    = useState([]);   // array de noticias (vacío al inicio)
    const [loading, setLoading] = useState(true); // true mientras esperamos la API
    const [error,   setError]   = useState(null); // null si todo fue bien

    const visible = useResponsiveVisible(); // cuántas cards mostrar según pantalla

    /* fetch a api.php — se ejecuta UNA sola vez al montar el componente ([] vacío) */
    useEffect(() => {
        fetch('api.php')                          // pide el JSON a PHP
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();                // convierte la respuesta a array JS
            })
            .then(data => setNews(data))          // guarda las noticias en el estado
            .catch(err => setError(err.message))  // guarda el error si algo falla
            .finally(() => setLoading(false));    // siempre apaga el loading al terminar
    }, []); // [] = sin dependencias, solo corre al montar

    return (
        <div>
            <h2 className="carousel-title">Últimas Noticias</h2>

            {/* Renderizado condicional: muestra uno u otro según el estado */}
            {loading && (
                <p className="carousel-status">Cargando noticias…</p>
            )}

            {error && (
                <p className="carousel-status carousel-status--error">
                    Error al cargar noticias: {error}
                </p>
            )}

            {/* Solo renderiza el carrusel cuando tenemos datos y no hay error */}
            {!loading && !error && (
                <NewsCarousel news={news} visible={visible} />
            )}
        </div>
    );
}


/* ── MOUNT ──────────────────────────────────────────────────────────────────
   Punto de entrada. React toma el div#root del HTML y empieza a renderizar.
   createRoot es la API de React 18. render(<App />) monta el componente raíz.
   A partir de aquí React controla todo lo que hay dentro de #root.
────────────────────────────────────────────────────────────────────────── */
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App />);

</script>

</body>
</html>
