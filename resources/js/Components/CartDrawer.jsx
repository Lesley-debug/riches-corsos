import { Link, router, usePage } from '@inertiajs/react';

function priceLabel(price) {
    return price ? `$${Number(price).toLocaleString()}` : 'Contact for price';
}

export default function CartDrawer({ open, onClose, items = [] }) {
    const { props } = usePage();
    const user = props.auth?.user;

    const removeItem = (puppy) => {
        router.delete(`/cart/${puppy.id}`, { preserveScroll: true });
    };

    const subtotal = items.reduce((sum, item) => sum + (Number(item.price) || 0), 0);

    return (
        <>
            <div className={`cart-backdrop ${open ? 'cart-backdrop--visible' : ''}`} onClick={onClose} />

            <aside className={`cart-drawer ${open ? 'cart-drawer--open' : ''}`} aria-label="Your cart">

                {/* Header with close button */}
                <div className="cart-drawer-header">
                    <div className="cart-drawer-header-left">
                        <p className="cart-drawer-kicker">Your selections</p>
                        <h2>Cart {items.length > 0 && <span className="cart-drawer-count">({items.length})</span>}</h2>
                    </div>
                    <button type="button" className="cart-drawer-close" onClick={onClose} aria-label="Close cart">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {/* Body */}
                <div className="cart-drawer-body">
                    {items.length === 0 ? (
                        <div className="cart-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4">
                                <path d="M3 4h2l1 12h13l2-9H7" strokeLinecap="round" strokeLinejoin="round" />
                                <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
                                <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
                            </svg>
                            <p>Your cart is empty.</p>
                            <Link href="/puppies" className="btn-solid" onClick={onClose}>Browse Puppies</Link>
                        </div>
                    ) : (
                        <div className="cart-items">
                            {items.map((puppy) => {
                                const image = puppy.images?.[0]?.path;
                                return (
                                    <article key={puppy.id} className="cart-item">
                                        <Link href={`/puppies/${puppy.slug}`} className="cart-item-image" onClick={onClose}>
                                            {image
                                                ? <img src={`/storage/${image}`} alt={puppy.name} />
                                                : <div className="cart-item-placeholder" />}
                                        </Link>
                                        <div className="cart-item-body">
                                            <Link href={`/puppies/${puppy.slug}`} className="cart-item-name" onClick={onClose}>{puppy.name}</Link>
                                            <p className="cart-item-meta">{[puppy.sex, puppy.color].filter(Boolean).join(' · ')}</p>
                                            <strong className="cart-item-price">{priceLabel(puppy.price)}</strong>
                                        </div>
                                        <button
                                            type="button"
                                            className="cart-item-remove"
                                            onClick={() => removeItem(puppy)}
                                            aria-label={`Remove ${puppy.name}`}
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" width="16" height="16">
                                                <path d="M18 6L6 18M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </article>
                                );
                            })}
                        </div>
                    )}
                </div>

                {/* Footer */}
                {items.length > 0 && (
                    <div className="cart-drawer-footer">
                        {subtotal > 0 && (
                            <div className="cart-drawer-subtotal">
                                <span>Estimated Total</span>
                                <strong>${subtotal.toLocaleString()}</strong>
                            </div>
                        )}
                        {user ? (
                            <Link href="/cart" className="btn-solid cart-drawer-cta" onClick={onClose}>
                                View Cart &amp; Checkout
                            </Link>
                        ) : (
                            <Link href="/login" className="btn-solid cart-drawer-cta" onClick={onClose}>
                                Sign In to Checkout
                            </Link>
                        )}
                        <Link href="/puppies" className="cart-continue-link" onClick={onClose}>
                            Continue Shopping
                        </Link>
                    </div>
                )}
            </aside>
        </>
    );
}
