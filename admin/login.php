<?php
$pageTitle = 'Admin sign in · Flowwithlit';
$adminApiBase = rtrim((string)(getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
$publicAppUrl = rtrim((string)(getenv('FLOW_APP_URL') ?: ''), '/');
$logoBase = $publicAppUrl !== '' ? $publicAppUrl . '/logo' : '../logo';
$customerLoginUrl = $publicAppUrl !== '' ? $publicAppUrl . '/login.php' : '../login.php';
$e = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow,noarchive"><meta name="theme-color" content="#071930">
  <title><?php echo $e($pageTitle); ?></title>
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $e($logoBase); ?>/flowwithlit-icon-32.png">
  <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap">
  <link rel="stylesheet" href="assets/auth.css?v=1">
</head>
<body>
  <main class="page">
    <img class="logo" src="<?php echo $e($logoBase); ?>/flowwithlit-wordmark-light.png" alt="Flowwithlit" width="201" height="58">
    <div class="card">
      <div id="alert" class="alert" hidden role="alert"></div>

      <section id="loginPanel" class="panel">
        <h1>Sign in to your account</h1>
        <form id="loginForm">
          <label class="field"><span class="sr-only">Email address</span><input id="email" type="email" autocomplete="username" placeholder="Email address" required></label>
          <label class="field"><span class="sr-only">Password</span><input id="password" class="password-input" type="password" autocomplete="current-password" placeholder="Password" required><button class="password-toggle" type="button" data-password-toggle="password" aria-label="Show password"><i data-lucide="eye" aria-hidden="true"></i></button></label>
          <button id="loginButton" class="button" type="submit">Sign in</button>
        </form>
        <div class="links"><button id="showForgot" class="link-button" type="button">Forgot your password?</button><button id="showBootstrap" class="link-button" type="button" hidden>Set up the first super administrator</button></div>
      </section>

      <section id="forgotPanel" class="panel" hidden>
        <h1>Reset your password</h1>
        <p class="lead" id="forgotLead">Enter your admin email and we'll send you a link to choose a new password.</p>
        <form id="forgotForm">
          <label class="field"><span class="sr-only">Email address</span><input id="forgotEmail" type="email" autocomplete="username" placeholder="Email address" required></label>
          <button id="forgotButton" class="button" type="submit">Send reset link</button>
        </form>
        <div id="forgotSent" class="sent" hidden role="status"><b aria-hidden="true">✓</b><span id="forgotSentText"></span></div>
        <div class="links"><button id="backFromForgot" class="link-button" type="button">Back to sign in</button></div>
      </section>

      <section id="totpPanel" class="panel" hidden>
        <h1>Verify your sign-in</h1>
        <p class="lead">Enter the six-digit code from your authenticator app.</p>
        <form id="totpForm">
          <label class="field"><span class="sr-only">Authenticator code</span><input id="totpCode" class="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" required></label>
          <button id="totpButton" class="button" type="submit">Verify and continue</button>
        </form>
        <div class="links"><button id="backToLogin" class="link-button" type="button">Use another account</button></div>
      </section>

      <section id="bootstrapPanel" class="panel" hidden>
        <h1>Create first super admin</h1>
        <p class="setup-note">This setup is available only until the first administrator is created. It then locks permanently.</p>
        <form id="bootstrapForm">
          <label class="field"><span class="sr-only">Full name</span><input id="setupName" autocomplete="name" placeholder="Full name" required></label>
          <label class="field"><span class="sr-only">Work email</span><input id="setupEmail" type="email" autocomplete="email" placeholder="Work email" required></label>
          <label class="field"><span class="sr-only">Password</span><input id="setupPassword" class="password-input" type="password" autocomplete="new-password" minlength="12" placeholder="Password (at least 12 characters)" required><button class="password-toggle" type="button" data-password-toggle="setupPassword" aria-label="Show password"><i data-lucide="eye" aria-hidden="true"></i></button></label>
          <button id="bootstrapButton" class="button" type="submit">Create super admin</button>
        </form>
        <div class="links"><button id="backFromBootstrap" class="link-button" type="button">Back to sign in</button></div>
      </section>
    </div>
    <p class="below">Not an administrator? <a href="<?php echo $e($customerLoginUrl); ?>">Customer sign in</a><small>Restricted access · All administrator actions are audited</small></p>
  </main>
  <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" integrity="sha384-uTYyvsSSUZeaPhb5RbKlQa0zY/WpX/QHfvg2mczXyBQOpkWPEDy9lczyp+w7SKXu" crossorigin="anonymous"></script>
  <script>
    const $=id=>document.getElementById(id),panels=['loginPanel','totpPanel','bootstrapPanel','forgotPanel'];let pendingToken='',pendingRole='';
    const adminApiBase=<?php echo json_encode($adminApiBase, JSON_UNESCAPED_SLASHES); ?>;
    const show=(id)=>panels.forEach(panel=>$(panel).hidden=panel!==id);const notice=(message='')=>{$('alert').textContent=message;$('alert').hidden=!message};
    const setBusy=(button,busy,label='Processing…')=>{if(busy){button.dataset.label=button.textContent.trim();button.disabled=true;button.classList.add('is-loading');button.setAttribute('aria-busy','true');button.innerHTML=`<span class="button-spinner" aria-hidden="true"></span>${label}`;return;}button.disabled=false;button.classList.remove('is-loading');button.removeAttribute('aria-busy');button.textContent=button.dataset.label||button.textContent;};
    const api=async(path,body,token)=>{const response=await fetch(path,{method:'POST',headers:{'Content-Type':'application/json',...(token?{Authorization:`Bearer ${token}`}:{})},body:JSON.stringify(body)});const data=await response.json().catch(()=>({error:'Unexpected server response.'}));if(!response.ok||!data.success)throw new Error(data.error||'Request failed.');return data};
    const enterConsole=(data)=>{localStorage.setItem('flow_admin_token',data.token);localStorage.setItem('flow_admin_role',data.role||'');window.location.replace('index.php')};
    fetch(`${adminApiBase}/bootstrap.php`,{headers:{Accept:'application/json'}}).then(response=>response.json()).then(status=>{if(status.success&&status.bootstrap_available)$('showBootstrap').hidden=false;}).catch(()=>{});
    $('loginForm').addEventListener('submit',async event=>{event.preventDefault();notice();const button=$('loginButton');setBusy(button,true,'Signing in…');try{const data=await api(`${adminApiBase}/login.php`,{email:$('email').value.trim(),password:$('password').value});if(data.requires_2fa){pendingToken=data.token;pendingRole=data.role;show('totpPanel');$('totpCode').focus();}else enterConsole(data);}catch(error){notice(error.message)}finally{setBusy(button,false)}});
    $('totpForm').addEventListener('submit',async event=>{event.preventDefault();notice();const button=$('totpButton');setBusy(button,true,'Verifying…');try{await api(`${adminApiBase}/totp-login.php`,{code:$('totpCode').value.trim()},pendingToken);enterConsole({token:pendingToken,role:pendingRole});}catch(error){notice(error.message)}finally{setBusy(button,false)}});
    $('bootstrapForm').addEventListener('submit',async event=>{event.preventDefault();notice();const button=$('bootstrapButton');setBusy(button,true,'Creating account…');try{const data=await api(`${adminApiBase}/bootstrap.php`,{full_name:$('setupName').value.trim(),email:$('setupEmail').value.trim(),password:$('setupPassword').value});enterConsole(data);}catch(error){notice(error.message)}finally{setBusy(button,false)}});
    $('showForgot').addEventListener('click',()=>{notice();$('forgotEmail').value=$('email').value.trim();$('forgotForm').hidden=false;$('forgotSent').hidden=true;$('forgotLead').hidden=false;show('forgotPanel');$('forgotEmail').focus()});
    $('backFromForgot').addEventListener('click',()=>{notice();show('loginPanel')});
    $('forgotForm').addEventListener('submit',async event=>{event.preventDefault();notice();const button=$('forgotButton');setBusy(button,true,'Sending…');try{const data=await api(`${adminApiBase}/forgot-password.php`,{email:$('forgotEmail').value.trim()});$('forgotSentText').textContent=data.message;$('forgotForm').hidden=true;$('forgotLead').hidden=true;$('forgotSent').hidden=false;}catch(error){notice(error.message)}finally{setBusy(button,false)}});
    // Arriving from a completed reset: confirm it under the heading, then clean the URL.
    if(new URLSearchParams(location.search).get('reset')==='done'){const ok=document.createElement('div');ok.className='sent';ok.setAttribute('role','status');ok.style.marginBottom='22px';ok.innerHTML='<b aria-hidden="true">✓</b><span>Password updated. Sign in with your new password.</span>';$('loginPanel').querySelector('h1').after(ok);history.replaceState(null,'',location.pathname);}
    $('showBootstrap').addEventListener('click',()=>{notice();show('bootstrapPanel')});$('backFromBootstrap').addEventListener('click',()=>show('loginPanel'));$('backToLogin').addEventListener('click',()=>{pendingToken='';show('loginPanel')});
    document.querySelectorAll('[data-password-toggle]').forEach(button=>button.addEventListener('click',()=>{const input=$(button.dataset.passwordToggle);const reveal=input.type==='password';input.type=reveal?'text':'password';button.setAttribute('aria-label',reveal?'Hide password':'Show password');button.innerHTML=`<i data-lucide="${reveal?'eye-off':'eye'}" aria-hidden="true"></i>`;window.lucide?.createIcons({attrs:{'aria-hidden':'true'}})}));
    window.lucide?.createIcons({attrs:{'aria-hidden':'true'}});
  </script>
</body></html>
