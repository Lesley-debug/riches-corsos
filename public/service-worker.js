const CACHE_NAME = 'riches-corsos-v1';
const PRECACHE_URLS = ['/manifest.json'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
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

// Network-first for pages (so puppy availability is never stale),
// cache-first for static assets (images, fonts, css/js).
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Skip navigations and let the browser handle them normally.
  // Skip cross-origin requests (Vite HMR) and non-GET methods (POST etc.).
  if (
    request.mode === 'navigate' ||
    request.method !== 'GET' ||
    new URL(request.url).origin !== self.location.origin
  ) {
    return;
  }

  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) return cached;
      return fetch(request).then((response) => {
        if (!response.ok) return response;
        const clone = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
        return response;
      });
    })
  );
});
