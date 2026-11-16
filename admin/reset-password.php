<?php
$adminApiBase = rtrim((string)(getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
$publicAppUrl = rtrim((string)(getenv('FLOW_APP_URL') ?: ''), '/');
$logoBase = $publicAppUrl !== '' ? $publicAppUrl . '/logo' : '../logo';
$e = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer'); // the reset token is in this page's URL; never leak it to other sites
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow,noarchive"><meta name="referrer" content="no-referrer"><meta name="theme-color" content="#071930">
  <title>Reset admin password · Flowwithlit</title>
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $e($logoBase); ?>/flowwithlit-icon-32.png">
  <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap">
  <link rel="stylesheet" href="assets/auth.css?v=1">
  <style>
    .sent{display:flex;gap:12px;align-items:flex-start;border:1px solid #cfe9dc;border-radius:4px;background:#f1faf5;padding:14px 16px;color:#1f5f43;font-size:15px;line-height:1.5}
    .sent b{display:grid;flex:none;width:24px;height:24px;place-items:center;border-radius:50%;background:#1f9d6a;color:#fff;font-size:13px}
    .hint{margin:-12px 0 22px;color:#5b6b7b;font-size:13px}
    .link-as-button{display:flex;align-items:center;justify-content:center;text-decoration:none}
  </style>
</head>
<body>
  <main class="page">
    <img class="logo" src="<?php echo $e($logoBase); ?>/flowwithlit-wordmark-light.png" alt="Flowwithlit" width="201" height="58">
    <div class="card">
      <div id="alert" class="alert" hidden role="alert"></div>

      <section id="checkingPanel" class="panel">
        <h1>Reset your password</h1>
        <p class="lead">Checking your reset link…</p>
      </section>

      <section id="formPanel" class="panel" hidden>
        <h1>Choose a new password</h1>
        <p class="lead">For <strong id="maskedEmail"></strong></p>
        <form id="resetForm" novalidate>
          <label class="field"><span class="sr-only">New password</span><input id="newPassword" class="password-input" type="password" autocomplete="new-password" minlength="12" placeholder="New password" required><button class="password-toggle" type="button" data-password-toggle="newPassword" aria-label="Show password"><i data-lucide="eye" aria-hidden="true"></i></button></label>
          <p class="hint">At least 12 characters. A short phrase is easier to remember.</p>
          <label class="field"><span class="sr-only">Confirm new password</span><input id="confirmPassword" class="password-input" type="password" autocomplete="new-password" minlength="12" placeholder="Confirm new password" required><button class="password-toggle" type="button" data-password-toggle="confirmPassword" aria-label="Show password"><i data-lucide="eye" aria-hidden="true"></i></button></label>
          <button id="resetButton" class="button" type="submit">Update password</button>
        </form>
      </section>

      <section id="invalidPanel" class="panel" hidden>
        <h1>Link expired</h1>
        <p class="lead" id="invalidText">This reset link has expired or was already used.</p>
        <a class="button link-as-button" href="login.php">Back to sign in</a>
        <div class="links"><a class="link-button" href="login.php#forgot">Request a new link</a></div>
      </section>

      <section id="donePanel" class="panel" hidden>
        <h1>Password updated</h1>
        <div class="sent" role="status" style="margin-bottom:24px"><b aria-hidden="true">✓</b><span>Your password has been changed and you've been signed out everywhere.</span></div>
        <a class="button link-as-button" href="login.php?reset=done">Sign in</a>
      </section>
    </div>
    <p class="below"><a href="login.php">Back to admin sign in</a><small>Restricted access · All administrator actions are audited</small></p>
  </main>
  <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" integrity="sha384-uTYyvsSSUZeaPhb5RbKlQa0zY/WpX/QHfvg2mczXyBQOpkWPEDy9lczyp+w7SKXu" crossorigin="anonymous"></script>
  <script>
    const $=id=>document.getElementById(id);
    const adminApiBase=<?php echo json_encode($adminApiBase, JSON_UNESCAPED_SLASHES); ?>;
    const panels=['checkingPanel','formPanel','invalidPanel','donePanel'];
    const show=id=>panels.forEach(p=>$(p).hidden=p!==id);
    const notice=(m='')=>{$('alert').textContent=m;$('alert').hidden=!m};
    // Read the token once, then drop it from the address bar and history.
    const token=new URLSearchParams(location.search).get('token')||'';
    history.replaceState(null,'',location.pathname);
    const invalid=msg=>{if(msg)$('invalidText').textContent=msg;show('invalidPanel')};
    (async()=>{
      if(!/^[a-f0-9]{64}$/.test(token))return invalid('This reset link is incomplete. Open the link from your email again, or request a new one.');
      try{
        const r=await fetch(`${adminApiBase}/reset-password.php?token=${encodeURIComponent(token)}`,{headers:{Accept:'application/json'}});
        const d=await r.json().catch(()=>({}));
        if(!r.ok||!d.success)return invalid(d.error);
        $('maskedEmail').textContent=d.email;show('formPanel');$('newPassword').focus();
      }catch{invalid('We could not check this link. Check your connection and refresh the page.')}
    })();
    $('resetForm').addEventListener('submit',async e=>{
      e.preventDefault();notice();
      const p=$('newPassword').value,c=$('confirmPassword').value;
      if(p.length<12)return notice('Use at least 12 characters.'),$('newPassword').focus();
      if(p!==c)return notice('The two passwords do not match.'),$('confirmPassword').focus();
      const b=$('resetButton');b.disabled=true;b.innerHTML='<span class="button-spinner" aria-hidden="true"></span>Updating…';
      try{
        const r=await fetch(`${adminApiBase}/reset-password.php`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({token,password:p})});
        const d=await r.json().catch(()=>({}));
        if(r.status===410)return invalid(d.error);
        if(!r.ok||!d.success)throw new Error(d.error||'Your password could not be updated.');
        show('donePanel');
      }catch(err){notice(err.message)}finally{b.disabled=false;b.textContent='Update password'}
    });
    document.querySelectorAll('[data-password-toggle]').forEach(btn=>btn.addEventListener('click',()=>{const i=$(btn.dataset.passwordToggle),reveal=i.type==='password';i.type=reveal?'text':'password';btn.setAttribute('aria-label',reveal?'Hide password':'Show password');btn.innerHTML=`<i data-lucide="${reveal?'eye-off':'eye'}" aria-hidden="true"></i>`;window.lucide?.createIcons({attrs:{'aria-hidden':'true'}})}));
    window.lucide?.createIcons({attrs:{'aria-hidden':'true'}});
  </script>
</body></html>
