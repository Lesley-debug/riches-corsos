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
    id: 'about',
    title: 'About Us',
    href: '/about',
    description: 'Our story, our breeding program, health testing, and what makes Riches Corsos different.',
    tags: 'about breeder story program health tested cane corso',
  },
  {
    id: 'faqs',
    title: 'FAQs',
    href: '/faqs',
    description: 'Frequently asked questions about our puppies, the reservation process, and what to expect.',
    tags: 'faq questions help reservation process deposit payment',
  },
  {
    id: 'contact',
    title: 'Contact Us',
    href: '/contact',
    description: 'Get in touch — email, phone, or send us a message directly.',
    tags: 'contact email phone message inquiry reach us',
  },
  {
    id: 'puppies',
    title: 'Available Puppies',
    href: '/puppies',
    description: 'Browse all available and upcoming Cane Corso puppies.',
    tags: 'puppies available for sale reserve cane corso vaccinated health tested',
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
    // partial match — e.g. "vacc" should still expand
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

export default function SearchOverlay({ open, onClose, puppies = [], posts = [] }) {
  const [query, setQuery] = useState('');
  const inputRef = useRef(null);

  useEffect(() => {
    if (open) {
      setQuery('');
      setTimeout(() => inputRef.current?.focus(), 60);
    }
  }, [open]);

  useEffect(() => {
    const onKey = (e) => { if (e.key === 'Escape') onClose(); };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [onClose]);

  const q = normalise(query);
  const terms = q ? expandQuery(q) : [];

  const matchedPuppies = q
    ? puppies
        .map((p) => ({
          ...p,
          _score: scoreText(p.name, terms) * 3
            + scoreText(p.breed, terms) * 2
            + scoreText(p.sex, terms) * 2
            + scoreText(p.status, terms)
            + scoreText(p.description, terms),
        }))
        .filter((p) => p._score > 0)
        .sort((a, b) => b._score - a._score)
    : [];

  const matchedPosts = q
    ? posts
        .map((p) => ({
          ...p,
          _score: scoreText(p.title, terms) * 3
            + scoreText(p.category, terms) * 2
            + scoreText(p.excerpt, terms),
        }))
        .filter((p) => p._score > 0)
        .sort((a, b) => b._score - a._score)
    : [];

  const matchedPages = q
    ? STATIC_PAGES.filter((p) =>
        matches(p.title, terms) ||
        matches(p.description, terms) ||
        matches(p.tags, terms),
      )
    : [];

  const hasResults = matchedPuppies.length > 0 || matchedPosts.length > 0 || matchedPages.length > 0;

  function handleSubmit(e) {
    e.preventDefault();
    if (!q) return;
    onClose();
    router.visit(`/puppies?q=${encodeURIComponent(query.trim())}`);
  }

  if (!open) return null;

  return (
    <div className="search-overlay" role="dialog" aria-modal="true" aria-label="Search">
      <div className="search-overlay-backdrop" onClick={onClose} />
      <div className="search-overlay-panel">
        <form className="search-bar-wrap" onSubmit={handleSubmit}>
          <svg className="search-bar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round">
            <circle cx="11" cy="11" r="7" />
            <path d="M21 21l-4.3-4.3" />
          </svg>
          <input
            ref={inputRef}
            className="search-bar-input"
            type="text"
            placeholder="Search puppies, blog, vaccinated, health tested…"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            autoComplete="off"
          />
          {query && (
            <button type="button" className="search-bar-clear" onClick={() => setQuery('')} aria-label="Clear">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" width="16" height="16">
                <path d="M18 6L6 18M6 6l12 12" />
              </svg>
            </button>
          )}
          <button type="button" className="search-close-btn" onClick={onClose}>Cancel</button>
        </form>

        <div className="search-results">
          {!q && (
            <div className="search-hint">
              <p>Search puppies, blog posts, or anything on the site.</p>
              <div className="search-quick-links">
                <Link href="/puppies" onClick={onClose} className="search-quick-chip">Available Puppies</Link>
                <Link href="/puppies?q=vaccinated" onClick={onClose} className="search-quick-chip">Vaccinated</Link>
                <Link href="/puppies?q=male" onClick={onClose} className="search-quick-chip">Male Puppies</Link>
                <Link href="/puppies?q=female" onClick={onClose} className="search-quick-chip">Female Puppies</Link>
                <Link href="/blog" onClick={onClose} className="search-quick-chip">Blog</Link>
                <Link href="/faqs" onClick={onClose} className="search-quick-chip">FAQs</Link>
                <Link href="/contact" onClick={onClose} className="search-quick-chip">Contact</Link>
              </div>
            </div>
          )}

          {q && !hasResults && (
            <div className="search-no-results">
              <p>No results for "<strong>{query}</strong>"</p>
              <p className="search-no-results-hint">Try "vaccinated", "male", "health tested", or browse <Link href="/puppies" onClick={onClose}>all puppies</Link>.</p>
            </div>
          )}

          {matchedPuppies.length > 0 && (
            <div className="search-group">
              <div className="search-group-label">Puppies</div>
              {matchedPuppies.map((puppy) => (
                <Link key={puppy.id} href={`/puppies/${puppy.slug}`} className="search-result-row" onClick={onClose}>
                  <div className="search-result-thumb">
                    {puppy.images?.[0]
                      ? <img src={`/storage/${puppy.images[0].path}`} alt={puppy.name} />
                      : <span />}
                  </div>
                  <div className="search-result-body">
                    <div className="search-result-title">{puppy.name}</div>
                    <div className="search-result-sub">{puppy.breed} · {puppy.sex} · {puppy.status}</div>
                  </div>
                  <div className="search-result-price">${Number(puppy.price).toLocaleString()}</div>
                </Link>
              ))}
            </div>
          )}

          {matchedPosts.length > 0 && (
            <div className="search-group">
              <div className="search-group-label">Blog</div>
              {matchedPosts.map((post) => (
                <Link key={post.id} href={`/blog/${post.slug}`} className="search-result-row" onClick={onClose}>
                  <div className="search-result-thumb search-result-thumb--blog">
                    {post.cover_image
                      ? <img src={`/storage/${post.cover_image}`} alt={post.title} />
                      : <span />}
                  </div>
                  <div className="search-result-body">
                    <div className="search-result-title">{post.title}</div>
                    <div className="search-result-sub">{post.category || 'Article'}</div>
                  </div>
                </Link>
              ))}
            </div>
          )}

          {matchedPages.length > 0 && (
            <div className="search-group">
              <div className="search-group-label">Pages</div>
              {matchedPages.map((page) => (
                <Link key={page.id} href={page.href} className="search-result-row" onClick={onClose}>
                  <div className="search-result-thumb search-result-thumb--page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                      <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                      <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" />
                    </svg>
                  </div>
                  <div className="search-result-body">
                    <div className="search-result-title">{page.title}</div>
                    <div className="search-result-sub">{page.description}</div>
                  </div>
                </Link>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
