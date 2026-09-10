import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';

export default function Privacy() {
    const { props } = usePage();
    const siteSettings = props.siteSettings ?? {};
    const contactEmail = siteSettings.email || 'info@richescorsos.com';
    const contactPhone = siteSettings.phone || '+1 (214) 212-3023';
    const cleanPhone = contactPhone.replace(/[^\d+]/g, '');
    return (
        <SiteLayout>
            <Head title="Privacy Policy — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="Privacy Policy"
                sub="How Riches Corsos collects, uses, protects, and handles your personal information with care and integrity."
            />

            <div className="home-page">
                <div className="home-section-wrap" style={{ paddingTop: 48, paddingBottom: 64 }}>
                    <div className="legal-container">
                        <div className="card-3d legal-card">
                            <div className="legal-header">
                                <span className="legal-tag">Legal &amp; Transparency</span>
                                <h1 className="legal-title">Privacy Policy</h1>
                                <p className="legal-meta">Last Updated: September 2026 • Riches Corsos Breeder Program</p>
                            </div>

                            <div className="legal-body">
                                <section className="legal-section">
                                    <h2>1. Our Commitment to Your Privacy</h2>
                                    <p>
                                        At <strong>Riches Corsos</strong>, we respect your privacy and are committed to protecting any personal information you share with us. We breed and raise Cane Corso companions with high standards of care and transparency, and we treat our community's data with the same uncompromising integrity.
                                    </p>
                                    <p>
                                        This Privacy Policy outlines the types of information we collect through our website (<a href="https://richescorsos.com">richescorsos.com</a>), during adoption inquiries, purchases, customer portal interactions, and when communicating directly with us.
                                    </p>
                                </section>

                                <section className="legal-section">
                                    <h2>2. Information We Collect</h2>
                                    <p>We only collect information necessary to provide you with exceptional service and facilitate the responsible adoption and care of our puppies:</p>
                                    <ul>
                                        <li><strong>Contact Details:</strong> Your name, email address, phone number, and physical mailing or delivery address.</li>
                                        <li><strong>Adoption &amp; Application Information:</strong> Your household environment, prior large breed experience, yard fencing, and lifestyle preferences provided during adoption inquiries.</li>
                                        <li><strong>Account &amp; Authentication:</strong> Login credentials, order history, wishlist selections, and account settings when you create a customer account.</li>
                                        <li><strong>Transactions &amp; Purchases:</strong> Records of puppy reservations, deposits, orders, and payment confirmation details (we do not store raw credit card numbers on our servers).</li>
                                        <li><strong>Communications:</strong> Inquiries submitted via contact forms, emails, text messages, or direct consultation notes.</li>
                                        <li><strong>Website Usage &amp; Analytics:</strong> IP addresses, browser types, and standard traffic data collected automatically to keep our website secure, responsive, and easy to use.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>3. How We Use Your Information</h2>
                                    <p>We use your personal information exclusively for legitimate breeder operations and customer service:</p>
                                    <ul>
                                        <li>Processing adoption inquiries and matching puppies with suitable homes.</li>
                                        <li>Facilitating puppy reservations, deposits, contracts, and delivery or pickup arrangements.</li>
                                        <li>Generating official health records, pedigrees, veterinary certificates, and microchip registrations.</li>
                                        <li>Providing ongoing lifetime breeder support, training advice, and care recommendations.</li>
                                        <li>Sending important notifications regarding litter updates, order status, or account security.</li>
                                        <li>Maintaining website functionality, troubleshooting technical issues, and preventing fraud.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>4. Information Sharing &amp; Third Parties</h2>
                                    <div className="legal-highlight-box">
                                        <strong>We do not sell, rent, or trade your personal information.</strong> Your information is never monetized or provided to third-party advertisers.
                                    </div>
                                    <p>We share information only with trusted third parties strictly when required to provide our services:</p>
                                    <ul>
                                        <li><strong>Licensed Veterinarians &amp; Registries:</strong> For official veterinary health certificates, vaccination records, AKC/pedigree registration, and ISO microchip registration in your name.</li>
                                        <li><strong>Licensed Pet Transport Partners:</strong> If you arrange white-glove puppy flight nanny or ground transportation, your contact and delivery location are provided to the courier.</li>
                                        <li><strong>Payment Processors &amp; Infrastructure:</strong> Secure payment gateways and cloud infrastructure providers who adhere to strict data security standards.</li>
                                        <li><strong>Legal Requirements:</strong> If mandated by applicable law, regulation, or legal process to protect animal welfare or rights.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>5. Data Security &amp; Retention</h2>
                                    <p>
                                        We implement industry-standard administrative and technical security measures to protect your personal information against unauthorized access, loss, alteration, or misuse. All website interactions and customer portal sessions are encrypted via HTTPS/TLS.
                                    </p>
                                    <p>
                                        We retain customer records and puppy pedigree histories for the lifetime of the dog to fulfill our lifetime breeder support and health warranty obligations. You may request account deletion or data review at any time by contacting us.
                                    </p>
                                </section>

                                <section className="legal-section">
                                    <h2>6. Cookies and Tracking Technologies</h2>
                                    <p>
                                        Our website uses standard essential session cookies to remember your login session, maintain your shopping cart, and preserve wishlist items. We do not deploy invasive cross-site tracking scripts or predatory advertising cookies. You can manage or disable cookies through your browser settings at any time.
                                    </p>
                                </section>

                                <section className="legal-section">
                                    <h2>7. Your Rights &amp; Choices</h2>
                                    <p>You have full control over your information:</p>
                                    <ul>
                                        <li><strong>Access &amp; Review:</strong> You can view and edit your personal details anytime by logging into your customer account.</li>
                                        <li><strong>Data Deletion:</strong> You may request deletion of your account and non-contractual personal data by contacting us.</li>
                                        <li><strong>Communications:</strong> You can opt out of any non-essential email announcements or marketing newsletters at any time.</li>
                                    </ul>
                                </section>

                                <section className="legal-section">
                                    <h2>8. Contact Us Regarding Your Privacy</h2>
                                    <p>
                                        If you have questions, concerns, or requests regarding this Privacy Policy or your personal information, please reach out to our team:
                                    </p>
                                    <div className="legal-contact-block">
                                        <p><strong>Riches Corsos</strong></p>
                                        <p>Email: <a href={`mailto:${contactEmail}`}>{contactEmail}</a></p>
                                        <p>Phone: <a href={`tel:${cleanPhone}`}>{contactPhone}</a></p>
                                        <p>Direct Inquiries: <Link href="/contact" className="legal-inline-link">Contact Page</Link></p>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="cta-band">
                <h2>Have questions or looking for a puppy?</h2>
                <Link href="/contact" className="btn-solid">Get In Touch With Us</Link>
            </div>
        </SiteLayout>
    );
}
