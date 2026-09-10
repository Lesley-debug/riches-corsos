import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';

export default function Terms() {
    const { props } = usePage();
    const siteSettings = props.siteSettings ?? {};
    const contactEmail = siteSettings.email || 'info@richescorsos.com';
    const contactPhone = siteSettings.phone || '+1 (214) 212-3023';
    const cleanPhone = contactPhone.replace(/[^\d+]/g, '');
    return (
        <SiteLayout>
            <Head title="Terms & Conditions — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="Terms &amp; Conditions"
                sub="Adoption policies, health guarantee, reservations, and ownership standards for Riches Corsos puppies."
            />

            <div className="home-page">
                <div className="home-section-wrap" style={{ paddingTop: 48, paddingBottom: 64 }}>
                    <div className="legal-container">
                        <div className="card-3d legal-card">
                            <div className="legal-header">
                                <span className="legal-tag">Adoption Policy &amp; Terms</span>
                                <h1 className="legal-title">Terms &amp; Conditions</h1>
                                <p className="legal-meta">Last Updated: September 2026 • Riches Corsos Breeder Program</p>
                            </div>

                            <div className="legal-body">
                                <section className="legal-section">
                                    <h2>1. Overview &amp; Agreement</h2>
                                    <p>
                                        Welcome to <strong>Riches Corsos</strong>. By browsing our website, submitting an inquiry, reserving a puppy, placing an order, or entering into an adoption agreement, you agree to comply with and be bound by the following Terms &amp; Conditions.
                                    </p>
                                    <p>
                                        These terms exist to safeguard the welfare and well-being of our dogs, establish clear expectations for adoptive families, and maintain the highest ethical standards of purebred Cane Corso breeding.
                                    </p>
                                </section>

                                <section className="legal-section">
                                    <h2>2. Puppy Reservations &amp; Deposits</h2>
                                    <p>To reserve a puppy from an active or upcoming litter:</p>
                                    <ul>
                                        <li><strong>Reservation Request:</strong> Prospective owners submit an inquiry or add an available puppy to their cart and checkout with their contact and household details.</li>
                                        <li><strong>Application Review:</strong> We evaluate all applications to verify whether a Cane Corso is an appropriate match for the buyer's home, lifestyle, and experience level.</li>
                                        <li><strong>Deposit Terms:</strong> A designated reservation deposit holds the chosen puppy exclusively for you. Deposits signify a firm commitment to adopt and are non-refundable, except in the unlikely event that a puppy fails veterinary health clearance before placement.</li>
                                        <li><strong>Final Payment:</strong> The remaining adoption balance must be settled prior to or upon scheduled pickup or transport handover.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>3. Rehoming Age &amp; Pickup / Delivery</h2>
                                    <p>
                                        In strict compliance with animal health regulations and behavioral development milestones, <strong>no puppy will leave our care before reaching at least eight (8) weeks of age</strong>.
                                    </p>
                                    <ul>
                                        <li><strong>In-Person Pickup:</strong> Families are encouraged to visit our home kennel by scheduled appointment to meet their puppy and complete adoption documentation.</li>
                                        <li><strong>Professional Transport:</strong> For out-of-state families, white-glove pet nanny flight transport or climate-controlled private ground delivery can be coordinated at buyer expense.</li>
                                        <li><strong>Timely Pickup:</strong> Puppies must be received within 7 days of their designated departure date unless alternative boarding arrangements have been agreed in writing.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>4. Comprehensive Health Guarantee</h2>
                                    <div className="legal-highlight-box">
                                        Every Riches Corsos puppy is backed by our comprehensive <strong>Health Warranty</strong> and lifetime breeder support.
                                    </div>
                                    <p>Our health guarantee includes:</p>
                                    <ul>
                                        <li><strong>Veterinary Examination:</strong> Complete pre-departure physical exam conducted by a licensed veterinarian.</li>
                                        <li><strong>Preventive Care:</strong> Age-appropriate core vaccinations, systematic deworming treatments, and ISO standard microchip implantation.</li>
                                        <li><strong>Initial Health Warranty:</strong> A 72-hour window during which the buyer is required to have the puppy examined by their own licensed veterinarian.</li>
                                        <li><strong>Genetic Health Guarantee:</strong> Protection against life-threatening congenital or genetic defects (including severe hip dysplasia and congenital heart defects) as specified in the signed adoption contract.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>5. Buyer Responsibilities &amp; Care Standards</h2>
                                    <p>Adoptive families agree to provide an enriching, safe, and loving permanent home:</p>
                                    <ul>
                                        <li><strong>Nutrition &amp; Housing:</strong> High-quality age-appropriate diet, clean freshwater, indoor shelter as an integrated family member, and a secure fenced perimeter.</li>
                                        <li><strong>Healthcare:</strong> Timely completion of puppy booster vaccinations, annual veterinary examinations, and recommended parasite preventatives.</li>
                                        <li><strong>Socialization &amp; Training:</strong> Consistent positive-reinforcement obedience training and structured early socialization essential for large working breeds.</li>
                                        <li><strong>Spay/Neuter:</strong> Unless full breeding rights are explicitly granted in writing via a separate contract, companion puppies are placed under a non-breeding pet agreement.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>6. Lifetime Rehoming Policy (Zero-Shelter Commitment)</h2>
                                    <p>
                                        We stand behind every dog we bring into this world for its entire life. <strong>Under no circumstances may a Riches Corsos dog be surrendered to an animal shelter, rescue facility, or sold to a third party without our knowledge.</strong>
                                    </p>
                                    <p>
                                        If an adoptive family ever encounters unforeseen life changes (illness, financial hardship, housing loss) making it impossible to keep their dog, Riches Corsos maintains first right of recovery. We will assist in rehoming the dog to an approved home or take the dog back into our care.
                                    </p>
                                </section>

                                <section className="legal-section">
                                    <h2>7. Intellectual Property &amp; Website Use</h2>
                                    <p>
                                        All content on this website — including puppy photographs, kennel logos, pedigree graphics, text descriptions, and blog articles — is the intellectual property of Riches Corsos and protected by copyright law. Unauthorized reproduction or commercial use without written permission is prohibited.
                                    </p>
                                </section>

                                <section className="legal-section">
                                    <h2>8. Questions &amp; Inquiries</h2>
                                    <p>
                                        If you have any questions regarding our terms, contracts, or the adoption process, our team is always here to assist you:
                                    </p>
                                    <div className="legal-contact-block">
                                        <p><strong>Riches Corsos</strong></p>
                                        <p>Email: <a href={`mailto:${contactEmail}`}>{contactEmail}</a></p>
                                        <p>Phone: <a href={`tel:${cleanPhone}`}>{contactPhone}</a></p>
                                        <p>Direct Inquiries: <Link href="/contact" className="legal-inline-link">Contact Page</Link></p>
                                        <p>Puppy Showcase: <Link href="/puppies" className="legal-inline-link">Available Puppies</Link></p>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="cta-band">
                <h2>Ready to welcome a noble companion?</h2>
                <Link href="/puppies" className="btn-solid">Explore Available Puppies</Link>
            </div>
        </SiteLayout>
    );
}
