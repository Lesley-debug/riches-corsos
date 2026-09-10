import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function FloatingContact() {
    const [open, setOpen] = useState(false);
    const { props } = usePage();
    const siteSettings = props.siteSettings ?? {};

    const phone = siteSettings.phone || '+1 (214) 212-3023';
    const cleanPhone = phone.replace(/[^\d+]/g, '');
    const whatsapp = siteSettings.whatsapp
        ? siteSettings.whatsapp.replace(/[^\d+]/g, '')
        : '12142123023';

    return (
        <div className="floating-contact-container">
            {open && (
                <div className="floating-contact-popup">
                    <div className="fcp-header">
                        <div>
                            <strong>Questions About Our Puppies?</strong>
                            <p>We typically respond within an hour</p>
                        </div>
                        <button
                            type="button"
                            className="fcp-close"
                            onClick={() => setOpen(false)}
                            aria-label="Close"
                        >
                            ✕
                        </button>
                    </div>
                    <div className="fcp-body">
                        <Link href="/contact" className="fcp-action fcp-action--primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="18" height="18">
                                <rect x="2" y="4" width="20" height="16" rx="2" /><path d="M2 7l10 7 10-7" />
                            </svg>
                            <span>Send Message or Inquiry</span>
                        </Link>
                        <a href={`tel:${cleanPhone}`} className="fcp-action">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="18" height="18">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <span>Call / Text: {phone}</span>
                        </a>
                        <a href={`https://wa.me/${whatsapp}`} target="_blank" rel="noopener noreferrer" className="fcp-action">
                            <svg viewBox="0 0 24 24" fill="var(--green-dark)" width="18" height="18">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <span>WhatsApp Us Directly</span>
                        </a>
                        <Link href="/puppies" className="fcp-action">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="18" height="18">
                                <path d="M10 2c-1 2-3.5 3-5 4.5S3 10 4 12s3 2.5 4 4 1 4 4 4 3-3 4-4 3-3 4-4 3-3 2-5-3-3-5-3.5S11 0 10 2z" />
                                <circle cx="14.5" cy="9.5" r="1" fill="currentColor" stroke="none" />
                            </svg>
                            <span>Browse Available Puppies</span>
                        </Link>
                    </div>
                </div>
            )}

            <button
                type="button"
                className={`floating-contact-btn ${open ? 'active' : ''}`}
                onClick={() => setOpen(!open)}
                aria-label="Contact breeder"
            >
                <span className="fc-pulse" />
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="22" height="22">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
                <span className="fc-btn-label">Questions? Ask Us</span>
            </button>
        </div>
    );
}
