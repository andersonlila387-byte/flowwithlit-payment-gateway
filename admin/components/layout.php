<?php
declare(strict_types=1);

require_once __DIR__.'/sidebar.php';
require_once __DIR__.'/header.php';

if (!function_exists('admin_use_shared_layout')) {
    function admin_capture_component(callable $renderer): string {
        ob_start();
        $renderer();
        return (string) ob_get_clean();
    }

    /**
     * Replaces legacy page-level chrome at render time while the individual pages
     * are migrated. The result contains only the shared sidebar and header.
     */
    function admin_use_shared_layout(string $activePage, bool $localPreview, array $submenu = [], string $brand = 'Flowwithlit'): void {
        $sidebar = admin_capture_component(static fn() => admin_render_sidebar($activePage, $localPreview, $submenu, $brand));
        $header = admin_capture_component(static fn() => admin_render_header($localPreview));
        $profileMenu = admin_capture_component(static fn() => admin_render_profile_menu());
        $authGate = '';
        if (!$localPreview) {
            $authGate = '<script>(function(){try{if(!localStorage.getItem("flow_admin_token")){window.location.replace("login.php");return;}document.documentElement.classList.add("admin-auth-pending");}catch(e){window.location.replace("login.php");}})();</script>'
                . '<style>html.admin-auth-pending body{visibility:hidden}html.admin-auth-pending::before{content:"Checking secure session…";position:fixed;inset:0;display:grid;place-items:center;background:#f4f7fb;color:#17345f;font:600 16px system-ui,-apple-system,"Segoe UI",sans-serif;z-index:2147483647}</style>';
        }
        ob_start(static function (string $html) use ($sidebar, $header, $profileMenu, $authGate): string {
            $html = str_replace(['Localxyz', 'Localxtz'], 'Flowwithlit', $html);
            $html = preg_replace('~</head>~i', $authGate.'<script>try{if(localStorage.getItem("flow_admin_theme")==="dark")document.documentElement.classList.add("dark")}catch(e){}</script>'."\n".'    <link rel="preconnect" href="https://fonts.googleapis.com">'."\n".'    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'."\n".'    <link href="https://fonts.googleapis.com/css2?family=Smooch+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">'."\n".'    <link rel="stylesheet" href="assets/theme.css">'."\n".'    <link rel="stylesheet" href="assets/palette.css">'."\n".'    <link rel="stylesheet" href="assets/admin-dark.css">'."\n".'    <link rel="stylesheet" href="assets/sidebar-flyouts.css">'."\n".'</head>', $html, 1) ?? $html;
            $replacedSidebar = preg_replace('~<aside class="sidebar"[^>]*>.*?</aside>~s', $sidebar, $html, 1);
            if ($replacedSidebar !== null && $replacedSidebar !== $html) {
                $html = $replacedSidebar;
            } else {
                // New pages use the shared shell directly and therefore have no
                // legacy sidebar markup to replace.
                $html = preg_replace('~(<div class="dashboard-shell"[^>]*>)~', '$1'.$sidebar, $html, 1) ?? $html;
            }
            $replacedHeader = preg_replace('~<header class="topbar">.*?</header>~s', $header, $html, 1);
            if ($replacedHeader !== null && $replacedHeader !== $html) {
                $html = $replacedHeader;
            } else {
                // Keep the fixed header inside the main layout so it follows the
                // same responsive spacing rules as every existing admin page.
                $html = preg_replace('~(<div class="main-layout"[^>]*>)~', '$1'.$header, $html, 1) ?? $html;
            }
            $profilePattern = '~<div id="profile-menu" class="popover(?:\s+profile-menu)?"(?:\s+role="menu")? hidden>.*?</div>~s';
            $replacedProfile = preg_replace($profilePattern, $profileMenu, $html, 1);
            if ($replacedProfile !== null && $replacedProfile !== $html) {
                $html = $replacedProfile;
            } else {
                // Newer pages have no page-level profile markup. Inject the shared
                // menu before page scripts run so the shared header can bind it.
                $withProfileMenu = preg_replace('~(<div id="toast"[^>]*>)~i', $profileMenu.'$1', $html, 1);
                $html = $withProfileMenu !== null && $withProfileMenu !== $html
                    ? $withProfileMenu
                    : (preg_replace('~</body>~i', $profileMenu.'</body>', $html, 1) ?? $html);
            }
            $replacementIcons = [
                '🛍️'=>'shopping-bag','🧑‍💼'=>'user-round-check','🇬🇧'=>'languages',
                '💰'=>'wallet-cards','📊'=>'chart-no-axes-column-increasing','💵'=>'banknote',
                '💸'=>'circle-arrow-up','💹'=>'arrow-left-right','🪴'=>'circle-arrow-down',
                '🏧'=>'landmark','💱'=>'arrow-left-right','🔍'=>'search','🏅'=>'award',
                '❔'=>'circle-help','➤'=>'landmark','◉'=>'landmark','♘'=>'landmark',
                '▰'=>'wallet-cards','◒'=>'landmark','◔'=>'landmark','⊙'=>'landmark',
                'Ð'=>'landmark','▣'=>'wallet-cards','←'=>'chevron-left','→'=>'chevron-right',
            ];
            $replacements = [];
            foreach ($replacementIcons as $glyph => $icon) {
                $replacements[$glyph] = '<i data-lucide="'.$icon.'" class="icon" aria-hidden="true"></i>';
            }
            $html = strtr($html, $replacements);
            return $html;
        });
    }
}
