<?php
/**
 * WordPress integration contract for task 4.2: registered menu locations and custom walkers.
 * Run after temp/setup-wordpress-test.sh with PHP or PHP WASM.
 */
$root = dirname(__DIR__, 3);
$walkerPath = $root . '/inc/Navigation/Walker.php';
$navigationPath = $root . '/inc/Navigation/Navigation.php';
$headerPath = $root . '/header.php';
$functionsPath = $root . '/inc/Theme.php';
$jsPath = $root . '/assets/js/main.js';
$failed = 0;
foreach ([$walkerPath, $navigationPath] as $path) {
    if (!is_file($path)) {
        fwrite(STDERR, 'FAIL missing navigation implementation: ' . basename($path) . "\n");
        $failed++;
    }
}
if ($failed) {
    exit(1);
}

$walkerSource = file_get_contents($walkerPath);
$navigationSource = file_get_contents($navigationPath);
$headerSource = file_get_contents($headerPath);
$themeSource = file_get_contents($functionsPath);
$jsSource = file_get_contents($jsPath);
foreach ([
    [$navigationSource, 'main-mega-menu', 'desktop menu location'],
    [$navigationSource, 'mobile-menu', 'mobile menu location'],
    [$navigationSource, 'tolidat-sherkat', 'company products menu location'],
    [$navigationSource, 'landing', 'landing menu location'],
    [$walkerSource, 'data-desktop-mega-trigger', 'desktop submenu trigger selector'],
    [$walkerSource, 'data-desktop-mega-panel', 'desktop submenu panel selector'],
    [$walkerSource, 'data-mobile-acc-btn', 'mobile accordion trigger selector'],
    [$walkerSource, 'data-mobile-acc-panel', 'mobile accordion panel selector'],
    [$headerSource, '%%ESE_DESKTOP_NAV%%', 'header desktop menu insertion point'],
    [$headerSource, '%%ESE_MOBILE_MENU%%', 'header mobile menu insertion point'],
    [$themeSource, 'Navigation::register_menus', 'theme menu registration'],
    [$jsSource, 'data-desktop-mega-trigger', 'desktop interactions'],
    [$jsSource, 'data-mobile-acc-btn', 'mobile interactions'],
] as [$source, $needle, $description]) {
    if (strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL missing {$description}\n");
        $failed++;
    }
}

$wpRoot = getenv('WP_TEST_ROOT') ?: $root . '/node_modules/.cache/ese-wp-test/WordPress-master';
if (!is_file($wpRoot . '/wp-load.php')) {
    fwrite(STDERR, "FAIL disposable WordPress installation not found at {$wpRoot}\n");
    exit(1);
}
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$navigationClass = 'ErfanSanat\\Theme\\Navigation\\Navigation';
$walkerClass = 'ErfanSanat\\Theme\\Navigation\\Walker';
foreach ([$navigationClass, $walkerClass] as $class) {
    if (!class_exists($class)) {
        fwrite(STDERR, "FAIL custom navigation class {$class} was not loaded\n");
        exit(1);
    }
}
$locations = get_registered_nav_menus();
foreach (['main-mega-menu', 'mobile-menu', 'tolidat-sherkat', 'landing'] as $location) {
    if (!isset($locations[$location])) {
        fwrite(STDERR, "FAIL {$location} was not registered with WordPress\n");
        $failed++;
    }
}

$originalLocations = get_theme_mod('nav_menu_locations', []);
$menus = [];
$createMenu = static function (string $name, array $items) use (&$menus): int {
    $menuId = wp_create_nav_menu($name);
    if (is_wp_error($menuId)) {
        return 0;
    }
    $menus[] = (int) $menuId;
    $created = [];
    foreach ($items as $index => $item) {
        $parentId = isset($item['parent']) ? ($created[$item['parent']] ?? 0) : 0;
        $itemId = wp_update_nav_menu_item(
            $menuId,
            0,
            [
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
                'menu-item-object' => 'custom',
                'menu-item-parent-id' => $parentId,
            ]
        );
        if (is_wp_error($itemId)) {
            return 0;
        }
        $created[$index] = (int) $itemId;
    }
    return (int) $menuId;
};
$mainMenu = $createMenu('Arena Main Navigation QA', [
    ['title' => 'محصولات آزمون', 'url' => '/shop/'],
    ['title' => 'گروه آزمایشی', 'url' => '/product_cat/test-group/', 'parent' => 0],
    ['title' => 'دستهٔ تودرتو', 'url' => '/product_cat/test-child/', 'parent' => 1],
    ['title' => 'پروژه‌های آزمون', 'url' => '/projects/'],
    ['title' => 'نوع پروژه', 'url' => '/project_type/test-type/', 'parent' => 3],
    ['title' => 'نمونه نوع', 'url' => '/projects/type-sample/', 'parent' => 4],
    ['title' => 'شهر پروژه', 'url' => '/city/test-city/', 'parent' => 3],
    ['title' => 'نمونه شهر', 'url' => '/projects/city-sample/', 'parent' => 6],
]);
$mobileMenu = $createMenu('Arena Mobile Navigation QA', [
    ['title' => 'پروژه‌های آزمون', 'url' => '/projects/'],
    ['title' => 'شهر آزمایشی', 'url' => '/city/test-city/', 'parent' => 0],
]);
$productMenu = $createMenu('Arena Company Products QA', [
    ['title' => 'محصول منتخب', 'url' => '/product/example-product/'],
]);
$landingMenu = $createMenu('Arena Landing Navigation QA', [
    ['title' => 'ویژگی‌ها', 'url' => '#arya-features'],
    ['title' => 'تماس', 'url' => '#contact'],
]);
if (!$mainMenu || !$mobileMenu || !$productMenu || !$landingMenu) {
    fwrite(STDERR, "FAIL WordPress navigation QA menus could not be created\n");
    $failed++;
} else {
    set_theme_mod('nav_menu_locations', [
        'main-mega-menu' => $mainMenu,
        'mobile-menu' => $mobileMenu,
        'tolidat-sherkat' => $productMenu,
        'landing' => $landingMenu,
    ]);

    $desktopMarkup = $navigationClass::render_desktop('<nav class="fallback-desktop">fallback</nav>');
    foreach (['محصولات آزمون', 'دستهٔ تودرتو', 'data-desktop-mega-trigger=', 'data-desktop-mega-panel=', 'data-mega-prod-tab=', 'data-mega-prod-panel=', 'data-mega-proj-tab="type"', 'data-mega-proj-panel="type"', 'data-mega-proj-tab="city"', 'data-mega-proj-panel="city"'] as $needle) {
        if (strpos($desktopMarkup, $needle) === false) {
            fwrite(STDERR, "FAIL desktop walker output is missing {$needle}\n");
            $failed++;
        }
    }
    $mobileMarkup = $navigationClass::render_mobile('<div data-mobile-menu="true">fallback</div>');
    foreach (['data-mobile-menu="true"', 'data-mobile-sidebar="true"', 'data-mobile-acc-btn=', 'data-mobile-acc-panel=', 'پروژه‌های آزمون', 'شهر آزمایشی'] as $needle) {
        if (strpos($mobileMarkup, $needle) === false) {
            fwrite(STDERR, "FAIL mobile walker output is missing {$needle}\n");
            $failed++;
        }
    }
    $productMarkup = $navigationClass::render_location('tolidat-sherkat', 'products');
    if (strpos($productMarkup, 'محصول منتخب') === false) {
        fwrite(STDERR, "FAIL tolidat-sherkat location was not rendered\n");
        $failed++;
    }
    $landingMarkup = $navigationClass::render_location('landing', 'landing');
    if (strpos($landingMarkup, 'arya-features') === false || strpos($landingMarkup, 'data-landing-nav') === false) {
        fwrite(STDERR, "FAIL landing menu did not render its section anchors\n");
        $failed++;
    }
    ob_start();
    include $headerPath;
    $integratedHeader = ob_get_clean();
    if (strpos($integratedHeader, 'محصولات آزمون') === false || strpos($integratedHeader, 'پروژه‌های آزمون') === false || strpos($integratedHeader, 'data-mobile-acc-panel=') === false) {
        fwrite(STDERR, "FAIL assigned menu locations were not integrated into the rendered header\n");
        $failed++;
    }
    ob_start();
    include $root . '/footer.php';
    $integratedFooter = ob_get_clean();
    if (strpos($integratedFooter, 'محصول منتخب') === false) {
        fwrite(STDERR, "FAIL tolidat-sherkat menu was not integrated into the rendered footer\n");
        $failed++;
    }

    set_theme_mod('nav_menu_locations', ['main-mega-menu' => $mainMenu]);
    $mainToMobileFallback = $navigationClass::render_mobile('<div>mobile-fallback</div>');
    if (strpos($mainToMobileFallback, 'محصولات آزمون') === false || strpos($mainToMobileFallback, 'mobile-fallback') !== false) {
        fwrite(STDERR, "FAIL mobile menu did not fall back to the assigned main menu\n");
        $failed++;
    }
    set_theme_mod('nav_menu_locations', []);
    if ($navigationClass::render_desktop('<nav class="fallback-desktop">fallback</nav>') !== '<nav class="fallback-desktop">fallback</nav>') {
        fwrite(STDERR, "FAIL desktop static fallback was not used when no menu is assigned\n");
        $failed++;
    }
    if ($navigationClass::render_mobile('<div data-mobile-menu="true">fallback</div>') !== '<div data-mobile-menu="true">fallback</div>') {
        fwrite(STDERR, "FAIL mobile static fallback was not used when no menu is assigned\n");
        $failed++;
    }
}

set_theme_mod('nav_menu_locations', $originalLocations);
foreach ($menus as $menuId) {
    wp_delete_nav_menu($menuId);
}
if ($failed) {
    exit(1);
}
fwrite(STDOUT, "PASS registered desktop/mobile/product/landing menus, custom walker selectors, nested menu output, and static fallbacks\n");
