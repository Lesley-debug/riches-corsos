import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import SiteLayout from '@/Layouts/SiteLayout';

function priceLabel(price) {
    return price ? `$${Number(price).toLocaleString()}` : 'Contact for price';
}

function totalPrice(items) {
    return items.reduce((sum, p) => sum + Number(p.price ?? 0), 0);
}

const PAYMENT_METHODS = [
    {
        id: 'paypal',
        label: 'PayPal',
        description: 'Pay securely via your PayPal account or card.',
        icon: '🅿',
    },
    {
        id: 'bank_transfer',
        label: 'International Bank Transfer (SWIFT/IBAN)',
        description: 'Wire transfer — bank details provided after order confirmation.',
        icon: '🏦',
    },
    {
        id: 'debit_credit_card',
        label: 'Debit / Credit Card',
        description: 'Visa, Mastercard, Amex, Discover — all major cards accepted.',
        icon: '💳',
    },
    {
        id: 'zelle',
        label: 'Zelle',
        description: 'Fast US bank-to-bank transfer via Zelle.',
        icon: '⚡',
    },
    {
        id: 'cashapp',
        label: 'Cash App',
        description: 'Send payment instantly via Cash App.',
        icon: '💚',
    },
    {
        id: 'crypto',
        label: 'Cryptocurrency',
        description: 'Bitcoin (BTC), Ethereum (ETH), USDT — wallet address provided after confirmation.',
        icon: '₿',
    },
    {
        id: 'western_union',
        label: 'Western Union / MoneyGram',
        description: 'International money transfer — details provided on confirmation.',
        icon: '🌍',
    },
];

// Step indicator
function StepBar({ step }) {
    const steps = ['Cart Review', 'Your Details', 'Place Order'];
    return (
        <div className="checkout-steps">
            {steps.map((label, i) => (
                <div key={label} className={`checkout-step ${i + 1 === step ? 'active' : ''} ${i + 1 < step ? 'done' : ''}`}>
                    <div className="checkout-step-circle">{i + 1 < step ? '✓' : i + 1}</div>
                    <span>{label}</span>
                    {i < steps.length - 1 && <div className="checkout-step-line" />}
                </div>
            ))}
        </div>
    );
}

export default function Checkout({ cartItems = [] }) {
    const { props } = usePage();
    const user = props.auth?.user;
    const [step, setStep] = useState(1);

    const { data, setData, post, processing, errors } = useForm({
        buyer_name: user?.name ?? '',
        buyer_email: user?.email ?? '',
        buyer_phone: '',
        buyer_address: '',
        notes: '',
        payment_method: '',
    });

    const total = totalPrice(cartItems);

    const removeItem = (puppy) => {
        router.delete(`/cart/${puppy.id}`, { preserveScroll: true });
    };

    const goToStep2 = (e) => {
        e.preventDefault();
        setStep(2);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const submit = (e) => {
        e.preventDefault();
        post('/checkout');
    };

    const isStep1Valid = cartItems.length > 0;
    const isStep2Valid = data.buyer_name && data.buyer_email && data.buyer_phone && data.payment_method;

    return (
        <SiteLayout>
            <Head title="Checkout | Riches Corsos" />

            <div className="checkout-page">
                <div className="checkout-shell">

                    {/* Header */}
                    <div className="checkout-header">
                        <p className="shop-eyebrow">Secure Checkout</p>
                        <h1>Complete Your Reservation</h1>
                        <p className="checkout-header-sub">
                            Reserve your Cane Corso puppy today. No payment is charged now — our team will confirm your order and send payment instructions directly to your email.
                        </p>
                    </div>

                    <StepBar step={step} />

                    {cartItems.length === 0 ? (
                        <div className="checkout-empty">
                            <svg viewBox="0 0 24 24" width="56" height="56" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M3 4h2l1 12h13l2-9H7" />
                                <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
                                <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
                            </svg>
                            <h2>Your cart is empty</h2>
                            <p>Browse our available puppies and add the ones you love.</p>
                            <Link href="/puppies" className="btn-solid">Browse Puppies</Link>
                        </div>
                    ) : (
                        <div className="checkout-layout">

                            {/* ── MAIN PANEL ─────────────────────────────── */}
                            <div className="checkout-main">

                                {/* STEP 1 — Cart Review */}
                                {step === 1 && (
                                    <section className="checkout-section">
                                        <div className="checkout-section-head">
                                            <h2>Review Your Cart</h2>
                                            <span className="checkout-badge">{cartItems.length} {cartItems.length === 1 ? 'puppy' : 'puppies'}</span>
                                        </div>

                                        <div className="checkout-items">
                                            {cartItems.map((puppy) => {
                                                const image = puppy.images?.[0]?.path;
                                                return (
                                                    <div key={puppy.id} className="checkout-item">
                                                        <Link href={`/puppies/${puppy.slug}`} className="checkout-item-img">
                                                            {image
                                                                ? <img src={`/storage/${image}`} alt={puppy.name} />
                                                                : <div className="checkout-item-img-placeholder" />}
                                                        </Link>
                                                        <div className="checkout-item-info">
                                                            <Link href={`/puppies/${puppy.slug}`} className="checkout-item-name">{puppy.name}</Link>
                                                            <p className="checkout-item-meta">{[puppy.breed, puppy.sex, puppy.color].filter(Boolean).join(' · ')}</p>
                                                            {puppy.deposit_required && (
                                                                <p className="checkout-item-deposit">
                                                                    Deposit: ${Number(puppy.deposit_amount ?? 0).toLocaleString()} to reserve
                                                                </p>
                                                            )}
                                                        </div>
                                                        <div className="checkout-item-right">
                                                            <strong className="checkout-item-price">{priceLabel(puppy.price)}</strong>
                                                            <button
                                                                type="button"
                                                                className="checkout-remove-btn"
                                                                onClick={() => removeItem(puppy)}
                                                                aria-label={`Remove ${puppy.name}`}
                                                            >
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                        </div>

                                        <div className="checkout-step-actions">
                                            <Link href="/puppies" className="checkout-back-link">← Continue browsing</Link>
                                            <button
                                                type="button"
                                                className="btn-solid"
                                                onClick={goToStep2}
                                                disabled={!isStep1Valid}
                                            >
                                                Continue to Details →
                                            </button>
                                        </div>
                                    </section>
                                )}

                                {/* STEP 2 — Details + Payment */}
                                {step === 2 && (
                                    <section className="checkout-section">
                                        <div className="checkout-section-head">
                                            <h2>Your Details</h2>
                                        </div>

                                        <form onSubmit={submit} className="checkout-form" id="checkout-form">
                                            {/* Buyer info */}
                                            <div className="checkout-field-grid">
                                                <div className="checkout-field">
                                                    <label htmlFor="buyer_name">Full Name <span className="req">*</span></label>
                                                    <input
                                                        id="buyer_name"
                                                        type="text"
                                                        value={data.buyer_name}
                                                        onChange={(e) => setData('buyer_name', e.target.value)}
                                                        autoComplete="name"
                                                        placeholder="John Doe"
                                                        required
                                                    />
                                                    {errors.buyer_name && <span className="form-error">{errors.buyer_name}</span>}
                                                </div>
                                                <div className="checkout-field">
                                                    <label htmlFor="buyer_email">Email Address <span className="req">*</span></label>
                                                    <input
                                                        id="buyer_email"
                                                        type="email"
                                                        value={data.buyer_email}
                                                        onChange={(e) => setData('buyer_email', e.target.value)}
                                                        autoComplete="email"
                                                        placeholder="john@example.com"
                                                        required
                                                    />
                                                    {errors.buyer_email && <span className="form-error">{errors.buyer_email}</span>}
                                                </div>
                                                <div className="checkout-field">
                                                    <label htmlFor="buyer_phone">Phone Number <span className="req">*</span></label>
                                                    <input
                                                        id="buyer_phone"
                                                        type="tel"
                                                        value={data.buyer_phone}
                                                        onChange={(e) => setData('buyer_phone', e.target.value)}
                                                        autoComplete="tel"
                                                        placeholder="+1 (555) 000-0000"
                                                        required
                                                    />
                                                    {errors.buyer_phone && <span className="form-error">{errors.buyer_phone}</span>}
                                                </div>
                                                <div className="checkout-field">
                                                    <label htmlFor="buyer_address">Delivery Address <span className="optional">(optional)</span></label>
                                                    <input
                                                        id="buyer_address"
                                                        type="text"
                                                        value={data.buyer_address}
                                                        onChange={(e) => setData('buyer_address', e.target.value)}
                                                        autoComplete="street-address"
                                                        placeholder="123 Main St, City, State, ZIP"
                                                    />
                                                    {errors.buyer_address && <span className="form-error">{errors.buyer_address}</span>}
                                                </div>
                                            </div>

                                            <div className="checkout-field checkout-field--full">
                                                <label htmlFor="notes">Additional Notes <span className="optional">(optional)</span></label>
                                                <textarea
                                                    id="notes"
                                                    rows={3}
                                                    value={data.notes}
                                                    onChange={(e) => setData('notes', e.target.value)}
                                                    placeholder="Any questions, preferences, or special requests…"
                                                />
                                                {errors.notes && <span className="form-error">{errors.notes}</span>}
                                            </div>

                                            {/* Payment method */}
                                            <div className="checkout-payment-section">
                                                <h3>Select Payment Method <span className="req">*</span></h3>
                                                <p className="checkout-payment-note">
                                                    No payment is charged now. After confirming your order, we will send full payment instructions to your email.
                                                </p>
                                                <div className="checkout-payment-grid">
                                                    {PAYMENT_METHODS.map((method) => (
                                                        <label
                                                            key={method.id}
                                                            className={`checkout-payment-card ${data.payment_method === method.id ? 'selected' : ''}`}
                                                        >
                                                            <input
                                                                type="radio"
                                                                name="payment_method"
                                                                value={method.id}
                                                                checked={data.payment_method === method.id}
                                                                onChange={() => setData('payment_method', method.id)}
                                                                className="checkout-payment-radio"
                                                            />
                                                            <span className="checkout-payment-icon">{method.icon}</span>
                                                            <div className="checkout-payment-info">
                                                                <strong>{method.label}</strong>
                                                                <span>{method.description}</span>
                                                            </div>
                                                            <div className={`checkout-payment-check ${data.payment_method === method.id ? 'visible' : ''}`}>✓</div>
                                                        </label>
                                                    ))}
                                                </div>
                                                {errors.payment_method && <span className="form-error">{errors.payment_method}</span>}
                                            </div>

                                            {errors.cart && <p className="form-error checkout-cart-error">{errors.cart}</p>}

                                            <div className="checkout-step-actions">
                                                <button type="button" className="checkout-back-link" onClick={() => setStep(1)}>← Back to Cart</button>
                                                <button
                                                    type="submit"
                                                    className="btn-solid checkout-submit-btn"
                                                    disabled={processing || !isStep2Valid}
                                                >
                                                    {processing ? (
                                                        <span className="checkout-submitting">
                                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" className="checkout-spinner"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" /></svg>
                                                            Processing…
                                                        </span>
                                                    ) : 'Place Reservation →'}
                                                </button>
                                            </div>
                                        </form>
                                    </section>
                                )}
                            </div>

                            {/* ── ORDER SUMMARY SIDEBAR ───────────────────── */}
                            <aside className="checkout-sidebar">
                                <div className="checkout-summary-card">
                                    <h3 className="checkout-summary-title">Order Summary</h3>

                                    <div className="checkout-summary-items">
                                        {cartItems.map((puppy) => {
                                            const image = puppy.images?.[0]?.path;
                                            return (
                                                <div key={puppy.id} className="checkout-summary-item">
                                                    <div className="checkout-summary-thumb">
                                                        {image
                                                            ? <img src={`/storage/${image}`} alt={puppy.name} />
                                                            : <div className="checkout-summary-thumb-placeholder" />}
                                                    </div>
                                                    <div className="checkout-summary-item-info">
                                                        <span className="checkout-summary-item-name">{puppy.name}</span>
                                                        <span className="checkout-summary-item-meta">{puppy.sex} · {puppy.color}</span>
                                                    </div>
                                                    <span className="checkout-summary-item-price">{priceLabel(puppy.price)}</span>
                                                </div>
                                            );
                                        })}
                                    </div>

                                    <div className="checkout-summary-divider" />

                                    <div className="checkout-summary-row">
                                        <span>Subtotal</span>
                                        <span>${total.toLocaleString()}</span>
                                    </div>
                                    <div className="checkout-summary-row checkout-summary-row--note">
                                        <span>Deposit</span>
                                        <span className="checkout-summary-tbd">Confirmed via email</span>
                                    </div>
                                    <div className="checkout-summary-row checkout-summary-row--note">
                                        <span>Delivery</span>
                                        <span className="checkout-summary-tbd">White-glove arranged</span>
                                    </div>

                                    <div className="checkout-summary-divider" />

                                    <div className="checkout-summary-total">
                                        <span>Total</span>
                                        <strong>${total.toLocaleString()}</strong>
                                    </div>

                                    <div className="checkout-trust-badges">
                                        <div className="checkout-trust-item">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                                            <span>Secure Reservation</span>
                                        </div>
                                        <div className="checkout-trust-item">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" /></svg>
                                            <span>2-Year Health Guarantee</span>
                                        </div>
                                        <div className="checkout-trust-item">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                                            <span>We respond within 24 hrs</span>
                                        </div>
                                    </div>
                                </div>
                            </aside>

                        </div>
                    )}
                </div>
            </div>
        </SiteLayout>
    );
}
