// Real "Sign in with Google" for the customer sign-in and sign-up pages.
// Renders Google's own button (Google Identity Services) and sends Google's signed ID token
// ("credential") to api/auth/google.php, which verifies it with Google before trusting it.
// The container is only printed by the page when FLOW_GOOGLE_CLIENT_ID is configured.
(() => {
  const box = document.getElementById('googleSignIn');
  if (!box || !box.dataset.clientId) return;
  const alertBox = document.getElementById(box.dataset.alert || 'alert-box');

  const showError = message => {
    if (!alertBox) return;
    alertBox.textContent = message;
    alertBox.className = 'mb-5 p-3.5 rounded-xl text-xs font-medium border border-red-200 bg-red-50 text-red-700';
    alertBox.classList.remove('hidden');
  };

  async function onCredential(response) {
    box.setAttribute('aria-busy', 'true');
    box.style.opacity = '.6';
    try {
      const res = await fetch('api/auth/google.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ credential: response.credential })
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.success) throw new Error(data.error || 'Google sign-in failed. Please try again.');
      localStorage.setItem('flow_token', data.token);
      localStorage.setItem('flow_user', JSON.stringify(data.user));
      if (data.requires_2fa) {
        window.location.href = `verify-otp?type=2fa&email=${encodeURIComponent(data.user.email)}`;
      } else {
        window.location.href = data.user.onboarding_complete ? 'app/' : 'app/onboarding.php';
      }
    } catch (error) {
      showError(error.message || 'Could not reach the server. Please try again.');
      box.removeAttribute('aria-busy');
      box.style.opacity = '';
    }
  }

  function render() {
    window.google.accounts.id.initialize({ client_id: box.dataset.clientId, callback: onCredential, ux_mode: 'popup', context: box.dataset.context || 'signin' });
    window.google.accounts.id.renderButton(box, {
      type: 'standard', theme: 'outline', size: 'large', shape: 'pill', logo_alignment: 'center',
      text: box.dataset.context === 'signup' ? 'signup_with' : 'continue_with',
      width: Math.min(400, Math.max(240, Math.round(box.getBoundingClientRect().width || 320)))
    });
  }

  const script = document.createElement('script');
  script.src = 'https://accounts.google.com/gsi/client';
  script.async = true;
  script.defer = true;
  script.onload = render;
  script.onerror = () => { box.closest('[data-google-block]')?.remove(); };
  document.head.appendChild(script);
})();
