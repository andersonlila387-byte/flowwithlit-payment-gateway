<?php
/**
 * Flowwithlit shared footer: call-to-action band, white footer and scripts.
 * Optional page variables: $hideCta (bool), $extraScripts (string of <script> tags).
 */
?>
<?php if (empty($hideCta)): ?>
<section class="cta-band">
  <div class="wrap">
    <div class="cta-band__box" data-reveal>
      <h2>Ready to start flowing with smooth payments today?</h2>
      <p>Join thousands of people and sellers using Flowwithlit for easy Crypto, Naira, and instant transfers.</p>
      <a href="register" class="btn btn--light"><span>Create free account</span><?= arrow() ?></a>
    </div>
  </div>
</section>
<?php endif; ?>

<footer class="site-footer">
  <div class="wrap">
    <div class="site-footer__grid">
      <div>
        <a href="index" class="site-footer__brand" aria-label="Flowwithlit home"><img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" width="153" height="44" loading="lazy" /></a>
        <p>The all-in-one payment gateway for seamless crypto &amp; naira banking, transfers, and e-commerce payments.</p>
      </div>
      <div>
        <h4>Product</h4>
        <ul><li><a href="index#features">Features</a></li><li><a href="developers">Developers</a></li><li><a href="index#security">Security</a></li><li><a href="index#integrations-section">Integrations</a></li></ul>
      </div>
      <div>
        <h4>Company</h4>
        <ul><li><a href="about">About us</a></li><li><a href="contact">Contact us</a></li><li><a href="index#faq">FAQ</a></li></ul>
      </div>
      <div>
        <h4>Stay updated</h4>
        <form class="subscribe" aria-label="Subscribe to updates">
          <input type="email" required placeholder="Enter your email" aria-label="Email address" />
          <button type="submit" class="btn btn--navy btn--sm"><span>Subscribe</span></button>
        </form>
      </div>
    </div>
    <div class="site-footer__bottom">
      <span>© <?= date('Y') ?> Flowwithlit. All rights reserved.</span>
      <ul>
        <li><a href="terms">Terms</a></li><li><a href="privacy">Privacy</a></li><li><a href="refund-policy">Refunds</a></li><li><a href="cookie-policy">Cookies</a></li><li><a href="security-policy">Security</a></li><li><a href="aml-kyc-policy">AML / KYC</a></li><li><a href="acceptable-use">Acceptable use</a></li><li><a href="sla">SLA</a></li>
      </ul>
    </div>
  </div>
</footer>

<script src="<?= e(asset('assets/site/site.js')) ?>" defer></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
