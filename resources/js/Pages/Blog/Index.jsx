import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function BlogIndex({ posts }) {
  const items = posts?.data ?? posts ?? [];

  return (
    <SiteLayout>
      <Head title="Blog — Riches Corsos" />

      <section className="section" style={{ paddingBottom: 40 }}>
        <div className="section-head">
          <h2>From the blog</h2>
          <p>Care guides, training tips, and stories from families who've brought a Corso home.</p>
        </div>
      </section>

      <section className="section" style={{ paddingTop: 0 }}>
        <div className="blog-grid">
          {items.length > 0 ? (
            items.map((post) => (
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
            <p style={{ color: 'var(--stone)', gridColumn: '1 / -1', textAlign: 'center' }}>
              No posts published yet — check back soon.
            </p>
          )}
        </div>
      </section>
    </SiteLayout>
  );
}
