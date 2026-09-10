import { useState } from 'react';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PuppyCard from '@/Components/PuppyCard';

const VACCINATION_LABELS = {
    not_started: 'Not Started',
    first_vaccination: 'First Vaccination',
    second_vaccination: 'Second Vaccination',
    fully_vaccinated: 'Fully Vaccinated',
};

function ageLabel(weeks) {
    if (weeks == null) {
        return null;
    }

    if (weeks < 4) {
        return `${weeks} week${weeks === 1 ? '' : 's'} old`;
    }

    if (weeks < 52) {
        const months = Math.round(weeks / 4.33);
        return `${months} month${months === 1 ? '' : 's'} old`;
    }

    const years = Math.floor(weeks / 52);
    return `${years} year${years === 1 ? '' : 's'} old`;
}

function dateLabel(value) {
    if (!value) {
        return null;
    }

    const date = new Date(String(value).includes('T') ? value : `${value}T00:00:00`);

    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

function priceLabel(price) {
    return price ? `$${Number(price).toLocaleString()}` : 'Contact for price';
}

function labelize(value) {
    return String(value ?? '').replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
}

function cleanList(value) {
    return Array.isArray(value) ? value.filter(Boolean) : [];
}

function CheckIcon() {
    return (
        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" aria-hidden="true">
            <circle cx="8.5" cy="8.5" r="8.5" fill="#2F6B4F" opacity=".12" />
            <path d="M5 8.6l2.1 2.1L12 5.8" stroke="#234F3A" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
}

function DocumentIcon() {
    return (
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z" />
            <path d="M14 2v5h5" />
            <path d="M8 13h8M8 17h5" />
        </svg>
    );
}

function HeartIcon({ filled }) {
    return (
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
            <path
                d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z"
                stroke="currentColor"
                strokeWidth="1.7"
                fill={filled ? 'currentColor' : 'none'}
            />
        </svg>
    );
}

function ShareIcon() {
    return (
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <circle cx="18" cy="5" r="3" />
            <circle cx="6" cy="12" r="3" />
            <circle cx="18" cy="19" r="3" />
            <path d="M8.6 10.5l6.8-4M8.6 13.5l6.8 4" />
        </svg>
    );
}

function CopyIcon() {
    return (
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <rect x="9" y="9" width="12" height="12" rx="2" />
            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
        </svg>
    );
}

function PlayIcon() {
    return (
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M8 5v14l11-7z" />
        </svg>
    );
}

export default function PuppyShow({ puppy, sire, dam, isWishlisted: initialWishlisted, related = [] }) {
    const { props } = usePage();
    const flashSuccess = props.flash?.success;
    const user = props.auth?.user;

    const [activeImage, setActiveImage] = useState(0);
    const [showForm, setShowForm] = useState(false);
    const [wishlisted, setWishlisted] = useState(initialWishlisted ?? false);
    const [copied, setCopied] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        puppy_id: puppy.id,
        buyer_name: '',
        buyer_email: '',
        buyer_phone: '',
        buyer_address: '',
        notes: '',
    });

    const images = cleanList(puppy.images);
    const videos = cleanList(puppy.videos);
    const documents = cleanList(puppy.documents).filter((document) => document.file_path);
    const age = ageLabel(puppy.age_in_weeks);
    const isAvailable = puppy.status === 'available';
    const status = labelize(puppy.status);
    const shareUrl = typeof window !== 'undefined' ? window.location.href : '';
    const shareText = `${puppy.name} - ${puppy.breed} puppy at Riches Corsos`;
    const pageTitle = puppy.seo_title || `${puppy.name} | ${puppy.breed} Puppy | Riches Corsos`;
    const metaDescription = puppy.meta_description || puppy.description?.slice(0, 155) || `Meet ${puppy.name}, a ${puppy.breed} puppy at Riches Corsos.`;
    const ogImage = images[0] ? `/storage/${images[0].path}` : '/images/logo.png';

    const detailItems = [
        { label: 'Breed', value: puppy.breed },
        { label: 'Sex', value: puppy.sex ? labelize(puppy.sex) : null },
        { label: 'Age', value: age },
        { label: 'Date Of Birth', value: dateLabel(puppy.date_of_birth) },
        { label: 'Color', value: puppy.color },
        { label: 'Markings', value: puppy.markings },
        { label: 'Current Weight', value: puppy.weight },
        { label: 'Expected Adult Weight', value: puppy.expected_adult_weight },
        { label: 'Energy Level', value: puppy.energy_level ? labelize(puppy.energy_level) : null },
        { label: 'Available From', value: dateLabel(puppy.available_date) || (isAvailable ? 'Available now' : null) },
        { label: 'Deposit', value: puppy.deposit_required ? priceLabel(puppy.deposit_amount) : null },
    ].filter((item) => item.value);

    const overviewItems = [
        { label: 'Sex', value: puppy.sex ? labelize(puppy.sex) : null },
        { label: 'Age', value: age },
        { label: 'Color', value: puppy.color },
        { label: 'Ready', value: dateLabel(puppy.available_date) || (isAvailable ? 'Now' : null) },
    ].filter((item) => item.value);

    const healthItems = [
        puppy.vet_checked ? `Vet checked${puppy.vet_check_date ? ` on ${dateLabel(puppy.vet_check_date)}` : ''}` : null,
        puppy.vaccination_status ? `Vaccinations: ${VACCINATION_LABELS[puppy.vaccination_status] ?? labelize(puppy.vaccination_status)}` : null,
        puppy.dewormed ? 'Dewormed' : null,
        puppy.microchipped ? 'Microchipped' : null,
        puppy.health_guarantee ? 'Health guarantee included' : null,
    ].filter(Boolean);

    const traitGroups = [
        { label: 'Temperament', items: cleanList(puppy.temperament) },
        { label: 'Family Compatibility', items: cleanList(puppy.compatibility) },
        { label: 'Training Progress', items: cleanList(puppy.training_progress) },
    ].filter((group) => group.items.length > 0);

    const parents = [
        { parent: sire, role: 'Father', label: 'Sire' },
        { parent: dam, role: 'Mother', label: 'Dam' },
    ].filter(({ parent }) => parent);

    const toggleWishlist = () => {
        if (!user) {
            router.visit('/login');
            return;
        }

        setWishlisted((current) => !current);
        router.post('/wishlist/toggle', { puppy_id: puppy.id }, {
            preserveScroll: true,
            onError: () => setWishlisted((current) => !current),
        });
    };

    const copyLink = () => {
        if (typeof navigator === 'undefined') {
            return;
        }

        navigator.clipboard?.writeText(shareUrl).then(() => {
            setCopied(true);
            window.setTimeout(() => setCopied(false), 2000);
        });
    };

    const handleShare = async () => {
        if (typeof navigator !== 'undefined' && navigator.share) {
            try {
                await navigator.share({ title: puppy.name, text: shareText, url: shareUrl });
            } catch (_) {
                return;
            }
        } else {
            copyLink();
        }
    };

    const submit = (event) => {
        event.preventDefault();
        post('/orders', {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setShowForm(false);
            },
        });
    };

    return (
        <SiteLayout>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={metaDescription} />
                <meta property="og:title" content={pageTitle} />
                <meta property="og:description" content={metaDescription} />
                <meta property="og:image" content={ogImage} />
                <meta property="og:url" content={shareUrl} />
                <link rel="canonical" href={shareUrl} />
            </Head>

            <div className="puppy-show-page">
                <nav className="puppy-breadcrumb">
                    <div className="puppy-breadcrumb-inner">
                        <Link href="/">Home</Link>
                        <span>/</span>
                        <Link href="/puppies">Available Puppies</Link>
                        <span>/</span>
                        <span>{puppy.name}</span>
                    </div>
                </nav>

                <section className="puppy-show-shell">
                    <div className="puppy-show-hero">
                        <div className="puppy-gallery-panel">
                            <div className="puppy-gallery-main">
                                {images[activeImage] ? (
                                    <img
                                        key={images[activeImage].id ?? activeImage}
                                        src={`/storage/${images[activeImage].path}`}
                                        alt={images[activeImage].alt_text || puppy.name}
                                        className="puppy-gallery-main-img"
                                    />
                                ) : (
                                    <div className="puppy-gallery-placeholder">
                                        <span>{puppy.name}</span>
                                    </div>
                                )}
                            </div>

                            {images.length > 1 && (
                                <div className="puppy-gallery-thumbs" aria-label={`${puppy.name} photo gallery`}>
                                    {images.map((image, index) => (
                                        <button
                                            type="button"
                                            key={image.id ?? image.path}
                                            onClick={() => setActiveImage(index)}
                                            className={`puppy-thumb-btn${index === activeImage ? ' active' : ''}`}
                                            aria-label={`View photo ${index + 1}`}
                                        >
                                            <img src={`/storage/${image.path}`} alt={image.alt_text || puppy.name} />
                                        </button>
                                    ))}
                                </div>
                            )}

                            {videos.length > 0 && (
                                <div className="puppy-video-list">
                                    {videos.map((video) => (
                                        <a key={video.id ?? video.video_url} href={video.video_url} target="_blank" rel="noopener noreferrer" className="puppy-video-link">
                                            <span><PlayIcon /></span>
                                            {video.title || 'Watch Video'}
                                        </a>
                                    ))}
                                </div>
                            )}
                        </div>

                        <aside className="puppy-summary-panel">
                            <div className="puppy-summary-top">
                                <span className={`puppy-status-pill puppy-status-pill--${puppy.status}`}>
                                    {status}
                                </span>
                                <div className="puppy-summary-tools">
                                    <button type="button" className="puppy-icon-btn" onClick={toggleWishlist} aria-label={wishlisted ? 'Remove from wishlist' : 'Add to wishlist'}>
                                        <HeartIcon filled={wishlisted} />
                                    </button>
                                    <button type="button" className="puppy-icon-btn" onClick={handleShare} aria-label="Share puppy">
                                        <ShareIcon />
                                    </button>
                                    <button type="button" className="puppy-icon-btn" onClick={copyLink} aria-label="Copy puppy link">
                                        <CopyIcon />
                                    </button>
                                </div>
                            </div>

                            <div>
                                <p className="puppy-summary-kicker">{puppy.breed}</p>
                                <h1 className="puppy-show-name">{puppy.name}</h1>
                                <p className="puppy-summary-price">{priceLabel(puppy.price)}</p>
                            </div>

                            {copied && <p className="puppy-copy-note">Link copied</p>}

                            {puppy.badges?.length > 0 && (
                                <div className="puppy-badges-row">
                                    {puppy.badges.map((badge) => (
                                        <span key={badge} className="puppy-badge-chip">{badge.replace(/_/g, ' ')}</span>
                                    ))}
                                </div>
                            )}

                            {overviewItems.length > 0 && (
                                <div className="puppy-overview-grid">
                                    {overviewItems.map((item) => (
                                        <div key={item.label}>
                                            <span>{item.label}</span>
                                            <strong>{item.value}</strong>
                                        </div>
                                    ))}
                                </div>
                            )}

                            {flashSuccess && <div className="form-success">{flashSuccess}</div>}

                            {isAvailable ? (
                                showForm ? (
                                    <form onSubmit={submit} className="reserve-form">
                                        <p className="form-note">No payment is taken here. We will contact you to confirm the next step.</p>
                                        <div className="form-field">
                                            <label htmlFor="buyer_name">Full Name</label>
                                            <input id="buyer_name" type="text" value={data.buyer_name} onChange={(event) => setData('buyer_name', event.target.value)} required />
                                            {errors.buyer_name && <div className="form-error">{errors.buyer_name}</div>}
                                        </div>
                                        <div className="form-field">
                                            <label htmlFor="buyer_email">Email</label>
                                            <input id="buyer_email" type="email" value={data.buyer_email} onChange={(event) => setData('buyer_email', event.target.value)} required />
                                            {errors.buyer_email && <div className="form-error">{errors.buyer_email}</div>}
                                        </div>
                                        <div className="form-field">
                                            <label htmlFor="buyer_phone">Phone</label>
                                            <input id="buyer_phone" type="text" value={data.buyer_phone} onChange={(event) => setData('buyer_phone', event.target.value)} required />
                                            {errors.buyer_phone && <div className="form-error">{errors.buyer_phone}</div>}
                                        </div>
                                        <div className="form-field">
                                            <label htmlFor="buyer_address">Address</label>
                                            <textarea id="buyer_address" rows={2} value={data.buyer_address} onChange={(event) => setData('buyer_address', event.target.value)} />
                                            {errors.buyer_address && <div className="form-error">{errors.buyer_address}</div>}
                                        </div>
                                        <div className="form-field">
                                            <label htmlFor="notes">Notes</label>
                                            <textarea id="notes" rows={3} value={data.notes} onChange={(event) => setData('notes', event.target.value)} />
                                            {errors.notes && <div className="form-error">{errors.notes}</div>}
                                        </div>
                                        {errors.puppy_id && <div className="form-error">{errors.puppy_id}</div>}
                                        <div className="reserve-form-actions">
                                            <button type="submit" className="btn-solid" disabled={processing}>
                                                {processing ? 'Sending...' : 'Send Reservation Request'}
                                            </button>
                                            <button type="button" className="btn-outline" onClick={() => setShowForm(false)}>
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                ) : (
                                    <div className="puppy-summary-actions">
                                        <button type="button" className="btn-solid" onClick={() => setShowForm(true)}>
                                            Reserve This Puppy
                                        </button>
                                        <Link href="/contact" className="btn-outline">Ask About {puppy.name}</Link>
                                    </div>
                                )
                            ) : (
                                <div className="puppy-unavailable-note">
                                    <p>{puppy.name} is currently {labelize(puppy.status)}.</p>
                                    <Link href="/puppies">View available puppies</Link>
                                </div>
                            )}

                            <div className="puppy-summary-assurance">
                                {healthItems.slice(0, 3).map((item) => (
                                    <span key={item}><CheckIcon />{item}</span>
                                ))}
                                {documents.length > 0 && <span><DocumentIcon />{documents.length} public {documents.length === 1 ? 'document' : 'documents'}</span>}
                            </div>
                        </aside>
                    </div>

                    <div className="puppy-profile-panel">
                        <div className="puppy-profile-heading">
                            <div>
                                <p className="shop-eyebrow">Complete Profile</p>
                                <h2>{puppy.name}'s Puppy Information</h2>
                            </div>
                            <Link href="/puppies" className="puppy-profile-back">Back To Puppies</Link>
                        </div>

                        <div className="puppy-profile-layout">
                            <div className="puppy-profile-main">
                                {puppy.description && (
                                    <section className="puppy-profile-block">
                                        <h3>About {puppy.name}</h3>
                                        <p className="puppy-profile-description">{puppy.description}</p>
                                    </section>
                                )}

                                {detailItems.length > 0 && (
                                    <section className="puppy-profile-block">
                                        <h3>Details</h3>
                                        <div className="puppy-details-grid">
                                            {detailItems.map((item) => (
                                                <div key={item.label} className="puppy-detail-item">
                                                    <span>{item.label}</span>
                                                    <strong>{item.value}</strong>
                                                </div>
                                            ))}
                                        </div>
                                    </section>
                                )}

                                {traitGroups.length > 0 && (
                                    <section className="puppy-profile-block">
                                        <h3>Personality, Family Fit, And Training</h3>
                                        <div className="puppy-trait-groups">
                                            {traitGroups.map((group) => (
                                                <div key={group.label}>
                                                    <p>{group.label}</p>
                                                    <div className="puppy-chips">
                                                        {group.items.map((item) => (
                                                            <span key={item} className="puppy-chip">{item}</span>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </section>
                                )}
                            </div>

                            <aside className="puppy-profile-side">
                                {healthItems.length > 0 && (
                                    <section className="puppy-profile-block puppy-side-block">
                                        <h3>Health And Care</h3>
                                        <div className="puppy-health-list">
                                            {healthItems.map((item) => (
                                                <div key={item}>
                                                    <CheckIcon />
                                                    <span>{item}</span>
                                                </div>
                                            ))}
                                        </div>
                                        {puppy.vaccination_notes && <p className="puppy-care-note">{puppy.vaccination_notes}</p>}
                                        {puppy.health_guarantee_notes && <p className="puppy-care-note">{puppy.health_guarantee_notes}</p>}
                                    </section>
                                )}

                                {documents.length > 0 && (
                                    <section className="puppy-profile-block puppy-side-block">
                                        <h3>Documents</h3>
                                        <div className="puppy-doc-list">
                                            {documents.map((document) => (
                                                <a key={document.id} href={`/storage/${document.file_path}`} target="_blank" rel="noopener noreferrer" className="puppy-doc-card">
                                                    <span className="puppy-doc-icon"><DocumentIcon /></span>
                                                    <span>
                                                        <strong>{document.title || document.type_label || labelize(document.document_type)}</strong>
                                                        <small>{document.type_label || labelize(document.document_type)}</small>
                                                        {document.document_number && <small>{document.document_number}</small>}
                                                        {(document.issued_at || document.generated_at || document.uploaded_at) && (
                                                            <small>{dateLabel(document.issued_at || document.generated_at || document.uploaded_at)}</small>
                                                        )}
                                                    </span>
                                                </a>
                                            ))}
                                        </div>
                                    </section>
                                )}
                            </aside>
                        </div>
                    </div>
                </section>

                {parents.length > 0 && (
                    <section className="puppy-support-section">
                        <div className="puppy-section-heading">
                            <p className="shop-eyebrow">Pedigree</p>
                            <h2>Meet The Parents</h2>
                        </div>
                        <div className="puppy-parents-grid">
                            {parents.map(({ parent, role, label }) => {
                                const image = parent.images?.[0];
                                const parentVideos = cleanList(parent.videos);

                                return (
                                    <article key={parent.id} className="puppy-parent-card">
                                        <div className="puppy-parent-img-wrap">
                                            {image ? (
                                                <img src={`/storage/${image.path}`} alt={image.alt_text || parent.name} className="puppy-parent-img" />
                                            ) : (
                                                <div className="puppy-parent-img-placeholder" />
                                            )}
                                        </div>
                                        <div className="puppy-parent-body">
                                            <p className="puppy-parent-role">{role} ({label})</p>
                                            <h3>{parent.name}</h3>
                                            <p className="puppy-parent-meta">
                                                {[parent.breed, parent.color, parent.weight, parent.height].filter(Boolean).join(' - ')}
                                            </p>
                                            {parent.description && <p className="puppy-parent-desc">{parent.description}</p>}
                                            {(parent.registration_organization || parent.registration_number) && (
                                                <p className="puppy-parent-registration">
                                                    {[parent.registration_organization, parent.registration_number].filter(Boolean).join(' ')}
                                                </p>
                                            )}
                                            {parent.titles?.length > 0 && (
                                                <div className="puppy-chips">
                                                    {parent.titles.map((title) => (
                                                        <span key={title} className="puppy-chip puppy-chip--gold">{title}</span>
                                                    ))}
                                                </div>
                                            )}
                                            {parent.health_tests && Object.keys(parent.health_tests).length > 0 && (
                                                <div className="puppy-parent-health">
                                                    {Object.entries(parent.health_tests).map(([test, result]) => (
                                                        <div key={test}>
                                                            <CheckIcon />
                                                            <span>{labelize(test)}: {result}</span>
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                            {parent.health_notes && <p className="puppy-care-note">{parent.health_notes}</p>}
                                            {parentVideos.length > 0 && (
                                                <div className="puppy-video-list puppy-video-list--compact">
                                                    {parentVideos.map((video) => (
                                                        <a key={video.id ?? video.video_url} href={video.video_url} target="_blank" rel="noopener noreferrer" className="puppy-video-link">
                                                            <span><PlayIcon /></span>
                                                            {video.title || `${parent.name} Video`}
                                                        </a>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                    </section>
                )}

                {related.length > 0 && (
                    <section className="puppy-support-section puppy-related-section">
                        <div className="puppy-section-heading puppy-section-heading--row">
                            <div>
                                <p className="shop-eyebrow">You May Also Like</p>
                                <h2>Related Puppies</h2>
                            </div>
                            <Link href="/puppies" className="btn-outline">View All Puppies</Link>
                        </div>
                        <div className="puppy-grid shop-puppy-grid">
                            {related.map((relatedPuppy) => (
                                <PuppyCard key={relatedPuppy.id} puppy={relatedPuppy} />
                            ))}
                        </div>
                    </section>
                )}
            </div>
        </SiteLayout>
    );
}
