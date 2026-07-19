// Navigate to a nav control by role, if it's live (disabled controls are
// rendered as <span> with no href).
function goNav(role) {
    const el = document.querySelector('[data-nav="' + role + '"]');
    const href = el && el.getAttribute('href');
    if (href) window.location.href = href;
}

// Keyboard navigation for the comic reader: ← previous (older), → next (newer).
document.addEventListener('keydown', (e) => {
    if (e.target.matches('input, textarea, select')) return;
    if (e.key === 'ArrowLeft') goNav('prev');
    if (e.key === 'ArrowRight') goNav('next');
});

// Swipe navigation on the comic art (mobile). Swipe left → older (matches the
// ← key and the "flick deeper into the feed" convention); swipe right → newer.
(function () {
    const art = document.querySelector('[data-swipe]');
    if (!art) return;

    let x0 = null, y0 = null, t0 = 0;

    art.addEventListener('touchstart', (e) => {
        const t = e.changedTouches[0];
        x0 = t.clientX; y0 = t.clientY; t0 = Date.now();
    }, { passive: true });

    art.addEventListener('touchend', (e) => {
        if (x0 === null) return;
        const t = e.changedTouches[0];
        const dx = t.clientX - x0;
        const dy = t.clientY - y0;
        const dt = Date.now() - t0;
        x0 = null;

        if (dt > 700) return;                          // too slow to be a flick
        if (Math.abs(dx) < 50) return;                 // too short
        if (Math.abs(dx) < Math.abs(dy) * 1.5) return; // mostly vertical: a scroll

        goNav(dx < 0 ? 'prev' : 'next');
    }, { passive: true });
})();
