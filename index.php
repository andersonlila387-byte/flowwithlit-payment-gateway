<?php
$pageTitle = 'Flowwithlit — Pay, Swap & Transfer Money with Crypto & Naira';
$pageDesc  = 'Flowwithlit is your all-in-one payment gateway. Pay with Crypto or Naira, convert currencies instantly, and send money fast with Flow Tags.';
$activePage = 'home';
$useTailwind = false; // the landing page uses its own production CSS, no Tailwind
$extraHead = '  <link rel="stylesheet" href="assets/landing/landing.css?v=' . (int) @filemtime(__DIR__ . '/assets/landing/landing.css') . '" />' . "\n";
require_once __DIR__ . '/includes/header.php';
$extraScripts = '<script src="' . e(asset('assets/landing/landing.js')) . '" defer></script>';

/**
 * Section photo from images/landing/<name>.jpg (or .webp/.png). With no alt text it only reports
 * whether the file exists, so a section falls back to a clean one-column layout until it is added.
 */
function landingImage(string $name, ?string $alt = null): string {
    foreach (['webp', 'jpg', 'png'] as $ext) {
        $path = 'images/landing/' . $name . '.' . $ext;
        if (!is_file(__DIR__ . '/' . $path)) continue;
        if ($alt === null) return $path;
        [$w, $h] = @getimagesize(__DIR__ . '/' . $path) ?: [1456, 1088];
        return '<figure class="media" data-reveal><img src="' . e($path) . '" alt="' . e($alt) . '" width="' . (int) $w . '" height="' . (int) $h . '" loading="lazy" decoding="async" /></figure>';
    }
    return '';
}
?>

<main>
  <!-- Hero: centered over the background image -->
  <section class="hero">
    <div class="hero__bg" aria-hidden="true"></div>
    <div class="container hero__inner">
      <span class="pill" data-reveal><b>New</b> Instant Crypto &amp; Naira Swaps</span>
      <h1 class="split">Pay, swap &amp; transfer money<br /> in Crypto and Naira</h1>
      <p class="hero__text" data-reveal style="--d:350ms">Accept, swap and send Naira and crypto from one simple wallet.</p>
      <div class="hero__ctas" data-reveal style="--d:480ms">
        <a href="register" class="btn btn--light"><span>Create free account</span><?= arrow() ?></a>
        <a href="login" class="btn btn--glass"><span>Access your account</span><?= arrow() ?></a>
      </div>
      <p class="hero__note" data-reveal style="--d:600ms">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
        No hidden fees · Naira and crypto in one wallet
      </p>
    </div>
    <span class="hero__scroll" aria-hidden="true"></span>
  </section>

  <!-- Supported assets marquee -->
  <section class="marquee" aria-label="Supported currencies and payment methods">
    <p class="marquee__label">One wallet for the money your customers already use</p>
    <div class="marquee__track">
      <?php for ($copy = 0; $copy < 2; $copy++): ?>
      <div class="marquee__group"<?= $copy ? ' aria-hidden="true"' : '' ?>>
        <span class="marquee__item"><span class="sym">₦</span>Naira</span><span class="marquee__sep"></span>
        <span class="marquee__item"><img src="logo/crypto/usdt.png" alt="" width="26" height="26" />USDT</span><span class="marquee__sep"></span>
        <span class="marquee__item"><img src="logo/crypto/btc.png" alt="" width="26" height="26" />Bitcoin</span><span class="marquee__sep"></span>
        <span class="marquee__item"><img src="logo/crypto/eth.png" alt="" width="26" height="26" />Ethereum</span><span class="marquee__sep"></span>
        <span class="marquee__item"><img src="logo/crypto/usdc.png" alt="" width="26" height="26" />USDC</span><span class="marquee__sep"></span>
        <span class="marquee__item"><span class="sym">@</span>Flow Tags</span><span class="marquee__sep"></span>
        <span class="marquee__item"><span class="sym">⇄</span>Bank transfers</span><span class="marquee__sep"></span>
        <span class="marquee__item"><span class="sym">↗</span>Payment links</span><span class="marquee__sep"></span>
      </div>
      <?php endfor; ?>
    </div>
  </section>

  <!-- Simple payments + stats -->
  <section class="section">
    <div class="container">
      <span class="tag" data-reveal>Simple payments</span>
      <p class="statement">Fast, easy money moves with no hidden fees.</p>
      <div class="grid grid--3" data-stagger>
        <article class="card stat" data-reveal>
          <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9z"/></svg></span>
          <div class="stat__value">Instant</div>
          <h3>Zero waiting time</h3>
          <p>Swaps and transfers settle in real time.</p>
        </article>
        <article class="card stat" data-reveal>
          <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
          <div class="stat__value num" data-count="99.99" data-suffix="%">99.99%</div>
          <h3>Platform uptime</h3>
          <p>Always on, so your money keeps moving.</p>
        </article>
        <article class="card stat" data-reveal>
          <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="M2.5 10h19"/></svg></span>
          <div class="stat__value">Multi-asset</div>
          <h3>Crypto &amp; fiat</h3>
          <p>USDT, BTC, ETH and Naira in one place.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="section section--surface" id="features">
    <div class="container">
      <span class="tag" data-reveal>Full payment suite</span>
      <h2 class="heading" data-reveal>Everything you need to pay &amp; get paid</h2>
      <div class="media-row<?= landingImage('merchant') ? '' : ' media-row--solo' ?>">
        <?= landingImage('merchant', 'Business owner receiving a payment on her phone') ?>
        <div class="rows" data-stagger>
          <div class="row-item" data-reveal>
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M7 16V4M7 4L3 8M7 4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg></span>
            <div><h3>Instant Crypto &amp; Naira swaps</h3><p>Naira to USDT, BTC or ETH and back, at live rates.</p></div>
          </div>
          <div class="row-item" data-reveal>
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 006 0v-1a10 10 0 10-3.9 7.9"/></svg></span>
            <div><h3>Simple Flow Tags</h3><p>Get paid with a handle like <code>@alex</code>, no account numbers.</p></div>
          </div>
          <div class="row-item" data-reveal>
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/></svg></span>
            <div><h3>E-commerce plugins</h3><p>Connect WooCommerce, Shopify or your own site in minutes.</p></div>
          </div>
          <div class="row-item" data-reveal>
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1.5" y="4" width="21" height="16" rx="2.5"/><path d="M1.5 10h21"/></svg></span>
            <div><h3>Fast payouts</h3><p>Withdraw to any Nigerian bank or crypto wallet.</p></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How it works -->
  <section class="section" id="how-it-works">
    <div class="container">
      <span class="tag" data-reveal>Made for momentum</span>
      <h2 class="heading" data-reveal>Move money in three simple steps</h2>
            <div class="grid grid--3 steps" data-stagger>
        <div class="steps__line" aria-hidden="true"><i></i></div>
        <article class="card step" data-reveal><span class="step__n">01</span><h3>Create your account</h3><p>Sign up in minutes.</p></article>
        <article class="card step" data-reveal><span class="step__n">02</span><h3>Choose how to move</h3><p>Send, receive, swap or share your Flow Tag.</p></article>
        <article class="card step" data-reveal><span class="step__n">03</span><h3>Track it instantly</h3><p>Every payment, live in one dashboard.</p></article>
      </div>
      <a href="register" class="btn btn--navy section-cta" data-reveal><span>Get started</span><?= arrow() ?></a>
    </div>
  </section>

  <!-- Payment methods -->
  <section class="section section--navy">
    <div class="container">
      <span class="tag" data-reveal>One wallet, more ways to pay</span>
      <h2 class="heading" data-reveal>Every payment method your customers expect</h2>
            <div class="grid grid--4" data-stagger>
        <article class="method" data-reveal><span class="method__sym">₦</span><h3>Naira transfers</h3><p>Send and settle to any Nigerian bank.</p></article>
        <article class="method" data-reveal><span class="method__sym">₿</span><h3>Crypto payments</h3><p>Accept USDT, BTC and more.</p></article>
        <article class="method" data-reveal><span class="method__sym">@</span><h3>Flow Tags</h3><p>Get paid with a simple handle.</p></article>
        <article class="method" data-reveal><span class="method__sym">⇄</span><h3>Instant swaps</h3><p>Naira to crypto and back.</p></article>
      </div>
      <a href="register" class="btn btn--light section-cta" data-reveal><span>Start accepting payments</span><?= arrow() ?></a>
    </div>
  </section>

  <!-- Business tools -->
  <section class="section section--surface" id="integrations-section">
    <div class="container media-row media-row--flip<?= landingImage('seller') ? '' : ' media-row--solo' ?>">
      <div class="media-row__text">
        <span class="tag" data-reveal>Built for businesses</span>
        <h2 class="heading" data-reveal>Payments that fit how your business works</h2>
        <p class="lede" data-reveal>Payment links, checkout, plugins and payouts in one dashboard.</p>
        <ul class="checks" data-reveal>
          <li>Developer-friendly APIs</li>
          <li>Store plugins</li>
          <li>Payment links and payouts</li>
        </ul>
        <a href="developers" class="link-arrow" data-reveal>Explore developer tools <span aria-hidden="true">→</span></a>
      </div>
      <?= landingImage('seller', 'Online store owner packing customer orders beside a laptop') ?>
    </div>
  </section>

  <!-- Security -->
  <section class="section" id="security">
    <div class="container media-row<?= landingImage('security') ? '' : ' media-row--solo' ?>">
      <?= landingImage('security', 'Navy shield with a gold padlock') ?>
      <div class="media-row__text">
        <span class="tag" data-reveal>Safety by design</span>
        <h2 class="heading" data-reveal>Secure payments, clear controls</h2>
        <div class="rows" data-stagger>
          <div class="row-item" data-reveal><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 018 0v3"/></svg></span><div><h3>Protected data</h3><p>Encrypted and handled securely.</p></div></div>
          <div class="row-item" data-reveal><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg></span><div><h3>Verified access</h3><p>Two-factor sign-in and account checks.</p></div></div>
          <div class="row-item" data-reveal><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></span><div><h3>Always visible</h3><p>Every balance and transaction, live.</p></div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="section section--surface">
    <div class="container">
      <span class="tag" data-reveal>Growing with our users</span>
      <h2 class="heading" data-reveal>Built for everyday money movement</h2>
      <div class="grid grid--3 quotes" data-stagger>
        <figure class="card quote" data-reveal><div><div class="stars" aria-label="5 out of 5">★★★★★</div><blockquote>“The Flow Tag makes collecting payments from clients feel effortless.”</blockquote></div><figcaption class="who"><span class="avatar">AO</span><span><strong>Ada O.</strong><small>Creative business owner</small></span></figcaption></figure>
        <figure class="card quote quote--navy" data-reveal><div><div class="stars" aria-label="5 out of 5">★★★★★</div><blockquote>“It gives our customers more ways to pay without making checkout complicated.”</blockquote></div><figcaption class="who"><span class="avatar">TM</span><span><strong>Tomi M.</strong><small>Online store operator</small></span></figcaption></figure>
        <figure class="card quote" data-reveal><div><div class="stars" aria-label="5 out of 5">★★★★★</div><blockquote>“The dashboard is simple, fast, and lets me see every payment at a glance.”</blockquote></div><figcaption class="who"><span class="avatar">KS</span><span><strong>Kunle S.</strong><small>Independent consultant</small></span></figcaption></figure>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section" id="faq">
    <div class="container">
      <span class="tag" data-reveal>Common questions</span>
      <h2 class="heading" data-reveal>Frequently asked questions</h2>
      <div class="faq" data-reveal>
        <?php
        $faqs = [
            ['How fast is Naira to Crypto conversion?', 'It happens instantly! As soon as you confirm your swap, your currency is converted and updated in your wallet balance right away.'],
            ['What is a Flow Tag?', 'Flow Tag is your personal handle (like @yourname). It lets anyone send you money instantly without typing long bank account numbers or crypto addresses.'],
            ['Can I accept payments on my online store?', 'Yes! Flowwithlit offers simple plugins for Shopify, WooCommerce, and WordPress, as well as easy developer APIs for custom sites.'],
            ['Is my money safe on Flowwithlit?', 'Yes! We use bank-grade security, top-level encryption, and strict protection protocols to ensure your money and data stay 100% safe.'],
        ];
        foreach ($faqs as $i => [$question, $answer]): ?>
        <div class="faq__item<?= $i === 0 ? ' open' : '' ?>">
          <button class="faq__q" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="faq-<?= $i ?>"><span><?= e($question) ?></span><span class="faq__plus" aria-hidden="true"></span></button>
          <div class="faq__a" id="faq-<?= $i ?>" role="region"><div><p><?= e($answer) ?></p></div></div>
        </div>
        <?php endforeach; ?>
      </div>
      <p class="faq-contact" data-reveal>Can’t find what you need? <a href="contact">Contact our team</a></p>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
