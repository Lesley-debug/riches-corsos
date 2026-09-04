import { useState } from 'react';
import { Head } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

const FAQS = [
  {
    q: 'How does the reservation process work?',
    a: "You submit a request from a puppy's page with your contact details — no payment is taken online. We reach out directly to confirm details, discuss a deposit, and walk through next steps together.",
  },
  {
    q: 'What health testing do the parents go through?',
    a: 'Both parents are cleared on hips, hearts, and relevant genetic panels before any litter is planned. Documentation is available on request.',
  },
  {
    q: 'Do you ship puppies, or is pickup required?',
    a: "We prefer in-person pickup so you can meet the puppy and ask questions directly, but we're happy to discuss transport options for buyers further away.",
  },
  {
    q: 'What comes with the puppy when I bring them home?',
    a: 'A health record, vaccination schedule, a starter supply of the food they\'re used to, and ongoing support from us whenever you need it.',
  },
  {
    q: "What if I'm not sure a Cane Corso is right for my home?",
    a: "Reach out before reserving — we'd rather talk through temperament, size, and exercise needs upfront than have it be a surprise later.",
  },
];

export default function Faqs() {
  const [openIndex, setOpenIndex] = useState(0);

  return (
    <SiteLayout>
      <Head title="FAQs — Riches Corsos" />

      <section className="section" style={{ maxWidth: 720 }}>
        <div className="section-head" style={{ marginBottom: 40 }}>
          <h2>Frequently asked questions</h2>
          <p>Answers to what most families ask before reserving a puppy.</p>
        </div>

        {FAQS.map((item, i) => (
          <div className="faq-item" key={item.q}>
            <button
              className={`faq-question ${openIndex === i ? 'open' : ''}`}
              onClick={() => setOpenIndex(openIndex === i ? -1 : i)}
            >
              {item.q}
              <svg viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" />
              </svg>
            </button>
            {openIndex === i && <p className="faq-answer">{item.a}</p>}
          </div>
        ))}
      </section>
    </SiteLayout>
  );
}
