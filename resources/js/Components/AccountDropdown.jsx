import { useRef, useState } from 'react';
import { Link, useForm, usePage } from '@inertiajs/react';

function GoogleIcon() {
  return (
    <svg viewBox="0 0 24 24" width="18" height="18">
      <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z" />
      <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.36 24 12 24z" />
      <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z" />
      <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" />
    </svg>
  );
}

export default function AccountDropdown({ user }) {
  const [open, setOpen] = useState(false);
  const timerRef = useRef(null);
  const { props } = usePage();
  const unreadCount = props.unreadNotificationsCount ?? 0;

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

  // Logged-in state — show name + account links + notifications
  if (user) {
    return (
      <div className="acct-dropdown" onMouseEnter={show} onMouseLeave={hide}>
        <button className="acct-trigger" aria-expanded={open}>
          {user.name.split(' ')[0]}
          {unreadCount > 0 && <span className="acct-trigger-dot" />}
        </button>
        {open && (
          <div className="acct-panel">
            <Link href="/account" className="acct-panel-link">My Account</Link>
            <Link href="/orders" className="acct-panel-link">My Orders</Link>
            <Link href="/wishlist" className="acct-panel-link">Wishlist</Link>
            <Link href="/account/notifications" className="acct-panel-link" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <span>Notifications</span>
              {unreadCount > 0 && <span className="acct-badge">{unreadCount}</span>}
            </Link>
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

  // Guest state — hover shows Google sign-in + mini login form
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

          <div className="acct-divider-text">
            <span>or continue with</span>
          </div>

          <a href="/auth/google/redirect" className="acct-google-btn">
            <GoogleIcon />
            <span>Continue with Google</span>
          </a>

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
