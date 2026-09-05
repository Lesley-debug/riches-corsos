import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';

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

function SectionTitle({ title, sub }) {
    return (
        <div className="sec-title-wrap">
            <div className="sec-title-plaque">
                <h2 className="sec-title-text">{title}</h2>
            </div>
            {sub && <p className="sec-title-sub">{sub}</p>}
        </div>
    );
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

export default function BlogIndex({ posts }) {
    const items = posts?.data ?? posts ?? [];
    const [featured, ...rest] = items;

    if (items.length === 0) {
        return (
            <SiteLayout>
                <Head title="Blog — Riches Corsos" />

                <PageHero
                    image="/images/about/1.jpeg"
                    title="From The Blog"
                    sub="Care guides, training insights, puppy preparation, breeding knowledge, and stories from families who have welcomed a RICHES CORSOS companion into their homes."
                />

                <div className="home-page">
                    <div className="home-section-wrap" style={{ paddingTop: 48, paddingBottom: 48 }}>
                        <div className="card-3d blog-empty">
                            <div className="blog-empty-img">
                                <picture>
                                    <source srcSet="/images/aboutsite.webp" type="image/webp" />
                                    <img src="/images/aboutsite.png" alt="RICHES CORSOS" />
                                </picture>
                            </div>
                            <div className="blog-empty-content">
                                <h2>Stories &amp; Insights Are Coming Soon</h2>
                                <p>We're preparing helpful guides, puppy-care resources, training tips, and stories from the RICHES CORSOS community. Check back soon.</p>
                                <div className="blog-empty-actions">
                                    <Link href="/contact" className="btn-solid">Contact Us</Link>
                                    <Link href="/puppies" className="btn-outline">View Available Puppies</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style={{ height: 48 }} />
                </div>
            </SiteLayout>
        );
    }

    return (
        <SiteLayout>
            <Head title="Blog — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="From The Blog"
                sub="Care guides, training insights, puppy preparation, breeding knowledge, and stories from families who have welcomed a RICHES CORSOS companion into their homes."
            />

            <div className="home-page">

                {/* Featured Article */}
                {featured && (
                    <>
                        <SectionTitle title="Featured Article" />
                        <div className="home-section-wrap">
                            <div className="card-3d">
                                <Link href={`/blog/${featured.slug}`} className="blog-featured">
                                    <div className="blog-featured-img">
                                        <div className={`blog-photo ${featured.cover_image ? '' : 'placeholder'}`}>
                                            {featured.cover_image && <img src={`/storage/${featured.cover_image}`} alt={featured.title} />}
                                        </div>
                                    </div>
                                    <div className="blog-featured-content">
                                        {featured.category && <span className="blog-tag">{featured.category}</span>}
                                        <h2 className="blog-featured-title">{featured.title}</h2>
                                        {featured.excerpt && <p className="blog-featured-excerpt">{featured.excerpt}</p>}
                                        <div className="blog-featured-meta">
                                            <span>{formatDate(featured.published_at)}</span>
                                        </div>
                                        <span className="blog-read-more">
                                            Read Article
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" width="16" height="16">
                                                <path d="M5 12h14M12 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        {rest.length > 0 && (
                            <>
                                <div className="section-divider"><DiamondDivider /></div>
                                <SectionTitle title="More Articles" />
                                <div className="home-section-wrap">
                                    <div className="card-3d">
                                        <div className="blog-grid">
                                            {rest.map((post) => (
                                                <Link href={`/blog/${post.slug}`} key={post.id} className="blog-card">
                                                    <div className={`blog-photo ${post.cover_image ? '' : 'placeholder'}`}>
                                                        {post.cover_image && <img src={`/storage/${post.cover_image}`} alt={post.title} />}
                                                    </div>
                                                    <div className="blog-info">
                                                        {post.category && <span className="blog-tag">{post.category}</span>}
                                                        <h3>{post.title}</h3>
                                                        <p>{post.excerpt}</p>
                                                        <div className="blog-card-footer">
                                                            <span className="blog-card-date">{formatDate(post.published_at)}</span>
                                                            <span className="blog-card-arrow">→</span>
                                                        </div>
                                                    </div>
                                                </Link>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </>
                        )}
                    </>
                )}

                <div style={{ height: 48 }} />
            </div>

            <div className="cta-band">
                <h2>Have a question for RICHES CORSOS?</h2>
                <Link href="/contact" className="btn-solid">Get In Touch</Link>
            </div>
        </SiteLayout>
    );
}
