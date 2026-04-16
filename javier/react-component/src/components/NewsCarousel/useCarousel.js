import { useState, useEffect, useRef } from 'react';

const BREAKPOINTS = [
    { maxWidth: 420,  visible: 1 },
    { maxWidth: 720,  visible: 2 },
    { maxWidth: 1100, visible: 4 },
];
const DEFAULT_VISIBLE = 6;

function getVisible() {
    const w = window.innerWidth;
    for (const bp of BREAKPOINTS) {
        if (w <= bp.maxWidth) return bp.visible;
    }
    return DEFAULT_VISIBLE;
}

/**
 * useCarousel
 * Encapsula toda la lógica del carrusel: índice, slide, dots, swipe, resize.
 *
 * @param {number} total  - cuántas cards hay en total
 * @returns {{ index, visible, maxIndex, trackRef, pages, activePage,
 *             prev, next, goTo, onTouchStart, onTouchEnd }}
 */
export function useCarousel(total) {
    const [index,   setIndex]   = useState(0);
    const [visible, setVisible] = useState(getVisible);
    const trackRef              = useRef(null);

    const maxIndex   = Math.max(0, total - visible);
    const pages      = Math.ceil(total / visible);
    const activePage = Math.floor(index / visible);

    // Mover el track cada vez que index cambia
    useEffect(() => {
        if (!trackRef.current || total === 0) return;
        const cardWidth = trackRef.current.children[0]?.offsetWidth ?? 0;
        trackRef.current.style.transform = `translateX(-${index * cardWidth}px)`;
    }, [index, total]);

    // Ajustar índice + visible al redimensionar (debounced 150ms)
    useEffect(() => {
        let timer;
        const onResize = () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const v = getVisible();
                setVisible(v);
                setIndex(prev => Math.min(prev, Math.max(0, total - v)));
            }, 150);
        };
        window.addEventListener('resize', onResize);
        return () => window.removeEventListener('resize', onResize);
    }, [total]);

    // Swipe táctil
    const touchStartX = useRef(0);
    const onTouchStart = (e) => { touchStartX.current = e.touches[0].clientX; };
    const onTouchEnd   = (e) => {
        const delta = touchStartX.current - e.changedTouches[0].clientX;
        if (Math.abs(delta) > 50) {
            if (delta > 0) next();
            else           prev();
        }
    };

    const prev  = () => setIndex(i => Math.max(i - 1, 0));
    const next  = () => setIndex(i => Math.min(i + 1, maxIndex));
    const goTo  = (page) => setIndex(Math.min(page * visible, maxIndex));

    return {
        index, visible, maxIndex, trackRef,
        pages, activePage,
        prev, next, goTo,
        onTouchStart, onTouchEnd,
    };
}
