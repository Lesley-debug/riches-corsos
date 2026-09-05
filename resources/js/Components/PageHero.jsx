import { Link } from '@inertiajs/react';

export default function PageHero({ image, title, sub, cta1, cta2 }) {
    return (
        <section
            className="page-hero"
            style={{ backgroundImage: `url(${image})` }}
        >
            <div className="page-hero-overlay" />
            <div className="page-hero-content">
                <h1 className="page-hero-title">{title}</h1>
                {sub && <p className="page-hero-sub">{sub}</p>}
                {(cta1 || cta2) && (
                    <div className="page-hero-actions">
                        {cta1 && (
                            <Link href={cta1.href} className="hero-btn hero-btn--primary">
                                {cta1.label}
                            </Link>
                        )}
                        {cta2 && (
                            <Link href={cta2.href} className="hero-btn hero-btn--secondary">
                                {cta2.label}
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </section>
    );
}
