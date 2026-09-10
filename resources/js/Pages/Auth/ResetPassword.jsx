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

export default function ResetPassword({ token, email = '' }) {
  const [showPassword, setShowPassword] = useState(false);
  const { data, setData, post, processing, errors } = useForm({
    token: token,
    email: email,
    password: '',
    password_confirmation: '',
  });

  const submit = (e) => {
    e.preventDefault();
    post(route('password.update'));
  };

  return (
    <SiteLayout>
      <Head title="Choose New Password — Riches Corsos" />

      <main className="auth-page">
        <div className="auth-card">
          <div className="auth-header">
            <span className="auth-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Secure Reset
            </span>
            <h1>Choose a new password</h1>
            <p>Please enter your email and set a new password of at least 8 characters.</p>
          </div>

          <form onSubmit={submit} className="auth-form">
            <input type="hidden" name="token" value={data.token} />

            <div className="auth-field">
              <label htmlFor="email" className="auth-field-label">Email address</label>
              <input
                id="email"
                type="email"
                className={`auth-input ${errors.email ? 'has-error' : ''}`}
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                autoComplete="email"
                required
              />
              {errors.email && <div className="form-error">{errors.email}</div>}
            </div>

            <div className="auth-field">
              <label htmlFor="password" className="auth-field-label">New password</label>
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
              <label htmlFor="password_confirmation" className="auth-field-label">Confirm new password</label>
              <div className="password-input-wrap">
                <input
                  id="password_confirmation"
                  type={showPassword ? 'text' : 'password'}
                  className="auth-input"
                  value={data.password_confirmation}
                  onChange={(e) => setData('password_confirmation', e.target.value)}
                  autoComplete="new-password"
                  placeholder="Repeat your new password"
                  required
                />
              </div>
            </div>

            <button type="submit" className="btn-solid auth-btn-submit" disabled={processing}>
              {processing ? 'Updating password…' : 'Reset Password'}
            </button>
          </form>

          <footer className="auth-card-footer">
            Remembered your password?
            <Link href={route('login')}>Return to log in</Link>
          </footer>
        </div>
      </main>
    </SiteLayout>
  );
}
