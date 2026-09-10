import { useEffect, useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';

// Synonym map — keys are canonical terms, values are alternate spellings/words
// that should match the same results.
const SYNONYMS = {
  vaccinated: ['vacinated', 'vacinnated', 'vaccinnated', 'vaxxed', 'shots', 'immunized'],
  health: ['healthy', 'tested', 'cleared', 'guaranteed'],
  available: ['for sale', 'for adoption', 'buy', 'purchase', 'reserve'],
  puppy: ['puppies', 'pup', 'pups', 'dog', 'dogs', 'corso', 'corsos', 'cane corso'],
  male: ['boy', 'boys', 'males'],
  female: ['girl', 'girls', 'females'],
  training: ['trained', 'socialized', 'socialization', 'foundations'],
  blog: ['article', 'articles', 'post', 'posts', 'guide', 'guides', 'tips'],
  contact: ['reach', 'email', 'phone', 'message', 'inquiry', 'inquire'],
  about: ['story', 'breeder', 'breeders', 'who we are', 'our program'],
  faq: ['faqs', 'questions', 'question', 'help', 'how', 'what'],
};

// Static site pages always available for search
const STATIC_PAGES = [
  {
    id: 'puppies',
    title: 'Available Puppies',
    href: '/puppies',
    description: 'Browse our current litter of health-tested, home-raised Cane Corso puppies.',
    tags: 'puppies puppy available for sale reserve buy adoption litters cane corso dogs price',
  },
  {
    id: 'about',
    title: 'Our Story & Breeding Program',
    href: '/about',
    description: 'Learn about our 9+ years of breeding experience, health testing protocols, and home environment.',
    tags: 'about breeder story program health tested parents sire dam pedigree history who we are',
  },
  {
    id: 'special',
    title: 'What Makes Us Special',
    href: '/about#what-makes-us-special',
    description: 'Early neurological stimulation, home socialization, health guarantees, and lifetime support.',
    tags: 'special socialization health guarantee stimulation puppy culture standards raising',
  },
  {
    id: 'faqs',
    title: 'Frequently Asked Questions',
    href: '/faqs',
    description: 'Answers about puppy adoption, deposits, pickup/delivery, vaccines, and contracts.',
    tags: 'faq faqs questions help deposit reservation payment shipping delivery pickup contract',
  },
  {
    id: 'health',
    title: 'Health Testing & Guarantees',
    href: '/faqs#health',
    description: 'Our comprehensive health screening standards: hips, cardiac, genetic panels, and health warranty.',
    tags: 'health testing guaranteed warranty hip cardiac genetics clear vet vaccinated shots medical',
  },
  {
    id: 'testimonials',
    title: 'Customer Testimonials & Reviews',
    href: '/testimonials',
    description: 'Read real stories and 5-star reviews from families across the country who welcomed our Corsos.',
    tags: 'testimonials reviews feedback rating google families stories happy clients',
  },
  {
    id: 'blog',
    title: 'Blog & Cane Corso Care Guides',
    href: '/blog',
    description: 'Training tips, nutrition, puppy development, and living with the Cane Corso breed.',
    tags: 'blog articles guide care feeding training tips crate leash socialization diet grooming',
  },
  {
    id: 'contact',
    title: 'Contact Us & Visiting Hours',
    href: '/contact',
    description: 'Reach our team directly by phone, email, WhatsApp, or submit an inquiry.',
    tags: 'contact email phone reach call message inquiry whatsapp dallas texas location hours',
  },
  {
    id: 'account',
    title: 'Customer Account & Profile',
    href: '/account',
    description: 'Manage your customer profile, saved puppies, and order history.',
    tags: 'account profile login customer portal register my account sign in',
  },
  {
    id: 'orders',
    title: 'Order Status & Reservations',
    href: '/orders',
    description: 'Track your pending applications, puppy reservations, and payment receipts.',
    tags: 'orders order tracking status reservation invoice checkout receipts cart',
  },
];

function normalise(str) {
  return (str ?? '').toLowerCase().trim();
}

// Expand a query term to include all synonyms
function expandQuery(q) {
  const terms = [q];
  for (const [canonical, alts] of Object.entries(SYNONYMS)) {
    if (alts.includes(q) || q === canonical) {
      terms.push(canonical, ...alts);
    }
    if (canonical.startsWith(q) || alts.some((a) => a.startsWith(q))) {
      terms.push(canonical, ...alts);
    }
  }
  return [...new Set(terms)];
}

function matches(text, terms) {
  const t = normalise(text);
  return terms.some((term) => t.includes(term));
}

function scoreText(text, terms) {
  const t = normalise(text);
  return terms.reduce((acc, term) => acc + (t.includes(term) ? 1 : 0), 0);
}

export default function SearchOverlay({
  open,
  onClose,
  puppies = [],
  posts = [],
  topOffset = 108,
}) {
  const [query, setQuery] = useState('');
  const inputRef = useRef(null);

  useEffect(() => {
    if (open) {
      setQuery('');
      setTimeout(() => inputRef.current?.focus(), 80);
    }
  }, [open]);

  useEffect(() => {
    const onKey = (e) => {
      if (e.key === 'Escape') onClose();
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [onClose]);

  const q = normalise(query);
  const terms = q ? expandQuery(q) : [];

  const matchedPuppies = q
    ? puppies
        .map((p) => ({
          ...p,
          _score:
            scoreText(p.name, terms) * 4 +
            scoreText(p.breed, terms) * 2 +
            scoreText(p.sex, terms) * 3 +
            scoreText(p.status, terms) * 2 +
            scoreText(p.color, terms) * 2 +
            scoreText(p.description, terms),
        }))
        .filter((p) => p._score > 0)
        .sort((a, b) => b._score - a._score)
    : [];

  const matchedPosts = q
    ? posts
        .map((p) => ({
          ...p,
          _score:
            scoreText(p.title, terms) * 3 +
            scoreText(p.category, terms) * 2 +
            scoreText(p.excerpt, terms),
        }))
        .filter((p) => p._score > 0)
        .sort((a, b) => b._score - a._score)
    : [];

  const matchedPages = q
    ? STATIC_PAGES.filter(
        (p) =>
          matches(p.title, terms) ||
          matches(p.description, terms) ||
          matches(p.tags, terms)
      )
    : [];

  const hasResults =
    matchedPuppies.length > 0 || matchedPosts.length > 0 || matchedPages.length > 0;

  function handleSubmit(e) {
    e.preventDefault();
    if (!q) return;
    onClose();
    router.visit(`/puppies?q=${encodeURIComponent(query.trim())}`);
  }

  if (!open) return null;

  return (
    <div className="search-overlay" role="dialog" aria-modal="true" aria-label="Site Search">
      <div className="search-overlay-backdrop" onClick={onClose} />
      <div
        className="search-draw-drawer"
        style={{ top: `${topOffset}px` }}
      >
        {/* Clean, minimalist search bar */}
        <div className="search-draw-header">
          <div className="search-draw-header-inner">
            <form className="search-draw-bar" onSubmit={handleSubmit}>
              <svg
                className="search-draw-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
              >
                <circle cx="11" cy="11" r="7" />
                <path d="M21 21l-4.3-4.3" />
              </svg>
              <input
                ref={inputRef}
                className="search-draw-input"
                type="text"
                placeholder="Search puppies, pedigree, care, blog, or anything on Riches Corsos…"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                autoComplete="off"
              />
              {query && (
                <button
                  type="button"
                  className="search-draw-clear"
                  onClick={() => setQuery('')}
                  aria-label="Clear query"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" width="18" height="18">
                    <path d="M18 6L6 18M6 6l12 12" />
                  </svg>
                </button>
              )}
              <button
                type="button"
                className="search-draw-close-btn"
                onClick={onClose}
                aria-label="Close search"
              >
                <span>Close</span>
                <kbd>ESC</kbd>
              </button>
            </form>
          </div>
        </div>

        {/* Blank clean body — only displays matched results when user types */}
        <div className="search-draw-body">
          <div className="search-draw-content-wrap">
            {q && !hasResults && (
              <div className="search-no-results-box">
                <p>No results found for "<strong>{query}</strong>"</p>
              </div>
            )}

            {q && hasResults && (
              <div className="search-results-canvas">
                {matchedPuppies.length > 0 && (
                  <div className="search-canvas-section">
                    <div className="search-canvas-header">
                      <h4>Puppies <span>({matchedPuppies.length})</span></h4>
                      <Link href={`/puppies?q=${encodeURIComponent(query)}`} onClick={onClose}>
                        View All In Store →
                      </Link>
                    </div>
                    <div className="search-puppies-list">
                      {matchedPuppies.map((puppy) => (
                        <Link
                          key={puppy.id}
                          href={`/puppies/${puppy.slug}`}
                          className="search-puppy-item"
                          onClick={onClose}
                        >
                          <div className="search-puppy-thumb">
                            {puppy.images?.[0] ? (
                              <img src={`/storage/${puppy.images[0].path}`} alt={puppy.name} />
                            ) : (
                              <div className="search-puppy-placeholder">🐾</div>
                            )}
                          </div>
                          <div className="search-puppy-details">
                            <div className="search-puppy-title-row">
                              <span className="search-puppy-name">{puppy.name}</span>
                              <span className={`search-puppy-status status--${puppy.status}`}>{puppy.status}</span>
                            </div>
                            <p className="search-puppy-specs">
                              {puppy.breed} · {puppy.sex} {puppy.color ? `· ${puppy.color}` : ''}
                            </p>
                            <span className="search-puppy-price">${Number(puppy.price).toLocaleString()}</span>
                          </div>
                        </Link>
                      ))}
                    </div>
                  </div>
                )}

                {matchedPosts.length > 0 && (
                  <div className="search-canvas-section">
                    <div className="search-canvas-header">
                      <h4>Articles &amp; Blog <span>({matchedPosts.length})</span></h4>
                      <Link href="/blog" onClick={onClose}>
                        All Articles →
                      </Link>
                    </div>
                    <div className="search-posts-list">
                      {matchedPosts.map((post) => (
                        <Link
                          key={post.id}
                          href={`/blog/${post.slug}`}
                          className="search-post-item"
                          onClick={onClose}
                        >
                          {post.cover_image && (
                            <div className="search-post-thumb">
                              <img src={`/storage/${post.cover_image}`} alt={post.title} />
                            </div>
                          )}
                          <div className="search-post-body">
                            {post.category && <span className="search-post-tag">{post.category}</span>}
                            <h5 className="search-post-title">{post.title}</h5>
                            {post.excerpt && <p className="search-post-excerpt">{post.excerpt}</p>}
                          </div>
                        </Link>
                      ))}
                    </div>
                  </div>
                )}

                {matchedPages.length > 0 && (
                  <div className="search-canvas-section">
                    <div className="search-canvas-header">
                      <h4>Pages <span>({matchedPages.length})</span></h4>
                    </div>
                    <div className="search-pages-list">
                      {matchedPages.map((page) => (
                        <Link
                          key={page.id}
                          href={page.href}
                          className="search-page-item"
                          onClick={onClose}
                        >
                          <div className="search-page-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
                              <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                              <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" />
                            </svg>
                          </div>
                          <div className="search-page-content">
                            <h5 className="search-page-title">{page.title}</h5>
                            <p className="search-page-desc">{page.description}</p>
                          </div>
                        </Link>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
