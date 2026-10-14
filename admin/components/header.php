<?php
declare(strict_types=1);
require_once __DIR__.'/sidebar.php';

if (!function_exists('admin_render_header')) {
    function admin_render_header(bool $localPreview = false): void {
        ?>
        <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>
        <header class="topbar">
            <button class="icon-button mobile-menu" aria-label="Open navigation"><?= admin_component_icon('menu') ?></button>
            <a class="header-brand header-brand-wordmark" href="<?= admin_component_escape(admin_component_route('index.php', $localPreview)) ?>" aria-label="Flowwithlit admin dashboard"><img src="../logo/flowwithlit-wordmark.png" alt="Flowwithlit"></a>
            <label class="global-search"><input type="search" id="global-search" placeholder="Search the dashboard" aria-label="Search dashboard"><?= admin_component_icon('search') ?></label>
            <div class="header-actions">
                <button class="icon-button" id="theme-toggle" aria-label="Switch dark mode"><?= admin_component_icon('moon') ?></button><span class="separator"></span>
                <button class="icon-button" id="fullscreen" aria-label="Toggle full screen"><?= admin_component_icon('fullscreen') ?></button><span class="separator"></span>
                <button class="icon-button language" data-popover="language-menu" aria-label="Choose language"><?= admin_component_icon('languages') ?></button><span class="separator"></span>
                <button class="icon-button notification-button" data-popover="notifications-menu" aria-label="Notifications"><?= admin_component_icon('bell') ?><b></b></button><span class="separator"></span>
                <button class="icon-button" data-popover="currency-menu" aria-label="Currency"><?= admin_component_icon('currency') ?></button><span class="separator"></span>
                <button class="icon-button profile-button" data-popover="profile-menu" aria-label="Open administrator menu" aria-haspopup="menu" aria-expanded="false"><span class="admin-avatar" data-admin-avatar aria-hidden="true">AD</span></button>
            </div>
        </header>
        <?php
    }

    function admin_render_profile_menu(): void {
        ?>
        <div id="profile-menu" class="popover profile-menu" role="menu" hidden>
            <div class="profile-menu-summary">
                <span class="admin-avatar profile-menu-avatar" data-admin-avatar aria-hidden="true">AD</span>
                <div><strong data-admin-name>Administrator</strong><span data-admin-role>Administrator</span></div>
            </div>
            <div class="profile-menu-actions">
                <button data-preview-action="Profile"><?= admin_component_icon('user') ?><span>My profile</span></button>
                <button data-preview-action="Settings"><?= admin_component_icon('settings') ?><span>Account settings</span></button>
                <button data-admin-signout class="profile-signout"><?= admin_component_icon('chevron-left') ?><span>Sign out</span></button>
            </div>
        </div>
        <?php
    }
}
