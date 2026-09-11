import { useEffect } from 'react';
import { Link, router } from '@inertiajs/react';

export default function MobileNavDrawer({
  open,
  onClose,
  user,
  unreadNotificationsCount = 0,
  wishlistCount = 0,
  cartCount = 0,
  onOpenCart,
  siteSettings = {},
}) {
  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === 'Escape' && open) {
        onClose();
      }
    };
    if (open) {
      document.body.style.overflow = 'hidden';
      window.addEventListener('keydown', handleKeyDown);
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [open, onClose]);

  const handleLogout = (e) => {
    e.preventDefault();
    onClose();
    router.post('/logout');
  };

  const handleCartClick = () => {
    onClose();
    if (onOpenCart) {
      onOpenCart();
    }
  };

  const phone = siteSettings.phone || '+1 (214) 212-3023';
  const cleanPhone = phone.replace(/[^+\d]/g, '');
  const whatsapp = siteSettings.whatsapp || '12142123023';
  const cleanWhatsapp = whatsapp.replace(/[^+\d]/g, '');

  return (
    <>
      <div
        className={`mobile-drawer-backdrop ${open ? 'is-visible' : ''}`}
        onClick={onClose}
        aria-hidden="true"
      />

      <aside
        className={`mobile-drawer ${open ? 'is-open' : ''}`}
        role="dialog"
        aria-modal="true"
        aria-label="Navigation Menu"
      >
        <div className="mobile-drawer-header">
          <Link href="/" onClick={onClose} className="mobile-drawer-logo">
            <picture>
              <source srcSet="/images/logo.webp" type="image/webp" />
              <img src="/images/logo-sm.png" alt="Riches Corsos" />
            </picture>
            <span className="mobile-drawer-title">RICHES CORSOS</span>
          </Link>
          <button
            type="button"
            className="mobile-drawer-close"
            onClick={onClose}
            aria-label="Close menu"
          >
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <div className="mobile-drawer-body">
          {/* User Account Quick Card */}
          <div className="mobile-drawer-user-card">
            {user ? (
              <div className="drawer-user-info">
                <div className="drawer-user-avatar">
                  {user.avatar ? (
                    <img src={user.avatar} alt={user.name} referrerPolicy="no-referrer" />
                  ) : (
                    <span>{user.name?.charAt(0)?.toUpperCase() || 'C'}</span>
                  )}
                </div>
                <div className="drawer-user-meta">
                  <span className="drawer-user-name">{user.name}</span>
                  <span className="drawer-user-role">Corso VIP Client</span>
                </div>
                <Link href="/account" onClick={onClose} className="drawer-user-btn">
                  Dashboard
                </Link>
              </div>
            ) : (
              <div className="drawer-guest-box">
                <p className="drawer-guest-lead">Welcome to Riches Corsos</p>
                <div className="drawer-guest-actions">
                  <Link href="/login" onClick={onClose} className="drawer-btn-primary">
                    Sign In
                  </Link>
                  <Link href="/register" onClick={onClose} className="drawer-btn-secondary">
                    Create Account
                  </Link>
                </div>
              </div>
            )}
          </div>

          {/* Main Navigation Links */}
          <nav className="mobile-drawer-nav" aria-label="Main links">
            <span className="drawer-section-label">Explore</span>
            <Link href="/" onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
              </svg>
              <span>Home</span>
            </Link>

            <Link href="/puppies" onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <path d="M10 2c-1 2-3.5 3-5 4.5S3 10 4 12s3 2.5 4 4 1 4 4 4 3-3 4-4 3-3 4-4 3-3 2-5-3-3-5-3.5S11 0 10 2z" />
                <circle cx="14.5" cy="9.5" r="1" fill="currentColor" stroke="none" />
              </svg>
              <span>Available Puppies</span>
              <span className="drawer-badge">Puppies</span>
            </Link>

            <Link href="/about" onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 16v-4" />
                <path d="M12 8h.01" />
              </svg>
              <span>About Us &amp; Standards</span>
            </Link>

            <Link href="/blog" onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
              </svg>
              <span>Blog &amp; Care Articles</span>
            </Link>

            <Link href="/testimonials" onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
              </svg>
              <span>Customer Reviews</span>
            </Link>

            <Link href="/faqs" onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <circle cx="12" cy="12" r="10" />
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
              </svg>
              <span>Adoption FAQs</span>
            </Link>

            <Link href="/contact" onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="M2 7l10 7 10-7" />
              </svg>
              <span>Contact Us</span>
            </Link>
          </nav>

          {/* Customer Portal Shortcuts */}
          <nav className="mobile-drawer-nav" aria-label="Account shortcuts">
            <span className="drawer-section-label">Your Account &amp; Activity</span>
            <Link href={user ? '/account' : '/login'} onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" />
              </svg>
              <span>Account Dashboard</span>
            </Link>

            <Link href={user ? '/orders' : '/login'} onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <rect x="4" y="4" width="16" height="16" rx="2" />
                <path d="M8 9h8M8 13h5" />
              </svg>
              <span>My Reservations</span>
            </Link>

            <Link href={user ? '/wishlist' : '/login'} onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
              </svg>
              <span>Saved Wishlist</span>
              {wishlistCount > 0 && <span className="drawer-count">{wishlistCount}</span>}
            </Link>

            <Link href={user ? '/account/notifications' : '/login'} onClick={onClose} className="drawer-link">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
              </svg>
              <span>Notifications</span>
              {unreadNotificationsCount > 0 && <span className="drawer-count">{unreadNotificationsCount}</span>}
            </Link>

            <button type="button" onClick={handleCartClick} className="drawer-link drawer-link--btn">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                <path d="M3 4h2l1 12h13l2-9H7" />
                <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
                <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
              </svg>
              <span>Shopping Cart</span>
              {cartCount > 0 && <span className="drawer-count">{cartCount}</span>}
            </button>

            {user?.isAdmin && (
              <a href="/admin" className="drawer-link drawer-admin-link">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                  <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                <span>Admin Panel ↗</span>
              </a>
            )}

            {user && (
              <button type="button" onClick={handleLogout} className="drawer-link drawer-logout-btn">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                  <polyline points="16 17 21 12 16 7" />
                  <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                <span>Log Out</span>
              </button>
            )}
          </nav>
        </div>

        {/* Quick Contact Footer inside Drawer */}
        <div className="mobile-drawer-footer">
          <a href={`tel:${cleanPhone}`} className="drawer-contact-btn">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.07 9.81 19.79 19.79 0 0 1 2 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L6.09 7.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 14.92z" />
            </svg>
            <span>Call Us</span>
          </a>
          <a
            href={`https://wa.me/${cleanWhatsapp}`}
            target="_blank"
            rel="noopener noreferrer"
            className="drawer-contact-btn drawer-contact-btn--wa"
          >
            <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
            <span>WhatsApp</span>
          </a>
        </div>
      </aside>
    </>
  );
}
