import { useEffect } from 'react';
import styles from './Modal.module.css';

/**
 * Modal
 * Muestra la info completa de una noticia.
 * Props:
 *   item    { id, title, summary, image } — noticia seleccionada
 *   onClose () => void                   — cierra el modal
 */
export function Modal({ item, onClose }) {
    // Cerrar con Escape
    useEffect(() => {
        const onKey = (e) => { if (e.key === 'Escape') onClose(); };
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [onClose]);

    // Bloquear scroll del body mientras el modal está abierto
    useEffect(() => {
        document.body.style.overflow = 'hidden';
        return () => { document.body.style.overflow = ''; };
    }, []);

    return (
        // Click en el backdrop (fondo oscuro) cierra el modal
        <div className={styles.backdrop} onClick={onClose}>

            {/* stopPropagation evita que el click dentro cierre el modal */}
            <div className={styles.modal} onClick={(e) => e.stopPropagation()}>

                <button className={styles.close} onClick={onClose} aria-label="Cerrar">
                    &#10005;
                </button>

                <div className={styles.imgWrap}>
                    <img src={item.image} alt={item.title} />
                </div>

                <div className={styles.body}>
                    <h2 className={styles.title}>{item.title}</h2>
                    <p className={styles.summary}>{item.summary}</p>
                </div>
            </div>
        </div>
    );
}
