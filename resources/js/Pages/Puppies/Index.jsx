import { useMemo, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PuppyCard from '@/Components/PuppyCard';

const STATUS_OPTIONS = [
    { value: 'all', label: 'All Status' },
    { value: 'available', label: 'Available' },
    { value: 'reserved', label: 'Reserved' },
    { value: 'pending', label: 'Pending' },
];

const GENDER_OPTIONS = [
    { value: 'all', label: 'All Sexes' },
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
];

function normalize(value) {
    return String(value ?? '').toLowerCase();
}

function statusCount(puppies, status) {
    return puppies.filter((puppy) => puppy.status === status).length;
}

export default function PuppiesIndex({ puppies = [] }) {
    const [search, setSearch] = useState('');
    const [gender, setGender] = useState('all');
    const [availability, setAvailability] = useState('all');
    const [sort, setSort] = useState('newest');

    const stats = useMemo(() => ({
        available: statusCount(puppies, 'available'),
        reserved: statusCount(puppies, 'reserved'),
        pending: statusCount(puppies, 'pending'),
    }), [puppies]);

    const filtered = useMemo(() => {
        const query = normalize(search.trim());

        return [...puppies]
            .filter((puppy) => {
                const matchesSearch = !query || [
                    puppy.name,
                    puppy.breed,
                    puppy.color,
                    puppy.description,
                ].some((value) => normalize(value).includes(query));

                const matchesGender = gender === 'all' || puppy.sex === gender;
                const matchesStatus = availability === 'all' || puppy.status === availability;

                return matchesSearch && matchesGender && matchesStatus;
            })
            .sort((a, b) => {
                if (sort === 'name') {
                    return a.name.localeCompare(b.name);
                }

                if (sort === 'price') {
                    return Number(a.price ?? 0) - Number(b.price ?? 0);
                }

                if (sort === 'age') {
                    return (a.age_in_weeks ?? 0) - (b.age_in_weeks ?? 0);
                }

                return 0;
            });
    }, [puppies, search, gender, availability, sort]);

    const hasFilters = search.trim() || gender !== 'all' || availability !== 'all' || sort !== 'newest';

    const resetFilters = () => {
        setSearch('');
        setGender('all');
        setAvailability('all');
        setSort('newest');
    };

    return (
        <SiteLayout>
            <Head title="Available Puppies | Riches Corsos" />

            <div className="shop-page">
                <section className="shop-shell" id="puppies">
                    <div className="shop-page-head">
                        <div>
                            <p className="shop-eyebrow">Riches Corsos</p>
                            <h1>Available Puppies</h1>
                            <p className="shop-page-desc">
                                Browse current Cane Corso puppies with updated photos, availability, pricing, and profile details.
                            </p>
                        </div>
                        <div className="shop-head-actions">
                            <Link href="/contact" className="btn-solid">Join The Waitlist</Link>
                            <Link href="/faqs#availability" className="btn-outline">Adoption FAQs</Link>
                        </div>
                    </div>

                    <div className="shop-status-strip" aria-label="Puppy availability summary">
                        <div>
                            <span>{puppies.length}</span>
                            Total Puppies
                        </div>
                        <div>
                            <span>{stats.available}</span>
                            Available Now
                        </div>
                        <div>
                            <span>{stats.reserved}</span>
                            Reserved
                        </div>
                        <div>
                            <span>{stats.pending}</span>
                            Pending
                        </div>
                    </div>

                    <div className="shop-toolbar">
                        <div className="shop-search-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" width="17" height="17" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" />
                                <path d="M21 21l-4.3-4.3" />
                            </svg>
                            <input
                                className="shop-search"
                                placeholder="Search name, breed, color..."
                                value={search}
                                onChange={(event) => setSearch(event.target.value)}
                                aria-label="Search puppies"
                            />
                        </div>
                        <div className="shop-filters">
                            <select className="shop-select" value={gender} onChange={(event) => setGender(event.target.value)} aria-label="Filter by sex">
                                {GENDER_OPTIONS.map((option) => (
                                    <option key={option.value} value={option.value}>{option.label}</option>
                                ))}
                            </select>
                            <select className="shop-select" value={availability} onChange={(event) => setAvailability(event.target.value)} aria-label="Filter by status">
                                {STATUS_OPTIONS.map((option) => (
                                    <option key={option.value} value={option.value}>{option.label}</option>
                                ))}
                            </select>
                            <select className="shop-select" value={sort} onChange={(event) => setSort(event.target.value)} aria-label="Sort puppies">
                                <option value="newest">Newest First</option>
                                <option value="name">Name A-Z</option>
                                <option value="age">Youngest First</option>
                                <option value="price">Lowest Price</option>
                            </select>
                            {hasFilters && (
                                <button type="button" className="shop-reset-btn" onClick={resetFilters}>
                                    Clear
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="shop-result-row">
                        <span>
                            Showing {filtered.length} {filtered.length === 1 ? 'puppy' : 'puppies'}
                        </span>
                    </div>

                    {filtered.length > 0 ? (
                        <div className="puppy-grid shop-puppy-grid">
                            {filtered.map((puppy) => <PuppyCard key={puppy.id} puppy={puppy} />)}
                        </div>
                    ) : (
                        <div className="shop-empty">
                            <div className="shop-empty-img">
                                <picture>
                                    <source srcSet="/images/aboutsite.webp" type="image/webp" />
                                    <img src="/images/aboutsite.png" alt="Riches Corsos" />
                                </picture>
                            </div>
                            <div className="shop-empty-content">
                                <h2>No Puppies Match Those Filters</h2>
                                <p>Clear the filters or contact us about upcoming litters and waitlist availability.</p>
                                <div className="shop-empty-actions">
                                    <button type="button" className="btn-solid" onClick={resetFilters}>Clear Filters</button>
                                    <Link href="/contact" className="btn-outline">Contact Us</Link>
                                </div>
                            </div>
                        </div>
                    )}
                </section>

                <section className="shop-guidance">
                    <div className="shop-guidance-inner">
                        <div>
                            <h2>Looking For A Specific Match?</h2>
                            <p>Tell us your timing, household, and preferred temperament so we can help you choose with confidence.</p>
                        </div>
                        <Link href="/contact" className="btn-solid">Talk To Us</Link>
                    </div>
                </section>
            </div>
        </SiteLayout>
    );
}
