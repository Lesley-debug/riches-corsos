import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import PageHero from '@/Components/PageHero';

const FAQ_CATEGORIES = [
    {
        id: 'about',
        label: 'About Us',
        items: [
            {
                q: 'What makes RICHES CORSOS different from other breeders?',
                a: 'We focus on three things that matter most: health, temperament, and home environment. Every breeding pair is health-tested before any litter is planned. Every puppy is raised inside our home — not in a kennel. And every family receives ongoing support long after their puppy goes home. We limit our litters deliberately so that quality is never compromised.',
            },
            {
                q: 'How are your puppies raised?',
                a: 'Our puppies grow up inside our home from birth. They experience everyday household life — people moving around, normal sounds, different surfaces, children, and regular human interaction. This home environment builds the confident, stable temperament the Cane Corso is known for.',
            },
            {
                q: 'Can I meet the puppies before adopting?',
                a: 'Yes. We welcome visits by appointment. We ask that you contact us first so we can arrange a suitable time. Visits allow you to meet the puppies, see the environment they are raised in, and ask any questions directly.',
            },
            {
                q: 'How long have you been breeding Cane Corsos?',
                a: 'RICHES CORSOS has been placing Cane Corso puppies with families for over nine years. In that time we have placed more than 140 puppies and maintained relationships with the families we have worked with.',
            },
        ],
    },
    {
        id: 'availability',
        label: 'Availability',
        items: [
            {
                q: 'When can I take my puppy home?',
                a: 'Puppies are ready to go home at eight weeks of age. Before that point they need time with their mother and littermates for proper development. We will not place a puppy before eight weeks regardless of circumstances.',
            },
            {
                q: 'Do you have a waitlist for upcoming litters?',
                a: 'Yes. If no puppies are currently available, you can contact us to be added to our waitlist. We will notify you when a new litter is planned and keep you informed throughout the process. Waitlist families are given first choice of available puppies.',
            },
            {
                q: 'How do I choose the right puppy for my family?',
                a: 'We help with this. When you contact us, we ask about your lifestyle, living situation, experience with dogs, and what you are looking for in a companion. We use that information to help match you with the right puppy — temperament, energy level, and personality all considered.',
            },
            {
                q: 'What is included when I receive my puppy?',
                a: 'Your puppy comes with a health record, vaccination documentation, a starter supply of the food they are used to, and our contact information for ongoing support. We also provide guidance on feeding, care, and the transition home.',
            },
            {
                q: 'What do I need when picking up my puppy?',
                a: 'You will need a secure, appropriately sized crate or carrier for safe transportation. We recommend bringing a blanket or item with a familiar scent to help your puppy settle. We will walk you through everything at pickup.',
            },
            {
                q: 'How does the reservation process work?',
                a: 'You can submit a reservation request from any available puppy\'s page. No payment is taken online. We contact you directly to confirm details, discuss a deposit, and walk through next steps together.',
            },
        ],
    },
    {
        id: 'health',
        label: 'Health',
        items: [
            {
                q: 'Are your puppies vaccinated?',
                a: 'Yes. Puppies receive age-appropriate vaccinations before going home. Full vaccination documentation is provided at pickup. We recommend continuing the vaccination schedule with your own veterinarian.',
            },
            {
                q: 'Do puppies receive veterinary checks?',
                a: 'Yes. Every puppy is examined by a veterinarian before placement. We take health seriously and will not place a puppy that has not been cleared by a vet.',
            },
            {
                q: 'Do you provide health records?',
                a: 'Yes. Every puppy leaves with a complete health record including vaccination history, veterinary examination notes, and any relevant health documentation. Your vet will have everything they need at your first appointment.',
            },
            {
                q: 'Do you health-test breeding parents?',
                a: 'Yes. Both parents undergo appropriate health screening before any litter is planned. This includes hip evaluations, cardiac evaluations, and relevant genetic health panels. We make informed breeding decisions based on this testing.',
            },
            {
                q: 'What happens if my puppy becomes sick?',
                a: 'Always contact your veterinarian first. We are not a substitute for professional veterinary care. However, we are always available to discuss what you are seeing and offer guidance. We take the health of every puppy we place seriously.',
            },
        ],
    },
    {
        id: 'care',
        label: 'Care',
        items: [
            {
                q: 'What should I feed my puppy?',
                a: 'We provide guidance on the diet your puppy is currently eating and recommend continuing that diet initially to avoid digestive upset. Any dietary changes should be made gradually. We recommend consulting your veterinarian for long-term feeding guidance appropriate for large-breed puppies.',
            },
            {
                q: 'How often should puppies eat?',
                a: 'Young puppies generally eat three to four times per day. As they grow, this reduces to twice daily. We provide specific feeding guidance at pickup based on your puppy\'s age and current routine.',
            },
            {
                q: 'How should I prepare my home?',
                a: 'Before your puppy arrives, set up a puppy-safe area with food, water, comfortable bedding, and appropriate toys. Remove hazards at floor level — cables, small objects, toxic plants. Have a crate ready. Establish a veterinary relationship before pickup day.',
            },
            {
                q: 'What toys are appropriate?',
                a: 'Choose durable, size-appropriate toys designed for large-breed puppies. Avoid toys with small parts that could be swallowed. Rope toys, rubber chew toys, and interactive puzzle toys are generally suitable. Supervise play, especially with new toys.',
            },
            {
                q: 'Are your puppies raised in a family environment?',
                a: 'Yes. Every puppy is raised inside our home from birth. They experience everyday family life — people, sounds, handling, and routine — from the very beginning. This is one of the most important things we do.',
            },
            {
                q: 'What does early socialisation involve?',
                a: 'We gradually introduce puppies to handling, different people, household sounds, various surfaces, and age-appropriate experiences. The goal is to build confidence and familiarity with the world before they go home. Continued socialisation after placement is equally important.',
            },
        ],
    },
    {
        id: 'training',
        label: 'Training',
        items: [
            {
                q: 'What basic training do puppies receive?',
                a: 'We begin foundational work before puppies leave us — basic handling, leash exposure, crate introduction, and simple commands. We are not trying to hand you a finished dog. We are giving you a confident foundation to build on.',
            },
            {
                q: 'When should I begin training?',
                a: 'Training can begin from the day your puppy comes home. Short, positive sessions work best with young puppies. We recommend enrolling in a puppy class with a qualified trainer as early as possible.',
            },
            {
                q: 'How do I handle teething?',
                a: 'Provide appropriate chew toys and redirect biting onto those toys consistently. Avoid rough play that encourages biting. Teething typically peaks between 3–6 months. Consistency and patience are key.',
            },
            {
                q: 'Can my puppy be crate trained?',
                a: 'Yes, and we recommend it. Crate training provides your puppy with a safe, secure space and supports house training. We introduce puppies to crates before they leave us to make the transition easier.',
            },
            {
                q: 'How do I introduce my puppy to other pets?',
                a: 'Introductions should be gradual and controlled. Allow both animals to become familiar with each other\'s scent before face-to-face meetings. Supervise all early interactions. Patience and positive reinforcement make a significant difference.',
            },
            {
                q: 'What if my puppy is nervous in a new environment?',
                a: 'Some nervousness in a new home is completely normal. Give your puppy time to adjust at their own pace. Avoid overwhelming them with too many new experiences at once. Consistent routine, calm handling, and patience will help them settle.',
            },
        ],
    },
    {
        id: 'family',
        label: 'Family',
        items: [
            {
                q: 'Are RICHES CORSOS puppies good with children?',
                a: 'Cane Corsos raised in a family environment can be wonderful with children. However, all interactions between dogs and young children should be supervised. We recommend teaching children how to interact respectfully with dogs from the beginning.',
            },
            {
                q: 'Can puppies live in an apartment?',
                a: 'Suitability depends on the individual dog, the household, and the commitment to exercise and routine. A Cane Corso in an apartment can thrive with sufficient daily exercise, mental stimulation, and consistent training. Contact us to discuss your specific situation.',
            },
            {
                q: 'Can puppies be left alone?',
                a: 'Puppies should not be left alone for extended periods, especially in the early weeks. Gradually build up alone time using crate training and a consistent routine. Adult Corsos can manage reasonable periods alone with proper preparation.',
            },
            {
                q: 'How much exercise does a puppy need?',
                a: 'Young puppies need age-appropriate exercise — short, gentle sessions rather than long runs. Over-exercising developing joints can cause long-term damage. A general guideline is five minutes of exercise per month of age, twice daily. We provide specific guidance at pickup.',
            },
        ],
    },
    {
        id: 'aftercare',
        label: 'Aftercare',
        items: [
            {
                q: 'Will I receive support after bringing my puppy home?',
                a: 'Yes. Our relationship with a puppy\'s family does not end at pickup. Whether you have a question on day one or two years later, we remain available. We genuinely care about the long-term wellbeing of every dog we place.',
            },
            {
                q: 'What if I can no longer care for my puppy?',
                a: 'Please contact us before making any other arrangements. We ask that families reach out to us first if circumstances change. We will work with you to find the best outcome for the dog.',
            },
            {
                q: 'Can I contact you after adoption?',
                a: 'Absolutely. We encourage families to stay in touch. You can reach us by email, phone, or WhatsApp. We love hearing updates and are always happy to help with questions as your puppy grows.',
            },
            {
                q: 'Do you provide training resources?',
                a: 'Yes. We provide guidance and resources at pickup and remain available for questions. We also recommend working with a qualified professional trainer, particularly for first-time Corso owners.',
            },
        ],
    },
];

function AccordionItem({ item, isOpen, onToggle }) {
    return (
        <div className="faq-item">
            <button
                className={`faq-question ${isOpen ? 'open' : ''}`}
                onClick={onToggle}
                aria-expanded={isOpen}
            >
                {item.q}
                <svg viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" />
                </svg>
            </button>
            {isOpen && <p className="faq-answer">{item.a}</p>}
        </div>
    );
}

export default function Faqs() {
    const [activeCategory, setActiveCategory] = useState('about');
    const [openIndex, setOpenIndex] = useState(0);

    const currentCategory = FAQ_CATEGORIES.find((c) => c.id === activeCategory);

    const handleCategoryChange = (id) => {
        setActiveCategory(id);
        setOpenIndex(0);
    };

    return (
        <SiteLayout>
            <Head title="FAQs — Riches Corsos" />

            <PageHero
                image="/images/about/1.jpeg"
                title="Frequently Asked Questions"
                sub="Answers to common questions about our puppies, health practices, preparation, training, care, and the process of bringing a RICHES CORSOS puppy home."
            />

            <div className="home-page">
                <div className="home-section-wrap" style={{ paddingTop: 48, paddingBottom: 48 }}>
                    <div className="card-3d">

                        {/* Category Filter */}
                        <div className="faq-category-nav">
                            {FAQ_CATEGORIES.map((cat) => (
                                <button
                                    key={cat.id}
                                    className={`faq-cat-btn ${activeCategory === cat.id ? 'active' : ''}`}
                                    onClick={() => handleCategoryChange(cat.id)}
                                >
                                    {cat.label}
                                </button>
                            ))}
                        </div>

                        {/* Active Category */}
                        <div className="faq-category-body">
                            <h3 className="faq-category-title">{currentCategory.label}</h3>
                            {currentCategory.items.map((item, i) => (
                                <AccordionItem
                                    key={item.q}
                                    item={item}
                                    isOpen={openIndex === i}
                                    onToggle={() => setOpenIndex(openIndex === i ? -1 : i)}
                                />
                            ))}
                        </div>

                        <div className="faq-footer-cta">
                            <p>Still have a question?</p>
                            <Link href="/contact" className="btn-solid">Contact Us Directly</Link>
                        </div>
                    </div>
                </div>

                <div style={{ height: 48 }} />
            </div>

            <div className="cta-band">
                <h2>Ready to bring a RICHES CORSOS puppy home?</h2>
                <Link href="/puppies" className="btn-solid">View Available Puppies</Link>
            </div>
        </SiteLayout>
    );
}
