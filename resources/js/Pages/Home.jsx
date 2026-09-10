import { Head, Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import SiteLayout from '@/Layouts/SiteLayout';
import PuppyCard from '@/Components/PuppyCard';
import CaneCorsoQuiz from '@/Components/CaneCorsoQuiz';

const STORY_THUMBS = ['1', '2', '3', '4', '5', '6', '7', '8'];

const TESTIMONIALS = [
    {
        author: 'Zoey Wilson',
        location: 'Dallas, TX',
        dog: 'Titan (Male • 18 mos)',
        text: 'Incredibly flexible with scheduling and so accommodating. The whole process was smooth from start to finish. You could tell Titan was raised with so much love and early handling.',
        rating: 5,
    },
    {
        author: 'Leilya Thao',
        location: 'Atlanta, GA',
        dog: 'Bella (Female • 2 yrs)',
        text: 'We were greeted warmly the moment we arrived. The owners are knowledgeable, caring, and genuinely passionate about their puppies. Bella has the sweetest, most steady temperament.',
        rating: 5,
    },
    {
        author: 'Bobbie Platt',
        location: 'Nashville, TN',
        dog: 'Diesel (Male • 1 yr)',
        text: 'Picking up our puppy was such a joy. Every dog there is treated like family. Our Corso has been an absolute blessing to our home and is wonderful with our young children.',
        rating: 5,
    },
    {
        author: 'Jason Poplin',
        location: 'Charlotte, NC',
        dog: 'Nero (Male • 3 yrs)',
        text: 'Worth every mile of the drive. The home was spotless and the puppies were healthy, confident, and well-socialized. We will definitely be back for our next Corso!',
        rating: 5,
    },
];

const MILESTONES = [
    {
        id: '8w',
        stage: '8 Weeks',
        title: 'Homecoming & Bonding',
        weight: '15 – 22 lbs',
        focus: 'Crate comfort, household sounds, gentle handling, and potty foundation.',
        desc: 'Puppies leave our home with age-appropriate core vaccinations, veterinary clearance, microchipped, and pre-socialized to everyday household noises.',
    },
    {
        id: '6m',
        stage: '6 Months',
        title: 'Structured Growth',
        weight: '60 – 75 lbs',
        focus: 'Boundary consistency, loose-leash walks, calm public manners, and puppy socialization classes.',
        desc: 'Rapid physical development begins here. Structured guidance and positive reinforcement ensure your Corso matures into an obedient, confident companion.',
    },
    {
        id: 'adult',
        stage: '18–24 Months',
        title: 'Majestic Maturity',
        weight: '85 – 115+ lbs',
        focus: 'Steadfast loyalty, watchful protection without reactivity, deep family devotion.',
        desc: 'Full bone density and muscle mass develop. A calm, noble guardian at home and a gentle giant at your feet every evening.',
    },
];

function DiamondDivider() {
    return (
        <div className="diamond-divider">
            <span className="diamond-line" />
            <span className="diamond-icon">◆</span>
            <span className="diamond-icon">◆</span>
            <span className="diamond-icon">◆</span>
            <span className="diamond-line" />
        </div>
    );
}

function SectionDivider() {
    return (
        <div className="section-divider">
            <DiamondDivider />
        </div>
    );
}

function SectionTitle({ number, title, sub }) {
    return (
        <div className="sec-title-wrap">
            <div className="sec-title-plaque">
                {number && <span className="sec-title-num">{number}</span>}
                <h2 className="sec-title-text">{title}</h2>
            </div>
            {sub && <p className="sec-title-sub">{sub}</p>}
        </div>
    );
}

function Stars({ count = 5 }) {
    return (
        <div className="star-row">
            {Array.from({ length: count }).map((_, i) => (
                <svg key={i} viewBox="0 0 24 24" fill="#F5A623" width="16" height="16">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
            ))}
        </div>
    );
}

export default function Home({
    featuredPuppies = [],
    recentPosts = [],
    homecomingPhotos = [],
    heroImage,
}) {
    // 43-second sequential story reel state
    const [activeThumb, setActiveThumb] = useState(0);
    const [isStoryPaused, setIsStoryPaused] = useState(false);
    const thumbsTrackRef = useRef(null);

    // Interactive Puppy filter
    const [puppyFilter, setPuppyFilter] = useState('all');

    // Milestones tab
    const [activeMilestone, setActiveMilestone] = useState('8w');

    // 43 seconds total across 8 thumbnails = 5375ms per slide
    const SLIDE_DURATION_MS = 5375;

    useEffect(() => {
        if (isStoryPaused) return;

        const interval = setInterval(() => {
            setActiveThumb((prev) => (prev + 1) % STORY_THUMBS.length);
        }, SLIDE_DURATION_MS);

        return () => clearInterval(interval);
    }, [isStoryPaused]);

    // Smoothly auto-scroll the thumbnail strip as the active photo changes
    useEffect(() => {
        if (thumbsTrackRef.current) {
            const track = thumbsTrackRef.current;
            const activeBtn = track.children[activeThumb];
            if (activeBtn) {
                const scrollLeft =
                    activeBtn.offsetLeft - track.offsetWidth / 2 + activeBtn.offsetWidth / 2;
                track.scrollTo({ left: scrollLeft, behavior: 'smooth' });
            }
        }
    }, [activeThumb]);

    // Puppy counts for filter tabs
    const malesCount = featuredPuppies.filter((p) => (p.sex || '').toLowerCase() === 'male').length;
    const femalesCount = featuredPuppies.filter((p) => (p.sex || '').toLowerCase() === 'female').length;
    const availableCount = featuredPuppies.filter((p) => p.status === 'available').length;

    const filteredPuppies = featuredPuppies.filter((puppy) => {
        if (puppyFilter === 'male') return (puppy.sex || '').toLowerCase() === 'male';
        if (puppyFilter === 'female') return (puppy.sex || '').toLowerCase() === 'female';
        if (puppyFilter === 'available') return puppy.status === 'available';
        return true;
    });

    return (
        <SiteLayout>
            <Head title="Riches Corsos — Health-Tested Cane Corso Puppies" />

            {/* ===== 1. HERO WITH CINEMATIC MOTION & LIVE STATUS PILL ===== */}
            <section className="hero-full hero-cinematic-wrap">
                <div
                    className="hero-full-bg hero-ken-burns"
                    style={{
                        backgroundImage: `url(${heroImage ? '/storage/' + heroImage : '/images/bg/homepagehero.jpg'})`,
                    }}
                />
                <div className="hero-full-overlay" />
                <div className="hero-full-content">
                    {/* Live status badge */}
                    <div className="hero-live-badge">
                        <span className="live-pulse-dot" />
                        <span className="live-badge-text">
                            Now Welcoming Inquiries for Fall 2026 Litters
                        </span>
                        <span className="live-badge-dot">•</span>
                        <span className="live-badge-sub">Health-Screened Bloodlines</span>
                    </div>

                    <h1 className="hero-full-title">Riches Corsos</h1>

                    <div className="hero-divider">
                        <span className="hero-divider-line" />
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="1.5"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        >
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                        <span className="hero-divider-line" />
                    </div>

                    <p className="hero-full-sub">
                        A small breeding program built on health testing, early socialization, and honest guidance — from the day you inquire to years after you bring your puppy home.
                    </p>

                    <div className="hero-full-actions">
                        <Link href="/contact" className="hero-btn hero-btn--primary">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="1.8"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            >
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="M2 7l10 7 10-7" />
                            </svg>
                            Get In Touch With Us
                        </Link>
                        <Link href="/puppies" className="hero-btn hero-btn--secondary">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="1.8"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            >
                                <path d="M10 2c-1 2-3.5 3-5 4.5S3 10 4 12s3 2.5 4 4 1 4 4 4 3-3 4-4 3-3 4-4 3-3 2-5-3-3-5-3.5S11 0 10 2z" />
                                <circle cx="14.5" cy="9.5" r="1" fill="currentColor" stroke="none" />
                            </svg>
                            Available Puppies
                        </Link>
                    </div>
                </div>
            </section>

            {/* ===== 2. TRUST NUMBERS WITH MICRO ICONS ===== */}
            <div className="home-page">
                <SectionTitle
                    title="Our Numbers"
                    sub="Nine years of health-tested, home-raised Cane Corso placements."
                />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="trust-grid-card">
                            <div className="trust-item-card">
                                <div className="trust-item-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        <polyline points="9 22 9 12 15 12 15 22" />
                                    </svg>
                                </div>
                                <div className="trust-item-info">
                                    <span>140+</span>
                                    <p>Families Placed</p>
                                </div>
                            </div>
                            <div className="trust-item-card">
                                <div className="trust-item-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7">
                                        <circle cx="12" cy="8" r="7" />
                                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88" />
                                    </svg>
                                </div>
                                <div className="trust-item-info">
                                    <span>9 yrs</span>
                                    <p>Breeding Experience</p>
                                </div>
                            </div>
                            <div className="trust-item-card">
                                <div className="trust-item-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        <path d="M9 12l2 2 4-4" />
                                    </svg>
                                </div>
                                <div className="trust-item-info">
                                    <span>100%</span>
                                    <p>Health Guaranteed</p>
                                </div>
                            </div>
                            <div className="trust-item-card">
                                <div className="trust-item-icon">
                                    <svg viewBox="0 0 24 24" fill="#F5A623" stroke="#D48806" strokeWidth="1">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                </div>
                                <div className="trust-item-info">
                                    <span>5.0</span>
                                    <p>Google Verified</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 01 ABOUT THE BREED ===== */}
                <SectionTitle number="01" title="About The Riches Corsos Puppy" />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="about-grid">
                            <div className="about-img-col">
                                <div className="about-img-wrap">
                                    <picture>
                                        <source srcSet="/images/aboutsite.webp" type="image/webp" />
                                        <img src="/images/aboutsite.png" alt="All About Riches Corsos" />
                                    </picture>
                                    <div className="about-img-label">ALL ABOUT RICHES CORSOS</div>
                                </div>
                                <Link href="/puppies" className="dark-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
                                        <path d="M10 2c-1 2-3.5 3-5 4.5S3 10 4 12s3 2.5 4 4 1 4 4 4 3-3 4-4 3-3 4-4 3-3 2-5-3-3-5-3.5S11 0 10 2z" />
                                        <circle cx="14.5" cy="9.5" r="1" fill="currentColor" stroke="none" />
                                    </svg>
                                    Available Puppies
                                </Link>
                            </div>
                            <div className="about-text-col">
                                <h3 className="about-col-title">Overview</h3>
                                <p>The Cane Corso is a large, powerful Italian mastiff breed known for its imposing presence and deeply loyal nature. Males typically weigh between 99–110 lbs and stand 25–27.5 inches tall; females are slightly smaller at 85–99 lbs and 23.5–26 inches.</p>
                                <p>With a lifespan of 9–12 years, the Corso is a long-term companion. They are intelligent, trainable, and deeply bonded to their family. Their short, dense double coat comes in black, grey, fawn, and brindle — requiring only weekly brushing and occasional baths.</p>
                                <p>Health-wise, responsible breeders screen for hip dysplasia, elbow dysplasia, cardiac conditions, and eye anomalies. At Riches Corsos, every breeding pair is fully health-tested before any litter is planned.</p>
                            </div>
                            <div className="about-text-col">
                                <h3 className="about-col-title">Temperament</h3>
                                <p>The Cane Corso is confident, calm, and deeply devoted. They are natural protectors — alert without being aggressive — and form an unbreakable bond with their immediate family.</p>
                                <p>With children they are gentle and patient when raised alongside them. They can coexist with other pets, especially when socialized early. Their intelligence means they thrive with consistent, firm, and positive training from puppyhood.</p>
                                <p>Early socialization is essential. A well-raised Corso is stable, adaptable, and a joy to live with — equally at home on a long walk or settled at your feet in the evening.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 02 AVAILABLE PUPPIES WITH QUICK FILTER TABS ===== */}
                <SectionTitle
                    number="02"
                    title="Available Puppies"
                    sub="Each one health-tested, vaccinated, and raised in-home before they meet you."
                />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        {/* Interactive Filter Pills */}
                        <div className="puppy-quick-filters">
                            <button
                                type="button"
                                className={`puppy-filter-btn ${puppyFilter === 'all' ? 'active' : ''}`}
                                onClick={() => setPuppyFilter('all')}
                            >
                                All Puppies <span className="filter-pill-count">{featuredPuppies.length}</span>
                            </button>
                            {malesCount > 0 && (
                                <button
                                    type="button"
                                    className={`puppy-filter-btn ${puppyFilter === 'male' ? 'active' : ''}`}
                                    onClick={() => setPuppyFilter('male')}
                                >
                                    ♂ Males <span className="filter-pill-count">{malesCount}</span>
                                </button>
                            )}
                            {femalesCount > 0 && (
                                <button
                                    type="button"
                                    className={`puppy-filter-btn ${puppyFilter === 'female' ? 'active' : ''}`}
                                    onClick={() => setPuppyFilter('female')}
                                >
                                    ♀ Females <span className="filter-pill-count">{femalesCount}</span>
                                </button>
                            )}
                            {availableCount > 0 && (
                                <button
                                    type="button"
                                    className={`puppy-filter-btn ${puppyFilter === 'available' ? 'active' : ''}`}
                                    onClick={() => setPuppyFilter('available')}
                                >
                                    Available Now <span className="filter-pill-count">{availableCount}</span>
                                </button>
                            )}
                        </div>

                        {/* Puppy Grid */}
                        <div className="puppy-grid">
                            {filteredPuppies.length > 0 ? (
                                filteredPuppies.map((puppy) => <PuppyCard key={puppy.id} puppy={puppy} />)
                            ) : (
                                <p style={{ color: 'var(--stone)', gridColumn: '1 / -1', textAlign: 'center', padding: '40px 0' }}>
                                    No puppies currently match the selected filter.
                                </p>
                            )}
                        </div>

                        {/* VIP Next Litter Waitlist Card */}
                        <div className="waitlist-banner-card">
                            <div className="waitlist-banner-content">
                                <span className="waitlist-badge">Upcoming Breedings</span>
                                <h4>Looking for a Specific Color or Upcoming Litter?</h4>
                                <p>
                                    Our litters are reserved quickly. Join our priority waitlist to receive private advance notices before public announcements.
                                </p>
                            </div>
                            <div className="waitlist-banner-action">
                                <Link href="/contact" className="btn-solid">
                                    Join Priority Waitlist
                                </Link>
                            </div>
                        </div>

                        <div className="section-cta">
                            <Link href="/puppies" className="dark-btn">
                                View All Available Puppies
                            </Link>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 03 OUR STORY: 43-SEC SEQUENTIAL REEL ===== */}
                <SectionTitle number="03" title="Riches Corsos: Our Story" />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div
                            className="story-grid"
                            onMouseEnter={() => setIsStoryPaused(true)}
                            onMouseLeave={() => setIsStoryPaused(false)}
                        >
                            <div className="story-text-col">
                                <p className="story-subtitle">
                                    WE ARE A TEAM OF DEDICATED PET LOVERS, CARETAKERS, AND TRAINERS.
                                </p>
                                <p>
                                    Riches Corsos was founded on a simple belief: that every puppy deserves the best possible start in life. Our program began over nine years ago with a single litter and a commitment to doing things the right way — health testing every breeding pair, raising every litter inside our home, and staying connected with every family long after their puppy goes home.
                                </p>
                                <p>
                                    We are not a kennel. We are a family. Our Corsos grow up under feet, around children, exposed to everyday sounds and experiences that build the confident, stable temperament this breed is known for. We take on a limited number of litters each year so that every puppy receives the individual attention they deserve.
                                </p>
                                <Link href="/about" className="dark-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
                                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                                    </svg>
                                    Learn More About Us
                                </Link>
                            </div>

                            <div className="story-img-col">
                                <div className="story-main-img-wrapper">
                                    <div className="story-main-img">
                                        <img
                                            key={activeThumb}
                                            src={`/images/ourstory/${STORY_THUMBS[activeThumb]}.jpeg`}
                                            alt={`Riches Corsos story photo ${activeThumb + 1}`}
                                            className="story-fade-in"
                                        />
                                    </div>
                                    {/* 43s cycle progress line */}
                                    <div className="story-progress-indicator">
                                        <div
                                            className={`story-progress-bar ${isStoryPaused ? 'paused' : ''}`}
                                            key={activeThumb}
                                            style={{ animationDuration: `${SLIDE_DURATION_MS}ms` }}
                                        />
                                    </div>
                                    <div className="story-slide-counter">
                                        Photo {activeThumb + 1} of {STORY_THUMBS.length}
                                        {isStoryPaused && ' (Paused)'}
                                    </div>
                                </div>

                                {/* Smoothly auto-scrolling thumbnail track */}
                                <div className="story-thumbs-track" ref={thumbsTrackRef}>
                                    {STORY_THUMBS.map((n, i) => (
                                        <button
                                            key={n}
                                            type="button"
                                            className={`story-thumb ${activeThumb === i ? 'active' : ''}`}
                                            onClick={() => setActiveThumb(i)}
                                            aria-label={`Select photo ${i + 1}`}
                                        >
                                            <img src={`/images/ourstory/${n}.jpeg`} alt={`Gallery ${n}`} />
                                            {activeThumb === i && <span className="thumb-active-marker" />}
                                        </button>
                                    ))}
                                </div>

                                <div className="story-dots">
                                    {STORY_THUMBS.map((_, i) => (
                                        <button
                                            key={i}
                                            type="button"
                                            className={`story-dot ${activeThumb === i ? 'active' : ''}`}
                                            onClick={() => setActiveThumb(i)}
                                            aria-label={`Photo ${i + 1}`}
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== INTERACTIVE CANE CORSO QUIZ ===== */}
                <SectionTitle
                    title="Find Your Perfect Match"
                    sub="Take our 30-second breed compatibility assessment."
                />
                <div className="home-section-wrap">
                    <CaneCorsoQuiz />
                </div>

                <SectionDivider />

                {/* ===== 04 WHAT RAISING THEM MEANS ===== */}
                <SectionTitle
                    number="04"
                    title="What Raising Them This Way Means"
                    sub="This section explains the actual practices, environment, and ongoing care that go into raising each puppy."
                />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="why-grid-full">
                            <div className="why-item-full">
                                <div className="why-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                    </svg>
                                </div>
                                <div className="why-item-body">
                                    <h3>Health Tested</h3>
                                    <p>Before a litter is planned, both parents undergo the appropriate health screening. This includes hip evaluations, heart evaluations, and genetic health panels. The goal is to make informed breeding decisions and give each puppy the strongest possible start.</p>
                                </div>
                            </div>

                            <div className="why-item-full">
                                <div className="why-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6">
                                        <path d="M3 12l9-8 9 8M5 10v10h14V10" />
                                    </svg>
                                </div>
                                <div className="why-item-body">
                                    <h3>Home Raised</h3>
                                    <p>Our puppies grow up inside our home rather than being raised in a kennel environment. They experience everyday household life from an early age — people moving around, normal household sounds, different surfaces, and regular human interaction. This helps them become familiar with the environment they will eventually share with their families.</p>
                                </div>
                            </div>

                            <div className="why-item-full">
                                <div className="why-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6">
                                        <path d="M4 4v16h16M4 15l5-5 4 4 7-7" />
                                    </svg>
                                </div>
                                <div className="why-item-body">
                                    <h3>Early Foundations</h3>
                                    <p>Preparation begins well before a puppy reaches eight weeks. Age-appropriate foundations are introduced gradually, including basic commands, leash exposure, gentle handling, everyday interaction, and confidence-building experiences. The focus is not on rushing training, but on giving each puppy positive early experiences that can serve as a foundation for life with its future family.</p>
                                </div>
                            </div>

                            <div className="why-item-full">
                                <div className="why-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                </div>
                                <div className="why-item-body">
                                    <h3>Support That Continues</h3>
                                    <p>Our relationship with a puppy's family does not end when the puppy goes home. Whether a family has a question during the first few days or needs guidance months later, we remain available to help. Questions at one year should receive the same care and attention as questions on day one.</p>
                                </div>
                            </div>
                        </div>

                        {/* Growth & Milestones interactive showcase */}
                        <div className="milestones-section">
                            <DiamondDivider />
                            <div className="milestones-header">
                                <h3>Puppy to Giant: What to Expect as Your Corso Grows</h3>
                                <p>Cane Corsos develop steadily over two years. Here is how your puppy matures at each stage:</p>
                            </div>

                            <div className="milestone-tabs">
                                {MILESTONES.map((m) => (
                                    <button
                                        key={m.id}
                                        type="button"
                                        className={`milestone-tab-btn ${activeMilestone === m.id ? 'active' : ''}`}
                                        onClick={() => setActiveMilestone(m.id)}
                                    >
                                        <span className="milestone-tab-stage">{m.stage}</span>
                                        <span className="milestone-tab-title">{m.title}</span>
                                    </button>
                                ))}
                            </div>

                            {MILESTONES.filter((m) => m.id === activeMilestone).map((m) => (
                                <div className="milestone-content-card" key={m.id}>
                                    <div className="milestone-metric-col">
                                        <div className="metric-pill">
                                            <span>Average Weight</span>
                                            <strong>{m.weight}</strong>
                                        </div>
                                        <div className="metric-pill">
                                            <span>Development Focus</span>
                                            <p>{m.focus}</p>
                                        </div>
                                    </div>
                                    <div className="milestone-desc-col">
                                        <h4>{m.title}</h4>
                                        <p>{m.desc}</p>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="why-closing">
                            <DiamondDivider />
                            <p>Raising puppies this way takes time, consistency, and attention to the details that happen long before a puppy meets their new family. That is the standard we aim to maintain with every litter.</p>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 05 TESTIMONIALS WITH REAL OWNER DETAILS ===== */}
                <SectionTitle number="05" title="What Our Customers Are Saying" />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <DiamondDivider />
                        <div className="google-badge">
                            <svg viewBox="0 0 24 24" width="22" height="22">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            <span>Google Verified Reviews</span>
                            <Stars />
                        </div>

                        <div className="testimonial-grid-4">
                            {TESTIMONIALS.map((t) => (
                                <div className="t-card-4" key={t.author}>
                                    <Stars count={t.rating} />
                                    <p>"{t.text}"</p>
                                    <div className="t-card-footer">
                                        <DiamondDivider />
                                        <cite>{t.author}</cite>
                                        <span className="t-card-meta">{t.location} • {t.dog}</span>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="testimonial-actions">
                            <Link href="/testimonials" className="dark-btn">More About What People Are Saying</Link>
                            <Link href="/contact" className="dark-btn dark-btn--outline">Submit A Testimony</Link>
                        </div>
                        <DiamondDivider />
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 06 BLOG ===== */}
                <SectionTitle number="06" title="From The Blog" sub="Care guides, training tips, and stories from families who've brought a Corso home." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="blog-grid">
                            {recentPosts.length > 0 ? (
                                recentPosts.map((post) => (
                                    <Link href={`/blog/${post.slug}`} key={post.id} className="blog-card">
                                        <div className={`blog-photo ${post.cover_image ? '' : 'placeholder'}`}>
                                            {post.cover_image && <img src={`/storage/${post.cover_image}`} alt={post.title} />}
                                        </div>
                                        <div className="blog-info">
                                            {post.category && <span className="blog-tag">{post.category}</span>}
                                            <h3>{post.title}</h3>
                                            <p>{post.excerpt}</p>
                                        </div>
                                    </Link>
                                ))
                            ) : (
                                <p style={{ color: 'var(--stone)', gridColumn: '1 / -1', textAlign: 'center', padding: '40px 0' }}>No posts published yet.</p>
                            )}
                        </div>
                        <div className="section-cta">
                            <Link href="/blog" className="dark-btn">View All Articles</Link>
                        </div>
                    </div>
                </div>

                {homecomingPhotos.length > 0 && (
                    <>
                        <SectionDivider />
                        <SectionTitle title="Recent Homecomings" sub="A few of the families who picked up their puppy this year." />
                        <div className="home-section-wrap">
                            <div className="card-3d">
                                <div className="homecomings-track">
                                    {homecomingPhotos.map((photo) => (
                                        <div className="homecoming-photo" key={photo.id}>
                                            <img src={`/storage/${photo.image}`} alt={photo.caption || 'Family with their puppy'} />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </>
                )}

                <div style={{ height: 48 }} />
            </div>

            {/* ===== CTA BAND ===== */}
            <div className="cta-band">
                <h2>Ready to meet your Corso?</h2>
                <Link href="/puppies" className="btn-solid">View Available Puppies</Link>
            </div>
        </SiteLayout>
    );
}
