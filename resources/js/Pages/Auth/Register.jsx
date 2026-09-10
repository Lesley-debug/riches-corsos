import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
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

export default function Register() {
  const [showPassword, setShowPassword] = useState(false);
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
      <Head title="Create Account — Riches Corsos" />

      <main className="auth-page">
        <div className="auth-card">
          <div className="auth-header">
            <span className="auth-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                <circle cx="8.5" cy="7" r="4" />
                <line x1="20" y1="8" x2="20" y2="14" />
                <line x1="23" y1="11" x2="17" y2="11" />
              </svg>
              New Customer
            </span>
            <h1>Create your account</h1>
            <p>Save puppies to your wishlist and track your reservation requests.</p>
          </div>

          <form onSubmit={submit} className="auth-form">
            <div className="auth-field">
              <label htmlFor="name" className="auth-field-label">Full name</label>
              <input
                id="name"
                type="text"
                className={`auth-input ${errors.name ? 'has-error' : ''}`}
                value={data.name}
                onChange={(e) => setData('name', e.target.value)}
                autoComplete="name"
                placeholder="e.g. Michael Smith"
                required
                autoFocus
              />
              {errors.name && <div className="form-error">{errors.name}</div>}
            </div>

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
              />
              {errors.email && <div className="form-error">{errors.email}</div>}
            </div>

            <div className="auth-field">
              <label htmlFor="password" className="auth-field-label">Password</label>
              <div className="password-input-wrap">
                <input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  className={`auth-input ${errors.password ? 'has-error' : ''}`}
                  value={data.password}
                  onChange={(e) => setData('password', e.target.value)}
                  autoComplete="new-password"
                  placeholder="At least 8 characters"
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

            <div className="auth-field">
              <label htmlFor="password_confirmation" className="auth-field-label">Confirm password</label>
              <div className="password-input-wrap">
                <input
                  id="password_confirmation"
                  type={showPassword ? 'text' : 'password'}
                  className="auth-input"
                  value={data.password_confirmation}
                  onChange={(e) => setData('password_confirmation', e.target.value)}
                  autoComplete="new-password"
                  placeholder="Repeat your password"
                  required
                />
              </div>
            </div>

            <button type="submit" className="btn-solid auth-btn-submit" disabled={processing}>
              {processing ? 'Creating account…' : 'Create Account'}
            </button>
          </form>

          <div className="auth-perks-list">
            <div className="auth-perk">
              <CheckIcon />
              <span>Instant access to puppy reservation requests</span>
            </div>
            <div className="auth-perk">
              <CheckIcon />
              <span>Synchronized wishlist across desktop and mobile</span>
            </div>
          </div>

          <footer className="auth-card-footer">
            Already have an account?
            <Link href="/login">Log in</Link>
          </footer>
        </div>
      </main>
    </SiteLayout>
  );
}
