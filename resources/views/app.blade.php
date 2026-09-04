<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>Riches Corsos</title>

    {{-- PWA: makes the browser offer "Install app" / "Add to Home Screen" --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2F6B4F">
    <link rel="apple-touch-icon" href="/images/pwa/icon-192.png">

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
