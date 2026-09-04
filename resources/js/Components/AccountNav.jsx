import { Link, usePage } from '@inertiajs/react';

export default function AccountNav() {
  const { url } = usePage();

  const links = [
    { label: 'Dashboard', href: '/account' },
    { label: 'My Orders', href: '/orders' },
    { label: 'Wishlist', href: '/wishlist' },
  ];

  return (
    <nav className="account-nav">
      {links.map((link) => (
        <Link key={link.href} href={link.href} className={url === link.href ? 'active' : ''}>
          {link.label}
        </Link>
      ))}
      <Link href="/logout" method="post" as="button" style={{ textAlign: 'left', color: '#B3452F' }}>
        Log Out
      </Link>
    </nav>
  );
}
