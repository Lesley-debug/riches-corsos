import { Component } from 'react';

export default class AppErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError() {
    return { hasError: true };
  }

  componentDidCatch(error, info) {
    if (import.meta.env.DEV) {
      console.error('React rendering error', error, info);
    }
  }

  render() {
    if (!this.state.hasError) {
      return this.props.children;
    }

    return (
      <main
        role="alert"
        style={{
          minHeight: '100vh',
          display: 'grid',
          placeItems: 'center',
          padding: 24,
          background: '#f7f3ed',
          color: '#1f2937',
          textAlign: 'center',
        }}
      >
        <div>
          <h1>We couldn&apos;t display this page</h1>
          <p>Please refresh the page. If the problem continues, return to the homepage.</p>
          <button
            type="button"
            onClick={() => window.location.reload()}
            style={{ marginRight: 12, padding: '10px 18px', cursor: 'pointer' }}
          >
            Try again
          </button>
          <a href="/" style={{ color: '#7a4d24', fontWeight: 700 }}>
            Homepage
          </a>
        </div>
      </main>
    );
  }
}