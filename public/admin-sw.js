const CACHE_NAME = 'riches-corsos-admin-v2';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only GET requests can be cached.
    if (request.method !== 'GET') {
        return;
    }

    // Only handle requests from this website.
    if (url.origin !== self.location.origin) {
        return;
    }

    // Never cache admin pages or dynamic admin requests.
    if (
        request.mode === 'navigate' ||
        url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/livewire')
    ) {
        return;
    }

    // Only cache static assets.
    const isStaticAsset =
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/vendor/') ||
        /\.(css|js|woff2?|ttf|eot|png|jpe?g|gif|svg|webp|ico)$/i.test(url.pathname);

    if (!isStaticAsset) {
        return;
    }

    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(request).then((response) => {
                if (!response || !response.ok) {
                    return response;
                }

                const responseToCache = response.clone();

                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(request, responseToCache);
                });

                return response;
            });
        })
    );
});
