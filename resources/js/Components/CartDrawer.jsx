import { Link, router } from '@inertiajs/react';

function priceLabel(price) {
    return price ? `$${Number(price).toLocaleString()}` : 'Contact for price';
}

export default function CartDrawer({ open, onClose, items = [] }) {
    const removeItem = (puppy) => {
        router.delete(`/cart/${puppy.id}`, { preserveScroll: true });
    };

    return (
        <>
            <div className={`cart-backdrop ${open ? 'cart-backdrop--visible' : ''}`} onClick={onClose} />

            <aside className={`cart-drawer ${open ? 'cart-drawer--open' : ''}`} aria-label="Your cart">
                <div className="cart-drawer-header">
                    <div>
                        <p className="cart-drawer-kicker">Your selections</p>
                        <h2>Your Cart ({items.length})</h2>
                    </div>
                    <button type="button" className="cart-drawer-close" onClick={onClose} aria-label="Close cart">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="cart-drawer-body">
                    {items.length === 0 ? (
                        <div className="cart-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4">
                                <path d="M3 4h2l1 12h13l2-9H7" strokeLinecap="round" strokeLinejoin="round" />
                                <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
                                <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
                            </svg>
                            <p>Your cart is empty.</p>
                            <Link href="/puppies" className="btn-solid" onClick={onClose}>
                                Browse Puppies
                            </Link>
                        </div>
                    ) : (
                        <div className="cart-items">
                            {items.map((puppy) => {
                                const image = puppy.images?.[0]?.path;

                                return (
                                    <article key={puppy.id} className="cart-item">
                                        <Link href={`/puppies/${puppy.slug}`} className="cart-item-image" onClick={onClose}>
                                            {image ? <img src={`/storage/${image}`} alt={puppy.name} /> : <span />}
                                        </Link>
                                        <div className="cart-item-body">
                                            <Link href={`/puppies/${puppy.slug}`} onClick={onClose}>{puppy.name}</Link>
                                            <p>{[puppy.sex, puppy.color].filter(Boolean).join(' - ')}</p>
                                            <strong>{priceLabel(puppy.price)}</strong>
                                        </div>
                                        <button type="button" className="cart-item-remove" onClick={() => removeItem(puppy)} aria-label={`Remove ${puppy.name} from cart`}>
                                            Remove
                                        </button>
                                    </article>
                                );
                            })}
                        </div>
                    )}
                </div>

                {items.length > 0 && (
                    <div className="cart-drawer-footer">
                        <Link href="/cart" className="btn-solid" onClick={onClose}>Proceed To Checkout</Link>
                        <Link href="/puppies" className="cart-continue-link" onClick={onClose}>Continue browsing</Link>
                    </div>
                )}
            </aside>
        </>
    );
}
