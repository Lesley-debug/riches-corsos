import { Head, Link, useForm, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function VerifyEmail() {
  const { props } = usePage();
  const user = props.auth?.user;
  const status = props.flash?.status;
  const { post, processing } = useForm({});

  const resend = (event) => {
    event.preventDefault();
    post(route('verification.send'));
  };

  return (
    <SiteLayout>
      <Head title="Verify Email — Riches Corsos" />

      <main className="auth-page">
        <div className="auth-card">
          <div className="auth-header">
            <span className="auth-badge">Email verification</span>
            <h1>Check your inbox</h1>
            <p>
              We sent a verification link to <strong>{user?.email}</strong>.
              Verify your address to open your dashboard and order history.
            </p>
          </div>

          {status && <div className="auth-status-alert"><span>{status}</span></div>}

          <form onSubmit={resend} className="auth-form">
            <button type="submit" className="btn-solid auth-btn-submit" disabled={processing}>
              {processing ? 'Sending…' : 'Resend verification email'}
            </button>
          </form>

          <footer className="auth-card-footer">
            You can continue shopping while you wait.
            <Link href={route('puppies.index')}>Browse puppies</Link>
          </footer>
        </div>
      </main>
    </SiteLayout>
  );
}