<?php
/**
 * Application Footer Component
 * Reusable footer for all Flowwithlit dashboard pages
 */
?>
  </div><!-- End of Workspace Container (from header.php) -->

  <!-- Include Mobile Bottom Navigation and "More Services" Drawer -->
  <?php require_once __DIR__ . '/bottom_nav.php'; ?>

  <!-- Global Application Scripts -->
  <script src="includes/user-avatar.js"></script>
  <script>
    // Global Lucide Icons refresher
    window.refreshIcons = function() {
      if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
      }
    };

    // Global Theme Switcher (Light / Dark Mode)
    window.toggleTheme = function() {
      const isDark = document.documentElement.classList.contains('dark');
      if (isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
      } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
      }
      if (window.refreshIcons) {
        window.refreshIcons();
      }
    };

    document.addEventListener('DOMContentLoaded', async () => {
      // Initialize Lucide icons
      window.refreshIcons();

      // Shared web client for the JSON session API. The mobile app uses the same response.
      const sessionToken = localStorage.getItem('flow_token');
      const currentAppPage = (window.location.pathname.split('/').pop() || 'index').replace(/\.php$/, '');
      const leaveApp = () => { localStorage.removeItem('flow_token'); localStorage.removeItem('flow_user'); window.location.replace('../login.php?reason=session'); };
      if (!sessionToken) { leaveApp(); return; }
      try {
        const response = await fetch('../api/auth/session-check.php', { method: 'POST', headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${sessionToken}` }, body: JSON.stringify({ token: sessionToken }) });
        const session = await response.json();
        if (!response.ok || !session.success || session.is_2fa_pending) { leaveApp(); return; }
        const user = session.user;
        localStorage.setItem('flow_user', JSON.stringify(user));
        document.getElementById('currentUserName')?.replaceChildren(document.createTextNode(user.full_name || 'Flowwithlit user'));
        const dashboardGreeting = document.getElementById('dashboardGreeting');
        if (dashboardGreeting) dashboardGreeting.textContent = `Welcome back, ${(user.full_name || 'there').split(/\s+/)[0]}`;
        const dashboardDate = document.getElementById('dashboardDate');
        if (dashboardDate) dashboardDate.textContent = new Intl.DateTimeFormat(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date());
        document.getElementById('currentUserEmail')?.replaceChildren(document.createTextNode(user.email || ''));
        document.getElementById('currentUserName')?.setAttribute('title', user.full_name || 'Flowwithlit user');
        const tierBadge = document.getElementById('currentUserTier');
        const tier = Number(user.kyc_tier);
        if (tierBadge && [1, 2, 3].includes(tier)) {
          const tierIcons = { 1: 'shield-check', 2: 'award', 3: 'crown' };
          tierBadge.innerHTML = `<i data-lucide="${tierIcons[tier]}" class="w-4 h-4" aria-hidden="true"></i>`;
          tierBadge.setAttribute('aria-label', `Tier ${tier}`);
          tierBadge.setAttribute('title', `Tier ${tier}`);
          tierBadge.classList.remove('hidden');
          tierBadge.classList.add('inline-flex');
          window.refreshIcons();
        }
        window.FlowAvatar.render(user);
        if (!user.onboarding_complete && currentAppPage !== 'onboarding') { window.location.replace('onboarding'); return; }
        window.dispatchEvent(new CustomEvent('flowwithlit:session-ready', { detail: user }));
      } catch (error) { leaveApp(); return; }

      // Let the liquid skeleton remain long enough to feel intentional, then reveal the page.
      const flowPreloader = document.getElementById('flowPreloader');
      if (flowPreloader) {
        window.setTimeout(() => flowPreloader.classList.add('is-complete'), 420);
      }

      // Header Top Tab Switcher (if tabs exist on page)
      const navItems = document.querySelectorAll('.nav-item');
      navItems.forEach(btn => {
        btn.addEventListener('click', () => {
          navItems.forEach(b => {
            b.className = 'nav-item text-gray-600 hover:text-gray-900 px-4 py-2 rounded-full transition';
          });
          btn.className = 'nav-item bg-black text-white px-5 py-2 rounded-full transition shadow-sm';
        });
      });

      // Profile Dropdown Toggle Logic
      const profileDropdownBtn = document.getElementById('profileDropdownBtn');
      const profileDropdownMenu = document.getElementById('profileDropdownMenu');
      const profileDropdownWrapper = document.getElementById('profileDropdownWrapper');
      const profileChevron = document.getElementById('profileChevron');
      const logoutBtn = document.getElementById('logoutBtn');
      function closeHeaderDropdowns(except = '') {
        ['profile', 'notification'].forEach(name => {
          if (name === except) return;
          document.getElementById(`${name}DropdownMenu`)?.classList.add('hidden');
          document.getElementById(`${name}DropdownBtn`)?.setAttribute('aria-expanded', 'false');
          if (name === 'profile') profileChevron?.classList.remove('rotate-180');
        });
      }
      document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        const active = ['profile', 'notification'].find(name => document.getElementById(`${name}DropdownBtn`)?.getAttribute('aria-expanded') === 'true');
        closeHeaderDropdowns();
        if (active) document.getElementById(`${active}DropdownBtn`)?.focus();
      });

      if (profileDropdownBtn && profileDropdownMenu) {
        function openProfileDropdown() {
          closeHeaderDropdowns('profile');
          profileDropdownMenu.classList.remove('hidden');
          profileDropdownBtn.setAttribute('aria-expanded', 'true');
          if (profileChevron) profileChevron.classList.add('rotate-180');
        }

        function closeProfileDropdown() {
          profileDropdownMenu.classList.add('hidden');
          profileDropdownBtn.setAttribute('aria-expanded', 'false');
          if (profileChevron) profileChevron.classList.remove('rotate-180');
        }

        profileDropdownBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          const isHidden = profileDropdownMenu.classList.contains('hidden');
          if (isHidden) {
            openProfileDropdown();
          } else {
            closeProfileDropdown();
          }
        });

        // Close on clicking outside
        document.addEventListener('click', (e) => {
          if (profileDropdownWrapper && !profileDropdownWrapper.contains(e.target)) {
            closeProfileDropdown();
          }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && !profileDropdownMenu.classList.contains('hidden')) {
            closeProfileDropdown();
          }
        });

        // Logout handler
        if (logoutBtn) {
          logoutBtn.addEventListener('click', async () => {
            if (confirm('Are you sure you want to log out of Flowwithlit?')) {
              try {
                await fetch('../api/auth/logout.php', {
                  method: 'POST',
                  headers: { 'Authorization': `Bearer ${sessionToken}` }
                });
              } catch (e) {}
              localStorage.removeItem('flow_token');
              localStorage.removeItem('flow_user');
              window.location.href = '../login.php';
            }
          });
        }
      }

      // Notification dropdown
      const notificationDropdownBtn = document.getElementById('notificationDropdownBtn');
      const notificationDropdownMenu = document.getElementById('notificationDropdownMenu');
      const notificationDropdownWrapper = document.getElementById('notificationDropdownWrapper');
      const notificationBadge = document.getElementById('notificationBadge');
      const markNotificationsRead = document.getElementById('markNotificationsRead');
      const notificationFeed = document.getElementById('notificationFeed');
      const notificationUnreadText = document.getElementById('notificationUnreadText');
      const escapeNotification = (value) => String(value || '').replace(/[&<>'"]/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
      const loadNotifications = async () => {
        const response = await fetch('../api/notifications/list.php', {method:'POST',headers:{'Content-Type':'application/json',Authorization:`Bearer ${sessionToken}`},body:JSON.stringify({token:sessionToken})});
        const data = await response.json(); if(!data.success) return;
        const count = data.unread_count || 0; notificationUnreadText.textContent = count ? `${count} unread update${count===1?'':'s'}` : 'You are all caught up';
        notificationBadge.textContent=count; notificationBadge.classList.toggle('hidden',!count);
        notificationFeed.innerHTML = data.notifications.length ? data.notifications.map((item) => `<a href="${escapeNotification(item.action_url || 'notifications.php')}" class="notification-item flex gap-3 rounded-xl ${item.read?'':'bg-sky-50/70 dark:bg-sky-950/25'} p-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-sky-100 dark:bg-sky-950/60 text-sky-600"><i data-lucide="bell" class="w-4 h-4"></i></span><span class="min-w-0 flex-1"><strong class="block text-sm text-gray-900 dark:text-white">${escapeNotification(item.title)}</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-300">${escapeNotification(item.body)}</small></span>${item.read?'':'<span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-sky-500"></span>'}</a>`).join('') : '<div class="py-8 text-center text-xs text-gray-400">No notifications yet.</div>';
        window.refreshIcons?.();
      };
      loadNotifications().catch(() => { if(notificationFeed) notificationFeed.innerHTML='<div class="py-8 text-center text-xs text-gray-400">Unable to load notifications.</div>'; });
      const closeNotifications = () => {
        if (!notificationDropdownMenu) return;
        notificationDropdownMenu.classList.add('hidden');
        notificationDropdownBtn?.setAttribute('aria-expanded', 'false');
      };
      if (notificationDropdownBtn && notificationDropdownMenu) {
        notificationDropdownBtn.addEventListener('click', (event) => {
          event.stopPropagation();
          closeHeaderDropdowns('notification');
          notificationDropdownMenu.classList.toggle('hidden');
          notificationDropdownBtn.setAttribute('aria-expanded', String(!notificationDropdownMenu.classList.contains('hidden')));
        });
        document.addEventListener('click', (event) => { if (!notificationDropdownWrapper.contains(event.target)) closeNotifications(); });
      }
      markNotificationsRead?.addEventListener('click', async () => { await fetch('../api/notifications/mark-read.php',{method:'POST',headers:{'Content-Type':'application/json',Authorization:`Bearer ${sessionToken}`},body:JSON.stringify({token:sessionToken})}); await loadNotifications(); });

      // Full-screen command search
      const commandSearchModal = document.getElementById('commandSearchModal');
      const openCommandSearch = document.getElementById('openCommandSearch');
      const closeCommandSearch = document.getElementById('closeCommandSearch');
      const commandSearchInput = document.getElementById('commandSearchInput');
      const commandSearchResults = document.getElementById('commandSearchResults');
      const commandSearchCount = document.getElementById('commandSearchCount');
      const commandSearchEmpty = document.getElementById('commandSearchEmpty');
      let commandSearchLastFocus = null;
      let commandSearchIndex = -1;

      const getCommandSearchItems = () => Array.from(document.querySelectorAll('[data-command-result]')).filter((item) => !item.classList.contains('hidden'));
      const updateCommandSearch = () => {
        if (!commandSearchInput) return;
        const query = commandSearchInput.value.trim().toLowerCase();
        const allItems = Array.from(document.querySelectorAll('[data-command-result]'));
        let matches = 0;
        allItems.forEach((item) => {
          const match = !query || item.textContent.toLowerCase().includes(query) || item.dataset.searchTerms.includes(query);
          item.classList.toggle('hidden', !match);
          if (match) matches++;
        });
        commandSearchIndex = -1;
        if (commandSearchCount) commandSearchCount.textContent = `${matches} destination${matches === 1 ? '' : 's'}`;
        if (commandSearchEmpty) commandSearchEmpty.classList.toggle('hidden', matches > 0);
        if (commandSearchResults) commandSearchResults.classList.toggle('hidden', matches === 0);
      };
      const closeCommandPalette = () => {
        if (!commandSearchModal || commandSearchModal.classList.contains('hidden')) return;
        commandSearchModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        if (commandSearchLastFocus) commandSearchLastFocus.focus();
      };
      const openCommandPalette = () => {
        if (!commandSearchModal || !commandSearchInput) return;
        closeHeaderDropdowns();
        commandSearchLastFocus = document.activeElement;
        commandSearchModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        commandSearchInput.value = '';
        updateCommandSearch();
        setTimeout(() => commandSearchInput.focus(), 20);
      };
      if (openCommandSearch) openCommandSearch.addEventListener('click', openCommandPalette);
      if (closeCommandSearch) closeCommandSearch.addEventListener('click', closeCommandPalette);
      if (commandSearchInput) commandSearchInput.addEventListener('input', updateCommandSearch);
      document.addEventListener('keydown', (event) => {
        const isShortcut = (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k';
        if (isShortcut) { event.preventDefault(); openCommandPalette(); return; }
        if (!commandSearchModal || commandSearchModal.classList.contains('hidden')) return;
        if (event.key === 'Escape') { closeCommandPalette(); return; }
        const visibleItems = getCommandSearchItems();
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
          event.preventDefault();
          commandSearchIndex = event.key === 'ArrowDown' ? (commandSearchIndex + 1) % visibleItems.length : (commandSearchIndex - 1 + visibleItems.length) % visibleItems.length;
          visibleItems[commandSearchIndex]?.focus();
        }
        if (event.key === 'Enter' && commandSearchIndex >= 0) visibleItems[commandSearchIndex]?.click();
      });

      // Mobile "More Services" Drawer Logic
      const mobileMoreBtn = document.getElementById('mobileMoreBtn');
      const mobileDrawerBackdrop = document.getElementById('mobileDrawerBackdrop');
      const mobileDrawerPanel = document.getElementById('mobileDrawerPanel');
      const closeMobileDrawerBtn = document.getElementById('closeMobileDrawerBtn');

      function openMobileDrawer() {
        if (!mobileDrawerBackdrop || !mobileDrawerPanel) return;
        mobileDrawerBackdrop.classList.remove('hidden');
        setTimeout(() => {
          mobileDrawerBackdrop.classList.remove('opacity-0');
          mobileDrawerPanel.classList.remove('translate-y-full');
        }, 10);
      }

      function closeMobileDrawer() {
        if (!mobileDrawerBackdrop || !mobileDrawerPanel) return;
        mobileDrawerBackdrop.classList.add('opacity-0');
        mobileDrawerPanel.classList.add('translate-y-full');
        setTimeout(() => {
          mobileDrawerBackdrop.classList.add('hidden');
        }, 250);
      }

      if (mobileMoreBtn) {
        mobileMoreBtn.addEventListener('click', openMobileDrawer);
      }

      if (closeMobileDrawerBtn) {
        closeMobileDrawerBtn.addEventListener('click', closeMobileDrawer);
      }

      if (mobileDrawerBackdrop) {
        mobileDrawerBackdrop.addEventListener('click', (e) => {
          if (e.target === mobileDrawerBackdrop) {
            closeMobileDrawer();
          }
        });
      }
    });
  </script>
</body>
</html>
