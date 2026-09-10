import { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
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

function CartIcon() {
    return (
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <path d="M3 4h2l1 12h13l2-9H7" />
            <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
            <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
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
    const user = props.auth?.user;
    const wishlistPuppyIds = props.wishlistPuppyIds ?? [];
    const cartPuppyIds = props.cartPuppyIds ?? [];

    const isWishlisted = wishlistPuppyIds.includes(puppy.id) || (initialWishlisted ?? false);
    const isInCart = cartPuppyIds.includes(puppy.id);

    const [activeImage, setActiveImage] = useState(0);
    const [shareOpen, setShareOpen] = useState(false);
    const [copied, setCopied] = useState(false);
    const [toastMessage, setToastMessage] = useState('');
    const [selectedParentIndex, setSelectedParentIndex] = useState(0);

    const showToast = (message) => {
        setToastMessage(message);
        window.setTimeout(() => setToastMessage(''), 2800);
    };

    const images = cleanList(puppy.images);
    const videos = cleanList(puppy.videos);
    const documents = cleanList(puppy.documents).filter((document) => document.file_path);
    const age = ageLabel(puppy.age_in_weeks);
    const isAvailable = puppy.status === 'available';
    const status = labelize(puppy.status);
    const shareUrl = typeof window !== 'undefined' ? window.location.href : '';
    const shareText = `Meet ${puppy.name} - ${puppy.breed} puppy at Riches Corsos!`;
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

        const willAdd = !isWishlisted;

        router.post('/wishlist/toggle', { puppy_id: puppy.id }, {
            preserveScroll: true,
            onSuccess: () => {
                showToast(willAdd ? `You like ${puppy.name}! Added to wishlist.` : `Removed ${puppy.name} from your wishlist.`);
            },
        });
    };

    const handleCart = () => {
        if (!user) {
            router.visit('/login');
            return;
        }

        if (isInCart) {
            showToast(`${puppy.name} is already in your cart.`);
            return;
        }

        router.post('/cart', { puppy_id: puppy.id }, {
            preserveScroll: true,
            onSuccess: () => {
                showToast(`${puppy.name} added to your cart.`);
            },
        });
    };

    const copyLink = () => {
        if (typeof navigator === 'undefined') {
            return;
        }

        navigator.clipboard?.writeText(shareUrl).then(() => {
            setCopied(true);
            showToast('Link copied to clipboard!');
            window.setTimeout(() => setCopied(false), 2400);
        });
    };

    const sharePlatforms = [
        {
            name: 'Facebook',
            color: '#1877F2',
            icon: (
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                </svg>
            ),
            action: () => {
                window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank', 'width=600,height=450');
            },
        },
        {
            name: 'X',
            color: '#000000',
            icon: (
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                </svg>
            ),
            action: () => {
                window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`, '_blank', 'width=600,height=450');
            },
        },
        {
            name: 'Snapchat',
            color: '#FFFC00',
            textColor: '#000000',
            icon: (
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M12.002 2C8.75 2 6.545 4.148 6.545 7.15c0 .736.147 1.838.257 2.427.073.392-.122.564-.343.686-.392.22-.98.392-1.397.662-.319.22-.392.515-.171.784.343.417 1.103.735 1.544.784.343.049.49.27.417.613-.196.931-.882 2.377-2.451 2.745-.343.074-.466.319-.441.564.049.466.833.686 1.348.809.564.123 1.054.196 1.495.662.392.417.27 1.029.074 1.544-.098.245-.049.441.171.539.735.343 2.157.613 4.902.613s4.167-.27 4.902-.613c.22-.098.27-.294.172-.539-.196-.515-.319-1.127.073-1.544.441-.466.931-.539 1.495-.662.515-.123 1.299-.343 1.348-.809.025-.245-.098-.49-.441-.564-1.569-.368-2.255-1.814-2.451-2.745-.073-.343.074-.564.417-.613.441-.049 1.201-.367 1.544-.784.22-.269.147-.564-.172-.784-.417-.27-1.005-.442-1.397-.662-.221-.122-.416-.294-.343-.686.11-.589.257-1.691.257-2.427C17.455 4.148 15.25 2 12.002 2z" />
                </svg>
            ),
            action: () => {
                window.open(`https://www.snapchat.com/share?url=${encodeURIComponent(shareUrl)}`, '_blank', 'width=600,height=500');
            },
        },
        {
            name: 'Instagram',
            color: '#E4405F',
            icon: (
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.8">
                    <rect x="2" y="2" width="20" height="20" rx="5" />
                    <circle cx="12" cy="12" r="4" />
                    <circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none" />
                </svg>
            ),
            action: () => {
                copyLink();
                showToast('Link copied! Open Instagram to share in stories or DM.');
            },
        },
        {
            name: 'TikTok',
            color: '#010101',
            icon: (
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.76a4.85 4.85 0 01-1.01-.07z" />
                </svg>
            ),
            action: () => {
                copyLink();
                showToast('Link copied! Open TikTok to share.');
            },
        },
    ];

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
                {toastMessage && (
                    <div className="puppy-toast-banner" role="status">
                        <span>{toastMessage}</span>
                    </div>
                )}

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
                                    <button
                                        type="button"
                                        className={`puppy-icon-btn ${isWishlisted ? 'puppy-icon-btn--active' : ''}`}
                                        onClick={toggleWishlist}
                                        aria-label={isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'}
                                        title={isWishlisted ? 'Liked' : 'Add to wishlist'}
                                    >
                                        <HeartIcon filled={isWishlisted} />
                                    </button>
                                    <button
                                        type="button"
                                        className="puppy-icon-btn"
                                        onClick={() => setShareOpen(true)}
                                        aria-label="Share puppy"
                                        title="Share puppy"
                                    >
                                        <ShareIcon />
                                    </button>
                                    <button
                                        type="button"
                                        className="puppy-icon-btn"
                                        onClick={copyLink}
                                        aria-label="Copy puppy link"
                                        title="Copy link"
                                    >
                                        <CopyIcon />
                                    </button>
                                </div>
                            </div>

                            <div>
                                <p className="puppy-summary-kicker">{puppy.breed}</p>
                                <h1 className="puppy-show-name">{puppy.name}</h1>
                                <p className="puppy-summary-price">{priceLabel(puppy.price)}</p>
                            </div>

                            {copied && <p className="puppy-copy-note">Link copied to clipboard</p>}

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

                            {isAvailable ? (
                                <div className="puppy-summary-actions">
                                    <button
                                        type="button"
                                        className={`btn-solid ${isInCart ? 'btn-solid--in-cart' : ''}`}
                                        onClick={handleCart}
                                    >
                                        <CartIcon />
                                        <span>{isInCart ? 'In Your Cart' : 'Add to Cart'}</span>
                                    </button>
                                    <Link href={`/contact?puppy=${encodeURIComponent(puppy.name)}`} className="btn-outline">
                                        Ask About {puppy.name}
                                    </Link>
                                </div>
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

                {parents.length > 0 && (() => {
                    const activeParent = parents[selectedParentIndex] || parents[0];
                    const parent = activeParent.parent;
                    const parentImages = cleanList(parent.images);
                    const parentVideos = cleanList(parent.videos);
                    const primaryImage = parentImages[0];

                    return (
                        <section className="puppy-support-section puppy-pedigree-section">
                            <div className="puppy-section-heading puppy-section-heading--row">
                                <div>
                                    <p className="shop-eyebrow">Pedigree</p>
                                    <h2>Meet The Parents</h2>
                                </div>
                                {parents.length > 1 && (
                                    <div className="puppy-parent-tabs" role="tablist" aria-label="Select Parent">
                                        {parents.map((item, index) => {
                                            const isActive = index === selectedParentIndex;
                                            return (
                                                <button
                                                    key={item.parent.id}
                                                    type="button"
                                                    role="tab"
                                                    aria-selected={isActive}
                                                    className={`puppy-parent-tab-btn ${isActive ? 'puppy-parent-tab-btn--active' : ''}`}
                                                    onClick={() => setSelectedParentIndex(index)}
                                                >
                                                    <span className="puppy-parent-tab-pill">{item.label}</span>
                                                    <span>{item.role}: {item.parent.name}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>

                            <article className="puppy-parent-spotlight">
                                <div className="puppy-parent-spotlight-media">
                                    <div className="puppy-parent-spotlight-img-wrap">
                                        {primaryImage ? (
                                            <img
                                                src={`/storage/${primaryImage.path}`}
                                                alt={primaryImage.alt_text || parent.name}
                                                className="puppy-parent-spotlight-img"
                                            />
                                        ) : (
                                            <div className="puppy-parent-spotlight-placeholder">
                                                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" strokeWidth="1.4" opacity="0.35">
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path d="M12 7v5l3 3" />
                                                </svg>
                                                <span>{parent.name}</span>
                                                <small>{activeParent.role} ({activeParent.label})</small>
                                            </div>
                                        )}
                                    </div>

                                    {parentVideos.length > 0 && (
                                        <div className="puppy-video-list puppy-video-list--compact">
                                            {parentVideos.map((video) => (
                                                <a
                                                    key={video.id ?? video.video_url}
                                                    href={video.video_url}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="puppy-video-link"
                                                >
                                                    <span><PlayIcon /></span>
                                                    {video.title || `${parent.name} Video`}
                                                </a>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                <div className="puppy-parent-spotlight-body">
                                    <div className="puppy-parent-spotlight-header">
                                        <span className="puppy-parent-role-badge">
                                            {activeParent.role} ({activeParent.label})
                                        </span>
                                        <h3>{parent.name}</h3>
                                        {(parent.registration_organization || parent.registration_number) && (
                                            <p className="puppy-parent-reg-meta">
                                                {[parent.registration_organization, parent.registration_number ? `Reg #${parent.registration_number}` : null].filter(Boolean).join(' • ')}
                                            </p>
                                        )}
                                    </div>

                                    <div className="puppy-parent-specs-grid">
                                        {parent.breed && (
                                            <div className="puppy-parent-spec-item">
                                                <span>Breed</span>
                                                <strong>{parent.breed}</strong>
                                            </div>
                                        )}
                                        {parent.color && (
                                            <div className="puppy-parent-spec-item">
                                                <span>Color</span>
                                                <strong>{parent.color}</strong>
                                            </div>
                                        )}
                                        {parent.weight && (
                                            <div className="puppy-parent-spec-item">
                                                <span>Weight</span>
                                                <strong>{parent.weight}</strong>
                                            </div>
                                        )}
                                        {parent.height && (
                                            <div className="puppy-parent-spec-item">
                                                <span>Height</span>
                                                <strong>{parent.height}</strong>
                                            </div>
                                        )}
                                    </div>

                                    {parent.titles?.length > 0 && (
                                        <div className="puppy-parent-titles">
                                            <p className="puppy-parent-subheading">Titles & Honors</p>
                                            <div className="puppy-chips">
                                                {parent.titles.map((title) => (
                                                    <span key={title} className="puppy-chip puppy-chip--gold">{title}</span>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {parent.description && (
                                        <div className="puppy-parent-bio">
                                            <p className="puppy-parent-subheading">About {parent.name}</p>
                                            <p className="puppy-parent-desc">{parent.description}</p>
                                        </div>
                                    )}

                                    {parent.health_tests && Object.keys(parent.health_tests).length > 0 && (
                                        <div className="puppy-parent-health-section">
                                            <p className="puppy-parent-subheading">Health Clearances</p>
                                            <div className="puppy-parent-health-grid">
                                                {Object.entries(parent.health_tests).map(([test, result]) => (
                                                    <div key={test} className="puppy-parent-health-chip">
                                                        <CheckIcon />
                                                        <span><strong>{labelize(test)}:</strong> {result}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {parent.health_notes && (
                                        <p className="puppy-care-note">{parent.health_notes}</p>
                                    )}
                                </div>
                            </article>
                        </section>
                    );
                })()}

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

            {/* Social Share Modal */}
            {shareOpen && (
                <div className="share-modal-backdrop" onClick={() => setShareOpen(false)}>
                    <div className="share-modal" onClick={(e) => e.stopPropagation()} role="dialog" aria-modal="true" aria-labelledby="share-title">
                        <div className="share-modal-header">
                            <div>
                                <p className="share-modal-kicker">Share puppy</p>
                                <h3 id="share-title">Share {puppy.name}</h3>
                            </div>
                            <button
                                type="button"
                                className="share-modal-close"
                                onClick={() => setShareOpen(false)}
                                aria-label="Close share dialog"
                            >
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round">
                                    <path d="M18 6L6 18M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div className="share-modal-body">
                            <p className="share-modal-desc">Share this puppy with friends and family on your favorite platforms:</p>

                            <div className="share-social-grid">
                                {sharePlatforms.map((platform) => (
                                    <button
                                        type="button"
                                        key={platform.name}
                                        className="share-social-btn"
                                        onClick={platform.action}
                                        style={{
                                            '--btn-brand': platform.color,
                                            '--btn-brand-text': platform.textColor || '#ffffff',
                                        }}
                                    >
                                        <span className="share-social-icon">{platform.icon}</span>
                                        <span className="share-social-name">{platform.name}</span>
                                    </button>
                                ))}
                            </div>

                            <div className="share-copy-section">
                                <label htmlFor="share-copy-input">Or copy direct link</label>
                                <div className="share-copy-input-row">
                                    <input
                                        id="share-copy-input"
                                        type="text"
                                        readOnly
                                        value={shareUrl}
                                        onClick={(e) => e.target.select()}
                                    />
                                    <button
                                        type="button"
                                        className={`btn-solid ${copied ? 'btn-solid--copied' : ''}`}
                                        onClick={copyLink}
                                    >
                                        {copied ? 'Copied!' : 'Copy Link'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </SiteLayout>
    );
}
