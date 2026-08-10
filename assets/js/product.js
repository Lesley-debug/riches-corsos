document.addEventListener('DOMContentLoaded', function () {
    const basePath = window.__RC?.basePath ?? '';
    const isLoggedIn = window.__RC?.isLoggedIn ?? false;
    const siteUrl = (path) => (basePath || '') + '/' + String(path || '').replace(/^\/+/, '');

    const mainImage = document.getElementById('mainImage');
    const thumbs = document.querySelectorAll('.thumb');

    thumbs.forEach((img) => {
        img.addEventListener('click', function () {
            mainImage.src = this.src;
            thumbs.forEach((t) => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    document.querySelectorAll('.tab-btn').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach((b) => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach((c) => c.classList.remove('active'));
            button.classList.add('active');
            document.getElementById(button.dataset.tab).classList.add('active');
        });
    });

    const wishlistBtn = document.querySelector('.btn-wishlist');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function () {
            if (!isLoggedIn) { window.location.href = siteUrl('/account/login.php?redirect=wishlist'); return; }

            fetch(siteUrl('/add-to-wishlist.php'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ id: this.dataset.id, name: this.dataset.name, price: this.dataset.price, image: this.dataset.image })
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data.loginRequired && data.redirectUrl) { window.location.href = data.redirectUrl; return; }
                    if (data.success) {
                        document.querySelectorAll('#wishlistCount, #bottomWishlistCount').forEach((el) => { el.textContent = data.wishlistCount || 0; });
                        window.RichesCorsosUI?.setWishlistCount(data.wishlistCount || 0);
                        this.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.35-10-9a6 6 0 0 1 10-6 6 6 0 0 1 10 6c-3 4.65-10 9-10 9z" /></svg> Added to Wishlist';
                        this.style.background = '#10b981';
                        this.disabled = true;
                    }
                })
                .catch(() => {});
        });
    }

    // ── You May Also Like slider ──
    const wrap    = document.querySelector('.related-track-wrap');
    const track   = document.getElementById('relatedTrack');
    const prevBtn = document.getElementById('relatedPrev');
    const nextBtn = document.getElementById('relatedNext');

    if (wrap && track && prevBtn && nextBtn) {
        const cards = Array.from(track.querySelectorAll('.related-card'));
        if (cards.length === 0) { prevBtn.hidden = nextBtn.hidden = true; }

        const updateBtns = () => {
            prevBtn.disabled = wrap.scrollLeft <= 0;
            nextBtn.disabled = wrap.scrollLeft >= wrap.scrollWidth - wrap.clientWidth - 1;
        };

        const scrollBy = (dir) => {
            const cardW = cards[0] ? cards[0].offsetWidth + 22 : wrap.clientWidth;
            wrap.scrollBy({ left: dir * cardW, behavior: 'smooth' });
        };

        prevBtn.addEventListener('click', () => scrollBy(-1));
        nextBtn.addEventListener('click', () => scrollBy(1));
        wrap.addEventListener('scroll', updateBtns, { passive: true });
        updateBtns();

        let startX = 0;
        wrap.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
        wrap.addEventListener('touchend',   (e) => { const diff = startX - e.changedTouches[0].clientX; if (Math.abs(diff) > 50) scrollBy(diff > 0 ? 1 : -1); });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('in-view'), i * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        cards.forEach((card) => { card.classList.add('animate-hidden'); observer.observe(card); });
    }
});
