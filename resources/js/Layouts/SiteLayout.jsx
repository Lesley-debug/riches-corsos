import { useEffect, useRef, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

const NAV_LINKS = [
  { label: 'Home', href: '/' },
  { label: 'Available Puppies', href: '/puppies' },
  { label: 'About Us', href: '/about' },
  { label: 'Blog', href: '/blog' },
  { label: 'Contact Us', href: '/contact' },
];

function Icon({ path, ...props }) {
  return (
    <svg viewBox="0 0 24 24" {...props}>
      {path}
    </svg>
  );
}

export default function SiteLayout({ children }) {
  const { url, props } = usePage();
  const [stuck, setStuck] = useState(false);
  const navRef = useRef(null);
  const user = props.auth?.user;

  useEffect(() => {
    const onScroll = () => setStuck(window.scrollY > 4);
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  const isActive = (href) => (href === '/' ? url === '/' : url.startsWith(href));

  return (
    <>
      {/* ===== MOBILE TOP BAR ===== */}
      <div className="mobile-topbar">
        <div className="m-left">
          <Icon
            viewBox="0 0 24 24"
            path={<path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" />}
          />
          <span>Menu</span>
        </div>
        <div className="m-logo">Riches Corsos</div>
        <Link href="/cart" className="m-cart">
          <Icon
            path={
              <path
                d="M3 4h2l1 12h13l2-9H7"
                stroke="currentColor"
                strokeWidth="1.6"
                fill="none"
              />
            }
          />
          <span className="cart-count">0</span>
        </Link>
      </div>

      {/* ===== DESKTOP TOP UTILITY BAR ===== */}
      <div className="topbar">
        <div className="topbar-inner">
          <Link href="/" className="logo">
            Riches <span>Corsos</span>
          </Link>
          <div className="topbar-actions">
            <Link href={user ? '/account' : '/login'} className="account-link">
              <Icon
                path={
                  <>
                    <circle cx="12" cy="8" r="4" stroke="currentColor" strokeWidth="1.6" fill="none" />
                    <path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" stroke="currentColor" strokeWidth="1.6" fill="none" />
                  </>
                }
              />
              {user ? user.name.split(' ')[0] : 'Login / Register'}
            </Link>
            <div className="topbar-icons">
              <Link href="/search" className="icon-btn">
                <Icon
                  path={
                    <>
                      <circle cx="11" cy="11" r="7" stroke="currentColor" strokeWidth="1.6" fill="none" />
                      <path d="M21 21l-4.3-4.3" stroke="currentColor" strokeWidth="1.6" />
                    </>
                  }
                />
              </Link>
              <Link href="/wishlist" className="icon-btn">
                <Icon
                  path={
                    <path
                      d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z"
                      stroke="currentColor"
                      strokeWidth="1.6"
                      fill="none"
                    />
                  }
                />
              </Link>
              <Link href="/cart" className="icon-btn">
                <Icon
                  path={
                    <>
                      <path d="M3 4h2l1 12h13l2-9H7" stroke="currentColor" strokeWidth="1.6" fill="none" />
                      <circle cx="9" cy="20" r="1.4" fill="currentColor" />
                      <circle cx="18" cy="20" r="1.4" fill="currentColor" />
                    </>
                  }
                />
                <span className="cart-count">0</span>
              </Link>
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
      <footer>
        <div className="footer-top">
          <div>
            <div className="footer-logo">Riches Corsos</div>
            <p style={{ color: 'var(--stone)', fontSize: 14, maxWidth: 260 }}>
              Health-tested Cane Corso puppies, raised in-home and placed with ongoing support.
            </p>
          </div>
          <div className="footer-col">
            <h4>Explore</h4>
            <Link href="/puppies">Available Puppies</Link>
            <Link href="/about">About</Link>
            <Link href="/contact">Contact</Link>
          </div>
          <div className="footer-col">
            <h4>Support</h4>
            <Link href="/faqs">FAQs</Link>
            <Link href="/blog">Blog</Link>
          </div>
          <div className="footer-col">
            <h4>Contact</h4>
            <a href="mailto:info@richescorsos.com">info@richescorsos.com</a>
            <a href="tel:+12142123023">+1 (214) 212-3023</a>
          </div>
        </div>
        <div className="footer-bottom">© {new Date().getFullYear()} Riches Corsos. All rights reserved.</div>
      </footer>

      {/* ===== MOBILE BOTTOM NAV ===== */}
      <div className="mobile-bottomnav">
        <Link href="/menu" className="mn-item">
          <Icon path={<path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" />} />
          <span>Menu</span>
        </Link>
        <Link href="/puppies" className={`mn-item ${isActive('/puppies') ? 'active' : ''}`}>
          <Icon
            path={
              <>
                <path d="M3 4h2l1 12h13l2-9H7" stroke="currentColor" strokeWidth="1.6" fill="none" />
                <circle cx="9" cy="20" r="1.4" fill="currentColor" />
                <circle cx="18" cy="20" r="1.4" fill="currentColor" />
              </>
            }
          />
          <span>Shop</span>
        </Link>
        <Link href="/wishlist" className="mn-item">
          <Icon
            path={
              <path
                d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z"
                stroke="currentColor"
                strokeWidth="1.6"
                fill="none"
              />
            }
          />
          <span>Wishlist</span>
        </Link>
        <Link href="/orders" className="mn-item">
          <Icon
            path={
              <>
                <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" strokeWidth="1.6" fill="none" />
                <path d="M8 9h8M8 13h5" stroke="currentColor" strokeWidth="1.6" />
              </>
            }
          />
          <span>Orders</span>
        </Link>
        <Link href={user ? '/account' : '/login'} className={`mn-item ${isActive('/account') ? 'active' : ''}`}>
          <Icon
            path={
              <>
                <circle cx="12" cy="8" r="4" stroke="currentColor" strokeWidth="1.6" fill="none" />
                <path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" stroke="currentColor" strokeWidth="1.6" fill="none" />
              </>
            }
          />
          <span>Account</span>
        </Link>
      </div>
    </>
  );
}
