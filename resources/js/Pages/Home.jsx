import { Head, Link } from "@inertiajs/react";
import SiteLayout from "@/Layouts/SiteLayout";
import PuppyCard from "@/Components/PuppyCard";

const WHY_ITEMS = [
    {
        title: "Health tested",
        body: "Hips, hearts, and genetic panels cleared on both parents before any litter is planned.",
        path: (
            <path
                d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z"
                stroke="currentColor"
                strokeWidth="1.6"
                fill="none"
            />
        ),
    },
    {
        title: "Home raised",
        body: "Every litter grows up inside our house, not a kennel — under feet, around noise, near people.",
        path: (
            <path
                d="M3 12l9-8 9 8M5 10v10h14V10"
                stroke="currentColor"
                strokeWidth="1.6"
                fill="none"
            />
        ),
    },
    {
        title: "Early foundations",
        body: "Basic commands, leash exposure, and confident handling begin well before eight weeks.",
        path: (
            <path
                d="M4 4v16h16M4 15l5-5 4 4 7-7"
                stroke="currentColor"
                strokeWidth="1.6"
                fill="none"
            />
        ),
    },
    {
        title: "Support that continues",
        body: "Questions at year one get the same answer as questions on day one. We stay reachable.",
        path: (
            <>
                <circle
                    cx="12"
                    cy="12"
                    r="9"
                    stroke="currentColor"
                    strokeWidth="1.6"
                    fill="none"
                />
                <path
                    d="M12 7v5l3 3"
                    stroke="currentColor"
                    strokeWidth="1.6"
                    fill="none"
                />
            </>
        ),
    },
];

export default function Home({
    featuredPuppies = [],
    recentPosts = [],
    testimonials = [],
    heroImage = null,
    homecomingPhotos = [],
}) {
    return (
        <SiteLayout>
            <Head title="Riches Corsos — Cane Corso Puppies" />

            <section className="hero">
                <div>
                    <h1>
                        Cane Corso puppies, raised for power and gentleness
                        alike.
                    </h1>
                    <p>
                        A small breeding program built on health testing, early
                        socialization, and honest guidance — from the day you
                        inquire to years after you bring your puppy home.
                    </p>
                    <div className="hero-actions">
                        <Link href="/contact" className="btn-outline">
                            Get In Touch With Us
                        </Link>
                        <Link href="/puppies" className="btn-solid">
                            Available Puppies
                        </Link>
                    </div>
                </div>
                <div
                    className={`hero-image ${heroImage ? "" : "placeholder"}`}
                    data-label="Clean, bright photo of one puppy"
                >
                    {heroImage && (
                        <img
                            src={`/storage/${heroImage}`}
                            alt="Riches Corsos"
                        />
                    )}
                </div>
            </section>

            <div className="trust-strip">
                <div className="trust-grid">
                    <div className="trust-item">
                        <span>140+</span>Families placed
                    </div>
                    <div className="trust-item">
                        <span>9 yrs</span>Breeding experience
                    </div>
                    <div className="trust-item">
                        <span>100%</span>Health guaranteed
                    </div>
                    <div className="trust-item">
                        <span>5.0</span>Average rating
                    </div>
                </div>
            </div>

            <section className="section">
                <div className="section-head">
                    <h2>Puppies available now</h2>
                    <p>
                        Each one health-tested, vaccinated, and raised in-home
                        before they meet you.
                    </p>
                </div>
                <div className="puppy-grid">
                    {featuredPuppies.length > 0 ? (
                        featuredPuppies.map((puppy) => (
                            <PuppyCard key={puppy.id} puppy={puppy} />
                        ))
                    ) : (
                        <p
                            style={{
                                color: "var(--stone)",
                                gridColumn: "1 / -1",
                                textAlign: "center",
                            }}
                        >
                            No puppies listed right now — check back soon.
                        </p>
                    )}
                </div>
                <div className="section-cta">
                    <Link href="/puppies" className="btn-outline">
                        View All Available Puppies
                    </Link>
                </div>
            </section>

            <div className="why">
                <div className="why-inner">
                    <div className="section-head">
                        <h2>What raising them this way means</h2>
                        <p>
                            Not marketing language — the specific things we do
                            before a puppy ever meets their family.
                        </p>
                    </div>
                    <div className="why-grid">
                        {WHY_ITEMS.map((item) => (
                            <div className="why-item" key={item.title}>
                                <div className="why-icon">
                                    <svg viewBox="0 0 24 24">{item.path}</svg>
                                </div>
                                <h3>{item.title}</h3>
                                <p>{item.body}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <section className="section">
                <div className="section-head">
                    <h2>From the blog</h2>
                    <p>
                        Care guides, training tips, and stories from families
                        who've brought a Corso home.
                    </p>
                </div>
                <div className="blog-grid">
                    {recentPosts.length > 0 ? (
                        recentPosts.map((post) => (
                            <Link
                                href={`/blog/${post.slug}`}
                                key={post.id}
                                className="blog-card"
                            >
                                <div
                                    className={`blog-photo ${post.cover_image ? "" : "placeholder"}`}
                                >
                                    {post.cover_image && (
                                        <img
                                            src={`/storage/${post.cover_image}`}
                                            alt={post.title}
                                        />
                                    )}
                                </div>
                                <div className="blog-info">
                                    {post.category && (
                                        <span className="blog-tag">
                                            {post.category}
                                        </span>
                                    )}
                                    <h3>{post.title}</h3>
                                    <p>{post.excerpt}</p>
                                </div>
                            </Link>
                        ))
                    ) : (
                        <p
                            style={{
                                color: "var(--stone)",
                                gridColumn: "1 / -1",
                                textAlign: "center",
                            }}
                        >
                            No posts published yet.
                        </p>
                    )}
                </div>
                <div className="section-cta">
                    <Link href="/blog" className="btn-outline">
                        View All Articles
                    </Link>
                </div>
        </section>
        
        

            <div className="homecomings">
                <div className="section-head">
                    <h2>Recent homecomings</h2>
                    <p>
                        A few of the families who picked up their puppy this
                        year.
                    </p>
                </div>
                <div className="homecomings-track">
                    {homecomingPhotos.length > 0
                        ? homecomingPhotos.map((photo) => (
                              <div className="homecoming-photo" key={photo.id}>
                                  <img
                                      src={`/storage/${photo.image}`}
                                      alt={
                                          photo.caption ||
                                          "Family with their puppy"
                                      }
                                  />
                              </div>
                          ))
                        : Array.from({ length: 6 }).map((_, i) => (
                              <div
                                  className="homecoming-photo placeholder"
                                  key={i}
                              />
                          ))}
                </div>
            </div>

            {testimonials.length > 0 && (
                <div className="testimonial-band">
                    <div className="section-head">
                        <h2>What our families say</h2>
                        <p>
                            A consistent 5-star rating, built one placement at a
                            time.
                        </p>
                    </div>
                    <div className="testimonial-grid">
                        {testimonials.map((t) => (
                            <div className="t-card" key={t.id}>
                                <div className="stars">
                                    {"★".repeat(t.rating)}
                                </div>
                                <p>&ldquo;{t.content}&rdquo;</p>
                                {t.author_name}
                            </div>
                        ))}
                    </div>
                </div>
            )}

            <div className="cta-band">
                <h2>Ready to meet your Corso?</h2>
                <Link href="/puppies" className="btn-solid">
                    View Available Puppies
                </Link>
            </div>
        </SiteLayout>
    );
}
