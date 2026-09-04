import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import AccountNav from '@/Components/AccountNav';

export default function Dashboard({ recentOrders = [], wishlistCount = 0 }) {
  const { props } = usePage();
  const user = props.auth?.user;

  return (
    <SiteLayout>
      <Head title="My Account — Riches Corsos" />

      <div className="account-grid">
        <AccountNav />

        <div>
          <h2 style={{ fontSize: 26, marginBottom: 6 }}>Hi, {user?.name?.split(' ')[0]}</h2>
          <p style={{ color: 'var(--stone)', marginBottom: 32 }}>{user?.email}</p>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 40 }}>
            <div className="account-stat">
              <div className="num">{recentOrders.length}</div>
              <div className="label">Recent reservation requests</div>
            </div>
            <div className="account-stat">
              <div className="num">{wishlistCount}</div>
              <div className="label">Puppies wishlisted</div>
            </div>
          </div>

          <h3 style={{ fontSize: 18, marginBottom: 16 }}>Recent orders</h3>
          {recentOrders.length > 0 ? (
            recentOrders.map((order) => (
              <div className="order-row" key={order.id}>
                <div>
                  <strong>{order.puppy?.name}</strong>
                  <div style={{ fontSize: 13, color: 'var(--stone)' }}>
                    {new Date(order.created_at).toLocaleDateString()}
                  </div>
                </div>
                <span className="order-status">{order.status.replace('_', ' ')}</span>
              </div>
            ))
          ) : (
            <p style={{ color: 'var(--stone)' }}>
              No reservation requests yet —{' '}
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
