<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>Riches Corsos</title>

    {{-- SEO & Social Meta --}}
    <meta name="description" content="Riches Corsos — Premier breeder of champion-line Italian Cane Corso puppies. AKC registered, health tested, raised with world-class care and nationwide delivery.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Riches Corsos">
    <meta property="og:title" content="Riches Corsos — Champion Cane Corso Puppies">
    <meta property="og:description" content="Discover champion-line Italian Cane Corso puppies. Health guaranteed, temperament tested, and raised with dedication.">
    <meta property="og:image" content="{{ asset('images/bg/homepagehero.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Riches Corsos — Champion Cane Corso Puppies">
    <meta name="twitter:description" content="Discover champion-line Italian Cane Corso puppies. Health guaranteed, temperament tested, and raised with dedication.">
    <meta name="twitter:image" content="{{ asset('images/bg/homepagehero.jpg') }}">

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
