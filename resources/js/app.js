// Keyboard navigation for the comic reader: ← previous, → next.
document.addEventListener('keydown', (e) => {
    if (e.target.matches('input, textarea, select')) return;

    if (e.key === 'ArrowLeft') {
        const prev = document.querySelector('[data-nav="prev"]');
        if (prev) window.location.href = prev.getAttribute('href');
    }

    if (e.key === 'ArrowRight') {
        const next = document.querySelector('[data-nav="next"]');
        if (next) window.location.href = next.getAttribute('href');
    }
});
