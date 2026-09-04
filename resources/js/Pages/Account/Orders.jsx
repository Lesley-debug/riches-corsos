import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import AccountNav from '@/Components/AccountNav';

export default function Orders({ orders = [] }) {
  return (
    <SiteLayout>
      <Head title="My Orders — Riches Corsos" />

      <div className="account-grid">
        <AccountNav />

        <div>
          <h2 style={{ fontSize: 26, marginBottom: 24 }}>My Orders</h2>

          {orders.length > 0 ? (
            orders.map((order) => (
              <div className="order-row" key={order.id}>
                <div>
                  <strong>{order.puppy?.name}</strong>
                  <div style={{ fontSize: 13, color: 'var(--stone)' }}>
                    Requested {new Date(order.created_at).toLocaleDateString()}
                  </div>
                </div>
                <span className="order-status">{order.status.replace('_', ' ')}</span>
              </div>
            ))
          ) : (
            <p style={{ color: 'var(--stone)' }}>
              You haven't reserved a puppy yet —{' '}
              <Link href="/puppies" style={{ color: 'var(--green-dark)', fontWeight: 600 }}>
                browse available puppies
              </Link>
              .
            </p>
          )}
        </div>
      </div>
    </SiteLayout>
  );
}
