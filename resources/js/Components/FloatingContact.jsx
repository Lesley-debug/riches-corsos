import { useState } from 'react';
import { Link } from '@inertiajs/react';

export default function FloatingContact() {
    const [open, setOpen] = useState(false);

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
                        <a href="tel:+1234567890" className="fcp-action">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="18" height="18">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <span>Call / Text: +1 (234) 567-890</span>
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
