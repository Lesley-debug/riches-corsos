import { Head } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function BlogShow({ post }) {
  return (
    <SiteLayout>
      <Head title={`${post.title} — Riches Corsos Blog`} />

      <article className="blog-article">
        {post.category && <span className="blog-tag">{post.category}</span>}
        <h1>{post.title}</h1>
        <div className="blog-meta">
          {new Date(post.published_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          })}
        </div>

        {post.cover_image && (
          <div className="blog-cover">
            <img src={`/storage/${post.cover_image}`} alt={post.title} />
          </div>
        )}

        {/* Body comes from the admin's rich text editor (own staff content, not user-submitted) */}
        <div className="blog-body" dangerouslySetInnerHTML={{ __html: post.body }} />
      </article>
    </SiteLayout>
  );
}
