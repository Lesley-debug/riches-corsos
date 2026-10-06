import { useState, useEffect, useCallback } from 'react';

function Stars() {
    return (
        <div className="tslider-stars">
            {[...Array(5)].map((_, i) => (
                <svg key={i} viewBox="0 0 24 24" width="15" height="15" fill="#B8860B">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
            ))}
        </div>
    );
}

export default function TestimonialSlider({ testimonials = [] }) {
    const [active, setActive] = useState(0);
    const [paused, setPaused] = useState(false);

    const goNext = useCallback(() => {
        setActive(i => (i + 1) % testimonials.length);
    }, [testimonials.length]);

    // Auto-advance every 4 seconds, pause after manual interaction for 8s
    useEffect(() => {
        if (paused) {
            const resume = setTimeout(() => setPaused(false), 8000);
            return () => clearTimeout(resume);
        }
        const timer = setInterval(goNext, 4000);
        return () => clearInterval(timer);
    }, [paused, goNext]);

    if (!testimonials.length) return null;

    const prev = () => {
        setPaused(true);
        setActive(i => (i - 1 + testimonials.length) % testimonials.length);
    };
    const next = () => {
        setPaused(true);
        setActive(i => (i + 1) % testimonials.length);
    };
    const goTo = (i) => {
        setPaused(true);
        setActive(i);
    };

    const t = testimonials[active];

    return (
        <div className="tslider-wrap">
            <div className="tslider-card">
                <span className="tslider-quote-mark">&ldquo;</span>
                <Stars />
                <p className="tslider-text">&ldquo;{t.text}&rdquo;</p>
                <div className="tslider-divider-top" />
                <p className="tslider-author">{t.author}</p>
                <p className="tslider-meta">{t.location} &bull; {t.dog}</p>
                <div className="tslider-divider-bot" />
            </div>

            <div className="tslider-nav">
                <button className="tslider-arrow" onClick={prev} aria-label="Previous">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#222" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                </button>

                <div className="tslider-indicators">
                    {testimonials.map((_, i) => (
                        <button
                            key={i}
                            onClick={() => goTo(i)}
                            className={`tslider-dot ${i === active ? 'tslider-dot--active' : ''}`}
                            aria-label={`Go to testimonial ${i + 1}`}
                        />
                    ))}
                </div>

                <button className="tslider-arrow" onClick={next} aria-label="Next">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#222" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>
            </div>
        </div>
    );
}
