const CACHE_NAME = 'riches-corsos-v2';
const PRECACHE_URLS = ['/manifest.json'];

const isLocalhost = Boolean(
  self.location.hostname === 'localhost' ||
  self.location.hostname === '127.0.0.1' ||
  self.location.hostname === '[::1]'
);

// If on localhost, proactively unregister and clear caches to avoid stale dev states
if (isLocalhost) {
  self.addEventListener('install', () => self.skipWaiting());
  self.addEventListener('activate', (event) => {
    event.waitUntil(
      caches.keys().then((keys) => Promise.all(keys.map((key) => caches.delete(key))))
        .then(() => self.registration.unregister())
    );
  });
} else {
  self.addEventListener('install', (event) => {
    event.waitUntil(
      caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS)).catch(() => {})
    );
    self.skipWaiting();
  });

  self.addEventListener('activate', (event) => {
    event.waitUntil(
      caches.keys().then((keys) =>
        Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
      )
    );
    self.clients.claim();
  });

  // Only cache static, immutable assets (build files, images, fonts)
  // NEVER intercept dynamic Inertia visits, API calls, or auth/account routes
  self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and cross-origin
    if (request.method !== 'GET' || url.origin !== self.location.origin) {
      return;
    }

    // Skip full document navigations and Inertia JSON requests
    if (
      request.mode === 'navigate' ||
      request.headers.get('x-inertia') ||
      url.pathname.startsWith('/account') ||
      url.pathname.startsWith('/admin') ||
      url.pathname.startsWith('/login') ||
      url.pathname.startsWith('/register') ||
      url.pathname.startsWith('/orders') ||
      url.pathname.startsWith('/cart') ||
      url.pathname.startsWith('/checkout') ||
      url.pathname.startsWith('/api') ||
      url.pathname.startsWith('/_')
    ) {
      return;
    }

    // Only cache known static asset types
    const isStaticAsset =
      url.pathname.startsWith('/build/') ||
      url.pathname.startsWith('/images/') ||
      /\.(css|js|woff2?|ttf|eot|png|jpe?g|gif|svg|webp|ico)$/i.test(url.pathname);

    if (!isStaticAsset) {
      return;
    }

    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached;
        return fetch(request).then((response) => {
          if (!response || !response.ok) return response;
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone)).catch(() => {});
          return response;
        }).catch(() => {
          // If offline/error on static asset, fail gracefully
          return new Response('', { status: 408, statusText: 'Request timed out' });
        });
      })
    );
  });
}
