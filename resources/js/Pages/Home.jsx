import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import SiteLayout from '@/Layouts/SiteLayout';
import PuppyCard from '@/Components/PuppyCard';

const STORY_THUMBS = ['1', '2', '3', '4', '5', '6', '7', '8'];

const TESTIMONIALS = [
    { author: 'Zoey Wilson', text: 'Incredibly flexible with scheduling and so accommodating. The whole process was smooth from start to finish. Couldn\'t ask for a better experience!', rating: 5 },
    { author: 'Leilya Thao', text: 'We were greeted warmly the moment we arrived. The owners are knowledgeable, caring, and genuinely passionate about their puppies. Highly recommend!', rating: 5 },
    { author: 'Bobbie Platt', text: 'Picking up our puppy was such a joy. You could tell every dog there is loved and well cared for. Our Corso has been an absolute blessing to our family.', rating: 5 },
    { author: 'Jason Poplin', text: 'Worth every mile of the drive. The facilities were spotless and the puppies were healthy, happy, and well-socialized. We will definitely be back!', rating: 5 },
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
    return <div className="section-divider"><DiamondDivider /></div>;
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
    const [activeThumb, setActiveThumb] = useState(0);

    return (
        <SiteLayout>
            <Head title="Riches Corsos — Cane Corso Puppies" />

            {/* ===== HERO ===== */}
            <section
                className="hero-full"
                style={{ backgroundImage: `url(${heroImage ? '/storage/' + heroImage : '/images/bg/homepagehero.jpg'})` }}
            >
                <div className="hero-full-overlay" />
                <div className="hero-full-content">
                    <h1 className="hero-full-title">Riches Corsos</h1>
                    <div className="hero-divider">
                        <span className="hero-divider-line" />
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                        <span className="hero-divider-line" />
                    </div>
                    <p className="hero-full-sub">
                        A small breeding program built on health testing, early socialization,
                        and honest guidance — from the day you inquire to years after you bring
                        your puppy home.
                    </p>
                    <div className="hero-full-actions">
                        <Link href="/contact" className="hero-btn hero-btn--primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" /><path d="M2 7l10 7 10-7" />
                            </svg>
                            Get In Touch With Us
                        </Link>
                        <Link href="/puppies" className="hero-btn hero-btn--secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M10 2c-1 2-3.5 3-5 4.5S3 10 4 12s3 2.5 4 4 1 4 4 4 3-3 4-4 3-3 4-4 3-3 2-5-3-3-5-3.5S11 0 10 2z" />
                                <circle cx="14.5" cy="9.5" r="1" fill="currentColor" stroke="none" />
                            </svg>
                            Available Puppies
                        </Link>
                    </div>
                </div>
            </section>

            {/* ===== TRUST NUMBERS ===== */}
            <div className="home-page">

                <SectionTitle title="Our Numbers" sub="Nine years of health-tested, home-raised Cane Corso placements." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="trust-grid-card">
                            <div className="trust-item-card"><span>140+</span>Families Placed</div>
                            <div className="trust-item-card"><span>9 yrs</span>Breeding Experience</div>
                            <div className="trust-item-card"><span>100%</span>Health Guaranteed</div>
                            <div className="trust-item-card"><span>5.0</span>Average Rating</div>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 01 ABOUT ===== */}
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

                {/* ===== 02 AVAILABLE PUPPIES ===== */}
                <SectionTitle number="02" title="Available Puppies" sub="Each one health-tested, vaccinated, and raised in-home before they meet you." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="puppy-grid">
                            {featuredPuppies.length > 0 ? (
                                featuredPuppies.map((puppy) => <PuppyCard key={puppy.id} puppy={puppy} />)
                            ) : (
                                <p style={{ color: 'var(--stone)', gridColumn: '1 / -1', textAlign: 'center', padding: '40px 0' }}>
                                    No puppies listed right now — check back soon.
                                </p>
                            )}
                        </div>
                        <div className="section-cta">
                            <Link href="/puppies" className="dark-btn">View All Available Puppies</Link>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 03 OUR STORY ===== */}
                <SectionTitle number="03" title="Riches Corsos: Our Story" />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="story-grid">
                            <div className="story-text-col">
                                <p className="story-subtitle">WE ARE A TEAM OF DEDICATED PET LOVERS, CARETAKERS, AND TRAINERS.</p>
                                <p>Riches Corsos was founded on a simple belief: that every puppy deserves the best possible start in life. Our program began over nine years ago with a single litter and a commitment to doing things the right way — health testing every breeding pair, raising every litter inside our home, and staying connected with every family long after their puppy goes home.</p>
                                <p>We are not a kennel. We are a family. Our Corsos grow up under feet, around children, exposed to everyday sounds and experiences that build the confident, stable temperament this breed is known for. We take on a limited number of litters each year so that every puppy receives the individual attention they deserve.</p>
                                <Link href="/about" className="dark-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
                                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                                    </svg>
                                    Learn More About Us
                                </Link>
                            </div>
                            <div className="story-img-col">
                                <div className="story-main-img">
                                    <img src={`/images/ourstory/${STORY_THUMBS[activeThumb]}.jpeg`} alt="Our story" />
                                </div>
                                <div className="story-thumbs">
                                    {STORY_THUMBS.map((n, i) => (
                                        <button key={n} className={`story-thumb ${activeThumb === i ? 'active' : ''}`} onClick={() => setActiveThumb(i)}>
                                            <img src={`/images/ourstory/${n}.jpeg`} alt={`Gallery ${n}`} />
                                        </button>
                                    ))}
                                </div>
                                <div className="story-dots">
                                    {STORY_THUMBS.map((_, i) => (
                                        <button key={i} className={`story-dot ${activeThumb === i ? 'active' : ''}`} onClick={() => setActiveThumb(i)} aria-label={`Photo ${i + 1}`} />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 04 WHAT RAISING THEM MEANS ===== */}
                <SectionTitle number="04" title="What Raising Them This Way Means" sub="This section explains the actual practices, environment, and ongoing care that go into raising each puppy." />
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
                                        <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" />
                                    </svg>
                                </div>
                                <div className="why-item-body">
                                    <h3>Support That Continues</h3>
                                    <p>Our relationship with a puppy's family does not end when the puppy goes home. Whether a family has a question during the first few days or needs guidance months later, we remain available to help. Questions at one year should receive the same care and attention as questions on day one.</p>
                                </div>
                            </div>

                        </div>
                        <div className="why-closing">
                            <DiamondDivider />
                            <p>Raising puppies this way takes time, consistency, and attention to the details that happen long before a puppy meets their new family. That is the standard we aim to maintain with every litter.</p>
                        </div>
                    </div>
                </div>

                <SectionDivider />

                {/* ===== 05 TESTIMONIALS ===== */}
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
                            <span>Google Reviews</span>
                            <Stars />
                        </div>
                        <div className="testimonial-grid-4">
                            {TESTIMONIALS.map((t) => (
                                <div className="t-card-4" key={t.author}>
                                    <Stars count={t.rating} />
                                    <p>"{t.text}"</p>
                                    <DiamondDivider />
                                    <cite>{t.author}</cite>
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
