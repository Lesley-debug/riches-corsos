import { Link, usePage } from '@inertiajs/react';

export default function AccountNav() {
  const { url, props } = usePage();
  const unreadCount = props.unreadNotificationsCount ?? 0;

  const links = [
    { label: 'Dashboard', href: '/account' },
    { label: 'My Orders', href: '/orders' },
    { label: 'Wishlist', href: '/wishlist' },
    { label: 'Notifications', href: '/account/notifications', badge: unreadCount },
  ];

  return (
    <nav className="account-nav">
      {links.map((link) => (
        <Link
          key={link.href}
          href={link.href}
          className={`account-nav-item ${url === link.href ? 'active' : ''}`}
          style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}
        >
          <span>{link.label}</span>
          {Boolean(link.badge) && link.badge > 0 && (
            <span className="account-nav-badge">{link.badge}</span>
          )}
        </Link>
      ))}
      <Link href="/logout" method="post" as="button" style={{ textAlign: 'left', color: '#B3452F', marginTop: 12 }}>
        Log Out
      </Link>
    </nav>
  );
}
