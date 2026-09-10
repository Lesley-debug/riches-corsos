import { Head } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import AccountNav from '@/Components/AccountNav';
import PuppyCard from '@/Components/PuppyCard';

export default function Wishlist({ puppies = [] }) {
  return (
    <SiteLayout>
      <Head title="My Wishlist — Riches Corsos" />

      <div className="account-grid">
        <AccountNav />

        <div>
          <h2 style={{ fontSize: 26, marginBottom: 24 }}>My Wishlist</h2>

          {puppies.length > 0 ? (
            <div className="puppy-grid" style={{ gridTemplateColumns: 'repeat(2, 1fr)' }}>
              {puppies.map((puppy) => (
                <PuppyCard key={puppy.id} puppy={puppy} wishlisted />
              ))}
            </div>
          ) : (
            <p style={{ color: 'var(--stone)' }}>
              Nothing saved yet — tap the heart icon on any puppy's page to add it here.
            </p>
          )}
        </div>
      </div>
    </SiteLayout>
  );
}
