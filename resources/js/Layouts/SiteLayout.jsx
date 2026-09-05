import { useEffect, useRef, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import CartDrawer from '@/Components/CartDrawer';
import SearchOverlay from '@/Components/SearchOverlay';
import AccountDropdown from '@/Components/AccountDropdown';

const NAV_LINKS = [
  { label: 'Home', href: '/' },
  { label: 'Available Puppies', href: '/puppies' },
  { label: 'About Us', href: '/about' },
  { label: 'Blog', href: '/blog' },
  { label: 'Contact Us', href: '/contact' },
];

function HeartIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round">
      <path d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z" />
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

function FooterSocials() {
  return (
    <div className="footer-socials">
      <a href="https://facebook.com" aria-label="Facebook" target="_blank" rel="noopener noreferrer" className="footer-social-btn">
        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
          <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
        </svg>
      </a>
      <a href="https://instagram.com" aria-label="Instagram" target="_blank" rel="noopener noreferrer" className="footer-social-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="18" height="18">
          <rect x="2" y="2" width="20" height="20" rx="5" />
          <circle cx="12" cy="12" r="4" />
          <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
        </svg>
      </a>
      <a href="https://tiktok.com" aria-label="TikTok" target="_blank" rel="noopener noreferrer" className="footer-social-btn">
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
  const navRef = useRef(null);
  const user = props.auth?.user;

  const searchPuppies = props.searchPuppies ?? [];
  const searchPosts = props.searchPosts ?? [];

  useEffect(() => {
    const onScroll = () => setStuck(window.scrollY > 4);
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  useEffect(() => {
    document.body.style.overflow = (cartOpen || searchOpen) ? 'hidden' : '';
    return () => { document.body.style.overflow = ''; };
  }, [cartOpen, searchOpen]);

  const isActive = (href) => (href === '/' ? url === '/' : url.startsWith(href));

  return (
    <>
      {/* ===== MOBILE TOP BAR ===== */}
      <div className="mobile-topbar">
        <div className="m-left">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="22" height="22">
            <path d="M4 7h16M4 12h16M4 17h16" />
          </svg>
          <span>Menu</span>
        </div>
        <Link href="/" className="m-logo">
          <picture>
            <source srcSet="/images/logo.webp" type="image/webp" />
            <img src="/images/logo-sm.png" alt="Riches Corsos" className="mobile-logo-img" />
          </picture>
        </Link>
        <div className="m-right">
          <button className="m-icon-btn" onClick={() => setSearchOpen(true)} aria-label="Search">
            <SearchIcon />
          </button>
          <button className="m-icon-btn m-cart" onClick={() => setCartOpen(true)} aria-label="Open cart">
            <CartIcon />
            <span className="cart-count">0</span>
          </button>
        </div>
      </div>

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
              <button className="icon-btn" onClick={() => setSearchOpen(true)} aria-label="Search">
                <SearchIcon />
                <span className="icon-label">Search</span>
              </button>
              <Link href={user ? '/wishlist' : '/login'} className="icon-btn" aria-label="Wishlist">
                <HeartIcon />
                <span className="icon-label">Wishlist</span>
              </Link>
              <button className="icon-btn icon-btn--cart" onClick={() => setCartOpen(true)} aria-label="Open cart">
                <CartIcon />
                <span className="cart-badge">0</span>
                <span className="icon-label">Cart</span>
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

      <main>{children}</main>

      {/* ===== FOOTER ===== */}
      <footer className="site-footer">
        {/* Main Footer Grid */}
        <div className="footer-main">
          <div className="footer-brand-col">
            <Link href="/" className="footer-logo-wrap">
              <picture>
                <source srcSet="/images/logo.webp" type="image/webp" />
                <img src="/images/logo-sm.png" alt="Riches Corsos" className="footer-logo-img-lg" />
              </picture>
            </Link>
            <p className="footer-brand-name">RICHES CORSOS</p>
            <p className="footer-brand-desc">
              A small, dedicated breeding program focused on raising healthy, well-socialised Cane Corso puppies in a caring home environment — with ongoing support for every family we place.
            </p>
            <FooterSocials />
          </div>

          <div className="footer-col">
            <h4>Quick Links</h4>
            <Link href="/">Home</Link>
            <Link href="/about">Our Story</Link>
            <Link href="/puppies">Available Puppies</Link>
            <Link href="/about#what-makes-us-special">What Makes Us Special</Link>
            <Link href="/testimonials">Testimonials</Link>
            <Link href="/blog">Blog</Link>
            <Link href="/faqs">FAQs</Link>
            <Link href="/contact">Contact</Link>
          </div>

          <div className="footer-col">
            <h4>Puppy Information</h4>
            <Link href="/puppies">Available Puppies</Link>
            <Link href="/faqs#care">Puppy Care</Link>
            <Link href="/faqs#health">Health &amp; Wellness</Link>
            <Link href="/about">Our Approach</Link>
            <Link href="/faqs#availability">Adoption Process</Link>
            <Link href="/faqs#care">Puppy Preparation</Link>
            <Link href="/faqs#aftercare">Support After Going Home</Link>
          </div>

          <div className="footer-col footer-contact-col">
            <h4>Contact</h4>
            <a href="mailto:info@richescorsos.com" className="footer-contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="16" height="16">
                <rect x="2" y="4" width="20" height="16" rx="2" /><path d="M2 7l10 7 10-7" />
              </svg>
              info@richescorsos.com
            </a>
            <a href="tel:+12142123023" className="footer-contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="16" height="16">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z" />
              </svg>
              +1 (214) 212-3023
            </a>
            <a href="https://wa.me/12142123023" className="footer-contact-item" target="_blank" rel="noopener noreferrer">
              <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
              </svg>
              WhatsApp Us
            </a>
            <div className="footer-contact-item footer-contact-location">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="16" height="16">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" /><circle cx="12" cy="10" r="3" />
              </svg>
              Dallas, Texas
            </div>
            <div className="footer-hours">
              <p className="footer-hours-title">Opening Hours</p>
              <p>Mon – Fri: 9am – 6pm</p>
              <p>Sat: 10am – 4pm</p>
              <p>Sun: By appointment</p>
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

      {/* ===== MOBILE BOTTOM NAV ===== */}
      <div className="mobile-bottomnav">
        <button className="mn-item" onClick={() => setSearchOpen(true)}>
          <SearchIcon />
          <span>Search</span>
        </button>
        <Link href="/puppies" className={`mn-item ${isActive('/puppies') ? 'active' : ''}`}>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="21" height="21">
            <path d="M3 4h2l1 12h13l2-9H7" />
            <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
            <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
          </svg>
          <span>Shop</span>
        </Link>
        <Link href={user ? '/wishlist' : '/login'} className={`mn-item ${isActive('/wishlist') ? 'active' : ''}`}>
          <HeartIcon />
          <span>Wishlist</span>
        </Link>
        <Link href="/orders" className={`mn-item ${isActive('/orders') ? 'active' : ''}`}>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="21" height="21">
            <rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8M8 13h5" />
          </svg>
          <span>Orders</span>
        </Link>
        <Link href={user ? '/account' : '/login'} className={`mn-item ${isActive('/account') ? 'active' : ''}`}>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="21" height="21">
            <circle cx="12" cy="8" r="4" /><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" />
          </svg>
          <span>Account</span>
        </Link>
      </div>

      {/* ===== OVERLAYS ===== */}
      <SearchOverlay
        open={searchOpen}
        onClose={() => setSearchOpen(false)}
        puppies={searchPuppies}
        posts={searchPosts}
      />
      <CartDrawer open={cartOpen} onClose={() => setCartOpen(false)} />
    </>
  );
}
