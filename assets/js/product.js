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
});
