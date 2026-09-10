import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import AccountNav from '@/Components/AccountNav';

export default function Orders({ orders = [] }) {
  return (
    <SiteLayout>
      <Head title="My Orders & Reservations — Riches Corsos" />

      <div className="account-grid">
        <AccountNav />

        <div className="account-content">
          <div className="account-section-header">
            <div>
              <h1 className="account-title">My Reservations & Orders</h1>
              <p className="account-subtitle">
                View your complete reservation history and adoption status.
              </p>
            </div>
            <Link href="/puppies" className="btn-solid" style={{ fontSize: 13, padding: '10px 18px' }}>
              Browse Available Puppies
            </Link>
          </div>

          {orders.length > 0 ? (
            <div className="orders-detailed-list">
              {orders.map((order) => {
                const puppy = order.puppy;
                const image = puppy?.images?.[0]?.path ? `/storage/${puppy.images[0].path}` : '/images/puppy-placeholder.jpg';
                const status = order.status || 'pending';

                return (
                  <div className="order-detailed-card" key={order.id}>
                    <div className="order-detailed-header">
                      <div className="order-id-block">
                        <span className="order-id-label">Reservation</span>
                        <strong>#{order.id}</strong>
                      </div>
                      <div className="order-header-meta">
                        <span className="order-date">
                          Placed on {new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                        </span>
                        <span className={`order-status-pill status-${status}`}>
                          {status.replace('_', ' ')}
                        </span>
                      </div>
                    </div>

                    <div className="order-detailed-body">
                      <div className="order-puppy-thumb">
                        <img src={image} alt={puppy?.name || 'Puppy'} />
                      </div>

                      <div className="order-puppy-info">
                        <div className="order-puppy-name-row">
                          <h3>{puppy?.name || 'Puppy'}</h3>
                          {puppy?.price && (
                            <span className="order-price">${Number(puppy.price).toLocaleString()}</span>
                          )}
                        </div>

                        <p className="order-puppy-specs">
                          {puppy?.breed || 'Cane Corso'} &bull; {puppy?.sex ? puppy.sex.toUpperCase() : ''} {puppy?.color ? `\u2022 ${puppy.color}` : ''}
                        </p>

                        <div className="order-customer-details">
                          <div><strong>Reserved for:</strong> {order.buyer_name} ({order.buyer_phone})</div>
                          {order.buyer_address && <div><strong>Delivery to:</strong> {order.buyer_address}</div>}
                          {order.notes && <div className="order-notes"><strong>Notes:</strong> {order.notes}</div>}
                        </div>
                      </div>
                    </div>

                    <div className="order-detailed-footer">
                      <span className="order-help-text">
                        Questions about this reservation? <Link href="/contact" style={{ color: 'var(--green-dark)', textDecoration: 'underline' }}>Contact Breeder</Link>
                      </span>
                      {puppy?.slug && (
                        <Link href={`/puppies/${puppy.slug}`} className="btn-secondary" style={{ fontSize: 13, padding: '8px 16px' }}>
                          View Puppy Page &rarr;
                        </Link>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>
          ) : (
            <div className="account-empty-state">
              <div className="empty-icon-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="32" height="32">
                  <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                  <line x1="3" y1="6" x2="21" y2="6" />
                  <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
              </div>
              <h3>No reservations placed yet</h3>
              <p>When you request a puppy from our available litter, you can monitor its progress here.</p>
              <Link href="/puppies" className="btn-solid" style={{ marginTop: 16 }}>
                Find Your Cane Corso
              </Link>
            </div>
          )}
        </div>
      </div>
    </SiteLayout>
  );
}
