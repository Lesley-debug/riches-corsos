import { useState, useEffect, useRef } from 'react';

const STORY_THUMBS = ['1', '2', '3', '4', '5', '6', '7', '8'];
const SLIDE_DURATION_MS = 5500;

export default function StoryGallery({ className = '' }) {
    const [activeThumb, setActiveThumb] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
    const thumbsTrackRef = useRef(null);

    const handlePrev = () => {
        setActiveThumb((prev) => (prev - 1 + STORY_THUMBS.length) % STORY_THUMBS.length);
    };

    const handleNext = () => {
        setActiveThumb((prev) => (prev + 1) % STORY_THUMBS.length);
    };

    // Auto-advance timer (pauses when user hovers or touches)
    useEffect(() => {
        if (isPaused) return;
        const interval = setInterval(() => {
            setActiveThumb((prev) => (prev + 1) % STORY_THUMBS.length);
        }, SLIDE_DURATION_MS);
        return () => clearInterval(interval);
    }, [isPaused]);

    // Smoothly scroll thumbnail strip to keep active thumb visible and centered
    useEffect(() => {
        if (thumbsTrackRef.current) {
            const track = thumbsTrackRef.current;
            const activeBtn = track.children[activeThumb];
            if (activeBtn) {
                const scrollLeft =
                    activeBtn.offsetLeft - track.offsetWidth / 2 + activeBtn.offsetWidth / 2;
                track.scrollTo({ left: scrollLeft, behavior: 'smooth' });
            }
        }
    }, [activeThumb]);

    return (
        <div
            className={`story-gallery-container ${className}`}
            onMouseEnter={() => setIsPaused(true)}
            onMouseLeave={() => setIsPaused(false)}
            onTouchStart={() => setIsPaused(true)}
            onTouchEnd={() => setIsPaused(false)}
        >
            {/* Main Image Stage */}
            <div className="story-main-img-wrapper">
                <div className="story-main-img">
                    <img
                        key={activeThumb}
                        src={`/images/ourstory/${STORY_THUMBS[activeThumb]}.jpeg`}
                        alt={`Riches Corsos story photo ${activeThumb + 1}`}
                        className="story-fade-in"
                    />
                </div>

                {/* Floating overlay arrows on main photo for easy tapping */}
                <button
                    type="button"
                    className="story-stage-arrow story-stage-arrow--prev"
                    onClick={handlePrev}
                    aria-label="Previous photo"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" width="20" height="20">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                </button>

                <button
                    type="button"
                    className="story-stage-arrow story-stage-arrow--next"
                    onClick={handleNext}
                    aria-label="Next photo"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" width="20" height="20">
                        <path d="M9 18l6-6-6-6" />
                    </svg>
                </button>

                {/* Cycle progress line */}
                <div className="story-progress-indicator">
                    <div
                        className={`story-progress-bar ${isPaused ? 'paused' : ''}`}
                        key={activeThumb}
                        style={{ animationDuration: `${SLIDE_DURATION_MS}ms` }}
                    />
                </div>

                <div className="story-slide-counter">
                    Photo {activeThumb + 1} of {STORY_THUMBS.length}
                    {isPaused && ' (Paused)'}
                </div>
            </div>

            {/* Thumbnail Row with < and > Switching Buttons */}
            <div className="story-thumbs-nav-wrap">
                <button
                    type="button"
                    className="story-nav-btn story-nav-btn--prev"
                    onClick={handlePrev}
                    aria-label="Previous thumbnail"
                    title="Previous photo"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.6" strokeLinecap="round" strokeLinejoin="round" width="16" height="16">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                </button>

                <div className="story-thumbs-track" ref={thumbsTrackRef}>
                    {STORY_THUMBS.map((n, i) => (
                        <button
                            key={n}
                            type="button"
                            className={`story-thumb ${activeThumb === i ? 'active' : ''}`}
                            onClick={() => setActiveThumb(i)}
                            aria-label={`Select photo ${i + 1}`}
                        >
                            <img src={`/images/ourstory/${n}.jpeg`} alt={`Gallery thumbnail ${n}`} />
                            {activeThumb === i && <span className="thumb-active-marker" />}
                        </button>
                    ))}
                </div>

                <button
                    type="button"
                    className="story-nav-btn story-nav-btn--next"
                    onClick={handleNext}
                    aria-label="Next thumbnail"
                    title="Next photo"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.6" strokeLinecap="round" strokeLinejoin="round" width="16" height="16">
                        <path d="M9 18l6-6-6-6" />
                    </svg>
                </button>
            </div>

            {/* Navigation Dots */}
            <div className="story-dots">
                {STORY_THUMBS.map((_, i) => (
                    <button
                        key={i}
                        type="button"
                        className={`story-dot ${activeThumb === i ? 'active' : ''}`}
                        onClick={() => setActiveThumb(i)}
                        aria-label={`Go to photo ${i + 1}`}
                    />
                ))}
            </div>
        </div>
    );
}
