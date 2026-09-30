import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import AppErrorBoundary from './Components/AppErrorBoundary';

const pages = import.meta.glob('./Pages/**/*.jsx');

createInertiaApp({
  resolve: (name) => {
    const page = pages[`./Pages/${name}.jsx`];

    if (!page) {
      throw new Error(`Unknown Inertia page: ${name}`);
    }

    return page();
  },
  setup({ el, App, props }) {
    createRoot(el).render(
      <AppErrorBoundary>
        <App {...props} />
      </AppErrorBoundary>,
    );
  },
});
