import { useEffect, useRef, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import CartDrawer from '@/Components/CartDrawer';
import SearchOverlay from '@/Components/SearchOverlay';
import AccountDropdown from '@/Components/AccountDropdown';
import MobileNavDrawer from '@/Components/MobileNavDrawer';

const NAV_LINKS = [
  { label: 'Home', href: '/' },
  { label: 'Available Puppies', href: '/puppies' },
  { label: 'About Us', href: '/about' },
  { label: 'Blog', href: '/blog' },
  { label: 'Contact Us', href: '/contact' },
];

function HeartIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
    </svg>
  );
}

function CartIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round">
      <path d="M3 4h2l1 12h13l2-9H7" />
      <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
      <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
    </svg>
  );
}

function SearchIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round">
      <circle cx="11" cy="11" r="7" />
      <path d="M21 21l-4.3-4.3" />
    </svg>
  );
}

function BellIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round">
      <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
      <path d="M13.73 21a2 2 0 0 1-3.46 0" />
    </svg>
  );
}

function FooterAccordion({ title, children }) {
  const [open, setOpen] = useState(false);
  return (
    <div className="footer-accordion">
      <button
        type="button"
        className="footer-accordion-trigger"
        onClick={() => setOpen(o => !o)}
        aria-expanded={open}
      >
        <span>{title}</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" width="16" height="16" style={{ transform: open ? 'rotate(45deg)' : 'none', transition: 'transform 0.2s' }}>
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
      </button>
      {open && <div className="footer-accordion-body">{children}</div>}
    </div>
  );
}

function FooterSocials({ siteSettings }) {
  const fb = siteSettings?.facebook || 'https://facebook.com';
  const ig = siteSettings?.instagram || 'https://instagram.com';
  const tt = siteSettings?.tiktok || 'https://tiktok.com';
  return (
    <div className="footer-socials">
      <a href={fb} aria-label="Facebook" target="_blank" rel="noopener noreferrer" className="footer-social-btn">
        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
          <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
        </svg>
      </a>
      <a href={ig} aria-label="Instagram" target="_blank" rel="noopener noreferrer" className="footer-social-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="18" height="18">
          <rect x="2" y="2" width="20" height="20" rx="5" />
          <circle cx="12" cy="12" r="4" />
          <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
        </svg>
      </a>
      <a href={tt} aria-label="TikTok" target="_blank" rel="noopener noreferrer" className="footer-social-btn">
        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
          <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.76a4.85 4.85 0 01-1.01-.07z" />
        </svg>
      </a>
    </div>
  );
}

export default function SiteLayout({ children }) {
  const { url, props } = usePage();
  const [stuck, setStuck] = useState(false);
  const [cartOpen, setCartOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [navBottom, setNavBottom] = useState(108);
  const navRef = useRef(null);
  const user = props.auth?.user;
  const siteSettings = props.siteSettings ?? {};

  const cartItems = props.cartItems ?? [];
  const cartCount = props.cartCount ?? 0;
  const wishlistCount = props.wishlistCount ?? 0;
  const unreadNotificationsCount = props.unreadNotificationsCount ?? 0;
  const loginNotice = props.flash?.login_notice;
  const [dismissedNotice, setDismissedNotice] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [installPrompt, setInstallPrompt] = useState(null);
  const [showInstallBanner, setShowInstallBanner] = useState(false);

  // Capture the beforeinstallprompt event
  useEffect(() => {
    const handler = (e) => {
      e.preventDefault();
      setInstallPrompt(e);
      // Only show on mobile
      if (window.innerWidth <= 860) {
        setShowInstallBanner(true);
        // Auto-dismiss after 8 seconds
        window.setTimeout(() => setShowInstallBanner(false), 8000);
      }
    };
    window.addEventListener('beforeinstallprompt', handler);
    return () => window.removeEventListener('beforeinstallprompt', handler);
  }, []);

  const handleInstall = async () => {
    if (!installPrompt) { return; }
    installPrompt.prompt();
    const { outcome } = await installPrompt.userChoice;
    if (outcome === 'accepted') {
      setInstallPrompt(null);
    }
    setShowInstallBanner(false);
  };

  useEffect(() => {
    if (loginNotice) {
      setDismissedNotice(false);
    }
  }, [loginNotice]);

  const updateNavBottom = () => {
    if (window.innerWidth <= 860) {
      const mTop = document.querySelector('.mobile-topbar');
      if (mTop) {
        setNavBottom(Math.round(mTop.getBoundingClientRect().bottom));
        return;
      }
    }
    if (navRef.current) {
      setNavBottom(Math.round(navRef.current.getBoundingClientRect().bottom));
    }
  };

  useEffect(() => {
    updateNavBottom();
    const onScroll = () => {
      setStuck(window.scrollY > 4);
      updateNavBottom();
    };
    window.addEventListener('scroll', onScroll);
    window.addEventListener('resize', updateNavBottom);
    return () => {
      window.removeEventListener('scroll', onScroll);
      window.removeEventListener('resize', updateNavBottom);
    };
  }, []);

  useEffect(() => {
    document.body.style.overflow = (cartOpen || searchOpen) ? 'hidden' : '';
    return () => { document.body.style.overflow = ''; };
  }, [cartOpen, searchOpen]);

  const handleOpenSearch = () => {
    updateNavBottom();
    setSearchOpen(true);
  };

  const isActive = (href) => (href === '/' ? url === '/' : url.startsWith(href));

  const CANONICAL_DOMAIN = 'https://richescorsos.com';

  return (
    <>
      {/* ===== MOBILE TOP BAR ===== */}
      <header className="mobile-topbar">
        <button
          type="button"
          className="m-menu-btn"
          onClick={() => setMobileMenuOpen(true)}
          aria-label="Open navigation menu"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" width="22" height="22">
            <path d="M4 7h16M4 12h16M4 17h16" />
          </svg>
          <span>Menu</span>
        </button>

        <Link href="/" className="m-logo" aria-label="Riches Corsos Home">
          <picture>
            <source srcSet="/images/logo.webp" type="image/webp" />
            <img src="/images/logo-sm.png" alt="Riches Corsos" className="mobile-logo-img" />
          </picture>
        </Link>

        <div className="m-right">
          <button
            type="button"
            className="m-icon-btn m-cart"
            onClick={() => setCartOpen(true)}
            aria-label="Open shopping cart"
          >
            <CartIcon />
            {cartCount > 0 && <span className="cart-count">{cartCount}</span>}
          </button>
        </div>
      </header>

      {/* ===== DESKTOP TOP UTILITY BAR ===== */}
      <div className="topbar">
        <div className="topbar-inner">
          <Link href="/" className="logo">
            <picture>
              <source srcSet="/images/logo.webp" type="image/webp" />
              <img src="/images/logo-sm.png" alt="Riches Corsos" className="logo-img" />
            </picture>
          </Link>

          <div className="topbar-actions">
            <AccountDropdown user={user} />
            <div className="topbar-icons">
              <button className="icon-btn" onClick={handleOpenSearch} aria-label="Search">
                <SearchIcon />
              </button>
              <Link href={user ? '/account/notifications' : '/login'} className="icon-btn icon-btn--notif" aria-label="Notifications">
                <BellIcon />
                {unreadNotificationsCount > 0 && <span className="cart-badge">{unreadNotificationsCount}</span>}
              </Link>
              <Link href={user ? '/wishlist' : '/login'} className="icon-btn icon-btn--wishlist" aria-label="Wishlist">
                <HeartIcon />
                {wishlistCount > 0 && <span className="cart-badge">{wishlistCount}</span>}
              </Link>
              <button className="icon-btn icon-btn--cart" onClick={() => setCartOpen(true)} aria-label="Open cart">
                <CartIcon />
                {cartCount > 0 && <span className="cart-badge">{cartCount}</span>}
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* ===== DESKTOP STICKY NAV ROW ===== */}
      <div ref={navRef} className={`navrow ${stuck ? 'is-stuck' : ''}`}>
        <div className="navrow-inner">
          <div className="nav-links">
            {NAV_LINKS.map((link) => (
              <Link key={link.href} href={link.href} className={isActive(link.href) ? 'active' : ''}>
                {link.label}
              </Link>
            ))}
          </div>
        </div>
      </div>

      {loginNotice && !dismissedNotice && (
        <aside className="login-dashboard-notice" role="alert" aria-live="polite">
          <div className="login-notice-container">
            <div className="login-notice-left">
              <div className="login-notice-icon-wrap">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
              </div>
              <div className="login-notice-content">
                <div className="login-notice-headline">
                  <span className="login-notice-tag">Account Active</span>
                  <span className="login-notice-greeting">Welcome back{user?.name ? `, ${user.name}` : ''}!</span>
                </div>
                <p className="login-notice-text">
                  {loginNotice}{' '}
                  <Link href="/account" className="login-notice-inline-link">
                    Open your dashboard to track your progress &rarr;
                  </Link>
                </p>
              </div>
            </div>
            <div className="login-notice-right">
              <Link href="/account" className="login-notice-cta-btn">
                <span>Go to Dashboard</span>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12" />
                  <polyline points="12 5 19 12 12 19" />
                </svg>
              </Link>
              <button
                type="button"
                onClick={() => setDismissedNotice(true)}
                className="login-notice-dismiss-btn"
                aria-label="Close notification"
                title="Dismiss message"
              >
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>
          </div>
        </aside>
      )}

      <div className="page-content-wrap">
      <main>{children}</main>

      {/* ===== FOOTER ===== */}
      <footer className="site-footer">
        <div className="footer-main">
          {/* Brand col */}
          <div className="footer-brand-col">
            <Link href="/" className="footer-logo-wrap">
              <picture>
                <source srcSet="/images/logo.webp" type="image/webp" />
                <img src="/images/logo-sm.png" alt="Riches Corsos" className="footer-logo-img-lg" />
              </picture>
            </Link>
            <p className="footer-brand-name">RICHES CORSOS</p>
            <p className="footer-brand-desc">Premium Cane Corso puppies raised in a loving home — with lifetime breeder support for every family.</p>
            <FooterSocials siteSettings={siteSettings} />
          </div>

          {/* Quick Links — accordion on mobile */}
          <div className="footer-col footer-col--accordion">
            <h4 className="footer-col-heading-desktop">Quick Links</h4>
            <FooterAccordion title="Quick Links">
              <Link href="/">Home</Link>
              <Link href="/about">Our Story</Link>
              <Link href="/puppies">Available Puppies</Link>
              <Link href="/testimonials">Customer Reviews</Link>
              <Link href="/blog">Blog &amp; Care Articles</Link>
              <Link href="/faqs">FAQs</Link>
            </FooterAccordion>
            {/* Desktop links (hidden on mobile, shown via CSS) */}
            <div className="footer-col-links-desktop">
              <Link href="/">Home</Link>
              <Link href="/about">Our Story</Link>
              <Link href="/puppies">Available Puppies</Link>
              <Link href="/testimonials">Customer Reviews</Link>
              <Link href="/blog">Blog &amp; Care Articles</Link>
              <Link href="/faqs">FAQs</Link>
            </div>
          </div>

          {/* Puppy Info — accordion on mobile */}
          <div className="footer-col footer-col--accordion">
            <h4 className="footer-col-heading-desktop">Puppy Information</h4>
            <FooterAccordion title="Puppy Information">
              <Link href="/puppies?sex=male">Male Puppies</Link>
              <Link href="/puppies?sex=female">Female Puppies</Link>
              <Link href="/faqs#health">Health Testing &amp; Guarantee</Link>
              <Link href="/faqs#availability">Adoption &amp; Reservation</Link>
              <Link href="/faqs#care">Puppy Care &amp; Going Home</Link>
              <Link href="/faqs#aftercare">Lifetime Breeder Support</Link>
            </FooterAccordion>
            <div className="footer-col-links-desktop">
              <Link href="/puppies?sex=male">Male Puppies</Link>
              <Link href="/puppies?sex=female">Female Puppies</Link>
              <Link href="/faqs#health">Health Testing &amp; Guarantee</Link>
              <Link href="/faqs#availability">Adoption &amp; Reservation</Link>
              <Link href="/faqs#care">Puppy Care &amp; Going Home</Link>
              <Link href="/faqs#aftercare">Lifetime Breeder Support</Link>
            </div>
          </div>

          {/* Contact + Hours */}
          <div className="footer-col footer-contact-col">
            <h4>Contact &amp; Hours</h4>
            <a href={`mailto:${siteSettings.email || 'info@richescorsos.com'}`} className="footer-contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="16" height="16"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="M2 7l10 7 10-7" /></svg>
              {siteSettings.email || 'info@richescorsos.com'}
            </a>
            <a href={`tel:${siteSettings.phone ? siteSettings.phone.replace(/[^+\d]/g, '') : '+12142123023'}`} className="footer-contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="16" height="16"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z" /></svg>
              {siteSettings.phone || '+1 (214) 212-3023'}
            </a>
            <div className="footer-hours-compact">
              <div><span>Mon–Fri</span><strong>9am – 6pm</strong></div>
              <div><span>Saturday</span><strong>10am – 4pm</strong></div>
              <div><span>Sunday</span><strong>By appointment</strong></div>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="footer-bottom-bar">
          <span>© {new Date().getFullYear()} RICHES CORSOS. All rights reserved.</span>
          <div className="footer-legal-links">
            <Link href="/privacy">Privacy Policy</Link>
            <span>|</span>
            <Link href="/terms">Terms &amp; Conditions</Link>
          </div>
        </div>
      </footer>

      </div>{/* end overflow-x:clip wrapper */}

      {/* ===== MOBILE BOTTOM NAV (5 ITEMS: SEARCH · SHOP · WISHLIST · ORDERS · ACCOUNT) ===== */}
      <div className="mobile-bottomnav">
        <button className="mn-item" onClick={handleOpenSearch} aria-label="Search puppies and articles">
          <SearchIcon />
          <span>Search</span>
        </button>

        <Link href="/puppies" className={`mn-item ${isActive('/puppies') ? 'active' : ''}`}>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="20" height="20">
            <path d="M10 2c-1 2-3.5 3-5 4.5S3 10 4 12s3 2.5 4 4 1 4 4 4 3-3 4-4 3-3 4-4 3-3 2-5-3-3-5-3.5S11 0 10 2z" />
            <circle cx="14.5" cy="9.5" r="1" fill="currentColor" stroke="none" />
          </svg>
          <span>Shop</span>
        </Link>

        <Link href={user ? '/wishlist' : '/login'} className={`mn-item ${isActive('/wishlist') ? 'active' : ''}`}>
          <div className="mn-icon-wrap">
            <HeartIcon />
            {wishlistCount > 0 && <span className="mn-badge">{wishlistCount}</span>}
          </div>
          <span>Wishlist</span>
        </Link>

        <Link href={user ? '/orders' : '/login'} className={`mn-item ${isActive('/orders') ? 'active' : ''}`}>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="20" height="20">
            <rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8M8 13h5" />
          </svg>
          <span>Orders</span>
        </Link>

        <Link href={user ? '/account' : '/login'} className={`mn-item ${isActive('/account') ? 'active' : ''}`}>
          <div className="mn-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="20" height="20">
              <circle cx="12" cy="8" r="4" /><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" />
            </svg>
            {unreadNotificationsCount > 0 && <span className="mn-badge">{unreadNotificationsCount}</span>}
          </div>
          <span>Account</span>
        </Link>
      </div>

      {/* ===== PWA INSTALL BANNER ===== */}
      {showInstallBanner && (
        <div className="pwa-install-banner">
          <div className="pwa-install-banner-left">
            <img src="/images/pwa/icon-192.png" alt="Riches Corsos" className="pwa-install-icon" />
            <div>
              <strong>Add to Home Screen</strong>
              <span>Quick access to our puppies</span>
            </div>
          </div>
          <div className="pwa-install-banner-actions">
            <button type="button" className="pwa-install-btn" onClick={handleInstall}>Install</button>
            <button type="button" className="pwa-dismiss-btn" onClick={() => setShowInstallBanner(false)} aria-label="Dismiss">✕</button>
          </div>
        </div>
      )}

      {/* ===== OVERLAYS ===== */}
      <SearchOverlay
        open={searchOpen}
        onClose={() => setSearchOpen(false)}
        topOffset={navBottom}
      />
      <CartDrawer open={cartOpen} onClose={() => setCartOpen(false)} items={cartItems} />
      <MobileNavDrawer
        open={mobileMenuOpen}
        onClose={() => setMobileMenuOpen(false)}
        user={user}
        wishlistCount={wishlistCount}
        unreadNotificationsCount={unreadNotificationsCount}
        cartCount={cartCount}
        onOpenCart={() => {
          setMobileMenuOpen(false);
          setCartOpen(true);
        }}
        siteSettings={siteSettings}
      />
    </>
  );
}
