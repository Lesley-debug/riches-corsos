import { useState } from 'react';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function PuppyShow({ puppy, isWishlisted: initialWishlisted }) {
  const { props } = usePage();
  const flashSuccess = props.flash?.success;
  const user = props.auth?.user;
  const [showForm, setShowForm] = useState(false);
  const [wishlisted, setWishlisted] = useState(initialWishlisted ?? false);

  const toggleWishlist = () => {
    if (!user) {
      router.visit('/login');
      return;
    }
    setWishlisted((w) => !w);
    router.post('/wishlist/toggle', { puppy_id: puppy.id }, { preserveScroll: true });
  };

  const { data, setData, post, processing, errors, reset } = useForm({
    puppy_id: puppy.id,
    buyer_name: '',
    buyer_email: '',
    buyer_phone: '',
    buyer_address: '',
    notes: '',
  });

  const submit = (e) => {
    e.preventDefault();
    post('/orders', {
      onSuccess: () => reset(),
    });
  };

  const images = puppy.images?.length ? puppy.images : [null];
  const isAvailable = puppy.status === 'available';

  return (
    <SiteLayout>
      <Head title={`${puppy.name} — Riches Corsos`} />

      <section className="section" style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 56 }}>
        <div>
          <div
            className={`hero-image ${images[0] ? '' : 'placeholder'}`}
            data-label="Main puppy photo"
            style={{ marginBottom: 14 }}
          >
            {images[0] && <img src={`/storage/${images[0].path}`} alt={puppy.name} />}
          </div>
          {images.length > 1 && (
            <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' }}>
              {images.slice(1).map((img) => (
                <div
                  key={img.id}
                  style={{ width: 84, height: 84, borderRadius: 8, overflow: 'hidden', background: 'var(--green-tint)' }}
                >
                  <img src={`/storage/${img.path}`} alt={puppy.name} />
                </div>
              ))}
            </div>
          )}
        </div>

        <div>
          <span className="puppy-badge" style={{ background: 'var(--green-tint)', color: 'var(--green-dark)' }}>
            {puppy.status}
          </span>
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
            <h1 style={{ fontSize: 36, margin: '14px 0 8px' }}>{puppy.name}</h1>
            <button
              onClick={toggleWishlist}
              aria-label="Toggle wishlist"
              style={{ width: 40, height: 40, display: 'flex', alignItems: 'center', justifyContent: 'center' }}
            >
              <svg viewBox="0 0 24 24" width="24" height="24">
                <path
                  d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z"
                  stroke="var(--green-dark)"
                  strokeWidth="1.6"
                  fill={wishlisted ? 'var(--green-dark)' : 'none'}
                />
              </svg>
            </button>
          </div>
          <p style={{ color: 'var(--stone)', marginBottom: 18, textTransform: 'capitalize' }}>
            {puppy.breed} · {puppy.sex} · {puppy.age_in_weeks} weeks old
          </p>
          <p className="puppy-price" style={{ fontSize: 28, marginBottom: 24 }}>
            ${Number(puppy.price).toLocaleString()}
          </p>
          <p style={{ color: 'var(--ink)', lineHeight: 1.7, marginBottom: 32 }}>{puppy.description}</p>

          {flashSuccess && <div className="form-success">{flashSuccess}</div>}

          {isAvailable ? (
            !showForm ? (
              <button className="btn-solid" onClick={() => setShowForm(true)}>
                Reserve This Puppy
              </button>
            ) : (
              <form onSubmit={submit} style={{ border: '1px solid var(--line)', borderRadius: 14, padding: 24 }}>
                <p className="form-note" style={{ marginBottom: 18 }}>
                  This sends a reservation request — no payment is taken here. We'll contact you directly to confirm
                  next steps.
                </p>

                <div className="form-field">
                  <label>Full name</label>
                  <input value={data.buyer_name} onChange={(e) => setData('buyer_name', e.target.value)} required />
                  {errors.buyer_name && <div className="form-error">{errors.buyer_name}</div>}
                </div>

                <div className="form-field">
                  <label>Email</label>
                  <input
                    type="email"
                    value={data.buyer_email}
                    onChange={(e) => setData('buyer_email', e.target.value)}
                    required
                  />
                  {errors.buyer_email && <div className="form-error">{errors.buyer_email}</div>}
                </div>

                <div className="form-field">
                  <label>Phone</label>
                  <input value={data.buyer_phone} onChange={(e) => setData('buyer_phone', e.target.value)} required />
                  {errors.buyer_phone && <div className="form-error">{errors.buyer_phone}</div>}
                </div>

                <div className="form-field">
                  <label>Address (optional)</label>
                  <textarea rows={2} value={data.buyer_address} onChange={(e) => setData('buyer_address', e.target.value)} />
                </div>

                <div className="form-field">
                  <label>Anything else we should know? (optional)</label>
                  <textarea rows={3} value={data.notes} onChange={(e) => setData('notes', e.target.value)} />
                </div>

                {errors.puppy_id && <div className="form-error" style={{ marginBottom: 12 }}>{errors.puppy_id}</div>}

                <button type="submit" className="btn-solid" disabled={processing} style={{ width: '100%' }}>
                  {processing ? 'Sending…' : 'Send Reservation Request'}
                </button>
              </form>
            )
          ) : (
            <p style={{ color: 'var(--stone)' }}>
              This puppy is currently {puppy.status} — check back, or view other{' '}
              <a href="/puppies" style={{ color: 'var(--green-dark)', fontWeight: 600 }}>
                available puppies
              </a>
              .
            </p>
          )}
        </div>
      </section>
    </SiteLayout>
  );
}
