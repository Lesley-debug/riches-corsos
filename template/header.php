<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';

$cartCount = 0;
$cartTotalAmount = 0.0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $cartItem) {
    $qty = max(1, (int)($cartItem['qty'] ?? 1));
    $price = (float)($cartItem['price'] ?? 0);
    $cartCount += $qty;
    $cartTotalAmount += $price * $qty;
  }
}

$wishlistCount = isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist'])
  ? count($_SESSION['wishlist'])
  : 0;

$scriptFilename = realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '';
if ($scriptFilename !== '' && str_starts_with(str_replace('\\', '/', $scriptFilename), $normalizedAppRoot)) {
  $routePath = ltrim(substr(str_replace('\\', '/', $scriptFilename), strlen($normalizedAppRoot)), '/');
} else {
  $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
  $routePath = !empty($basePath) && str_starts_with($requestPath, $basePath)
    ? trim(substr($requestPath, strlen($basePath)), '/')
    : trim($requestPath, '/');
}

if ($routePath === '') {
  $routePath = 'index.php';
}

if (!str_ends_with($routePath, '.php')) {
  $routePath = rtrim($routePath, '/') . '/index.php';
}
$pageStyles = [
  'index.php' => ['home.css'],
  'pages/about.php' => ['about.css'],
  'pages/testimonials.php' => ['testimonials.css'],
  'pages/faqs.php' => ['faqs.css'],
  'pages/contact.php' => ['contact.css'],
  'pages/terms.php' => ['about.css'],
  'pages/privacy.php' => ['about.css'],
  'pages/cart.php' => ['cart.css'],
  'pages/checkout.php' => ['checkout.css'],
  'shop/shop.php' => ['shop.css'],
  'shop/product.php' => ['product.css'],
  'search.php' => ['shop.css'],
  'blog/index.php' => ['blog.css'],
  'blog/single-puppy.php' => ['blog-single.css'],
  'account/login.php' => ['auth.css'],
  'account/register.php' => ['auth.css'],
  'account/forgot-password.php' => ['auth.css'],
  'account/dashboard.php' => ['dashboard.css'],
  'account/orders.php' => ['dashboard.css'],
  'account/wishlist.php' => ['dashboard.css'],
  'account/settings.php' => ['dashboard.css']
];

$globalStyles = ['tokens.css', 'style.css', 'header.css', 'mobile.css', 'footer.css'];
$stylesToLoad = array_merge($globalStyles, $pageStyles[$routePath] ?? []);

$routeBodyClass = 'page-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower(str_replace('.php', '', $routePath)));
$bodyClass = trim(($pageClass ?? '') . ' ' . $routeBodyClass);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Riches Corsos'; ?></title>
  <link rel="icon" type="image/png" href="<?= $basePath; ?>/assets/images/logo1.png">
  <?php foreach ($stylesToLoad as $styleFile): ?>
    <link rel="stylesheet" href="<?= $basePath; ?>/assets/css/<?= htmlspecialchars($styleFile); ?>">
  <?php endforeach; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <?php if (!empty($metaDescription)): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDescription); ?>">
  <?php endif; ?>
  <?php if (!empty($pageTitle) || !empty($metaDescription)): ?>
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? ''); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription ?? ''); ?>">
  <?php endif; ?>
  <?php if (!empty($metaImage)): ?>
    <meta property="og:image" content="<?= htmlspecialchars($metaImage); ?>">
  <?php endif; ?>
  <meta property="og:type" content="<?= isset($ogType) ? htmlspecialchars($ogType) : 'website'; ?>">
  <?php if (!empty($canonicalUrl)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl); ?>">
  <?php endif; ?>
</head>

<body class="<?= htmlspecialchars($bodyClass); ?>">
  <header class="whb-header">
      <!-- =============================
        TOP HEADER (HAMBURGER LEFT, LOGO CENTER, CART RIGHT)
      ============================== -->
      <div class="top-header">
        <div class="whb-general-header-inner">

          <!-- HAMBURGER (mobile left) -->
          <button class="hamburger-toggle" type="button" aria-label="Open menu" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
          </button>

          <!-- LOGO (center) -->
          <div class="site-logo">
            <a href="<?= $basePath; ?>/index.php" aria-label="Site logo">
              <img src="<?= $basePath; ?>/assets/images/logo1.png" alt="Riches Corsos" class="logo-img" width="300" height="50">
            </a>
          </div>

          <!-- RIGHT TOOLS (desktop: search + wishlist + cart + account) -->
          <div class="header-tools">

            <!-- ACCOUNT (desktop only) -->
            <div class="wd-header-my-account account-icon">
              <button class="header-icon account-toggle" type="button" aria-label="Account" aria-expanded="false">
                <svg viewBox="0 0 24 24">
                  <circle cx="12" cy="8" r="4" />
                  <path d="M4 22c0-4 16-4 16 0" />
                </svg>
              </button>

              <div class="wd-dropdown">
                <?php if ($isLoggedIn): ?>
                  <div class="login-dropdown-inner">
                    <div class="wd-heading">
                      <span class="title">Hello, <?= htmlspecialchars($userName); ?></span>
                    </div>
                    <div class="account-menu">
                      <a href="<?= $basePath; ?>/account/dashboard.php" class="account-menu-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="3" y="3" width="7" height="7" />
                          <rect x="14" y="3" width="7" height="7" />
                          <rect x="14" y="14" width="7" height="7" />
                          <rect x="3" y="14" width="7" height="7" />
                        </svg>
                        My Dashboard
                      </a>
                      <a href="<?= $basePath; ?>/account/orders.php" class="account-menu-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="3" y="3" width="18" height="18" rx="2" />
                          <path d="M9 3v18M15 3v18" />
                        </svg>
                        My Orders
                      </a>
                      <a href="<?= $basePath; ?>/account/wishlist.php" class="account-menu-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M12 21s-7-4.35-10-9a6 6 0 0 1 10-6 6 6 0 0 1 10 6c-3 4.65-10 9-10 9z" />
                        </svg>
                        Wishlist
                      </a>
                      <a href="<?= $basePath; ?>/account/settings.php" class="account-menu-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="12" cy="12" r="3" />
                          <path d="M12 1v6m0 6v6M1 12h6m6 0h6" />
                        </svg>
                        Settings
                      </a>
                    </div>
                    <a href="<?= $basePath; ?>/account/logout.php" class="btn-logout">Logout</a>
                  </div>
                <?php else: ?>
                  <div class="login-dropdown-inner">
                    <div class="wd-heading">
                      <span class="title">Sign in</span>
                      <a href="<?= $basePath; ?>/account/register.php" class="create-account-link">Create Account</a>
                    </div>
                    <form action="<?= $basePath; ?>/account/login.php" method="POST">
                      <?= csrfField(); ?>
                      <p>
                        <label>Email</label>
                        <input type="email" name="email" placeholder="you@email.com">
                      </p>
                      <p>
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••">
                      </p>
                      <div class="form-options">
                        <label class="remember-me">
                          <input type="checkbox" name="remember">
                          <span>Remember me</span>
                        </label>
                        <a href="<?= $basePath; ?>/account/forgot-password.php" class="forgot-password">Forgot password?</a>
                      </div>
                      <button type="submit">Log in</button>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- SEARCH (desktop only) -->
            <button class="header-icon search-icon" id="searchToggle" type="button" aria-label="Search" aria-expanded="false" title="Search Bar">
              <svg viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </button>

            <!-- WISHLIST (desktop only) -->
            <button class="header-icon wishlist-icon" id="wishlistBtn" type="button" aria-label="Wishlist" title="Wishlist">
              <svg viewBox="0 0 24 24">
                <path d="M12 21s-7-4.35-10-9a6 6 0 0 1 10-6 6 6 0 0 1 10 6c-3 4.65-10 9-10 9z" />
              </svg>
              <span class="badge" id="wishlistCount"><?= $wishlistCount; ?></span>
            </button>

            <!-- CART (right on all screens) -->
            <button class="header-icon cart-icon" id="cartToggle" type="button" aria-label="Cart" aria-expanded="false" title="Shopping Cart">
              <svg viewBox="0 0 24 24">
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path d="M1 1h4l2.6 13h11.4" />
              </svg>
              <span class="badge" id="cartCount"><?= $cartCount; ?></span>
            </button>
            <span class="cart-total" id="cartTotal">$<?= number_format($cartTotalAmount, 2); ?></span>

          </div>
        </div>
      </div>

    </header>

    <!-- =============================
        SECOND HEADER (STICKY MENU)
    ============================== -->
    <div class="bottom-header">
      <nav class="main-nav">
        <ul class="menu">
          <?php
          $navLinks = [
            'index.php'              => 'Home',
            'shop/shop.php'          => 'Available Puppies',
            'pages/about.php'        => 'About Us',
            'pages/testimonials.php' => 'Testimonials',
            'blog/index.php'         => 'Blog',
            'pages/faqs.php'         => 'FAQs',
            'pages/contact.php'      => 'Contact Us',
          ];
          foreach ($navLinks as $path => $label):
            $isActive = ($routePath === $path) ? ' class="active"' : '';
          ?>
            <li><a href="<?= $basePath; ?>/<?= $path; ?>"<?= $isActive; ?>><?= $label; ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>

  <div class="website-wrapper">

  <?php require __DIR__ . '/../inc/cookie-consent.php'; ?>

  <div class="drawer-overlay" id="drawerOverlay"></div>

  <!-- LEFT SIDE MENU (Mobile Drawer) -->
  <div class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-content">
      <button class="drawer-close" type="button" aria-label="Close menu">&times;</button>
      <nav class="drawer-nav">
        <ul>
          <li><a href="<?= $basePath; ?>/index.php">Home</a></li>
          <li><a href="<?= $basePath; ?>/shop/shop.php">Available Puppies</a></li>
          <li><a href="<?= $basePath; ?>/pages/about.php">About Us</a></li>
          <li><a href="<?= $basePath; ?>/pages/testimonials.php">Testimonials</a></li>
          <li><a href="<?= $basePath; ?>/blog/index.php">Blog</a></li>
          <li><a href="<?= $basePath; ?>/pages/faqs.php">FAQs</a></li>
          <li><a href="<?= $basePath; ?>/pages/contact.php">Contact Us</a></li>
          <li class="divider"></li>
          <?php if ($isLoggedIn): ?>
            <li><a href="<?= $basePath; ?>/account/logout.php" class="drawer-login">Logout</a></li>
          <?php else: ?>
            <li><a href="<?= $basePath; ?>/account/login.php" class="drawer-login">Login</a></li>
            <li><a href="<?= $basePath; ?>/account/register.php" class="drawer-login">Register</a></li>
          <?php endif; ?>
          <li><a href="<?= $isLoggedIn ? $basePath . '/account/wishlist.php' : $basePath . '/account/login.php?redirect=wishlist'; ?>" class="drawer-wishlist">Wishlist</a></li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- SEARCH OVERLAY -->
  <div class="search-overlay" id="searchOverlay">
    <div class="search-inner">
      <button class="close-search" type="button" aria-label="Close Search">✕</button>
      <form action="<?= $basePath; ?>/search.php" method="GET" id="searchForm">
        <input type="text" name="q" placeholder="Search puppies, info, blog…" autofocus />
      </form>
    </div>
  </div>

  <!-- MINI CART -->
  <div class="mini-cart" id="miniCart">
    <div class="mini-cart-header">
      <h3>Your Cart</h3>
      <button class="close-cart" type="button" aria-label="Close Cart">✕</button>
    </div>
    <div class="mini-cart-content" id="miniCartContent"></div>
    <div class="mini-cart-footer">
      <div class="cart-subtotal">
        <span>Subtotal:</span>
        <span id="miniCartSubtotal">$0.00</span>
      </div>
      <a href="<?= $basePath; ?>/pages/cart.php" class="view-cart-btn">View Full Cart</a>
    </div>
  </div>

  <!-- MOBILE BOTTOM NAV (wishlist left, cart center, account right) -->
  <nav class="mobile-bottom-nav">
    <button class="bottom-nav-item" id="bottomWishlist" type="button" aria-label="Wishlist">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 21s-7-4.35-10-9a6 6 0 0 1 10-6 6 6 0 0 1 10 6c-3 4.65-10 9-10 9z" />
      </svg>
      <span class="bottom-badge" id="bottomWishlistCount"><?= $wishlistCount; ?></span>
      <span class="bottom-nav-label">Wishlist</span>
    </button>
    <button class="bottom-nav-item" id="bottomAccount" type="button" aria-label="Account">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="8" r="4" />
        <path d="M4 22c0-4 16-4 16 0" />
      </svg>
      <span class="bottom-nav-label">Account</span>
    </button>
  </nav>
