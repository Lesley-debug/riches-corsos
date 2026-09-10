import { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function ForgotPassword() {
  const { props } = usePage();
  const status = props.flash?.status;
  const { data, setData, post, processing, errors } = useForm({
    email: '',
  });

  const submit = (e) => {
    e.preventDefault();
    post(route('password.email'));
  };

  return (
    <SiteLayout>
      <Head title="Forgot Password — Riches Corsos" />

      <main className="auth-page">
        <div className="auth-card">
          <div className="auth-header">
            <span className="auth-badge">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Account Recovery
            </span>
            <h1>Reset your password</h1>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
          </div>

          {status && (
            <div className="auth-status-alert">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              <span>{status}</span>
            </div>
          )}

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

            <button type="submit" className="btn-solid auth-btn-submit" disabled={processing}>
              {processing ? 'Sending link…' : 'Send Password Reset Link'}
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
