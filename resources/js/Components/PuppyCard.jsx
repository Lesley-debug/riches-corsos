import { Link } from '@inertiajs/react';

export default function PuppyCard({ puppy }) {
  const cover = puppy.images?.[0]?.path;
  const ageWeeks = puppy.age_in_weeks ?? null;

  return (
    <div className="puppy-card">
      <div className={`puppy-photo ${cover ? '' : 'placeholder'}`}>
        {cover && <img src={`/storage/${cover}`} alt={puppy.name} />}
        {puppy.status !== 'available' && <span className="puppy-badge">{puppy.status}</span>}
        {puppy.status === 'available' && <span className="puppy-badge">Available</span>}
      </div>
      <div className="puppy-info">
        <div className="puppy-meta">
          {puppy.sex} {ageWeeks ? `· ${ageWeeks} weeks` : ''}
        </div>
        <h3>{puppy.name}</h3>
        <div className="puppy-row">
          <span className="puppy-price">${Number(puppy.price).toLocaleString()}</span>
          <Link href={`/puppies/${puppy.slug}`} className="puppy-link">
            View Details
          </Link>
        </div>
      </div>
    </div>
  );
}
