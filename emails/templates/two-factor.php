<!doctype html>
<html lang="en"><body style="margin:0;background:#f3f7fb;color:#0b2545;font-family:Arial,sans-serif">
  <main style="max-width:560px;margin:32px auto;padding:32px;background:#ffffff;border-radius:16px">
    <img src="<?= htmlspecialchars((getenv('FLOW_APP_URL') ?: 'https://flowwithlit.com').'/logo/flowwithlit-icon-192.png', ENT_QUOTES, 'UTF-8') ?>" alt="Flowwithlit" width="42" height="42" style="border-radius:10px">
    <h1 style="margin:24px 0 8px;font-size:24px">Confirm two-factor authentication</h1>
    <p style="line-height:1.6;color:#52667c">Use this code to enable two-factor authentication on your Flowwithlit account. It expires in 15 minutes.</p>
    <p style="margin:24px 0;padding:16px;background:#eef4ff;border-radius:10px;font-size:28px;font-weight:700;letter-spacing:8px;text-align:center"><?= htmlspecialchars((string) ($code ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
    <p style="line-height:1.6;color:#52667c">If you did not request this change, you can ignore this email.</p>
  </main>
</body></html>
