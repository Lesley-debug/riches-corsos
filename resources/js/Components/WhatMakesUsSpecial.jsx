function FeatureCard({ icon, title, body }) {
    return (
        <div className="feature-card">
            <div className="feature-card-icon">{icon}</div>
            <h3>{title}</h3>
            <p>{body}</p>
        </div>
    );
}

const FEATURES = [
    {
        title: 'Health First',
        body: 'Every breeding pair undergoes thorough health screening — hips, hearts, and genetic panels — before any litter is planned. We make informed decisions so each puppy starts life with the strongest possible foundation.',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                <path d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z" />
            </svg>
        ),
    },
    {
        title: 'Home Raised',
        body: 'Our puppies grow up inside our home — not in a kennel. They experience everyday household life from the very beginning: people, sounds, different surfaces, and consistent human interaction.',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                <path d="M3 12l9-8 9 8M5 10v10h14V10" />
            </svg>
        ),
    },
    {
        title: 'Early Socialization',
        body: 'Puppies are gradually introduced to handling, people, sounds, routines, and new experiences from an early age. This builds the confident, stable temperament the Cane Corso is known for.',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                <circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                <path d="M16 3.13a4 4 0 010 7.75M21 21v-2a4 4 0 00-3-3.87" />
            </svg>
        ),
    },
    {
        title: 'Thoughtful Breeding',
        body: 'Every breeding decision prioritises health, temperament, structure, and responsible practices. We take on a limited number of litters each year so that quality is never compromised.',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
        ),
    },
    {
        title: 'Individual Attention',
        body: 'Because we limit our litters, every puppy receives genuine individual attention and care. We know each puppy\'s personality, preferences, and development before they ever meet their new family.',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                <circle cx="12" cy="8" r="4" /><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" />
            </svg>
        ),
    },
    {
        title: 'Lifetime Support',
        body: 'Our relationship with a puppy\'s family does not end at pickup. Whether you have a question on day one or two years later, we remain available. Every family deserves ongoing guidance and support.',
        icon: (
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" />
            </svg>
        ),
    },
];

export default function WhatMakesUsSpecial() {
    return (
        <div className="wmus-section">
            <div className="sec-title-wrap">
                <div className="sec-title-plaque">
                    <h2 className="sec-title-text">What Makes Us Special</h2>
                </div>
                <p className="sec-title-sub">Six reasons families choose RICHES CORSOS — and come back to tell others.</p>
            </div>
            <div className="wmus-grid">
                {FEATURES.map((f) => (
                    <FeatureCard key={f.title} {...f} />
                ))}
            </div>
        </div>
    );
}
