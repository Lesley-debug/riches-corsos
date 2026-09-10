import { useRef, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';

export default function AccountDropdown({ user }) {
  const [open, setOpen] = useState(false);
  const timerRef = useRef(null);

  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  function show() {
    clearTimeout(timerRef.current);
    setOpen(true);
  }

  function hide() {
    timerRef.current = setTimeout(() => setOpen(false), 180);
  }

  function handleLogin(e) {
    e.preventDefault();
    post('/login', { onSuccess: () => reset() });
  }

  // Logged-in state — show name + account links
  if (user) {
    return (
      <div className="acct-dropdown" onMouseEnter={show} onMouseLeave={hide}>
        <button className="acct-trigger" aria-expanded={open}>
          {user.name.split(' ')[0]}
        </button>
        {open && (
          <div className="acct-panel">
            <Link href="/account" className="acct-panel-link">My Account</Link>
            <Link href="/orders" className="acct-panel-link">My Orders</Link>
            <Link href="/wishlist" className="acct-panel-link">Wishlist</Link>
            {user.isAdmin && (
              <a href="/admin" className="acct-panel-link acct-panel-link--admin">
                Admin Dashboard
              </a>
            )}
            <div className="acct-panel-divider" />
            <Link href="/logout" method="post" as="button" className="acct-panel-link acct-panel-link--danger">
              Sign Out
            </Link>
          </div>
        )}
      </div>
    );
  }

  // Guest state — hover shows mini login form
  return (
    <div className="acct-dropdown" onMouseEnter={show} onMouseLeave={hide}>
      <button className="acct-trigger" aria-expanded={open}>
        Login / Register
      </button>

      {open && (
        <div className="acct-panel acct-panel--form">
          <p className="acct-panel-heading">Sign in to your account</p>
          <form onSubmit={handleLogin} className="acct-login-form">
            <input
              type="email"
              placeholder="Email address"
              value={data.email}
              onChange={(e) => setData('email', e.target.value)}
              className={errors.email ? 'has-error' : ''}
              autoComplete="email"
              required
            />
            {errors.email && <span className="acct-field-error">{errors.email}</span>}
            <input
              type="password"
              placeholder="Password"
              value={data.password}
              onChange={(e) => setData('password', e.target.value)}
              className={errors.password ? 'has-error' : ''}
              autoComplete="current-password"
              required
            />
            <label className="acct-remember">
              <input
                type="checkbox"
                checked={data.remember}
                onChange={(e) => setData('remember', e.target.checked)}
              />
              Remember me
            </label>
            <button type="submit" className="acct-login-btn" disabled={processing}>
              {processing ? 'Signing in…' : 'Sign In'}
            </button>
          </form>
          <div className="acct-panel-divider" />
          <div className="acct-panel-footer">
            New here?{' '}
            <Link href="/register" className="acct-register-link">Create an account</Link>
          </div>
        </div>
      )}
    </div>
  );
}
