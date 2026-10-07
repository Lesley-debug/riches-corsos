<link rel="manifest" href="{{ asset('admin-manifest.json') }}">
<meta name="theme-color" content="#2F6B4F">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="RC Admin">
<link rel="apple-touch-icon" href="{{ asset('images/pwa/icon-192.png') }}">

<script>
    (() => {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/admin-sw.js', {
                scope: '/admin/',
            }).catch((error) => {
                console.error('Riches Corsos Admin PWA registration failed:', error);
            });
        });
    })();
</script>
