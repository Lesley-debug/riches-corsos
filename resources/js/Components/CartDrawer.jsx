import { Link } from '@inertiajs/react';

export default function CartDrawer({ open, onClose }) {
  return (
    <>
      {/* Backdrop */}
      <div
        className={`cart-backdrop ${open ? 'cart-backdrop--visible' : ''}`}
        onClick={onClose}
      />

      {/* Drawer */}
      <div className={`cart-drawer ${open ? 'cart-drawer--open' : ''}`}>
        <div className="cart-drawer-header">
          <h2>Your Reservation</h2>
          <button className="cart-drawer-close" onClick={onClose} aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div className="cart-drawer-body">
          <div className="cart-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4">
              <path d="M3 4h2l1 12h13l2-9H7" strokeLinecap="round" strokeLinejoin="round" />
              <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
              <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
            </svg>
            <p>No reservations yet.</p>
            <Link href="/puppies" className="btn-solid" onClick={onClose}>
              Browse Available Puppies
            </Link>
          </div>
        </div>
      </div>
    </>
  );
}
