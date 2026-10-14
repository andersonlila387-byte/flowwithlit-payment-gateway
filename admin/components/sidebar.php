<?php
declare(strict_types=1);

if (!function_exists('admin_component_escape')) {
    function admin_component_escape(mixed $value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    function admin_component_icon(string $name, string $class = ''): string {
        $lucide = [
            'search'=>'search','chevron-down'=>'chevron-down','chevron-left'=>'chevron-left','grid'=>'layout-dashboard',
            'user'=>'circle-user-round','users'=>'users-round','card'=>'credit-card','shield'=>'shield-check','bank'=>'landmark',
            'deposit'=>'circle-arrow-down','withdraw'=>'circle-arrow-up','charge'=>'badge-dollar-sign','money'=>'arrow-left-right',
            'percent'=>'badge-percent','levels'=>'chart-no-axes-column-increasing','settings'=>'settings','report'=>'file-chart-column',
            'help'=>'circle-help','moon'=>'moon','fullscreen'=>'maximize','bell'=>'bell','currency'=>'circle-dollar-sign',
            'menu'=>'menu','filter'=>'list-filter','download'=>'cloud-download','upload'=>'upload','calendar'=>'calendar-days',
            'more'=>'ellipsis-vertical','sort'=>'arrow-up-down','copy'=>'copy','rocket'=>'rocket','globe'=>'globe','edit'=>'pencil',
            'languages'=>'languages','brand'=>'landmark','api'=>'plug-zap',
        ];
        return '<i data-lucide="'.admin_component_escape($lucide[$name] ?? 'circle').'" class="icon '.admin_component_escape($class).'" aria-hidden="true"></i>';
        $paths = [
            'search' => '<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/>',
            'chevron-down' => '<path d="m6 9 6 6 6-6"/>', 'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
            'grid' => '<rect x="3" y="3" width="6" height="6" rx="1"/><rect x="15" y="3" width="6" height="6" rx="1"/><rect x="3" y="15" width="6" height="6" rx="1"/><rect x="15" y="15" width="6" height="6" rx="1"/>',
            'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2Z"/>',
            'users' => '<circle cx="9" cy="7" r="3"/><path d="M3 20v-3a6 6 0 0 1 12 0v3M16 4a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5v1"/>',
            'card' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18M7 14h4"/>',
            'shield' => '<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6Z"/>',
            'bank' => '<path d="m3 8 9-5 9 5ZM3 21h18M5 11v7m5-7v7m4-7v7m5-7v7"/>',
            'deposit' => '<rect x="3" y="4" width="18" height="7" rx="1"/><path d="M6 17v4h12v-4M12 10v8m-3-3 3 3 3-3"/>',
            'withdraw' => '<rect x="3" y="4" width="18" height="7" rx="1"/><path d="M6 17v4h12v-4M12 18v-8m-3 3 3-3 3 3"/>',
            'charge' => '<path d="m12 3 4 7-4 3-4-3Zm-5 9 4 7H3Zm10 0 4 7h-8Z"/>',
            'money' => '<rect x="2" y="6" width="20" height="13" rx="2"/><circle cx="12" cy="12.5" r="3"/>',
            'percent' => '<circle cx="12" cy="12" r="9"/><path d="m8 16 8-8M8 8h.01M16 16h.01"/>',
            'levels' => '<path d="M4 16h3v5H4zm6-6h3v11h-3zm6-7h3v18h-3z"/>',
            'settings' => '<path d="m9 3-.7 3-2 .9L3.5 6l-2 3.5 2.3 2v2l-2.3 2L3.5 19l2.8-.9 2 .9.7 3h4l.7-3 2-.9 2.8.9 2-3.5-2.3-2v-2l2.3-2-2-3.5-2.8.9-2-.9L13 3Z"/><circle cx="11" cy="12" r="3"/>',
            'report' => '<path d="M6 3h12v18H6zM9 7h6M9 11h6M9 15h3"/>',
            'help' => '<circle cx="12" cy="12" r="9"/><path d="M9 8a3 3 0 0 1 6 0c0 3-3 2-3 5m0 4h.01"/>',
            'moon' => '<path d="M21 12.8A9 9 0 0 1 11.2 3 9 9 0 0 0 21 12.8Z"/>',
            'fullscreen' => '<path d="M8 3H3v5m13-5h5v5M3 16v5h5m8 0h5v-5"/>',
            'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>',
            'currency' => '<circle cx="12" cy="12" r="9"/><path d="M15 8h-4a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4H9m3-10v12"/>',
            'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        ];
        return '<svg class="icon '.admin_component_escape($class).'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name] ?? $paths['grid']).'</svg>';
    }

    function admin_component_preview_suffix(bool $preview): string {
        return $preview ? '?preview=1' : '';
    }

    function admin_component_route(string $route, bool $preview): string {
        [$path, $fragment] = array_pad(explode('#', $route, 2), 2, '');
        return $path.admin_component_preview_suffix($preview).($fragment !== '' ? '#'.$fragment : '');
    }

    function admin_render_sidebar(string $activePage, bool $localPreview = false, array $submenu = [], string $brand = 'Flowwithlit'): void {
        // One route per page. Every sidebar link now points to its own file,
        // so the active state is driven by the label passed to admin_use_shared_layout().
        $items = [
            ['Dashboard', 'grid', 'index.php', false],
            ['Users', 'user', 'users.php', true],
            ['Transaction', 'card', 'transactions.php', false],
            ['Admin Setup', 'users', 'staff.php', false],
            ['Authorization', 'shield', 'authorization.php', false],
            ['Payment', 'bank', 'payment.php', false],
            ['Deposit', 'deposit', 'deposit.php', false],
            ['Withdraw', 'withdraw', 'withdraw.php', false],
            ['Charge', 'charge', 'charges.php', false],
            ['Transfer', 'money', 'transfer.php', false],
            ['Promo Code', 'percent', 'promo-codes.php', false],
            ['Levels', 'levels', 'levels.php', false],
            ['API Connect', 'api', 'api-connect.php', false],
            ['Settings', 'settings', 'settings.php', false],
            ['Reports', 'report', 'reports.php', false],
            ['Faq', 'help', 'faq.php', false],
        ];
        $userSubmenu = $submenu ?: [
            ['Users List', 'users-list.php', false],
            ['User Identity', 'identity.php', false],
            ['User Settings', 'users.php', $activePage === 'Users'],
        ];
        ?>
        <aside class="sidebar" aria-label="Main navigation">
            <button class="sidebar-toggle" aria-label="Collapse sidebar" aria-expanded="true"><?= admin_component_icon('chevron-left') ?></button>
            <nav class="navigation">
                <?php foreach ($items as [$label, $icon, $route, $expand]):
                    $active = $label === $activePage;
                    $children = $label === 'Users' ? $userSubmenu : [];
                    $hasChildren = $children !== [];
                ?>
                    <?php if ($hasChildren): ?>
                        <button class="nav-item nav-parent <?= $active ? 'active' : '' ?>" type="button" data-submenu-toggle="users-menu" data-section="<?= admin_component_escape($label) ?>" aria-controls="users-menu" aria-expanded="<?= $active ? 'true' : 'false' ?>" data-tooltip="<?= admin_component_escape($label) ?>" title="<?= admin_component_escape($label) ?>">
                            <?= admin_component_icon($icon) ?><span><?= admin_component_escape($label) ?></span><?= admin_component_icon('chevron-down', 'nav-chevron'); ?>
                        </button>
                        <div class="user-subnav" id="users-menu" aria-label="<?= admin_component_escape($label) ?> menu" <?= $active ? '' : 'hidden' ?>>
                            <?php foreach ($children as [$subLabel, $subRoute, $selected]): ?>
                                <a class="<?= $selected ? 'active' : '' ?>" href="<?= admin_component_escape(admin_component_route($subRoute, $localPreview)) ?>"><?= admin_component_escape($subLabel) ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <a class="nav-item <?= $active ? 'active' : '' ?>" href="<?= admin_component_escape(admin_component_route($route, $localPreview)) ?>" data-section="<?= admin_component_escape($label) ?>" data-tooltip="<?= admin_component_escape($label) ?>" title="<?= admin_component_escape($label) ?>" <?= $active ? 'aria-current="page"' : '' ?>>
                            <?= admin_component_icon($icon) ?><span><?= admin_component_escape($label) ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <p class="no-menus" hidden>No matching menus</p>
            </nav>
        </aside>
        <?php
    }
}
