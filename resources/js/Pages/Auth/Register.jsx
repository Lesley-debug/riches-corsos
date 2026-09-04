import { Head, Link, useForm } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function Register() {
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  const submit = (e) => {
    e.preventDefault();
    post('/register');
  };

  return (
    <SiteLayout>
      <Head title="Register — Riches Corsos" />

      <div className="form-card">
        <div className="section-head" style={{ marginBottom: 32 }}>
          <h2>Create an account</h2>
          <p>Save puppies to your wishlist and track your reservation requests.</p>
        </div>

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
            <label>Password</label>
            <input
              type="password"
              value={data.password}
              onChange={(e) => setData('password', e.target.value)}
              required
            />
            {errors.password && <div className="form-error">{errors.password}</div>}
          </div>

          <div className="form-field">
            <label>Confirm password</label>
            <input
              type="password"
              value={data.password_confirmation}
              onChange={(e) => setData('password_confirmation', e.target.value)}
              required
            />
          </div>

          <button type="submit" className="btn-solid" disabled={processing} style={{ width: '100%', marginBottom: 16 }}>
            {processing ? 'Creating account…' : 'Create Account'}
          </button>

          <p className="form-note" style={{ textAlign: 'center' }}>
            Already have an account?{' '}
            <Link href="/login" style={{ color: 'var(--green-dark)', fontWeight: 600 }}>
              Log in
            </Link>
          </p>
        </form>
      </div>
    </SiteLayout>
  );
}
