// Stable account identity, shared by every current-user avatar.
window.FlowAvatar = (() => {
  const colors = ['#2454A4', '#6B3FA0', '#A13358', '#21675C', '#865016', '#414D91'];
  function identity(user) {
    const words = String(user.full_name || '').trim().split(/\s+/).filter(Boolean);
    const initials = words.slice(0, 2).map(word => Array.from(word)[0]).join('').toLocaleUpperCase() || '?';
    const key = String(user.uuid || user.id || user.email || user.full_name || 'user');
    let hash = 0;
    for (const character of key) hash = (Math.imul(hash, 31) + character.codePointAt(0)) >>> 0;
    return { initials, color: colors[hash % colors.length] };
  }
  function render(user, root = document) {
    const { initials, color } = identity(user);
    root.querySelectorAll('[data-user-avatar]').forEach(avatar => {
      avatar.replaceChildren();
      avatar.style.backgroundColor = color;
      avatar.setAttribute('role', 'img');
      avatar.setAttribute('aria-label', `${user.full_name || 'Your'} profile`);
      const fallback = document.createElement('span');
      fallback.textContent = initials;
      fallback.setAttribute('aria-hidden', 'true');
      avatar.append(fallback);
      const avatarUrl = typeof user.avatar_url === 'string' ? user.avatar_url.trim() : '';
      if (avatarUrl) {
        const image = document.createElement('img');
        image.alt = '';
        image.className = 'user-avatar-photo';
        image.hidden = true;
        image.onload = () => { image.hidden = false; fallback.hidden = true; };
        image.onerror = () => { image.remove(); fallback.hidden = false; };
        avatar.append(image);
        if (avatarUrl.startsWith('private-avatar:')) {
          fetch('../api/settings/avatar.php', { headers: { Authorization: `Bearer ${localStorage.getItem('flow_token') || ''}` } })
            .then((response) => response.ok ? response.blob() : Promise.reject(new Error('Avatar unavailable')))
            .then((blob) => { image.src = URL.createObjectURL(blob); })
            .catch(() => { image.remove(); fallback.hidden = false; });
        } else image.src = avatarUrl;
      }
    });
  }
  return { identity, render };
})();
