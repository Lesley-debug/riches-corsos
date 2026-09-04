import { Head, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function Contact() {
  const { props } = usePage();
  const flashSuccess = props.flash?.success;

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
      <Head title="Contact Us — Riches Corsos" />

      <div className="form-card">
        <div className="section-head" style={{ marginBottom: 32 }}>
          <h2>Get in touch</h2>
          <p>Questions about a puppy, an upcoming litter, or anything else — we read every message.</p>
        </div>

        {flashSuccess && <div className="form-success">{flashSuccess}</div>}

        <form onSubmit={submit}>
          <div className="form-field">
            <label>Full name</label>
            <input value={data.name} onChange={(e) => setData('name', e.target.value)} required />
            {errors.name && <div className="form-error">{errors.name}</div>}
          </div>

          <div className="form-field">
            <label>Email</label>
            <input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} required />
            {errors.email && <div className="form-error">{errors.email}</div>}
          </div>

          <div className="form-field">
            <label>Phone (optional)</label>
            <input value={data.phone} onChange={(e) => setData('phone', e.target.value)} />
          </div>

          <div className="form-field">
            <label>Subject (optional)</label>
            <input value={data.subject} onChange={(e) => setData('subject', e.target.value)} />
          </div>

          <div className="form-field">
            <label>Message</label>
            <textarea rows={5} value={data.message} onChange={(e) => setData('message', e.target.value)} required />
            {errors.message && <div className="form-error">{errors.message}</div>}
          </div>

          <button type="submit" className="btn-solid" disabled={processing} style={{ width: '100%' }}>
            {processing ? 'Sending…' : 'Send Message'}
          </button>
        </form>
      </div>
    </SiteLayout>
  );
}
