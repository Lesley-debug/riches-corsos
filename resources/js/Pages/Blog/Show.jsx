import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

function readingTime(body) {
    if (!body) return '3 min read';
    const words = body.replace(/<[^>]+>/g, '').split(/\s+/).length;
    return `${Math.max(1, Math.round(words / 200))} min read`;
}

function RelatedCard({ post }) {
    const image = post.cover_image ? `/storage/${post.cover_image}` : null;
    return (
        <Link href={`/blog/${post.slug}`} className="related-card">
            <div className="related-card-img">
                {image ? <img src={image} alt={post.title} loading="lazy" /> : <div className="related-card-img-placeholder" />}
            </div>
            <div className="related-card-body">
                {post.category && <span className="mag-category-pill related-pill">{post.category}</span>}
                <h4 className="related-card-title">{post.title}</h4>
                <span className="related-card-date">{formatDate(post.published_at)}</span>
            </div>
        </Link>
    );
}

export default function BlogShow({ post, related = [] }) {
    const canonicalUrl = `https://richescorsos.com/blog/${post.slug}`;
    const ogImage = post.cover_image ? `/storage/${post.cover_image}` : '/images/bg/homepagehero.jpg';

    const handleShare = (platform) => {
        const url = encodeURIComponent(canonicalUrl);
        const text = encodeURIComponent(post.title);
        const links = {
            twitter: `https://twitter.com/intent/tweet?url=${url}&text=${text}`,
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
            whatsapp: `https://wa.me/?text=${text}%20${url}`,
        };
        if (links[platform]) window.open(links[platform], '_blank', 'width=600,height=450');
    };

    const copyLink = () => {
        navigator.clipboard?.writeText(canonicalUrl);
    };

    return (
        <SiteLayout>
            <Head>
                <title>{post.title} — Riches Corsos Blog</title>
                <meta name="description" content={post.excerpt || post.title} />
                <meta property="og:title" content={post.title} />
                <meta property="og:description" content={post.excerpt || ''} />
                <meta property="og:image" content={ogImage} />
                <meta property="og:url" content={canonicalUrl} />
                <meta property="og:type" content="article" />
                <link rel="canonical" href={canonicalUrl} />
            </Head>

            <div className="article-page">

                {/* Breadcrumb */}
                <nav className="article-breadcrumb">
                    <div className="article-breadcrumb-inner">
                        <Link href="/">Home</Link>
                        <span>/</span>
                        <Link href="/blog">Blog</Link>
                        <span>/</span>
                        <span>{post.title}</span>
                    </div>
                </nav>

                <div className="article-shell">
                    <div className="article-layout">

                        {/* ── MAIN ARTICLE ───────────────────────── */}
                        <article className="article-main">

                            {/* Article header */}
                            <header className="article-header">
                                {post.category && <span className="mag-category-pill">{post.category}</span>}
                                <h1 className="article-title">{post.title}</h1>
                                {post.excerpt && <p className="article-excerpt">{post.excerpt}</p>}
                                <div className="article-meta-row">
                                    <div className="article-meta-left">
                                        <div className="article-author-avatar">RC</div>
                                        <div className="article-meta-info">
                                            <span className="article-author-name">Riches Corsos</span>
                                            <div className="article-meta-sub">
                                                <span>{formatDate(post.published_at)}</span>
                                                <span className="article-meta-dot">·</span>
                                                <span>{readingTime(post.body)}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {/* Share buttons inline */}
                                    <div className="article-share-inline">
                                        <button type="button" onClick={() => handleShare('twitter')} className="article-share-btn" aria-label="Share on Twitter">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                            </svg>
                                        </button>
                                        <button type="button" onClick={() => handleShare('facebook')} className="article-share-btn" aria-label="Share on Facebook">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                                            </svg>
                                        </button>
                                        <button type="button" onClick={() => handleShare('whatsapp')} className="article-share-btn" aria-label="Share on WhatsApp">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                            </svg>
                                        </button>
                                        <button type="button" onClick={copyLink} className="article-share-btn" aria-label="Copy link" title="Copy link">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round">
                                                <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" />
                                                <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </header>

                            {/* Cover image */}
                            {post.cover_image && (
                                <div className="article-cover">
                                    <img src={`/storage/${post.cover_image}`} alt={post.title} />
                                </div>
                            )}

                            {/* Article body */}
                            <div
                                className="article-body"
                                dangerouslySetInnerHTML={{ __html: post.body }}
                            />

                            {/* Bottom share */}
                            <div className="article-bottom-share">
                                <span>Share this article</span>
                                <div className="article-share-row">
                                    <button type="button" onClick={() => handleShare('twitter')} className="article-share-pill">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                                        Twitter
                                    </button>
                                    <button type="button" onClick={() => handleShare('facebook')} className="article-share-pill">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" /></svg>
                                        Facebook
                                    </button>
                                    <button type="button" onClick={() => handleShare('whatsapp')} className="article-share-pill">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
                                        WhatsApp
                                    </button>
                                    <button type="button" onClick={copyLink} className="article-share-pill">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" /><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" /></svg>
                                        Copy Link
                                    </button>
                                </div>
                            </div>

                            {/* Back to blog */}
                            <div className="article-back">
                                <Link href="/blog" className="article-back-link">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M19 12H5M12 5l-7 7 7 7" /></svg>
                                    Back to Blog
                                </Link>
                            </div>
                        </article>

                        {/* ── SIDEBAR ─────────────────────────────── */}
                        <aside className="article-sidebar">

                            {/* About box */}
                            <div className="sidebar-card sidebar-about">
                                <div className="sidebar-about-avatar">RC</div>
                                <h4>Riches Corsos</h4>
                                <p>Premier breeder of champion-line Italian Cane Corso puppies. Health tested, home raised, nationwide delivery.</p>
                                <Link href="/puppies" className="btn-solid sidebar-cta-btn">View Available Puppies</Link>
                            </div>

                            {/* Newsletter / contact nudge */}
                            <div className="sidebar-card sidebar-cta-card">
                                <h4>🐾 Interested in a Puppy?</h4>
                                <p>Join our priority waitlist and be the first to know when new litters are available.</p>
                                <Link href="/contact" className="btn-solid sidebar-cta-btn">Join Waitlist</Link>
                            </div>

                            {/* Quick facts */}
                            <div className="sidebar-card">
                                <h4 className="sidebar-section-title">Quick Facts</h4>
                                <ul className="sidebar-facts-list">
                                    <li><span>📅</span><span>Published {formatDate(post.published_at)}</span></li>
                                    <li><span>⏱</span><span>{readingTime(post.body)}</span></li>
                                    {post.category && <li><span>🏷</span><span>{post.category}</span></li>}
                                </ul>
                            </div>
                        </aside>

                    </div>

                    {/* Related articles */}
                    {related.length > 0 && (
                        <section className="related-section">
                            <div className="related-header">
                                <h3>More From The Blog</h3>
                                <Link href="/blog" className="related-view-all">View all articles →</Link>
                            </div>
                            <div className="related-grid">
                                {related.map((p) => <RelatedCard key={p.id} post={p} />)}
                            </div>
                        </section>
                    )}
                </div>
            </div>
        </SiteLayout>
    );
}
