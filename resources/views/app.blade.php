<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>Riches Corsos</title>

    {{-- Favicons & PWA --}}
    <link rel="icon" type="image/svg+xml" href="/images/pwa/icon.svg">
    <link rel="icon" type="image/png" sizes="192x192" href="/images/pwa/icon-192.png">
    <link rel="shortcut icon" href="/images/pwa/icon-192.png">
    <link rel="apple-touch-icon" href="/images/pwa/icon-192.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2F6B4F">

    @routes
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body>
    @inertia

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    </script>
</body>
</html>
