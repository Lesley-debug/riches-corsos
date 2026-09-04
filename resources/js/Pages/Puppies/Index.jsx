import { Head } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PuppyCard from '@/Components/PuppyCard';

export default function PuppiesIndex({ puppies = [] }) {
  return (
    <SiteLayout>
      <Head title="Available Puppies — Riches Corsos" />

      <section className="section" style={{ paddingBottom: 40 }}>
        <div className="section-head">
          <h2>Available Puppies</h2>
          <p>Every listing below is health-tested, vaccinated, and raised in-home.</p>
        </div>
      </section>

      <section className="section" style={{ paddingTop: 0 }}>
        <div className="puppy-grid">
          {puppies.length > 0 ? (
            puppies.map((puppy) => <PuppyCard key={puppy.id} puppy={puppy} />)
          ) : (
            <p style={{ color: 'var(--stone)', gridColumn: '1 / -1', textAlign: 'center' }}>
              No puppies available right now — check back soon, or contact us to be notified about the next litter.
            </p>
          )}
        </div>
      </section>
    </SiteLayout>
  );
}
