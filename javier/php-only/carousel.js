(function () {
    'use strict';

    const VISIBLE_DEFAULT = 6;

    const track    = document.getElementById('carouselTrack');
    const prevBtn  = document.getElementById('prevBtn');
    const nextBtn  = document.getElementById('nextBtn');
    const dotsWrap = document.getElementById('carouselDots');

    const cards = Array.from(track.children);
    const total = cards.length;

    let currentIndex = 0;

    /* ── How many cards are visible right now (CSS breakpoints) ── */
    function getVisible() {
        const w = window.innerWidth;
        if (w <= 420)  return 1;
        if (w <= 720)  return 2;
        if (w <= 1100) return 4;
        return VISIBLE_DEFAULT;
    }

    /* ── Max index we can scroll to ── */
    function maxIndex() {
        return Math.max(0, total - getVisible());
    }

    /* ── Build dot indicators ── */
    function buildDots() {
        dotsWrap.innerHTML = '';
        const visible = getVisible();
        const pages   = Math.ceil(total / visible);

        for (let i = 0; i < pages; i++) {
            const btn = document.createElement('button');
            btn.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            btn.setAttribute('aria-label', `Página ${i + 1}`);
            btn.addEventListener('click', () => {
                currentIndex = Math.min(i * visible, maxIndex());
                render();
            });
            dotsWrap.appendChild(btn);
        }
    }

    /* ── Update dot highlight ── */
    function updateDots() {
        const visible = getVisible();
        const activePage = Math.floor(currentIndex / visible);
        Array.from(dotsWrap.children).forEach((dot, i) => {
            dot.classList.toggle('active', i === activePage);
        });
    }

    /* ── Slide the track ── */
    function render() {
        const cardWidth = cards[0].offsetWidth; // real px width
        track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;

        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= maxIndex();

        updateDots();
    }

    /* ── Button handlers ── */
    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            render();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentIndex < maxIndex()) {
            currentIndex++;
            render();
        }
    });

    /* ── Rebuild on resize (breakpoint can change visible count) ── */
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // clamp index in case visible count changed
            currentIndex = Math.min(currentIndex, maxIndex());
            buildDots();
            render();
        }, 150);
    });

    /* ── Swipe support (touch) ── */
    let touchStartX = 0;
    track.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });

    track.addEventListener('touchend', (e) => {
        const delta = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(delta) > 50) {
            if (delta > 0 && currentIndex < maxIndex()) { currentIndex++; render(); }
            if (delta < 0 && currentIndex > 0)          { currentIndex--; render(); }
        }
    });

    /* ── Modal ── */
    const backdrop   = document.getElementById('modalBackdrop');
    const modalImg   = document.getElementById('modalImg');
    const modalTitle = document.getElementById('modalTitle');
    const modalSum   = document.getElementById('modalSummary');
    const modalClose = document.getElementById('modalClose');

    function openModal(card) {
        const img     = card.querySelector('img');
        const title   = card.querySelector('.news-card__title');
        const summary = card.querySelector('.news-card__summary');

        modalImg.src       = img.src;
        modalImg.alt       = img.alt;
        modalTitle.textContent = title.textContent;
        modalSum.textContent   = summary.textContent;

        backdrop.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        backdrop.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Click en cualquier card abre el modal
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => openModal(card));
    });

    // Cerrar: botón X, backdrop, o Escape
    modalClose.addEventListener('click', closeModal);
    backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    /* ── Init ── */
    buildDots();
    render();
})();
