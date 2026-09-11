import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';
import WhatMakesUsSpecial from '@/Components/WhatMakesUsSpecial';
import StoryGallery from '@/Components/StoryGallery';

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

function SectionTitle({ number, title, sub }) {
    return (
        <div className="sec-title-wrap">
            <div className="sec-title-plaque">
                {number && <span className="sec-title-num">{number}</span>}
                <h2 className="sec-title-text">{title}</h2>
            </div>
            {sub && <p className="sec-title-sub">{sub}</p>}
        </div>
    );
}

export default function About() {
    return (
        <SiteLayout>
            <Head title="Our Story — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="RICHES CORSOS: Our Story"
                sub="Meet the people, values, environment, and philosophy behind RICHES CORSOS — a breeding program built on doing things the right way."
                cta1={{ href: '/puppies', label: 'Meet Our Puppies' }}
                cta2={{ href: '/contact', label: 'Learn More About Us' }}
            />

            <div className="home-page" id="our-story">

                {/* ===== OUR STORY ===== */}
                <SectionTitle number="01" title="RICHES CORSOS: Our Story" />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="story-grid">
                            <div className="story-text-col">
                                <p className="story-subtitle">WE ARE A TEAM OF DEDICATED PET LOVERS, CARETAKERS, AND TRAINERS.</p>
                                <p>RICHES CORSOS was founded on a simple belief: that every puppy deserves the best possible start in life. Our program began over nine years ago with a single litter and a commitment to doing things the right way — health testing every breeding pair, raising every litter inside our home, and staying connected with every family long after their puppy goes home.</p>
                                <p>We are not a kennel. We are a family. Our Corsos grow up under feet, around children, exposed to everyday sounds and experiences that build the confident, stable temperament this breed is known for. We take on a limited number of litters each year so that every puppy receives the individual attention they deserve.</p>
                                <p>What started as a passion for the Cane Corso breed has grown into a trusted program with over 140 families placed across the country. Every one of those families matters to us — not just on pickup day, but for the lifetime of their dog.</p>
                                <p>We started RICHES CORSOS because we wanted to be the kind of breeder we wished existed when we were looking for our first Corso. Someone who answered questions honestly, who didn't rush litters, who cared about where each puppy ended up. That is still the standard we hold ourselves to today.</p>
                            </div>
                            <div className="story-img-col">
                                <StoryGallery />
                            </div>
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* ===== OUR VALUES ===== */}
                <SectionTitle number="02" title="Our Values &amp; Philosophy" sub="The principles that guide every decision we make at RICHES CORSOS." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="values-grid">
                            <div className="value-item">
                                <div className="value-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                    </svg>
                                </div>
                                <h3>Health Above All</h3>
                                <p>Every breeding decision begins with health. We screen both parents thoroughly before any litter is planned. A healthy puppy is the foundation of everything else.</p>
                            </div>
                            <div className="value-item">
                                <div className="value-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M3 12l9-8 9 8M5 10v10h14V10" />
                                    </svg>
                                </div>
                                <h3>Home Environment</h3>
                                <p>Puppies raised in a home environment develop differently from those raised in kennels. They are calmer, more confident, and better prepared for family life.</p>
                            </div>
                            <div className="value-item">
                                <div className="value-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" />
                                    </svg>
                                </div>
                                <h3>Long-Term Commitment</h3>
                                <p>Our commitment to a family does not end at pickup. We remain available for questions, guidance, and support throughout the life of every dog we place.</p>
                            </div>
                            <div className="value-item">
                                <div className="value-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M4 4v16h16M4 15l5-5 4 4 7-7" />
                                    </svg>
                                </div>
                                <h3>Honest Guidance</h3>
                                <p>We would rather talk a family out of a Corso than place one in the wrong home. Honest guidance before and after placement is part of who we are.</p>
                            </div>
                            <div className="value-item">
                                <div className="value-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                </div>
                                <h3>Quality Over Quantity</h3>
                                <p>We limit our litters deliberately. Fewer litters means more time, more attention, and a better outcome for every puppy and every family.</p>
                            </div>
                            <div className="value-item">
                                <div className="value-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                                        <path d="M16 3.13a4 4 0 010 7.75M21 21v-2a4 4 0 00-3-3.87" />
                                    </svg>
                                </div>
                                <h3>Family First</h3>
                                <p>Every puppy we place becomes part of a family. We take that responsibility seriously — matching temperament, lifestyle, and expectations carefully.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* ===== ABOUT THE PUPPY ===== */}
                <SectionTitle number="03" title="About The RICHES CORSOS Puppy" />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="about-grid">
                            <div className="about-img-col">
                                <div className="about-img-wrap">
                                    <picture>
                                        <source srcSet="/images/aboutsite.webp" type="image/webp" />
                                        <img src="/images/aboutsite.png" alt="About RICHES CORSOS" />
                                    </picture>
                                    <div className="about-img-label">ALL ABOUT RICHES CORSOS</div>
                                </div>
                                <Link href="/puppies" className="dark-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" width="18" height="18">
                                        <path d="M10 2c-1 2-3.5 3-5 4.5S3 10 4 12s3 2.5 4 4 1 4 4 4 3-3 4-4 3-3 4-4 3-3 2-5-3-3-5-3.5S11 0 10 2z" />
                                        <circle cx="14.5" cy="9.5" r="1" fill="currentColor" stroke="none" />
                                    </svg>
                                    Available Puppies
                                </Link>
                            </div>
                            <div className="about-text-col">
                                <h3 className="about-col-title">Overview</h3>
                                <p>The Cane Corso is a large, powerful Italian mastiff breed known for its imposing presence and deeply loyal nature. Males typically weigh between 99–110 lbs and stand 25–27.5 inches tall; females are slightly smaller at 85–99 lbs and 23.5–26 inches.</p>
                                <p>With a lifespan of 9–12 years, the Corso is a long-term companion. They are intelligent, trainable, and deeply bonded to their family. Their short, dense double coat comes in black, grey, fawn, and brindle — requiring only weekly brushing and occasional baths.</p>
                                <p>At RICHES CORSOS, every breeding pair is fully health-tested before any litter is planned — hips, hearts, elbows, eyes, and relevant genetic panels.</p>
                            </div>
                            <div className="about-text-col">
                                <h3 className="about-col-title">Temperament</h3>
                                <p>The Cane Corso is confident, calm, and deeply devoted. They are natural protectors — alert without being aggressive — and form an unbreakable bond with their immediate family.</p>
                                <p>With children they are gentle and patient when raised alongside them. They can coexist with other pets, especially when socialised early. Their intelligence means they thrive with consistent, firm, and positive training from puppyhood.</p>
                                <p>Early socialisation is essential. A well-raised Corso is stable, adaptable, and a joy to live with — equally at home on a long walk or settled at your feet in the evening.</p>
                            </div>
                        </div>

                        <DiamondDivider />

                        <div className="care-grid">
                            <div className="care-item">
                                <h4>Feeding</h4>
                                <p>Puppies are fed a high-quality diet appropriate for large-breed development. We provide feeding guidance and transition support when your puppy goes home.</p>
                            </div>
                            <div className="care-item">
                                <h4>Grooming</h4>
                                <p>The Corso's short coat requires minimal grooming — weekly brushing and a bath every 4–6 weeks. Nails, ears, and teeth should be checked regularly.</p>
                            </div>
                            <div className="care-item">
                                <h4>Exercise</h4>
                                <p>Puppies need age-appropriate exercise. Over-exercising young joints can cause long-term damage. We provide guidance on appropriate activity levels at each stage.</p>
                            </div>
                            <div className="care-item">
                                <h4>Training</h4>
                                <p>Early, consistent, positive training is essential for this breed. We begin foundational work before puppies leave us and recommend continued training with a qualified professional.</p>
                            </div>
                            <div className="care-item">
                                <h4>Veterinary Care</h4>
                                <p>Puppies leave with a health record and vaccination schedule. We recommend establishing a relationship with a veterinarian before your puppy comes home.</p>
                            </div>
                            <div className="care-item">
                                <h4>Socialisation</h4>
                                <p>Continued socialisation after placement is critical. Exposure to new people, environments, and experiences during the early months shapes a confident adult dog.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* ===== APPEARANCE ===== */}
                <SectionTitle number="04" title="Appearance" sub="The physical characteristics of a well-bred Cane Corso." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <DiamondDivider />
                        <div className="appearance-body">
                            <p>The Cane Corso is a large, muscular dog with a rectangular body, broad skull, and a distinctly square muzzle. Their eyes are medium-sized, slightly oval, and dark in colour — conveying alertness and intelligence. The nose is large and black, the ears naturally drop forward but are often cropped to a short, equilateral triangle.</p>
                            <p>The coat is short, dense, and slightly coarse with a light undercoat — available in black, grey (light to dark), fawn (light to dark), and brindle variations. Coat maintenance is minimal: a weekly brush and a bath every 4–6 weeks keeps them clean and healthy.</p>
                            <p>Males stand 25–27.5 inches at the shoulder and weigh 99–110 lbs; females 23.5–26 inches and 85–99 lbs. The tail is thick at the base and traditionally docked, though many breeders now leave it natural. Despite their size, they move with surprising agility and grace.</p>
                            <p>Adult appearance is heavily influenced by genetics, nutrition, and environment during the first two years. At RICHES CORSOS, we select breeding pairs with strong structure, correct proportions, and breed-typical appearance — so families know what to expect as their puppy grows.</p>
                        </div>
                        <DiamondDivider />
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* ===== ACHIEVEMENTS ===== */}
                <SectionTitle number="05" title="Our Achievements" sub="Nine years of responsible breeding, healthy placements, and ongoing family support." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="trust-grid-card">
                            <div className="trust-item-card"><span>140+</span>Families Placed</div>
                            <div className="trust-item-card"><span>9 yrs</span>Breeding Experience</div>
                            <div className="trust-item-card"><span>100%</span>Health Commitment</div>
                            <div className="trust-item-card"><span>5.0</span>Average Rating</div>
                        </div>
                        <DiamondDivider />
                        <div className="achievement-cards">
                            <div className="achievement-card">
                                <div className="achievement-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                    </svg>
                                </div>
                                <h4>Healthy Beginnings</h4>
                                <p>Every puppy leaves with a full health record, vaccinations, and veterinary clearance.</p>
                            </div>
                            <div className="achievement-card">
                                <div className="achievement-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M3 12l9-8 9 8M5 10v10h14V10" />
                                    </svg>
                                </div>
                                <h4>Home-Raised Puppies</h4>
                                <p>Every litter is raised inside our home — never in a kennel or outbuilding.</p>
                            </div>
                            <div className="achievement-card">
                                <div className="achievement-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                                    </svg>
                                </div>
                                <h4>Family Focused</h4>
                                <p>We match each puppy carefully to the right family — lifestyle, experience, and expectations all considered.</p>
                            </div>
                            <div className="achievement-card">
                                <div className="achievement-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" />
                                    </svg>
                                </div>
                                <h4>Ongoing Support</h4>
                                <p>Our relationship with families continues long after pickup day — we remain available for the life of every dog we place.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* ===== WHAT MAKES US SPECIAL ===== */}
                <div id="what-makes-us-special">
                    <WhatMakesUsSpecial />
                </div>

                <div style={{ height: 48 }} />
            </div>
        </SiteLayout>
    );
}
