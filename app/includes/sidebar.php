<?php
/**
 * Application Sidebar Component
 * Reusable fixed left dock sidebar for all Flowwithlit dashboard pages
 * 
 * Available variables to set before including:
 * - $activePage (string): 'dashboard', 'transactions', 'wallets', 'payments', 'cards', 'payment-links', 'customers', 'exchange', 'developer', 'settings'
 */
$activePage = $activePage ?? 'dashboard';

$sidebarMenus = [
  [
    'id'       => 'dashboard',
    'title'    => 'Dashboard',
    'url'      => './',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="layout-grid" class="w-5 h-5"></i>'
  ],
  [
    'id'       => 'transactions',
    'title'    => 'Transactions',
    'url'      => 'transactions.php',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="receipt-text" class="w-5 h-5"></i>'
  ],
  [
    'id'       => 'wallets',
    'title'    => 'Wallets & Balances',
    'url'      => 'wallets.php',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="wallet" class="w-5 h-5"></i>'
  ],
  [
    'id'       => 'payments',
    'title'    => 'Payments & Transfers',
    'url'      => 'payments.php',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="arrow-left-right" class="w-5 h-5"></i>'
  ],
  [
    'id'       => 'cards',
    'title'    => 'Cards',
    'url'      => 'cards.php',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="credit-card" class="w-5 h-5"></i>'
  ],
  [
    'id'       => 'payment-links',
    'title'    => 'Payment Links & Invoices',
    'url'      => 'payment-links.php',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="link" class="w-5 h-5"></i>'
  ],
  [
    'id'       => 'customers',
    'title'    => 'Customers',
    'url'      => 'customers.php',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="users" class="w-5 h-5"></i>'
  ],
  [
    'id'       => 'developer',
    'title'    => 'Developer API',
    'url'      => 'developer.php',
    'hasBadge' => true,
    'badgeText'=> 'Live / Sandbox',
    'icon'     => '<i data-lucide="code-2" class="w-5 h-5"></i>
                   <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>'
  ],
  [
    'id'       => 'settings',
    'title'    => 'Settings',
    'url'      => 'settings.php',
    'hasBadge' => false,
    'badgeText'=> '',
    'icon'     => '<i data-lucide="settings" class="w-5 h-5"></i>'
  ]
];
?>

<!-- Fixed Sidebar Dock (Hidden on mobile, never scrolls, equal space top & bottom, menu centralized) -->
<aside class="hidden md:flex w-20 lg:w-24 h-full flex-shrink-0 flex-col items-center justify-center px-2 py-6 bg-white dark:bg-[#09182b] z-30 select-none transition-colors duration-200">
  
  <!-- Centralized Menu Items (my-auto centers vertically with equal top/bottom spacing) -->
  <div id="sidebarMenu" class="my-auto flex flex-col items-center justify-center gap-2.5 select-none">
    
    <?php foreach ($sidebarMenus as $menu): 
      $isActive = ($activePage === $menu['id']);
      // Plain icons (no circles); the active page gets a gold marker on the left edge.
      $activeClasses = $isActive ? 'is-active' : '';
    ?>
      <div class="relative group flex items-center">
        <a href="<?= htmlspecialchars($menu['url']) ?>" 
           class="sidebar-item w-11 h-11 <?= $activeClasses ?> flex items-center justify-center transition-colors"<?= $isActive ? ' aria-current="page"' : '' ?> 
           data-title="<?= htmlspecialchars($menu['title']) ?>" 
           title="<?= htmlspecialchars($menu['title']) ?>">
          <?= $menu['icon'] ?>
        </a>
        
        <!-- Tooltip -->
        <div class="absolute left-14 ml-1 px-2.5 py-1 bg-[#0b2545] dark:bg-[#134074] border border-blue-900/50 text-white text-[11px] font-semibold rounded-lg shadow-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none z-50 flex items-center gap-1.5">
          <span><?= htmlspecialchars($menu['title']) ?></span>
          <?php if (!empty($menu['badgeText'])): ?>
            <span class="px-1 py-0.2 rounded text-[9px] bg-emerald-500/20 text-emerald-400 font-bold uppercase tracking-wide"><?= htmlspecialchars($menu['badgeText']) ?></span>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

  </div>
</aside>
