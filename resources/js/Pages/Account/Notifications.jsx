import { Head, Link, router, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import AccountNav from '@/Components/AccountNav';

function NotificationIcon({ type }) {
  if (type === 'welcome') {
    return (
      <div className="notif-icon notif-icon--welcome">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
        </svg>
      </div>
    );
  }
  if (type === 'order_placed') {
    return (
      <div className="notif-icon notif-icon--order">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
      </div>
    );
  }
  if (type === 'new_puppy') {
    return (
      <div className="notif-icon notif-icon--puppy">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
          <circle cx="12" cy="14" r="5" />
          <circle cx="8" cy="8" r="2.5" />
          <circle cx="16" cy="8" r="2.5" />
          <circle cx="4" cy="12" r="2" />
          <circle cx="20" cy="12" r="2" />
        </svg>
      </div>
    );
  }
  if (type === 'wishlist_added') {
    return (
      <div className="notif-icon notif-icon--wishlist">
        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
        </svg>
      </div>
    );
  }
  return (
    <div className="notif-icon notif-icon--general">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
      </svg>
    </div>
  );
}

export default function Notifications({ notifications, unreadCount = 0 }) {
  const items = notifications?.data ?? notifications ?? [];

  const markAllRead = () => {
    router.post('/account/notifications/read-all', {}, { preserveScroll: true });
  };

  const markRead = (id) => {
    router.post(`/account/notifications/${id}/read`, {}, { preserveScroll: true });
  };

  return (
    <SiteLayout>
      <Head title="Notification Center — Riches Corsos" />

      <div className="account-grid">
        <AccountNav />

        <div className="account-content">
          <div className="account-section-header">
            <div>
              <h1 className="account-title">Notification Center</h1>
              <p className="account-subtitle">
                Updates on your puppy reservations, litter alerts, and saved favorites.
              </p>
            </div>

            {unreadCount > 0 && (
              <button onClick={markAllRead} className="btn-secondary notif-mark-all-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="16" height="16">
                  <polyline points="20 6 9 17 4 12" />
                </svg>
                Mark all as read ({unreadCount})
              </button>
            )}
          </div>

          {items.length > 0 ? (
            <div className="notif-list">
              {items.map((item) => (
                <div key={item.id} className={`notif-card ${!item.read ? 'notif-card--unread' : ''}`}>
                  <NotificationIcon type={item.type} />

                  <div className="notif-body">
                    <div className="notif-header">
                      <h3 className="notif-item-title">{item.title}</h3>
                      <span className="notif-time">{item.created_at}</span>
                    </div>
                    <p className="notif-message">{item.message}</p>

                    <div className="notif-actions">
                      {item.action_url && (
                        <Link href={item.action_url} className="notif-action-link">
                          View details &rarr;
                        </Link>
                      )}
                      {!item.read && (
                        <button
                          onClick={() => markRead(item.id)}
                          className="notif-read-btn"
                          title="Mark as read"
                        >
                          Mark as read
                        </button>
                      )}
                    </div>
                  </div>

                  {!item.read && <div className="notif-unread-dot" title="Unread" />}
                </div>
              ))}
            </div>
          ) : (
            <div className="account-empty-state">
              <div className="empty-icon-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" width="32" height="32">
                  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                  <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
              </div>
              <h3>You're all caught up!</h3>
              <p>No notifications right now. Activity and puppy alerts will appear here.</p>
              <Link href="/puppies" className="btn-solid" style={{ marginTop: 16 }}>
                Explore Available Puppies
              </Link>
            </div>
          )}
        </div>
      </div>
    </SiteLayout>
  );
}
