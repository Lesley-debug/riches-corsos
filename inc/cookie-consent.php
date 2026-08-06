<?php
// Only render if the user hasn't already accepted/declined cookies
if (!isset($_COOKIE['rc_cookie_consent'])): ?>
<div class="cookie-banner" id="cookieBanner" role="dialog" aria-live="polite" aria-label="Cookie consent">
  <div class="cookie-banner-inner">
    <p>
      We use cookies to improve your experience. By continuing to use this site you agree to our
      <a href="<?= $basePath; ?>/pages/privacy.php">Privacy Policy</a> and
      <a href="<?= $basePath; ?>/pages/terms.php">Terms &amp; Conditions</a>.
    </p>
    <div class="cookie-actions">
      <button class="cookie-btn cookie-accept" id="cookieAccept" type="button">Accept</button>
      <button class="cookie-btn cookie-decline" id="cookieDecline" type="button">Decline</button>
    </div>
  </div>
</div>
<?php endif; ?>
