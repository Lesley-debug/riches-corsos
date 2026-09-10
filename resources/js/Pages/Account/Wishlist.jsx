import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import AccountNav from '@/Components/AccountNav';
import PuppyCard from '@/Components/PuppyCard';

export default function Wishlist({ puppies = [] }) {
  return (
    <SiteLayout>
      <Head title="My Saved Puppies & Wishlist — Riches Corsos" />

      <div className="account-grid">
        <AccountNav />

        <div className="account-content">
          <div className="account-section-header">
            <div>
              <h1 className="account-title">My Saved Favorites</h1>
              <p className="account-subtitle">
                Cane Corso puppies you've bookmarked. Keep track of availability or proceed with a reservation request.
              </p>
            </div>
            {puppies.length > 0 && (
              <Link href="/puppies" className="btn-secondary" style={{ fontSize: 13, padding: '9px 16px' }}>
                Browse More Puppies
              </Link>
            )}
          </div>

          {puppies.length > 0 ? (
            <div className="puppy-grid" style={{ gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: 24 }}>
              {puppies.map((puppy) => (
                <PuppyCard key={puppy.id} puppy={puppy} wishlisted />
              ))}
            </div>
          ) : (
            <div className="account-empty-state">
              <div className="empty-icon-circle" style={{ background: '#fce7f3', color: '#db2777' }}>
                <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
              </div>
              <h3>Your wishlist is empty</h3>
              <p>
                Whenever you find a Cane Corso puppy you love, tap the heart icon on their card or profile to save them here for quick access.
              </p>
              <Link href="/puppies" className="btn-solid" style={{ marginTop: 18 }}>
                Explore Available Litters
              </Link>
            </div>
          )}
        </div>
      </div>
    </SiteLayout>
  );
}
