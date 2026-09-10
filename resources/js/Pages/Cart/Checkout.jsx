import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

function priceLabel(price) {
    return price ? `$${Number(price).toLocaleString()}` : 'Contact for price';
}

export default function Checkout({ cartItems = [] }) {
    const { props } = usePage();
    const user = props.auth?.user;
    const { data, setData, post, processing, errors } = useForm({
        buyer_name: user?.name ?? '',
        buyer_email: user?.email ?? '',
        buyer_phone: '',
        buyer_address: '',
        notes: '',
    });

    const submit = (event) => {
        event.preventDefault();
        post('/checkout');
    };

    const removeItem = (puppy) => {
        router.delete(`/cart/${puppy.id}`, { preserveScroll: true });
    };

    return (
        <SiteLayout>
            <Head title="Checkout | Riches Corsos" />

            <main className="checkout-page">
                <div className="checkout-shell">
                    <div className="checkout-heading">
                        <p className="shop-eyebrow">Your Cart</p>
                        <h1>Complete Your Order Request</h1>
                        <p>Submit your details and we will contact you to confirm availability and the next steps. No payment is collected here.</p>
                    </div>

                    {cartItems.length === 0 ? (
                        <div className="checkout-empty">
                            <h2>Your cart is empty</h2>
                            <p>Browse the available puppies and add the ones you would like to discuss.</p>
                            <Link href="/puppies" className="btn-solid">Browse Puppies</Link>
                        </div>
                    ) : (
                        <div className="checkout-grid">
                            <section className="checkout-form-panel">
                                <h2>Your Details</h2>
                                <form onSubmit={submit} className="checkout-form">
                                    <div className="checkout-field-grid">
                                        <label>
                                            Full name
                                            <input value={data.buyer_name} onChange={(event) => setData('buyer_name', event.target.value)} autoComplete="name" required />
                                            {errors.buyer_name && <span className="form-error">{errors.buyer_name}</span>}
                                        </label>
                                        <label>
                                            Email address
                                            <input type="email" value={data.buyer_email} onChange={(event) => setData('buyer_email', event.target.value)} autoComplete="email" required />
                                            {errors.buyer_email && <span className="form-error">{errors.buyer_email}</span>}
                                        </label>
                                        <label>
                                            Phone number
                                            <input value={data.buyer_phone} onChange={(event) => setData('buyer_phone', event.target.value)} autoComplete="tel" required />
                                            {errors.buyer_phone && <span className="form-error">{errors.buyer_phone}</span>}
                                        </label>
                                        <label>
                                            Address (optional)
                                            <input value={data.buyer_address} onChange={(event) => setData('buyer_address', event.target.value)} autoComplete="street-address" />
                                            {errors.buyer_address && <span className="form-error">{errors.buyer_address}</span>}
                                        </label>
                                    </div>
                                    <label>
                                        Notes (optional)
                                        <textarea rows="4" value={data.notes} onChange={(event) => setData('notes', event.target.value)} />
                                        {errors.notes && <span className="form-error">{errors.notes}</span>}
                                    </label>
                                    {errors.cart && <p className="form-error">{errors.cart}</p>}
                                    <button type="submit" className="btn-solid" disabled={processing}>
                                        {processing ? 'Placing Order...' : 'Place Order Request'}
                                    </button>
                                </form>
                            </section>

                            <aside className="checkout-summary">
                                <div className="checkout-summary-heading">
                                    <h2>Your Puppies</h2>
                                    <span>{cartItems.length}</span>
                                </div>
                                <div className="checkout-items">
                                    {cartItems.map((puppy) => {
                                        const image = puppy.images?.[0]?.path;

                                        return (
                                            <article key={puppy.id} className="checkout-item">
                                                <Link href={`/puppies/${puppy.slug}`} className="checkout-item-image">
                                                    {image ? <img src={`/storage/${image}`} alt={puppy.name} /> : <span />}
                                                </Link>
                                                <div>
                                                    <Link href={`/puppies/${puppy.slug}`}>{puppy.name}</Link>
                                                    <p>{[puppy.sex, puppy.color].filter(Boolean).join(' - ')}</p>
                                                    <strong>{priceLabel(puppy.price)}</strong>
                                                </div>
                                                <button type="button" onClick={() => removeItem(puppy)}>Remove</button>
                                            </article>
                                        );
                                    })}
                                </div>
                                <Link href="/puppies" className="checkout-continue-link">Continue browsing</Link>
                            </aside>
                        </div>
                    )}
                </div>
            </main>
        </SiteLayout>
    );
}
