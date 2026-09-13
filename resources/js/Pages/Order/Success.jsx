import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

const PAYMENT_ICONS = {
    paypal: '🅿',
    bank_transfer: '🏦',
    debit_credit_card: '💳',
    zelle: '⚡',
    cashapp: '💚',
    crypto: '₿',
    western_union: '🌍',
};

export default function Success({ orders = [] }) {
    const totalPuppies = orders.length;
    const totalAmount = orders.reduce((sum, o) => sum + (Number(o.puppy_price) || 0), 0);
    const firstOrder = orders[0] ?? {};
    const paymentMethod = firstOrder.payment_method ?? '';
    const paymentIcon = PAYMENT_ICONS[paymentMethod] ?? '💳';

    return (
        <SiteLayout>
            <Head title="Order Placed Successfully — Riches Corsos" />

            <div className="success-page">
                <div className="success-shell">

                    {/* Hero checkmark */}
                    <div className="success-hero">
                        <div className="success-checkmark">
                            <svg viewBox="0 0 52 52" fill="none">
                                <circle cx="26" cy="26" r="25" stroke="var(--green)" strokeWidth="2" fill="var(--green-tint)" />
                                <path d="M14 26l9 9 15-17" stroke="var(--green-dark)" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <h1 className="success-title">Reservation Placed Successfully!</h1>
                        <p className="success-subtitle">
                            Thank you, <strong>{firstOrder.buyer_name}</strong>! Your Cane Corso reservation is confirmed.
                            A confirmation email has been sent to <strong>{firstOrder.buyer_email}</strong>.
                        </p>
                    </div>

                    <div className="success-layout">

                        {/* Order details */}
                        <div className="success-main">

                            {/* What happens next */}
                            <div className="success-card success-card--next-steps">
                                <h2 className="success-card-title">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                                    What Happens Next
                                </h2>
                                <ol className="success-steps-list">
                                    <li>
                                        <strong>Check your email</strong>
                                        <span>We've sent full order details and payment instructions to {firstOrder.buyer_email}</span>
                                    </li>
                                    <li>
                                        <strong>We'll contact you within 24 hours</strong>
                                        <span>Our team will reach out via phone or email to confirm your reservation and walk you through the next steps</span>
                                    </li>
                                    <li>
                                        <strong>Complete payment ({paymentIcon} {paymentMethod.replace(/_/g, ' ')})</strong>
                                        <span>Payment instructions for your selected method will be in your email</span>
                                    </li>
                                    <li>
                                        <strong>White-glove delivery arranged</strong>
                                        <span>We'll coordinate safe transport of your puppy directly to your door</span>
                                    </li>
                                </ol>
                            </div>

                            {/* Order items */}
                            <div className="success-card">
                                <h2 className="success-card-title">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" /><line x1="3" y1="6" x2="21" y2="6" /></svg>
                                    Order Details
                                </h2>

                                <div className="success-order-meta">
                                    <div className="success-meta-row">
                                        <span>Order ID{orders.length > 1 ? 's' : ''}</span>
                                        <strong>{orders.map(o => `#${o.id}`).join(', ')}</strong>
                                    </div>
                                    <div className="success-meta-row">
                                        <span>Puppies Reserved</span>
                                        <strong>{totalPuppies} {totalPuppies === 1 ? 'puppy' : 'puppies'}</strong>
                                    </div>
                                    <div className="success-meta-row">
                                        <span>Total Value</span>
                                        <strong className="success-total">${totalAmount.toLocaleString()}</strong>
                                    </div>
                                    <div className="success-meta-row">
                                        <span>Payment Method</span>
                                        <strong>{paymentIcon} {paymentMethod.replace(/_/g, ' ')}</strong>
                                    </div>
                                    {firstOrder.buyer_address && (
                                        <div className="success-meta-row">
                                            <span>Delivery Address</span>
                                            <strong>{firstOrder.buyer_address}</strong>
                                        </div>
                                    )}
                                </div>

                                {/* Puppy list */}
                                <div className="success-puppy-list">
                                    {orders.map((order) => (
                                        <div key={order.id} className="success-puppy-row">
                                            <div className="success-puppy-info">
                                                <span className="success-puppy-name">{order.puppy_name}</span>
                                                <span className="success-puppy-id">Order #{order.id}</span>
                                            </div>
                                            {order.puppy_price && (
                                                <span className="success-puppy-price">${Number(order.puppy_price).toLocaleString()}</span>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Sidebar actions */}
                        <aside className="success-sidebar">
                            <div className="success-sidebar-card">
                                <h3>Manage Your Reservation</h3>
                                <div className="success-sidebar-actions">
                                    <Link href="/orders" className="btn-solid success-sidebar-btn">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" /><line x1="3" y1="6" x2="21" y2="6" /></svg>
                                        View My Orders
                                    </Link>
                                    <Link href="/account" className="btn-secondary success-sidebar-btn">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><circle cx="12" cy="8" r="4" /><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" /></svg>
                                        Visit Dashboard
                                    </Link>
                                    <Link href="/puppies" className="success-back-link">
                                        ← Back to Available Puppies
                                    </Link>
                                </div>

                                <div className="success-contact-note">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="M2 7l10 7 10-7" /></svg>
                                    <span>Questions? Email us at <a href="mailto:info@richescorsos.com">info@richescorsos.com</a></span>
                                </div>
                            </div>

                            <div className="success-trust-card">
                                <div className="success-trust-item">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="var(--green-dark)" strokeWidth="2" strokeLinecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                                    <span>2-Year Genetic Health Guarantee</span>
                                </div>
                                <div className="success-trust-item">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="var(--green-dark)" strokeWidth="2" strokeLinecap="round"><polyline points="22 4 12 14.01 9 11.01" /></svg>
                                    <span>AKC Registered Champion Bloodlines</span>
                                </div>
                                <div className="success-trust-item">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="var(--green-dark)" strokeWidth="2" strokeLinecap="round"><rect x="1" y="3" width="15" height="13" /><polygon points="16 8 20 8 23 11 23 16 16 16 16 8" /><circle cx="5.5" cy="18.5" r="2.5" /><circle cx="18.5" cy="18.5" r="2.5" /></svg>
                                    <span>White-Glove Nationwide Delivery</span>
                                </div>
                            </div>
                        </aside>

                    </div>
                </div>
            </div>
        </SiteLayout>
    );
}
