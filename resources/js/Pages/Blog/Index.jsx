import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

function readingTime(body) {
    if (!body) return '3 min read';
    const words = body.replace(/<[^>]+>/g, '').split(/\s+/).length;
    return `${Math.max(1, Math.round(words / 200))} min read`;
}

function CategoryPill({ category }) {
    if (!category) return null;
    return <span className="mag-category-pill">{category}</span>;
}

function PostCard({ post, featured = false }) {
    const image = post.cover_image ? `/storage/${post.cover_image}` : null;
    return (
        <Link href={`/blog/${post.slug}`} className={`mag-card ${featured ? 'mag-card--featured' : ''}`}>
            <div className="mag-card-img">
                {image
                    ? <img src={image} alt={post.title} loading="lazy" />
                    : <div className="mag-card-img-placeholder" />}
                <CategoryPill category={post.category} />
            </div>
            <div className="mag-card-body">
                <h2 className="mag-card-title">{post.title}</h2>
                {post.excerpt && <p className="mag-card-excerpt">{post.excerpt}</p>}
                <div className="mag-card-meta">
                    <span className="mag-card-date">{formatDate(post.published_at)}</span>
                    <span className="mag-card-dot">·</span>
                    <span className="mag-card-read">{readingTime(post.body)}</span>
                </div>
            </div>
        </Link>
    );
}

export default function BlogIndex({ posts }) {
    const items = posts?.data ?? posts ?? [];
    const [featured, second, third, ...rest] = items;

    return (
        <SiteLayout>
            <Head title="Blog — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="From The Blog"
                sub="Care guides, training insights, breeding knowledge, and stories from families who've welcomed a champion Cane Corso into their home."
            />

            <div className="mag-page">

                {items.length === 0 ? (
                    <div className="mag-empty">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round">
                            <path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                        </svg>
                        <h2>Articles Coming Soon</h2>
                        <p>We're preparing care guides, training tips, and stories from the Riches Corsos community. Check back soon.</p>
                        <div className="mag-empty-actions">
                            <Link href="/puppies" className="btn-solid">View Available Puppies</Link>
                            <Link href="/contact" className="btn-outline">Contact Us</Link>
                        </div>
                    </div>
                ) : (
                    <>
                        {/* Hero feature — first post full width */}
                        {featured && (
                            <section className="mag-hero-section">
                                <Link href={`/blog/${featured.slug}`} className="mag-hero-card">
                                    <div className="mag-hero-img">
                                        {featured.cover_image
                                            ? <img src={`/storage/${featured.cover_image}`} alt={featured.title} />
                                            : <div className="mag-hero-img-placeholder" />}
                                        <div className="mag-hero-overlay" />
                                    </div>
                                    <div className="mag-hero-content">
                                        <CategoryPill category={featured.category} />
                                        <h2 className="mag-hero-title">{featured.title}</h2>
                                        {featured.excerpt && <p className="mag-hero-excerpt">{featured.excerpt}</p>}
                                        <div className="mag-hero-meta">
                                            <span>{formatDate(featured.published_at)}</span>
                                            <span className="mag-card-dot">·</span>
                                            <span>{readingTime(featured.body)}</span>
                                            <span className="mag-hero-cta">Read Article →</span>
                                        </div>
                                    </div>
                                </Link>
                            </section>
                        )}

                        {/* Second row — two cards side by side */}
                        {(second || third) && (
                            <section className="mag-duo-section">
                                {second && <PostCard post={second} />}
                                {third && <PostCard post={third} />}
                            </section>
                        )}

                        {/* Rest — 3-column grid */}
                        {rest.length > 0 && (
                            <section className="mag-grid-section">
                                <div className="mag-section-label">
                                    <span>More Articles</span>
                                    <div className="mag-section-line" />
                                </div>
                                <div className="mag-grid">
                                    {rest.map((post) => <PostCard key={post.id} post={post} />)}
                                </div>
                            </section>
                        )}
                    </>
                )}
            </div>

            {/* CTA band */}
            <div className="cta-band">
                <h2>Have a question about Cane Corsos?</h2>
                <Link href="/contact" className="btn-solid">Get In Touch</Link>
            </div>
        </SiteLayout>
    );
}
