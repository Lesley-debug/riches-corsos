<footer class="footer-container">
    <div class="footer-main container">
        <div class="footer-column footer-brand">
            <img src="<?= $basePath; ?>/assets/images/logo1.png" alt="Riches Corsos Logo" class="logo-img footer-logo" loading="lazy" decoding="async">
            <p>
                Riches Corsos breeds Cane Corso puppies with a focus on health, temperament, and family readiness.
                Our breeding program offers trusted bloodlines, thoughtful socialization, and ongoing support.
            </p>
            <a href="<?= $basePath; ?>/shop/shop.php" class="footer-cta">Browse Available Puppies</a>
        </div>

        <div class="footer-column footer-links">
            <h3>Explore</h3>
            <ul>
                <li><a href="<?= $basePath; ?>/index.php">Home</a></li>
                <li><a href="<?= $basePath; ?>/shop/shop.php">Available Puppies</a></li>
                <li><a href="<?= $basePath; ?>/pages/about.php">About Us</a></li>
                <li><a href="<?= $basePath; ?>/pages/contact.php">Contact</a></li>
            </ul>
        </div>

        <div class="footer-column footer-links">
            <h3>Support</h3>
            <ul>
                <li><a href="<?= $basePath; ?>/pages/faqs.php">FAQs</a></li>
                <li><a href="<?= $basePath; ?>/pages/testimonials.php">Testimonials</a></li>
                <li><a href="<?= $basePath; ?>/pages/contact.php">Customer Care</a></li>
                <li><a href="<?= $basePath; ?>/pages/terms.php">Terms &amp; Conditions</a></li>
                <li><a href="<?= $basePath; ?>/pages/privacy.php">Privacy Policy</a></li>
            </ul>
        </div>

        <div class="footer-column footer-contact">
            <h3>Contact</h3>
            <ul>
                <li><a href="mailto:info@richescorsos.com">info@richescorsos.com</a></li>
                <li><a href="tel:+1214212-3023">+1 (214) 212-3023</a></li>
                <li><a href="<?= $basePath; ?>/pages/contact.php">Send a message</a></li>
            </ul>
            <div class="footer-social">
                <a href="https://www.facebook.com/" target="_blank" rel="noreferrer" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22 12.07C22 6.49 17.52 2 11.93 2S1.87 6.49 1.87 12.07C1.87 17.09 5.59 21.2 10.36 21.98v-7.02H7.64v-2.9h2.72V9.79c0-2.7 1.61-4.18 4.08-4.18 1.18 0 2.42.21 2.42.21v2.66h-1.37c-1.35 0-1.77.84-1.77 1.7v2.05h3.01l-.48 2.9h-2.53V21.98C18.41 21.2 22 17.09 22 12.07z" />
                    </svg>
                </a>
                <a href="https://www.tiktok.com/" target="_blank" rel="noreferrer" aria-label="TikTok">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-bottom container">
        <p>© 2026 <a href="<?= $basePath; ?>/index.php">Riches Corsos</a>. All rights reserved.
            <a href="<?= $basePath; ?>/admin/login.php" class="footer-admin-link"></a>
        </p>
    </div>
</footer>

<script>
    window.__RC = <?= json_encode([
                        'basePath'      => $basePath ?? '',
                        'isLoggedIn'    => $isLoggedIn ?? false,
                        'wishlistCount' => (int)($wishlistCount ?? 0),
                    ], JSON_HEX_TAG | JSON_HEX_AMP); ?>;
</script>
<script src="<?= $basePath; ?>/assets/js/site.js" defer></script>
</body>

</html>