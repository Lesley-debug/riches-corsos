import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function About() {
  return (
    <SiteLayout>
      <Head title="About Us — Riches Corsos" />

      <section className="hero" style={{ paddingBottom: 40 }}>
        <div>
          <h1>A small program, built on doing this the right way.</h1>
          <p>
            Riches Corsos started with one goal: raise Cane Corsos the way we'd want to bring one home ourselves —
            health-tested parents, real socialization, and a breeder who's still reachable years after pickup day.
          </p>
        </div>
        <div className="hero-image placeholder" data-label="Photo of the breeder with the dogs" />
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

      <section className="section" style={{ maxWidth: 760 }}>
        <h2 style={{ fontSize: 28, marginBottom: 20 }}>Our approach</h2>
        <p style={{ color: 'var(--ink)', lineHeight: 1.8, marginBottom: 20 }}>
          Every litter starts with two health-tested parents — hips, hearts, and genetic panels cleared before
          breeding is ever considered. Puppies are whelped and raised inside our home, not in a kennel building, so
          they grow up around everyday noise, people, and handling from day one.
        </p>
        <p style={{ color: 'var(--ink)', lineHeight: 1.8, marginBottom: 20 }}>
          By the time a puppy is ready to go home, they've already had exposure to leashes, crates, car rides, and
          basic commands. We're not trying to hand you a finished dog — we're trying to hand you a confident
          foundation, and stay available for whatever comes up after that.
        </p>
      </section>

      <div className="cta-band">
        <h2>Have a question before reaching out?</h2>
        <Link href="/faqs" className="btn-solid">
          Read Our FAQs
        </Link>
      </div>
    </SiteLayout>
  );
}
