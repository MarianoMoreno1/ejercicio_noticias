import styles from './NewsCarousel.module.css';

/**
 * NewsCard
 * Presenta una sola noticia: imagen, título y resumen.
 * Props: { title, summary, image }
 */
export function NewsCard({ title, summary, image }) {
    return (
        <article className={styles.card}>
            <div className={styles.cardImgWrap}>
                <img src={image} alt={title} loading="lazy" />
            </div>
            <div className={styles.cardBody}>
                <span className={styles.cardBadge}>NOTICIA</span>
                <h3 className={styles.cardTitle}>{title}</h3>
                <p className={styles.cardSummary}>{summary}</p>
            </div>
        </article>
    );
}
