import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';
import WhatMakesUsSpecial from '@/Components/WhatMakesUsSpecial';

function Stars({ count = 5 }) {
    return (
        <div className="star-row">
            {Array.from({ length: count }).map((_, i) => (
                <svg key={i} viewBox="0 0 24 24" fill="#F5A623" width="16" height="16">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
            ))}
        </div>
    );
}

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

const ALL_TESTIMONIALS = [
    {
        author: 'Zoey Wilson',
        location: 'Austin, TX',
        rating: 5,
        text: 'Incredibly flexible with scheduling and so accommodating. The whole process was smooth from start to finish. Couldn\'t ask for a better experience! Our puppy settled in within days and has been an absolute joy.',
    },
    {
        author: 'Leilya Thao',
        location: 'Dallas, TX',
        rating: 5,
        text: 'We were greeted warmly the moment we arrived. The owners are knowledgeable, caring, and genuinely passionate about their puppies. Highly recommend! You can tell every dog there is loved.',
    },
    {
        author: 'Bobbie Platt',
        location: 'Houston, TX',
        rating: 5,
        text: 'Picking up our puppy was such a joy. You could tell every dog there is loved and well cared for. Our Corso has been an absolute blessing to our family. The transition home was smoother than we expected.',
    },
    {
        author: 'Jason Poplin',
        location: 'San Antonio, TX',
        rating: 5,
        text: 'Worth every mile of the drive. The facilities were spotless and the puppies were healthy, happy, and well-socialised. We will definitely be back for our next Corso!',
    },
    {
        author: 'Marcus & Diane Reeves',
        location: 'Oklahoma City, OK',
        rating: 5,
        text: 'We had been searching for a reputable Cane Corso breeder for over a year. RICHES CORSOS was everything we hoped for. The communication throughout the process was excellent — every question answered promptly and honestly. Our boy is now 18 months old and is everything they said he would be.',
    },
    {
        author: 'Tanya Okafor',
        location: 'Memphis, TN',
        rating: 5,
        text: 'I was nervous about the whole process as a first-time Corso owner. The team at RICHES CORSOS walked me through everything — what to expect, how to prepare my home, what to feed her. Six months in and I feel completely supported. I still reach out with questions and always get a helpful response.',
    },
    {
        author: 'Chris & Amber Holloway',
        location: 'Nashville, TN',
        rating: 5,
        text: 'Our puppy came home confident, curious, and already comfortable with basic handling. You could tell she had been raised with real care and attention. She bonded with our kids immediately. The whole family is obsessed with her.',
    },
    {
        author: 'Darnell Washington',
        location: 'Atlanta, GA',
        rating: 5,
        text: 'I\'ve had dogs my whole life but never a Cane Corso. RICHES CORSOS helped me understand the breed properly before I even committed. That honesty and transparency made all the difference. My Corso is now two years old and is the most loyal, well-tempered dog I\'ve ever owned.',
    },
    {
        author: 'Priya Nair',
        location: 'Phoenix, AZ',
        rating: 5,
        text: 'The health documentation provided was thorough and professional. Our vet was impressed. The puppy was clearly well-socialised — calm at the vet, comfortable with strangers, and already responding to basic commands. RICHES CORSOS clearly puts in the work before puppies leave.',
    },
    {
        author: 'Kevin & Stacey Morales',
        location: 'Fort Worth, TX',
        rating: 5,
        text: 'We were on the waitlist for a few months and it was absolutely worth the wait. The communication during that time was great — we were kept informed and never felt forgotten. When our puppy finally came home, it was clear she had been raised with love. She fit into our family like she had always been here.',
    },
    {
        author: 'Brittany Simmons',
        location: 'Baton Rouge, LA',
        rating: 5,
        text: 'I cannot say enough good things. From the first inquiry to the day we picked up our puppy, every interaction was warm, professional, and reassuring. Our Corso is now eight months old and is the most gentle, affectionate dog. He is wonderful with our toddler. RICHES CORSOS truly cares about where their puppies go.',
    },
];

export default function Testimonials() {
    return (
        <SiteLayout>
            <Head title="Testimonials — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="What Our Customers Are Saying"
                sub="Real experiences from families who have welcomed a RICHES CORSOS companion into their homes."
            />

            <div className="home-page">

                {/* Google Reviews Badge */}
                <div className="home-section-wrap" style={{ paddingTop: 48 }}>
                    <div className="card-3d">
                        <div className="google-badge" style={{ marginBottom: 0 }}>
                            <svg viewBox="0 0 24 24" width="28" height="28">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05" />
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                            </svg>
                            <div>
                                <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                                    <span style={{ fontWeight: 700, fontSize: 16 }}>Google Reviews</span>
                                    <Stars />
                                </div>
                                <p style={{ fontSize: 13, color: 'var(--stone)', marginTop: 2 }}>5.0 average · {ALL_TESTIMONIALS.length}+ verified reviews</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Testimonials Grid */}
                <SectionTitle title="Customer Stories" sub="Every review below is from a real family who brought a RICHES CORSOS puppy home." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="testimonials-masonry">
                            {ALL_TESTIMONIALS.map((t) => (
                                <div className="t-card-full" key={t.author}>
                                    <Stars count={t.rating} />
                                    <p>"{t.text}"</p>
                                    <DiamondDivider />
                                    <div className="t-card-author">
                                        <cite>{t.author}</cite>
                                        {t.location && <span className="t-card-location">{t.location}</span>}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* What Makes Us Special */}
                <WhatMakesUsSpecial />

                {/* CTA */}
                <div className="home-section-wrap" style={{ paddingBottom: 48 }}>
                    <div className="card-3d" style={{ textAlign: 'center' }}>
                        <h2 style={{ fontSize: 26, marginBottom: 12 }}>Ready to become part of the RICHES CORSOS family?</h2>
                        <p style={{ color: 'var(--stone)', marginBottom: 28, maxWidth: 480, margin: '0 auto 28px' }}>
                            Join over 140 families who have welcomed a RICHES CORSOS puppy into their home.
                        </p>
                        <div style={{ display: 'flex', gap: 14, justifyContent: 'center', flexWrap: 'wrap' }}>
                            <Link href="/puppies" className="btn-solid">View Available Puppies</Link>
                            <Link href="/contact" className="btn-outline">Contact Us</Link>
                        </div>
                    </div>
                </div>

                <div style={{ height: 48 }} />
            </div>
        </SiteLayout>
    );
}
