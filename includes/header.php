<?php
/**
 * Flowwithlit shared header: <head>, fixed top navigation and mobile menu.
 *
 * Page variables (set before including):
 *   $pageTitle, $pageDesc, $activePage ('home'|'about'|'developers'|'contact'|'')
 *   $useTailwind  (default true)  older content pages still use Tailwind utility classes
 *   $extraHead    (optional)      extra <head> markup, e.g. a page stylesheet
 */
if (!isset($pageTitle)) $pageTitle = 'Flowwithlit — Pay, Swap & Transfer Money with Crypto & Naira';
if (!isset($pageDesc)) $pageDesc = 'Flowwithlit is your all-in-one payment gateway. Pay with Crypto or Naira, convert currencies instantly, and send money fast.';
if (!isset($activePage)) $activePage = '';
if (!isset($useTailwind)) $useTailwind = true;

if (!function_exists('e')) {
    function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('arrow')) {
    /** Arrow inside every button; two copies slide past each other on hover. */
    function arrow(): string {
        $svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M7 17L17 7M17 7H8M17 7v9"/></svg>';
        return '<span class="btn__icon">' . $svg . $svg . '</span>';
    }
}
if (!function_exists('asset')) {
    /** Asset URL with a cache-busting version taken from the file's modification time. */
    function asset(string $path): string { return $path . '?v=' . (string) @filemtime(dirname(__DIR__) . '/' . $path); }
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = preg_replace('/[^A-Za-z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
$appUrl = rtrim((string) (getenv('FLOW_APP_URL') ?: $scheme . '://' . $host), '/');
$socialImage = $appUrl . '/logo/flowwithlit-payment-success-social.png';
$navLinks = [
    ['index#features', 'Features', 'features'],
    ['index#how-it-works', 'How it works', 'how'],
    ['about', 'About us', 'about'],
    ['developers', 'Developers', 'developers'],
    ['contact', 'Contact', 'contact'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($pageDesc) ?>" />
  <meta name="theme-color" content="#0b2545" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= e($pageTitle) ?>" />
  <meta property="og:description" content="<?= e($pageDesc) ?>" />
  <meta property="og:image" content="<?= e($socialImage) ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:image" content="<?= e($socialImage) ?>" />
  <link rel="icon" type="image/png" sizes="32x32" href="logo/flowwithlit-icon-32.png" />
  <link rel="apple-touch-icon" sizes="192x192" href="logo/flowwithlit-icon-192.png" />
  <link rel="manifest" href="site.webmanifest" />
  <script type="application/ld+json">{"@context":"https://schema.org","@type":"Organization","name":"Flowwithlit","logo":"logo/flowwithlit-icon-512.png","description":"Payments, wallets, transfers and business tools."}</script>
  <link rel="preconnect" href="https://api.fontshare.com" crossorigin />
  <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700,900&display=swap" />
<?php if ($useTailwind): ?>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Brand theme for the older Tailwind-based pages: navy and white, gold accents, Satoshi type.
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Satoshi', 'system-ui', 'sans-serif'], mono: ['ui-monospace', 'Menlo', 'monospace'] },
          colors: {
            ink: '#0b2545',
            mint: '#faf5e8',
            gold: { 50: '#faf5e8', 300: '#e2c97f', 500: '#c9a44a', 700: '#8a6a1d' },
            brand: { 50: '#f4f6fa', 100: '#e6ebf3', 500: '#0b2545', 600: '#071930' },
            // Old "blue" accents become gold on dark backgrounds and navy on light ones.
            blue: { 50: '#f4f6fa', 100: '#e6ebf3', 200: '#ecdcae', 300: '#e2c97f', 400: '#c9a44a', 500: '#16355f', 600: '#0b2545', 700: '#071930', 800: '#071930', 900: '#050f1f' },
          },
          boxShadow: { card: '0 1px 2px rgba(11,37,69,.04), 0 12px 32px -12px rgba(11,37,69,.18)' },
        },
      },
    };
  </script>
<?php endif; ?>
  <link rel="stylesheet" href="<?= e(asset('assets/site/site.css')) ?>" />
<?= $extraHead ?? '' ?>
</head>
<body>

<header class="site-header">
  <div class="wrap site-header__bar">
    <a href="index" class="site-header__logo" aria-label="Flowwithlit home"><img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" width="139" height="40" /></a>
    <nav class="site-header__links" aria-label="Main">
      <?php foreach ($navLinks as [$href, $label, $key]): ?>
      <a href="<?= e($href) ?>"<?= $activePage === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="site-header__actions">
      <a href="login" class="site-header__signin">Sign in</a>
      <a href="register" class="btn btn--navy btn--sm"><span>Create account</span><?= arrow() ?></a>
      <button type="button" class="site-header__toggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu"><span></span></button>
    </div>
  </div>
</header>

<div class="mobile-menu" id="mobile-menu">
  <nav aria-label="Mobile">
    <?php foreach ($navLinks as [$href, $label, $key]): ?>
    <a href="<?= e($href) ?>"<?= $activePage === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="mobile-menu__ctas">
    <a href="register" class="btn btn--navy"><span>Create free account</span><?= arrow() ?></a>
    <a href="login" class="btn btn--ghost"><span>Sign in</span><?= arrow() ?></a>
  </div>
</div>
