import { useState } from 'react';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

// ── Helpers ──────────────────────────────────────────────────────────────────
function ageLabel(weeks) {
  if (weeks < 4)  return `${weeks} week${weeks !== 1 ? 's' : ''} old`;
  if (weeks < 52) {
    const m = Math.round(weeks / 4.33);
    return `${m} month${m !== 1 ? 's' : ''} old`;
  }
  const y = Math.floor(weeks / 52);
  return `${y} year${y !== 1 ? 's' : ''} old`;
}

function statusColor(s) {
  return { available: '#2d7a4f', reserved: '#b45309', pending: '#b45309', sold: '#6b7280', not_available: '#6b7280' }[s] ?? '#6b7280';
}

function Check() {
  return (
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style={{ flexShrink: 0 }}>
      <circle cx="8" cy="8" r="8" fill="#2d7a4f" opacity=".12" />
      <path d="M4.5 8l2.5 2.5 4.5-5" stroke="#2d7a4f" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function DocIcon() {
  return (
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
      <polyline points="14 2 14 8 20 8" />
    </svg>
  );
}

// ── Main Component ────────────────────────────────────────────────────────────
export default function PuppyShow({ puppy, sire, dam, isWishlisted: initialWishlisted }) {
  const { props } = usePage();
  const flashSuccess = props.flash?.success;
  const user = props.auth?.user;

  const [activeImg, setActiveImg]   = useState(0);
  const [showForm, setShowForm]     = useState(false);
  const [wishlisted, setWishlisted] = useState(initialWishlisted ?? false);

  const toggleWishlist = () => {
    if (!user) { router.visit('/login'); return; }
    setWishlisted(w => !w);
    router.post('/wishlist/toggle', { puppy_id: puppy.id }, { preserveScroll: true });
  };

  const { data, setData, post, processing, errors, reset } = useForm({
    puppy_id:     puppy.id,
    buyer_name:   '',
    buyer_email:  '',
    buyer_phone:  '',
    buyer_address:'',
    notes:        '',
  });

  const submit = e => {
    e.preventDefault();
    post('/orders', { onSuccess: () => reset() });
  };

  const images     = puppy.images?.length ? puppy.images : [];
  const isAvailable = puppy.status === 'available';
  const age        = ageLabel(puppy.age_in_weeks);

  const docTypeLabel = {
    health_certificate:   'Health Certificate',
    vaccination_record:   'Vaccination Record',
    pedigree_certificate: 'Pedigree Certificate',
    registration_papers:  'Registration Papers',
    genetic_test:         'Genetic Test Results',
    hip_elbow_results:    'Hip/Elbow Results',
    purchase_agreement:   'Purchase Agreement',
    health_guarantee:     'Health Guarantee',
    other:                'Document',
  };

  const vaccinationLabel = {
    not_started:        'Not Started',
    first_vaccination:  'First Vaccination',
    second_vaccination: 'Second Vaccination',
    fully_vaccinated:   'Fully Vaccinated',
  };

  return (
    <SiteLayout>
      <Head title={`${puppy.name} — Riches Corsos`} />

      <div className="home-page">

        {/* ── HERO SECTION: Gallery + Info Card ── */}
        <section className="puppy-show-hero section">
          {/* Gallery */}
          <div className="puppy-gallery">
            <div className="puppy-gallery-main">
              {images[activeImg] ? (
                <img
                  key={activeImg}
                  src={`/storage/${images[activeImg].path}`}
                  alt={images[activeImg].alt_text || puppy.name}
                  className="puppy-gallery-main-img"
                />
              ) : (
                <div className="puppy-gallery-placeholder" />
              )}
            </div>
            {images.length > 1 && (
              <div className="puppy-gallery-thumbs">
                {images.map((img, i) => (
                  <button
                    key={img.id}
                    onClick={() => setActiveImg(i)}
                    className={`puppy-thumb-btn${i === activeImg ? ' active' : ''}`}
                  >
                    <img src={`/storage/${img.path}`} alt={img.alt_text || puppy.name} />
                  </button>
                ))}
              </div>
            )}
            {/* Videos row */}
            {puppy.videos?.length > 0 && (
              <div className="puppy-videos-row">
                {puppy.videos.map(v => (
                  <a
                    key={v.id}
                    href={v.video_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="puppy-video-thumb"
                  >
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
                    <span>{v.title || 'Watch Video'}</span>
                  </a>
                ))}
              </div>
            )}
          </div>

          {/* Info Card */}
          <div className="puppy-info-card card-3d">
            {/* Status + wishlist */}
            <div style={{ display:'flex', alignItems:'center', justifyContent:'space-between', marginBottom:12 }}>
              <span
                className="puppy-badge"
                style={{ background: statusColor(puppy.status)+'1a', color: statusColor(puppy.status), textTransform:'capitalize' }}
              >
                {puppy.status.replace('_',' ')}
              </span>
              <button onClick={toggleWishlist} aria-label="Toggle wishlist" className="wishlist-btn">
                <svg viewBox="0 0 24 24" width="22" height="22">
                  <path
                    d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z"
                    stroke="var(--green-dark)" strokeWidth="1.6"
                    fill={wishlisted ? 'var(--green-dark)' : 'none'}
                  />
                </svg>
              </button>
            </div>

            <h1 className="puppy-show-name">{puppy.name}</h1>
            <p className="puppy-show-meta">{puppy.breed} · {puppy.sex === 'male' ? '♂ Male' : '♀ Female'} · {age}</p>
            {puppy.color && <p className="puppy-show-meta" style={{ marginTop:2 }}>Color: {puppy.color}{puppy.markings ? ` · ${puppy.markings}` : ''}</p>}

            <p className="puppy-price" style={{ fontSize:30, margin:'16px 0' }}>${Number(puppy.price).toLocaleString()}</p>

            {/* Badges */}
            {puppy.badges?.length > 0 && (
              <div className="puppy-badges-row">
                {puppy.badges.map(b => (
                  <span key={b} className="puppy-badge-chip">{b.replace(/_/g,' ')}</span>
                ))}
              </div>
            )}

            {puppy.description && (
              <p className="puppy-show-desc">{puppy.description}</p>
            )}

            {flashSuccess && <div className="form-success" style={{ marginBottom:16 }}>{flashSuccess}</div>}

            {isAvailable ? (
              !showForm ? (
                <div style={{ display:'flex', flexDirection:'column', gap:10 }}>
                  <button className="btn-solid" onClick={() => setShowForm(true)} style={{ width:'100%' }}>
                    Reserve This Puppy
                  </button>
                  <a href="/contact" className="btn-outline" style={{ textAlign:'center', display:'block' }}>
                    Inquire About {puppy.name}
                  </a>
                </div>
              ) : (
                <form onSubmit={submit} className="reserve-form">
                  <p className="form-note" style={{ marginBottom:16 }}>
                    No payment is taken here — we'll contact you to confirm next steps.
                  </p>
                  {[
                    { label:'Full name', key:'buyer_name', type:'text', required:true },
                    { label:'Email',     key:'buyer_email', type:'email', required:true },
                    { label:'Phone',     key:'buyer_phone', type:'text', required:true },
                  ].map(f => (
                    <div className="form-field" key={f.key}>
                      <label>{f.label}</label>
                      <input type={f.type} value={data[f.key]} onChange={e => setData(f.key, e.target.value)} required={f.required} />
                      {errors[f.key] && <div className="form-error">{errors[f.key]}</div>}
                    </div>
                  ))}
                  <div className="form-field">
                    <label>Address (optional)</label>
                    <textarea rows={2} value={data.buyer_address} onChange={e => setData('buyer_address', e.target.value)} />
                  </div>
                  <div className="form-field">
                    <label>Anything else? (optional)</label>
                    <textarea rows={3} value={data.notes} onChange={e => setData('notes', e.target.value)} />
                  </div>
                  {errors.puppy_id && <div className="form-error" style={{ marginBottom:12 }}>{errors.puppy_id}</div>}
                  <button type="submit" className="btn-solid" disabled={processing} style={{ width:'100%' }}>
                    {processing ? 'Sending…' : 'Send Reservation Request'}
                  </button>
                  <button type="button" onClick={() => setShowForm(false)} className="btn-outline" style={{ width:'100%', marginTop:8 }}>
                    Cancel
                  </button>
                </form>
              )
            ) : (
              <p style={{ color:'var(--stone)', lineHeight:1.6 }}>
                This puppy is currently <strong>{puppy.status.replace('_',' ')}</strong> — check back, or view other{' '}
                <a href="/puppies" style={{ color:'var(--green-dark)', fontWeight:600 }}>available puppies</a>.
              </p>
            )}
          </div>
        </section>

        {/* ── DETAILS GRID ── */}
        <section className="section puppy-details-section">
          <div className="sec-title-plaque">Puppy Details</div>
          <div className="puppy-details-grid">
            {[
              { label:'Breed',                value: puppy.breed },
              { label:'Date of Birth',        value: puppy.date_of_birth ? new Date(puppy.date_of_birth).toLocaleDateString('en-GB',{day:'numeric',month:'long',year:'numeric'}) : null },
              { label:'Age',                  value: age },
              { label:'Sex',                  value: puppy.sex === 'male' ? 'Male' : 'Female' },
              { label:'Color',                value: puppy.color },
              { label:'Markings',             value: puppy.markings },
              { label:'Current Weight',       value: puppy.weight },
              { label:'Expected Adult Weight',value: puppy.expected_adult_weight },
              { label:'Energy Level',         value: puppy.energy_level ? puppy.energy_level.charAt(0).toUpperCase()+puppy.energy_level.slice(1) : null },
            ].filter(d => d.value).map(d => (
              <div key={d.label} className="puppy-detail-item">
                <span className="puppy-detail-label">{d.label}</span>
                <span className="puppy-detail-value">{d.value}</span>
              </div>
            ))}
          </div>
        </section>

        {/* ── TEMPERAMENT ── */}
        {(puppy.temperament?.length > 0 || puppy.compatibility?.length > 0 || puppy.training_progress?.length > 0) && (
          <section className="section puppy-traits-section">
            <div className="sec-title-plaque">Personality & Temperament</div>
            {puppy.temperament?.length > 0 && (
              <div style={{ marginBottom:20 }}>
                <p className="puppy-traits-label">Temperament</p>
                <div className="puppy-chips">
                  {puppy.temperament.map(t => <span key={t} className="puppy-chip">{t}</span>)}
                </div>
              </div>
            )}
            {puppy.compatibility?.length > 0 && (
              <div style={{ marginBottom:20 }}>
                <p className="puppy-traits-label">Family Compatibility</p>
                <div className="puppy-chips">
                  {puppy.compatibility.map(c => <span key={c} className="puppy-chip puppy-chip--green">{c}</span>)}
                </div>
              </div>
            )}
            {puppy.training_progress?.length > 0 && (
              <div>
                <p className="puppy-traits-label">Training Progress</p>
                <div className="puppy-chips">
                  {puppy.training_progress.map(t => <span key={t} className="puppy-chip puppy-chip--outline">{t}</span>)}
                </div>
              </div>
            )}
          </section>
        )}

        {/* ── HEALTH & CARE ── */}
        {(puppy.vet_checked || puppy.vaccination_status || puppy.dewormed || puppy.microchipped || puppy.health_guarantee) && (
          <section className="section puppy-health-section">
            <div className="sec-title-plaque">Health & Care</div>
            <div className="puppy-health-grid">
              {puppy.vet_checked && (
                <div className="puppy-health-item"><Check /><span>Vet Checked{puppy.vet_check_date ? ` — ${new Date(puppy.vet_check_date).toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'})}` : ''}</span></div>
              )}
              {puppy.vaccination_status && (
                <div className="puppy-health-item"><Check /><span>Vaccinations: {vaccinationLabel[puppy.vaccination_status] ?? puppy.vaccination_status}</span></div>
              )}
              {puppy.dewormed && (
                <div className="puppy-health-item"><Check /><span>Dewormed</span></div>
              )}
              {puppy.microchipped && (
                <div className="puppy-health-item"><Check /><span>Microchipped</span></div>
              )}
              {puppy.health_guarantee && (
                <div className="puppy-health-item"><Check /><span>Health Guarantee Included</span></div>
              )}
            </div>
            {puppy.vaccination_notes && (
              <p className="puppy-health-notes">{puppy.vaccination_notes}</p>
            )}
            {puppy.health_guarantee && puppy.health_guarantee_notes && (
              <p className="puppy-health-notes">{puppy.health_guarantee_notes}</p>
            )}
          </section>
        )}

        {/* ── MEET THE PARENTS ── */}
        {(sire || dam) && (
          <section className="section puppy-parents-section">
            <div className="sec-title-plaque">Meet The Parents</div>
            <div className="puppy-parents-grid">
              {[{ parent: sire, role:'Father (Sire)' }, { parent: dam, role:'Mother (Dam)' }]
                .filter(p => p.parent)
                .map(({ parent, role }) => {
                  const img = parent.images?.[0];
                  return (
                    <div key={parent.id} className="puppy-parent-card card-3d">
                      <div className="puppy-parent-img-wrap">
                        {img ? (
                          <img src={`/storage/${img.path}`} alt={parent.name} className="puppy-parent-img" />
                        ) : (
                          <div className="puppy-parent-img-placeholder" />
                        )}
                      </div>
                      <div className="puppy-parent-body">
                        <span className="puppy-parent-role">{role}</span>
                        <h3 className="puppy-parent-name">{parent.name}</h3>
                        {parent.color && <p className="puppy-parent-meta">{parent.color}{parent.weight ? ` · ${parent.weight}` : ''}</p>}
                        {parent.description && <p className="puppy-parent-desc">{parent.description}</p>}
                        {parent.titles?.length > 0 && (
                          <div className="puppy-chips" style={{ marginTop:10 }}>
                            {parent.titles.map(t => <span key={t} className="puppy-chip puppy-chip--gold">{t}</span>)}
                          </div>
                        )}
                        {parent.health_tests && Object.keys(parent.health_tests).length > 0 && (
                          <div className="puppy-parent-health">
                            {Object.entries(parent.health_tests).map(([test, result]) => (
                              <div key={test} className="puppy-health-item" style={{ fontSize:13 }}>
                                <Check /><span>{test}: {result}</span>
                              </div>
                            ))}
                          </div>
                        )}
                      </div>
                    </div>
                  );
                })}
            </div>
          </section>
        )}

        {/* ── DOCUMENTS ── */}
        {puppy.documents?.length > 0 && (
          <section className="section puppy-docs-section">
            <div className="sec-title-plaque">Documents & Health Records</div>
            <div className="puppy-docs-grid">
              {puppy.documents.map(doc => (
                <a
                  key={doc.id}
                  href={`/storage/${doc.file_path}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="puppy-doc-card card-3d"
                >
                  <span className="puppy-doc-icon"><DocIcon /></span>
                  <div>
                    <p className="puppy-doc-type">{docTypeLabel[doc.document_type] ?? 'Document'}</p>
                    <p className="puppy-doc-title">{doc.title}</p>
                    {doc.description && <p className="puppy-doc-desc">{doc.description}</p>}
                  </div>
                </a>
              ))}
            </div>
          </section>
        )}

        {/* ── BOTTOM CTA ── */}
        <section className="section" style={{ textAlign:'center', padding:'48px 24px' }}>
          <p style={{ color:'var(--stone)', marginBottom:20 }}>
            Questions about {puppy.name}? We're happy to help.
          </p>
          <div style={{ display:'flex', gap:12, justifyContent:'center', flexWrap:'wrap' }}>
            <a href="/contact" className="btn-solid">Contact Us</a>
            <a href="/puppies" className="btn-outline">View All Puppies</a>
          </div>
        </section>

      </div>
    </SiteLayout>
  );
}
