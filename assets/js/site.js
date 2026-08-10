(function () {
    'use strict';

    const basePath = window.__RC?.basePath ?? '';
    const isLoggedIn = window.__RC?.isLoggedIn ?? false;

    const siteUrl = (path) => `${basePath || ''}/${String(path || '').replace(/^\/+/, '')}`;
    const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value) || 0);
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));

    const normalizeImageUrl = (path) => {
        const value = String(path || '').trim();
        if (!value) return siteUrl('/assets/images/luna.webp');
        if (/^https?:\/\//i.test(value)) return value;
        if (basePath && value.startsWith(`${basePath}/`)) return value;
        if (value.startsWith('/richescorsos/')) {
            return basePath === '/richescorsos' ? value : siteUrl(value.replace(/^\/richescorsos\//, '/'));
        }
        return value.startsWith('/') ? siteUrl(value) : siteUrl(`/${value}`);
    };

    const body = document.body;
    const drawerOverlay = document.getElementById('drawerOverlay');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const hamburgerToggle = document.querySelector('.hamburger-toggle');
    const drawerClose = document.querySelector('.drawer-close');
    const searchToggle = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const closeSearch = document.querySelector('.close-search');
    const searchInput = searchOverlay?.querySelector('input[name="q"]');
    const miniCart = document.getElementById('miniCart');
    const cartToggle = document.getElementById('cartToggle');
    const closeCart = document.querySelector('.close-cart');
    const miniCartContent = document.getElementById('miniCartContent');
    const accountToggle = document.querySelector('.account-toggle');
    const accountWrap = document.querySelector('.wd-header-my-account');
    const bottomWishlist = document.getElementById('bottomWishlist');
    const bottomAccount = document.getElementById('bottomAccount');
    const wishlistButton = document.getElementById('wishlistBtn');
    const bottomHeader = document.querySelector('.bottom-header');

    const accountUrl = isLoggedIn ? siteUrl('/account/dashboard.php') : siteUrl('/account/login.php');
    const wishlistUrl = isLoggedIn ? siteUrl('/account/wishlist.php') : siteUrl('/account/login.php?redirect=wishlist');

    let isMenuOpen = false;
    let isCartOpen = false;

    const setOverlayState = () => {
        const active = isMenuOpen || isCartOpen;
        drawerOverlay?.classList.toggle('active', active);
        body?.classList.toggle('drawer-active', active || searchOverlay?.classList.contains('active'));
    };

    const setCartCount = (count) => {
        const normalized = Math.max(0, Number(count) || 0);
        document.querySelectorAll('#cartCount').forEach((el) => { el.textContent = normalized; });
    };

    const setWishlistCount = (count) => {
        const normalized = Math.max(0, Number(count) || 0);
        document.querySelectorAll('#wishlistCount, #bottomWishlistCount').forEach((el) => {
            el.textContent = normalized;
            el.classList.toggle('is-empty', normalized === 0);
        });
    };

    const renderMiniCart = (items) => {
        if (!miniCartContent) return;
        if (!Array.isArray(items) || items.length === 0) {
            miniCartContent.innerHTML = `<div class="mini-cart-empty"><p>Your cart is empty.</p><a href="${siteUrl('/shop/shop.php')}">Browse Puppies</a></div>`;
            return;
        }
        miniCartContent.innerHTML = items.map((item) => {
            const qty = Math.max(1, Number(item.qty) || 1);
            const price = Number(item.price) || 0;
            return `<article class="mini-cart-item">
                <img src="${normalizeImageUrl(item.image)}" alt="${escapeHtml(item.name || 'Puppy')}" loading="lazy" decoding="async">
                <div class="mini-cart-item-body">
                    <h4>${escapeHtml(item.name || 'Puppy')}</h4>
                    <p>${qty} x ${money(price)}</p>
                    <strong>${money(price * qty)}</strong>
                </div>
                <button class="mini-cart-remove" type="button" data-id="${escapeHtml(item.id)}" aria-label="Remove ${escapeHtml(item.name || 'item')}">&times;</button>
            </article>`;
        }).join('');
    };

    const setCartState = (data) => {
        if (!data || typeof data !== 'object') return;
        setCartCount(data.cartCount);
        document.querySelectorAll('#cartTotal, #miniCartSubtotal').forEach((el) => { el.textContent = money(data.totalAmount); });
        renderMiniCart(data.cart || []);
    };

    const refreshCart = () => fetch(siteUrl('/get-cart.php'), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then((r) => r.ok ? r.json() : null)
        .then((data) => { if (data) setCartState(data); return data; })
        .catch(() => null);

    const openMenu = () => { isMenuOpen = true; mobileDrawer?.classList.add('drawer-open'); hamburgerToggle?.classList.add('is-active'); hamburgerToggle?.setAttribute('aria-expanded', 'true'); setOverlayState(); };
    const closeMenu = () => { isMenuOpen = false; mobileDrawer?.classList.remove('drawer-open'); hamburgerToggle?.classList.remove('is-active'); hamburgerToggle?.setAttribute('aria-expanded', 'false'); setOverlayState(); };
    const openCart = () => { isCartOpen = true; miniCart?.classList.add('active'); cartToggle?.setAttribute('aria-expanded', 'true'); setOverlayState(); refreshCart(); };
    const closeMiniCart = () => { isCartOpen = false; miniCart?.classList.remove('active'); cartToggle?.setAttribute('aria-expanded', 'false'); setOverlayState(); };
    const openSearch = () => { searchOverlay?.classList.add('active'); searchToggle?.setAttribute('aria-expanded', 'true'); body?.classList.add('drawer-active'); window.setTimeout(() => searchInput?.focus(), 80); };
    const closeSearchOverlay = () => { searchOverlay?.classList.remove('active'); searchToggle?.setAttribute('aria-expanded', 'false'); setOverlayState(); };

    hamburgerToggle?.addEventListener('click', () => isMenuOpen ? closeMenu() : openMenu());
    drawerClose?.addEventListener('click', closeMenu);
    drawerOverlay?.addEventListener('click', () => { closeMenu(); closeMiniCart(); });
    searchToggle?.addEventListener('click', openSearch);
    closeSearch?.addEventListener('click', closeSearchOverlay);
    searchOverlay?.addEventListener('click', (e) => { if (e.target === searchOverlay) closeSearchOverlay(); });
    cartToggle?.addEventListener('click', openCart);
    closeCart?.addEventListener('click', closeMiniCart);
    wishlistButton?.addEventListener('click', () => { window.location.href = wishlistUrl; });
    bottomWishlist?.addEventListener('click', () => { window.location.href = wishlistUrl; });
    bottomAccount?.addEventListener('click', () => { window.location.href = accountUrl; });

    accountToggle?.addEventListener('click', (e) => {
        e.preventDefault(); e.stopPropagation();
        if (window.matchMedia('(max-width: 1023px)').matches) { window.location.href = accountUrl; return; }
        const isOpen = accountWrap?.classList.toggle('dropdown-open');
        accountToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', (e) => {
        if (accountWrap && !accountWrap.contains(e.target)) {
            accountWrap.classList.remove('dropdown-open');
            accountToggle?.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { closeMenu(); closeMiniCart(); closeSearchOverlay(); accountWrap?.classList.remove('dropdown-open'); accountToggle?.setAttribute('aria-expanded', 'false'); }
    });

    document.addEventListener('click', (e) => {
        const cartLink = e.target.closest('a[href*="add-to-cart.php"]');
        if (!cartLink) return;
        e.preventDefault();
        const origText = cartLink.textContent;
        cartLink.textContent = 'Adding…';
        cartLink.style.pointerEvents = 'none';
        fetch(cartLink.href, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then((r) => {
                if (r.redirected && !r.url.includes('add-to-cart.php')) { window.location.href = r.url; return null; }
                const ct = r.headers.get('content-type') || '';
                if (ct.includes('application/json')) return r.json();
                window.location.href = cartLink.href; return null;
            })
            .then((data) => {
                cartLink.textContent = origText;
                cartLink.style.pointerEvents = '';
                if (!data) return;
                if (data.loginRequired && data.redirectUrl) { window.location.href = data.redirectUrl; return; }
                if (data.success) { setCartState(data); openCart(); }
            })
            .catch(() => { cartLink.textContent = origText; cartLink.style.pointerEvents = ''; window.location.href = cartLink.href; });
    });

    miniCartContent?.addEventListener('click', (e) => {
        const btn = e.target.closest('.mini-cart-remove');
        if (!btn) return;
        fetch(siteUrl('/remove-from-cart.php'), { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ id: btn.dataset.id }) })
            .then((r) => r.json()).then(setCartState).catch(() => {});
    });

    if (bottomHeader) {
        const stickyOffset = bottomHeader.offsetTop;
        window.addEventListener('scroll', () => { bottomHeader.classList.toggle('is-stuck', window.scrollY > stickyOffset); }, { passive: true });
    }

    window.RichesCorsosUI = { openCart, refreshCart, setCartState, setWishlistCount };

    refreshCart();
    setWishlistCount(window.__RC?.wishlistCount ?? 0);

    // ── Strip Facebook's #_=_ fragment ────────────────────────────────────────
    if (window.location.hash === '#_=_') {
        history.replaceState(null, '', window.location.pathname + window.location.search);
    }

    // ── HTTPS check ──────────────────────────────────────────────────────────
    if (location.protocol === 'http:' && location.hostname !== 'localhost' && !location.hostname.startsWith('127.')) {
        const secureUrl = location.href.replace(/^http:/, 'https:');
        const warn = document.createElement('div');
        warn.className = 'insecure-warning';
        warn.innerHTML = `
            <div class="insecure-warning-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <h2>Insecure Connection</h2>
                <p>Your connection to this site is not secure. Please switch to the encrypted version to protect your information.</p>
                <a href="${secureUrl}">Switch to Secure (HTTPS)</a>
            </div>`;
        document.body.appendChild(warn);
    }

    // ── Cookie consent ───────────────────────────────────────────────────────
    const cookieBanner = document.getElementById('cookieBanner');
    const setCookie = (name, value, days) => {
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `${name}=${value}; expires=${expires}; path=/; SameSite=Lax${location.protocol === 'https:' ? '; Secure' : ''}`;
    };
    document.getElementById('cookieAccept')?.addEventListener('click', () => {
        setCookie('rc_cookie_consent', 'accepted', 365);
        cookieBanner?.remove();
    });
    document.getElementById('cookieDecline')?.addEventListener('click', () => {
        setCookie('rc_cookie_consent', 'declined', 30);
        cookieBanner?.remove();
    });
})();
