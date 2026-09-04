# Riches Corsos — Rebuild

Laravel 11 + Filament 3 (admin) + Inertia.js + React. This package contains the
backend foundation: migrations, models, notifications, and the Puppies / Orders /
Messages / Blog admin screens. Frontend Inertia/React pages are the next piece to
build against the locked design system (white + green, sticky two-tier header,
mobile bottom nav) from our mockups.

## What's included so far
- `database/migrations/` — puppies, puppy_images, orders, contact_messages, blog_posts, testimonials
- `app/Models/` — Puppy, PuppyImage, Order, ContactMessage, BlogPost, Testimonial
- `app/Filament/Resources/` — Puppies, Orders, Messages, Blog admin screens
- `app/Notifications/` + `app/Observers/` — auto email + dashboard bell when an order or message comes in
- `app/Http/Controllers/` — public pages + the order/contact submission logic
- `routes/web.php`
- `public/manifest.json` + `public/service-worker.js` — PWA install support
- `composer.json`, `package.json`, `vite.config.js`
- `resources/css/app.css` — the full locked design system (white + green, Fraunces/Inter)
- `resources/js/Layouts/SiteLayout.jsx` — two-tier sticky header (desktop) + hamburger/logo/cart bar + 5-item bottom nav (mobile)
- `resources/js/Components/PuppyCard.jsx` — shared puppy card
- `resources/js/Pages/` — Home, Puppies/Index, Puppies/Show (with the reserve/order form), Blog/Index, Blog/Show, Contact
- `resources/views/app.blade.php` — Inertia root template with manifest + service worker registration

## Local setup (do this on your own machine — my sandbox has no internet access)

1. Start a fresh Laravel 11 install, then copy these files into it:
   ```
   composer create-project laravel/laravel riches-corsos
   cd riches-corsos
   # copy in the files from this package, overwriting composer.json
   composer install
   ```
2. Install Filament and Inertia (composer.json already lists them, so this just publishes their files):
   ```
   php artisan filament:install --panels
   php artisan inertia:middleware
   ```
3. Set your `.env` — point `DB_*` at your MySQL database (local, or Hostinger's if developing directly against it).
4. Run migrations:
   ```
   php artisan migrate
   ```
5. Create your admin user:
   ```
   php artisan make:filament-user
   ```
   Log in at `/admin`.
6. Register the observers — already wired in `AppServiceProvider`, nothing else to do.

## Deploying to Hostinger
- Upload via Git or File Manager to your domain's `public_html` (point the domain root at this project's `public/` folder, not the project root — same as any Laravel app).
- Run `composer install --optimize-autoloader --no-dev` and `php artisan migrate --force` on the server (via Hostinger's SSH access if your plan includes it — Business plans and up).
- Set `APP_ENV=production`, `APP_DEBUG=false` in `.env`.

## Additional setup for the frontend
```
npm install
npm run dev      # local dev with hot reload
npm run build    # production build before deploying to Hostinger
```
Puppy/blog images are expected at `/storage/...` — run `php artisan storage:link` once so the `storage/app/public` disk (where Filament's FileUpload saves images) is web-accessible.

## One manual step: register the Inertia middleware
`app/Http/Middleware/HandleInertiaRequests.php` is included, but Laravel 11's `bootstrap/app.php` (not part of this package — it's generated fresh by `composer create-project`) needs to know about it. Add this inside the `->withMiddleware()` closure in your `bootstrap/app.php`:
```php
$middleware->web(append: [
    \App\Http\Middleware\HandleInertiaRequests::class,
]);
```
This is what shares the logged-in user and flash messages with every page — without it, the header won't know someone's logged in.

## Customer accounts vs admin
Two separate things on purpose: `/admin` (Filament) is for you/the client only — `User::canAccessPanel()` checks `role === 'admin'`. `/login`, `/register`, `/account`, `/orders`, `/wishlist` are for site visitors — anyone who registers gets `role = 'customer'` by default and can never reach `/admin`. Create the client's admin login with `php artisan make:filament-user` as before; customer accounts are created by visitors themselves via `/register`.

## Still to build
- Real puppy/blog photos (current pages show a soft green placeholder wherever an image is missing — swap in via the admin dashboard)
- 301 redirect map from the old `/shop/product.php?id=X` URLs
- JSON-LD structured data + sitemap (`spatie/laravel-sitemap`) for SEO
- Inertia SSR (`@inertiajs/server`) if you want the strongest possible SEO — see our earlier SEO discussion
- PWA icons (192px, 512px, maskable 512px) — placeholders referenced in `manifest.json`, need real exported icons from your logo
- Password reset flow (login/register work; "forgot password" isn't built)
- The desktop search icon and cart icon are UI-only placeholders — no search or cart backend yet (orders bypass a cart entirely, going straight from puppy page to reservation)
