import { useCarousel } from './useCarousel';
import { NewsCard }    from './NewsCard';
import styles          from './NewsCarousel.module.css';

/**
 * NewsCarousel
 * Componente reutilizable de carrusel de noticias.
 *
 * Props:
 *   news  {Array}  — array de objetos { id, title, summary, image }
 *   title {string} — título de la sección (opcional)
 *
 * Uso:
 *   <NewsCarousel news={newsArray} title="Últimas Noticias" />
 */
export function NewsCarousel({ news = [], title = 'Noticias' }) {
    const total = news.length;

    const {
        index, maxIndex, trackRef,
        pages, activePage,
        prev, next, goTo,
        onTouchStart, onTouchEnd,
    } = useCarousel(total);

    if (total === 0) return <p className={styles.empty}>Sin noticias.</p>;

    return (
        <section className={styles.section}>
            <h2 className={styles.title}>{title}</h2>

            <div className={styles.wrapper}>
                {/* ← Anterior */}
                <button
                    className={styles.btn}
                    aria-label="Anterior"
                    disabled={index === 0}
                    onClick={prev}
                >
                    &#8249;
                </button>

                {/* Viewport recorta overflow */}
                <div className={styles.viewport}>
                    <div
                        className={styles.track}
                        ref={trackRef}
                        onTouchStart={onTouchStart}
                        onTouchEnd={onTouchEnd}
                    >
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

                {/* → Siguiente */}
                <button
                    className={styles.btn}
                    aria-label="Siguiente"
                    disabled={index >= maxIndex}
                    onClick={next}
                >
                    &#8250;
                </button>
            </div>

            {/* Dots */}
            <div className={styles.dots}>
                {Array.from({ length: pages }, (_, i) => (
                    <button
                        key={i}
                        className={`${styles.dot}${i === activePage ? ` ${styles.dotActive}` : ''}`}
                        aria-label={`Página ${i + 1}`}
                        onClick={() => goTo(i)}
                    />
                ))}
            </div>
        </section>
    );
}
