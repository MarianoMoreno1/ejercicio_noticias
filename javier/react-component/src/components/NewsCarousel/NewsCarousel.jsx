import { useState }    from 'react';
import { useCarousel } from './useCarousel';
import { NewsCard }    from './NewsCard';
import { Modal }       from './Modal';
import styles          from './NewsCarousel.module.css';

export function NewsCarousel({ news = [], title = 'Noticias' }) {
    const total = news.length;

    const {
        index, maxIndex, trackRef,
        pages, activePage,
        prev, next, goTo,
        onTouchStart, onTouchEnd,
    } = useCarousel(total);

    // null = cerrado, objeto noticia = abierto
    const [selected, setSelected] = useState(null);

    if (total === 0) return <p className={styles.empty}>Sin noticias.</p>;

    return (
        <section className={styles.section}>
            <h2 className={styles.title}>{title}</h2>

            <div className={styles.wrapper}>
                <button
                    className={styles.btn}
                    aria-label="Anterior"
                    disabled={index === 0}
                    onClick={prev}
                >
                    &#8249;
                </button>

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
                                onClick={() => setSelected(item)}
                            />
                        ))}
                    </div>
                </div>

                <button
                    className={styles.btn}
                    aria-label="Siguiente"
                    disabled={index >= maxIndex}
                    onClick={next}
                >
                    &#8250;
                </button>
            </div>

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

            {/* Modal: solo se renderiza si hay una noticia seleccionada */}
            {selected && (
                <Modal item={selected} onClose={() => setSelected(null)} />
            )}
        </section>
    );
}
