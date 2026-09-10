import { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';
import WhatMakesUsSpecial from '@/Components/WhatMakesUsSpecial';

const STORY_THUMBS = ['1', '2', '3', '4', '5', '6', '7', '8'];

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

export default function Contact() {
    const { props } = usePage();
    const flashSuccess = props.flash?.success;
    const siteSettings = props.siteSettings ?? {};
    const [activeThumb, setActiveThumb] = useState(0);

    const contactPhone = siteSettings.phone || '+1 (214) 212-3023';
    const cleanPhone = contactPhone.replace(/[^\d+]/g, '');
    const contactWhatsapp = siteSettings.whatsapp
        ? siteSettings.whatsapp.replace(/[^\d+]/g, '')
        : (cleanPhone.replace('+', '') || '12142123023');
    const contactEmail = siteSettings.email || 'info@richescorsos.com';
    const contactAddress = siteSettings.address || 'Dallas, Texas';

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/contact', { onSuccess: () => reset() });
    };

    return (
        <SiteLayout>
            <Head title="Contact RICHES CORSOS" />

            <PageHero
                image="/images/about/1.jpeg"
                title="Contact RICHES CORSOS"
                sub="Have a question about an available puppy, upcoming litter, adoption process, or anything else? We'd love to hear from you."
            />

            <div className="home-page">

                {/* ===== FORM + GALLERY ===== */}
                <SectionTitle title="Get In Touch" sub="We read every message and respond personally." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="contact-main-grid">

                            {/* LEFT — Form */}
                            <div className="contact-form-col">
                                <h3 className="contact-col-heading">Send Us A Message</h3>

                                {flashSuccess && <div className="form-success">{flashSuccess}</div>}

                                <form onSubmit={submit} className="contact-form">
                                    <div className="form-field">
                                        <label>Full Name</label>
                                        <input
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            placeholder="Your full name"
                                            required
                                        />
                                        {errors.name && <div className="form-error">{errors.name}</div>}
                                    </div>

                                    <div className="contact-form-row">
                                        <div className="form-field">
                                            <label>Email</label>
                                            <input
                                                type="email"
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder="your@email.com"
                                                required
                                            />
                                            {errors.email && <div className="form-error">{errors.email}</div>}
                                        </div>
                                        <div className="form-field">
                                            <label>Phone (optional)</label>
                                            <input
                                                value={data.phone}
                                                onChange={(e) => setData('phone', e.target.value)}
                                                placeholder="+1 (000) 000-0000"
                                            />
                                        </div>
                                    </div>

                                    <div className="form-field">
                                        <label>Subject</label>
                                        <input
                                            value={data.subject}
                                            onChange={(e) => setData('subject', e.target.value)}
                                            placeholder="What is your message about?"
                                        />
                                    </div>

                                    <div className="form-field">
                                        <label>Message</label>
                                        <textarea
                                            rows={6}
                                            value={data.message}
                                            onChange={(e) => setData('message', e.target.value)}
                                            placeholder="Tell us what's on your mind..."
                                            required
                                        />
                                        {errors.message && <div className="form-error">{errors.message}</div>}
                                    </div>

                                    <button type="submit" className="btn-solid contact-submit-btn" disabled={processing}>
                                        {processing ? (
                                            <>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="16" height="16" style={{ animation: 'spin 1s linear infinite' }}>
                                                    <circle cx="12" cy="12" r="10" strokeOpacity=".25" />
                                                    <path d="M12 2a10 10 0 0110 10" />
                                                </svg>
                                                Sending…
                                            </>
                                        ) : 'Send Message'}
                                    </button>
                                </form>
                            </div>

                            {/* RIGHT — Story Gallery */}
                            <div className="contact-gallery-col">
                                <h3 className="contact-col-heading">RICHES CORSOS: Our Story</h3>
                                <p className="contact-gallery-sub">A glimpse into the environment where our puppies are raised — inside our home, surrounded by family.</p>
                                <div className="story-main-img contact-gallery-main">
                                    <img src={`/images/ourstory/${STORY_THUMBS[activeThumb]}.jpeg`} alt="Our story" />
                                </div>
                                <div className="story-thumbs">
                                    {STORY_THUMBS.map((n, i) => (
                                        <button key={n} className={`story-thumb ${activeThumb === i ? 'active' : ''}`} onClick={() => setActiveThumb(i)}>
                                            <img src={`/images/ourstory/${n}.jpeg`} alt={`Gallery ${n}`} />
                                        </button>
                                    ))}
                                </div>
                                <div className="story-dots">
                                    {STORY_THUMBS.map((_, i) => (
                                        <button key={i} className={`story-dot ${activeThumb === i ? 'active' : ''}`} onClick={() => setActiveThumb(i)} aria-label={`Photo ${i + 1}`} />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* ===== CONTACT INFO CARDS ===== */}
                <SectionTitle title="Find Us" sub="Multiple ways to reach the RICHES CORSOS team." />
                <div className="home-section-wrap">
                    <div className="card-3d">
                        <div className="contact-info-grid">
                            <div className="contact-info-card">
                                <div className="contact-info-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" /><circle cx="12" cy="10" r="3" />
                                    </svg>
                                </div>
                                <h4>Visit Us</h4>
                                <p>{contactAddress}</p>
                                <p className="contact-info-note">Visits by appointment only</p>
                            </div>
                            <div className="contact-info-card">
                                <div className="contact-info-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z" />
                                    </svg>
                                </div>
                                <h4>Call Us</h4>
                                <a href={`tel:${cleanPhone}`}>{contactPhone}</a>
                                <p className="contact-info-note">Mon–Fri 9am–6pm CT</p>
                            </div>
                            <div className="contact-info-card">
                                <div className="contact-info-icon" style={{ background: '#E7F5EC' }}>
                                    <svg viewBox="0 0 24 24" fill="var(--green-dark)" width="22" height="22">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                    </svg>
                                </div>
                                <h4>WhatsApp</h4>
                                <a href={`https://wa.me/${contactWhatsapp}`} target="_blank" rel="noopener noreferrer">Message on WhatsApp</a>
                                <p className="contact-info-note">Quick responses during business hours</p>
                            </div>
                            <div className="contact-info-card">
                                <div className="contact-info-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <rect x="2" y="4" width="20" height="16" rx="2" /><path d="M2 7l10 7 10-7" />
                                    </svg>
                                </div>
                                <h4>Email Us</h4>
                                <a href={`mailto:${contactEmail}`}>{contactEmail}</a>
                                <p className="contact-info-note">We respond within 24 hours</p>
                            </div>
                            <div className="contact-info-card contact-info-card--hours">
                                <div className="contact-info-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round">
                                        <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" />
                                    </svg>
                                </div>
                                <h4>Opening Hours</h4>
                                <div className="contact-hours-list">
                                    <div><span>Mon – Fri</span><span>9am – 6pm</span></div>
                                    <div><span>Saturday</span><span>10am – 4pm</span></div>
                                    <div><span>Sunday</span><span>By appointment</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="section-divider"><DiamondDivider /></div>

                {/* What Makes Us Special */}
                <WhatMakesUsSpecial />

                <div style={{ height: 48 }} />
            </div>
        </SiteLayout>
    );
}
