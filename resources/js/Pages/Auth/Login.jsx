import { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

function EyeIcon({ visible }) {
  if (visible) {
    return (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" />
        <line x1="1" y1="1" x2="23" y2="23" />
      </svg>
    );
  }
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
      <circle cx="12" cy="12" r="3" />
    </svg>
  );
}

function CheckIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
      <polyline points="20 6 9 17 4 12" />
    </svg>
  );
}

export default function Login() {
  const { props } = usePage();
  const flashSuccess = props.flash?.success;
  const [showPassword, setShowPassword] = useState(false);

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
      <Head title="Log In — Riches Corsos" />

      <main className="auth-page">
        <div className="auth-card">
          <div className="auth-header">
            <span className="auth-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
              Customer Portal
            </span>
            <h1>Welcome back</h1>
            <p>Log in to track your puppy reservations and saved favorites.</p>
          </div>

          {flashSuccess && (
            <div className="auth-status-alert">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              <span>{flashSuccess}</span>
            </div>
          )}

          <a href="/auth/google/redirect" className="auth-google-btn">
            <svg viewBox="0 0 24 24" width="20" height="20">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z" />
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.36 24 12 24z" />
              <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z" />
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" />
            </svg>
            <span>Continue with Google</span>
          </a>

          <div className="auth-divider">
            <span>or sign in with email</span>
          </div>

          <form onSubmit={submit} className="auth-form">
            <div className="auth-field">
              <label htmlFor="email" className="auth-field-label">Email address</label>
              <input
                id="email"
                type="email"
                className={`auth-input ${errors.email ? 'has-error' : ''}`}
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                autoComplete="email"
                placeholder="you@example.com"
                required
                autoFocus
              />
              {errors.email && <div className="form-error">{errors.email}</div>}
            </div>

            <div className="auth-field">
              <div className="auth-field-label-row">
                <label htmlFor="password" className="auth-field-label">Password</label>
                <Link href={route('password.request')} className="auth-forgot-link">
                  Forgot password?
                </Link>
              </div>
              <div className="password-input-wrap">
                <input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  className={`auth-input ${errors.password ? 'has-error' : ''}`}
                  value={data.password}
                  onChange={(e) => setData('password', e.target.value)}
                  autoComplete="current-password"
                  placeholder="Enter your password"
                  required
                />
                <button
                  type="button"
                  className="password-toggle-btn"
                  onClick={() => setShowPassword(!showPassword)}
                  aria-label={showPassword ? 'Hide password' : 'Show password'}
                >
                  <EyeIcon visible={showPassword} />
                </button>
              </div>
              {errors.password && <div className="form-error">{errors.password}</div>}
            </div>

            <label className="auth-checkbox-label">
              <input
                type="checkbox"
                checked={data.remember}
                onChange={(e) => setData('remember', e.target.checked)}
              />
              <span>Remember this device for 30 days</span>
            </label>

            <button type="submit" className="btn-solid auth-btn-submit" disabled={processing}>
              {processing ? 'Logging in…' : 'Log In'}
            </button>
          </form>

          <div className="auth-perks-list">
            <div className="auth-perk">
              <CheckIcon />
              <span>Track reservation updates and delivery timeline</span>
            </div>
            <div className="auth-perk">
              <CheckIcon />
              <span>Access official puppy health certificates and care guides</span>
            </div>
          </div>

          <footer className="auth-card-footer">
            Don't have an account yet?
            <Link href="/register">Create an account</Link>
          </footer>
        </div>
      </main>
    </SiteLayout>
  );
}
