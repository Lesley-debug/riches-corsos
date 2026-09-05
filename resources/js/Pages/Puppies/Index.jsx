import { useState, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';
import PuppyCard from '@/Components/PuppyCard';

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

const CHECKLIST = [
    'Puppy-safe space set up',
    'Food & water bowls ready',
    'Comfortable bedding',
    'Age-appropriate toys',
    'Grooming supplies',
    'Secure crate or carrier',
    'Veterinary appointment booked',
    'Training plan in place',
];

export default function PuppiesIndex({ puppies = [] }) {
    const [search, setSearch] = useState('');
    const [gender, setGender] = useState('all');
    const [availability, setAvailability] = useState('all');
    const [sort, setSort] = useState('newest');

    const filtered = useMemo(() => {
        let list = [...puppies];
        if (search.trim()) {
            const q = search.toLowerCase();
            list = list.filter((p) => p.name?.toLowerCase().includes(q) || p.breed?.toLowerCase().includes(q));
        }
        if (gender !== 'all') list = list.filter((p) => p.sex === gender);
        if (availability !== 'all') list = list.filter((p) => p.status === availability);
        if (sort === 'name') list.sort((a, b) => a.name.localeCompare(b.name));
        else if (sort === 'price') list.sort((a, b) => Number(a.price) - Number(b.price));
        else if (sort === 'age') list.sort((a, b) => (a.age_in_weeks ?? 0) - (b.age_in_weeks ?? 0));
        return list;
    }, [puppies, search, gender, availability, sort]);

    return (
        <SiteLayout>
            <Head title="Available Puppies — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="Available Puppies"
                sub="Find your next RICHES CORSOS companion. Every puppy is health-tested, home-raised, and placed with ongoing support."
                cta1={{ href: '#puppies', label: 'Browse Puppies' }}
            />

            <div className="home-page" id="puppies">

                {/* Shop Header */}
                <div className="home-section-wrap" style={{ paddingTop: 48 }}>
                    <div className="card-3d">
                        <div className="shop-header">
                            <div>
                                <h2 className="shop-header-title">Available Puppies</h2>
                                <p className="shop-header-sub">Every puppy is raised with care, attention, socialisation, and a focus on health and temperament.</p>
                            </div>
                            <span className="shop-count">{filtered.length} {filtered.length === 1 ? 'puppy' : 'puppies'}</span>
                        </div>

                        {/* Filters */}
                        <div className="shop-toolbar">
                            <div className="shop-search-wrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" width="16" height="16">
                                    <circle cx="11" cy="11" r="7" /><path d="M21 21l-4.3-4.3" />
                                </svg>
                                <input
                                    className="shop-search"
                                    placeholder="Search by name or breed…"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                            </div>
                            <div className="shop-filters">
                                <select className="shop-select" value={gender} onChange={(e) => setGender(e.target.value)}>
                                    <option value="all">All Genders</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <select className="shop-select" value={availability} onChange={(e) => setAvailability(e.target.value)}>
                                    <option value="all">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="pending">Pending</option>
                                </select>
                                <select className="shop-select" value={sort} onChange={(e) => setSort(e.target.value)}>
                                    <option value="newest">Newest First</option>
                                    <option value="name">Name A–Z</option>
                                    <option value="age">Age</option>
                                    <option value="price">Price</option>
                                </select>
                            </div>
                        </div>

                        {/* Puppy Grid */}
                        {filtered.length > 0 ? (
                            <div className="puppy-grid shop-puppy-grid">
                                {filtered.map((puppy) => <PuppyCard key={puppy.id} puppy={puppy} />)}
                            </div>
                        ) : (
                            <div className="shop-empty">
                                <div className="shop-empty-img">
                                    <picture>
                                        <source srcSet="/images/aboutsite.webp" type="image/webp" />
                                        <img src="/images/aboutsite.png" alt="RICHES CORSOS" />
                                    </picture>
                                </div>
                                <div className="shop-empty-content">
                                    <h3>No Puppies Available Right Now</h3>
                                    <p>Our next puppies may be on the way. Join our notification list or contact us to be among the first to hear when new puppies become available.</p>
                                    <div className="shop-empty-actions">
                                        <Link href="/contact" className="btn-solid">Contact Us</Link>
                                        <Link href="/contact" className="btn-outline">Join the Waitlist</Link>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* Preparation Checklist */}
                <SectionTitle title="Prepare For Your New Companion" sub="Before your puppy comes home, make sure you have everything ready." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="prep-grid">
                            <div className="prep-checklist">
                                {CHECKLIST.map((item) => (
                                    <div className="prep-item" key={item}>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" strokeWidth="2.2" strokeLinecap="round" width="18" height="18">
                                            <path d="M20 6L9 17l-5-5" />
                                        </svg>
                                        <span>{item}</span>
                                    </div>
                                ))}
                            </div>
                            <div className="prep-cta-col">
                                <h3>Need help preparing?</h3>
                                <p>Our FAQs cover everything from feeding and grooming to training and socialisation. We are also available directly if you have specific questions.</p>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: 12, marginTop: 20 }}>
                                    <Link href="/faqs" className="btn-solid">Read Our Puppy Preparation Guide</Link>
                                    <Link href="/contact" className="btn-outline">Ask Us Directly</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* Still Looking CTA */}
                <div className="home-section-wrap" style={{ paddingBottom: 48 }}>
                    <div className="card-3d shop-still-looking">
                        <h2>Your RICHES CORSOS Puppy Could Be Next</h2>
                        <p>If no puppy is currently available, contact us or join our waitlist. We will notify you as soon as a new litter is planned.</p>
                        <div className="shop-still-looking-btns">
                            <Link href="/contact" className="btn-solid">Contact Us</Link>
                            <Link href="/contact" className="btn-outline">Join The Waitlist</Link>
                        </div>
                    </div>
                </div>

                <div style={{ height: 16 }} />
            </div>
        </SiteLayout>
    );
}
