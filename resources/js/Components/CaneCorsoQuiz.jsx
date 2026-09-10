import { useState } from 'react';
import { Link } from '@inertiajs/react';

const QUESTIONS = [
    {
        id: 'space',
        question: 'What does your living environment look like?',
        options: [
            {
                label: 'Secure Fenced Yard / Acreage',
                sub: 'Private fenced yard with room to patrol and run',
                score: 'excellent',
            },
            {
                label: 'Suburban Home (Partial Yard / Parks nearby)',
                sub: 'Daily structured walks, play sessions, and park access',
                score: 'good',
            },
            {
                label: 'Apartment / Townhouse',
                sub: 'Committed to multiple dedicated exercise sessions daily',
                score: 'moderate',
            },
        ],
    },
    {
        id: 'experience',
        question: 'What is your experience with dogs or mastiffs?',
        options: [
            {
                label: 'Experienced with Working or Mastiff Breeds',
                sub: 'Familiar with strong-willed, protective, and large working dogs',
                score: 'excellent',
            },
            {
                label: 'Experienced Pet Owner (Medium to Large breeds)',
                sub: 'Have owned dogs before and ready to follow structured training',
                score: 'good',
            },
            {
                label: 'First-Time Dog Owner',
                sub: 'Eager to learn, open to professional guidance and puppy classes',
                score: 'needs_guidance',
            },
        ],
    },
    {
        id: 'purpose',
        question: 'What role will your Corso play in your home?',
        options: [
            {
                label: 'Devoted Family Guardian & Protector',
                sub: 'Gentle with immediate family, watchful and loyal at home',
                focus: 'Guardian',
            },
            {
                label: 'Everyday Loyal Companion & Shadow',
                sub: 'Going everywhere with family, lounging at your feet',
                focus: 'Companion',
            },
            {
                label: 'Active Working, Hiking, or Farm Partner',
                sub: 'Engaging in outdoor activity, agility, or property stewardship',
                focus: 'Active Working',
            },
        ],
    },
];

export default function CaneCorsoQuiz() {
    const [step, setStep] = useState(0);
    const [answers, setAnswers] = useState({});
    const [completed, setCompleted] = useState(false);

    const currentQ = QUESTIONS[step];

    function handleSelect(option) {
        const updated = { ...answers, [currentQ.id]: option };
        setAnswers(updated);

        if (step < QUESTIONS.length - 1) {
            setStep(step + 1);
        } else {
            setCompleted(true);
        }
    }

    function resetQuiz() {
        setStep(0);
        setAnswers({});
        setCompleted(false);
    }

    return (
        <div className="quiz-card card-3d">
            <div className="quiz-header">
                <span className="quiz-badge">Breed Match Tool</span>
                <h3 className="quiz-title">Is a Cane Corso the Right Match for Your Home?</h3>
                <p className="quiz-desc">
                    The Cane Corso is a majestic, deeply devoted working mastiff. Answer 3 quick questions to discover compatibility and key considerations for your family.
                </p>
            </div>

            {!completed ? (
                <div className="quiz-body">
                    {/* Step indicator */}
                    <div className="quiz-progress-bar">
                        <div
                            className="quiz-progress-fill"
                            style={{ width: `${((step + 1) / QUESTIONS.length) * 100}%` }}
                        />
                    </div>
                    <div className="quiz-step-meta">
                        <span>Question {step + 1} of {QUESTIONS.length}</span>
                        {step > 0 && (
                            <button
                                type="button"
                                className="quiz-back-btn"
                                onClick={() => setStep(step - 1)}
                            >
                                ← Back
                            </button>
                        )}
                    </div>

                    <h4 className="quiz-question-text">{currentQ.question}</h4>

                    <div className="quiz-options-list">
                        {currentQ.options.map((option, idx) => (
                            <button
                                key={idx}
                                type="button"
                                className="quiz-option-btn"
                                onClick={() => handleSelect(option)}
                            >
                                <span className="quiz-option-radio" />
                                <div className="quiz-option-text">
                                    <strong>{option.label}</strong>
                                    <small>{option.sub}</small>
                                </div>
                            </button>
                        ))}
                    </div>
                </div>
            ) : (
                <div className="quiz-result-box">
                    <div className="quiz-result-icon">🐾</div>
                    <h4 className="quiz-result-heading">
                        {answers.experience?.score === 'needs_guidance'
                            ? 'A Corso Can Flourish with Early Training Guidance'
                            : 'You Are Well-Suited for a Cane Corso!'}
                    </h4>
                    <p className="quiz-result-body">
                        {answers.experience?.score === 'needs_guidance' ? (
                            <>
                                Cane Corsos are intelligent, sensitive, and naturally protective giants. While they can make incredible first mastiffs, consistent early socialization and puppy obedience training are critical. At Riches Corsos, we provide <strong>lifetime guidance</strong> to help first-time owners raise confident, stable companions.
                            </>
                        ) : (
                            <>
                                Your lifestyle, yard arrangement, and experience make an ideal environment for a healthy Cane Corso. A well-bred Corso will thrive as your <strong>{answers.purpose?.focus || 'family companion'}</strong> with steady boundaries, daily affection, and purposeful companionship.
                            </>
                        )}
                    </p>

                    <div className="quiz-result-tags">
                        <span className="quiz-tag">✓ Health-Tested Bloodlines</span>
                        <span className="quiz-tag">✓ Raised In-Home with Children</span>
                        <span className="quiz-tag">✓ Lifetime Breeder Support</span>
                    </div>

                    <div className="quiz-result-actions">
                        <Link href="/puppies" className="btn-solid">
                            View Matching Puppies
                        </Link>
                        <Link href="/contact" className="dark-btn dark-btn--outline">
                            Ask Us Any Question
                        </Link>
                        <button type="button" className="quiz-retake-btn" onClick={resetQuiz}>
                            Retake Assessment
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
