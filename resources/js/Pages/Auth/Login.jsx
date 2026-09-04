import { Head, Link, useForm } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function Login() {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  const submit = (e) => {
    e.preventDefault();
    post('/login');
  };

  return (
    <SiteLayout>
      <Head title="Login — Riches Corsos" />

      <div className="form-card">
        <div className="section-head" style={{ marginBottom: 32 }}>
          <h2>Welcome back</h2>
          <p>Log in to see your order status and saved puppies.</p>
        </div>

        <form onSubmit={submit}>
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

          <button type="submit" className="btn-solid" disabled={processing} style={{ width: '100%', marginBottom: 16 }}>
            {processing ? 'Logging in…' : 'Log In'}
          </button>

          <p className="form-note" style={{ textAlign: 'center' }}>
            Don't have an account?{' '}
            <Link href="/register" style={{ color: 'var(--green-dark)', fontWeight: 600 }}>
              Register
            </Link>
          </p>
        </form>
      </div>
    </SiteLayout>
  );
}
