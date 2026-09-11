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

    <!-- Smartsupp Live Chat -->
    <script type="text/javascript">
        var _smartsupp = _smartsupp || {};
        _smartsupp.key = '4114976a62bb67402887a26c24e488f31a85061f';
        window.smartsupp||(function(d) {
            var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
            s=d.getElementsByTagName('script')[0];c=d.createElement('script');
            c.type='text/javascript';c.charset='utf-8';c.async=true;
            c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
        })(document);
    </script>
    <noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>

    <script>
        try {
            if ('serviceWorker' in navigator) {
                if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                    navigator.serviceWorker.getRegistrations().then(function(registrations) {
                        for (let registration of registrations) {
                            registration.unregister();
                        }
                    });
                } else {
                    window.addEventListener('load', () => {
                        navigator.serviceWorker.register('/service-worker.js').catch(function() {});
                    });
                }
            }
        } catch (e) {
            // Ignored in sandboxed contexts
        }
    </script>
</body>
</html>
