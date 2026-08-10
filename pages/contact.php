<?php
require_once __DIR__ . '/../inc/security.php';
require_once __DIR__ . '/../inc/paths.php';
require_once __DIR__ . '/../inc/email.php';

$contactAlert = $_SESSION['contact_alert'] ?? null;
unset($_SESSION['contact_alert']);

$contactValues = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCSRF();
    foreach ($contactValues as $field => $value) {
        $contactValues[$field] = trim((string)($_POST[$field] ?? ''));
    }

    $missingRequired = [];
    foreach (['name', 'email', 'phone', 'address', 'subject'] as $field) {
        if ($contactValues[$field] === '') {
            $missingRequired[] = $field;
        }
    }

    if (!empty($missingRequired)) {
        $contactAlert = [
            'type' => 'error',
            'message' => 'Please complete every required field before submitting.',
        ];
    } elseif (!filter_var($contactValues['email'], FILTER_VALIDATE_EMAIL)) {
        $contactAlert = [
            'type' => 'error',
            'message' => 'Please enter a valid email address.',
        ];
    } else {
        $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $adminEmail = getenv('MAIL_CONTACT_TO') ?: getenv('MAIL_USERNAME') ?: 'barbarapettra@gmail.com';
        $safeSubject = trim(preg_replace('/[\r\n]+/', ' ', $contactValues['subject']));
        $emailSubject = 'Website contact: ' . substr($safeSubject, 0, 140);
        $emailBody = '
            <h2>New contact form message</h2>
            <div class="info-box">
                <p><strong>Name:</strong> ' . $escape($contactValues['name']) . '</p>
                <p><strong>Email:</strong> ' . $escape($contactValues['email']) . '</p>
                <p><strong>Phone:</strong> ' . $escape($contactValues['phone']) . '</p>
                <p><strong>Address:</strong> ' . $escape($contactValues['address']) . '</p>
                <p><strong>Subject:</strong> ' . $escape($contactValues['subject']) . '</p>
            </div>
            <h3>Message</h3>
            <p>' . nl2br($escape($contactValues['message'] !== '' ? $contactValues['message'] : 'No additional message provided.')) . '</p>
        ';

        $sent = sendEmail(
            $adminEmail,
            'Riches Corsos',
            $emailSubject,
            $emailBody,
            $contactValues['email'],
            $contactValues['name']
        );

        if ($sent) {
            $_SESSION['contact_alert'] = [
                'type' => 'success',
                'message' => 'Thank you. Your message has been sent successfully.',
            ];
            header('Location: ' . site_url('/pages/contact.php#contactForm'));
            exit;
        }

        $contactAlert = [
            'type' => 'error',
            'message' => 'Sorry, the message could not be sent right now. Please check the email app password settings and try again.',
        ];
    }
}

require __DIR__ . '/../template/header.php';
?>

<section class="page-hero" style="background-image: url('<?= $basePath; ?>/assets/images/happy_puppy.webp')">
  <div class="page-hero-inner">
    <span class="page-hero-eyebrow">Get In Touch</span>
    <h1>Contact Us</h1>
    <p>We’re here to answer your questions and guide you to the right Cane Corso puppy.</p>
    <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
      <a href="<?= $basePath; ?>/index.php">Home</a><span>/</span><span>Contact</span>
    </nav>
  </div>
</section>

<section class="contact-shell" id="contactForm">
    <div class="contact-grid">
        <div class="contact-left">
            <h2>Get In Touch</h2>
            <p class="contact-intro">
                For availability, health details, and placement questions, send us a message below.
            </p>

            <?php if ($contactAlert): ?>
                <div class="contact-alert contact-alert--<?= htmlspecialchars($contactAlert['type']); ?>">
                    <?= htmlspecialchars($contactAlert['message']); ?>
                </div>
            <?php endif; ?>

            <form class="contact-form" method="POST" action="<?= site_url('/pages/contact.php#contactForm'); ?>">
                <input type="text" name="name" placeholder="Your Name *" value="<?= htmlspecialchars($contactValues['name']); ?>" required>
                <input type="email" name="email" placeholder="Your Email *" value="<?= htmlspecialchars($contactValues['email']); ?>" required>
                <input type="tel" name="phone" placeholder="Your Phone Number *" value="<?= htmlspecialchars($contactValues['phone']); ?>" required>
                <input type="text" name="address" placeholder="Your Address (City/State) *" value="<?= htmlspecialchars($contactValues['address']); ?>" required>
                <input type="text" name="subject" placeholder="Subject (Or Name of Puppy) *" value="<?= htmlspecialchars($contactValues['subject']); ?>" required>
                <textarea name="message" placeholder="Type Your Message (Optional)"><?= htmlspecialchars($contactValues['message']); ?></textarea>
                <button type="submit" class="submit-btn">Submit</button>
                <?= csrfField(); ?>
            </form>
        </div>

        <aside class="contact-right">
            <h3 class="team-text">Dedicated breeders committed to healthy, well-socialized puppies.</h3>

            <div class="image-wrapper">
                <img src="<?= $basePath; ?>/assets/images/luna.webp" alt="Riches Corsos" class="main-image" id="mainImage">
            </div>

            <div class="thumb-scroll">
                <img src="<?= $basePath; ?>/assets/images/luna.webp" alt="Puppy" class="thumb active" data-src="<?= $basePath; ?>/assets/images/luna.webp">
                <img src="<?= $basePath; ?>/assets/images/happy_puppy.webp" alt="Puppy" class="thumb" data-src="<?= $basePath; ?>/assets/images/happy_puppy.webp">
                <img src="<?= $basePath; ?>/assets/images/contact1.jpeg" alt="Puppy" class="thumb" data-src="<?= $basePath; ?>/assets/images/contact1.jpeg">
                <img src="<?= $basePath; ?>/assets/images/contact2.jpeg" alt="Puppy" class="thumb" data-src="<?= $basePath; ?>/assets/images/contact2.jpeg">
            </div>

            <div class="contact-notes">
                <p><strong>Email:</strong> <a href="mailto:info@richescorsos.com">info@richescorsos.com</a></p>
                <p><strong>Phone:</strong> <a href="tel:+14707216309">+1 (470) 721-6309</a></p>
                <p><strong>Response Time:</strong> Usually within 24 hours.</p>
            </div>
        </aside>
    </div>
</section>

<script src="<?= $basePath; ?>/assets/js/contact.min.js" defer></script>

<?php require __DIR__ . '/../template/footer.php'; ?>
