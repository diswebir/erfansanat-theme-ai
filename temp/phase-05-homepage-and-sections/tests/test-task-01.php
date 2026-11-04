<?php
/**
 * Integration contract for task 5.1: homepage section partials and rendering.
 * Dynamic card areas are tested in task 5.2; here the canonical section roots and
 * default/custom order are retained from the original mirror.
 */
$root = dirname(__DIR__, 3);
$frontPagePath = $root . '/front-page.php';
$expected = [
    'hero', 'projects', 'project-archive', 'about', 'company-identity', 'history',
    'products', 'catalog', 'services', 'benefits', 'after-sales', 'clients',
    'blog-preview', 'contact', 'location',
];
$dynamicSections = ['hero', 'projects', 'project-archive', 'about', 'products', 'clients', 'blog-preview'];
$failed = 0;

if (!is_file($frontPagePath)) {
    fwrite(STDERR, "FAIL missing file: front-page.php\n");
    exit(1);
}
$frontPageSource = file_get_contents($frontPagePath);
if ($frontPageSource === false || strpos($frontPageSource, 'SectionManager::render_sections') === false) {
    fwrite(STDERR, "FAIL front-page.php must render through SectionManager::render_sections\n");
    $failed++;
}
foreach ($expected as $id) {
    $relative = 'template-parts/home/' . $id . '.php';
    if (!is_file($root . '/' . $relative)) {
        fwrite(STDERR, "FAIL missing homepage section part: {$relative}\n");
        $failed++;
    }
}

$wpRoot = getenv('WP_TEST_ROOT');
if (!$wpRoot) {
    $wpTestDir = getenv('WP_TEST_DIR') ?: $root . '/node_modules/.cache/ese-wp-test';
    $wpRoot = rtrim($wpTestDir, DIRECTORY_SEPARATOR) . '/WordPress-master';
}
if (!is_file($wpRoot . '/wp-load.php')) {
    fwrite(STDERR, "FAIL WordPress test installation not found at {$wpRoot}\n");
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

$class = 'ErfanSanat\\Theme\\Admin\\SectionManager';
if (!class_exists($class)) {
    fwrite(STDERR, "FAIL SectionManager class was not loaded\n");
    exit(1);
}

// Split the canonical main into its 15 top-level section nodes. Dynamic query/card
// interiors may differ, but each section's original root tag and static parts stay intact.
$mirror = file_get_contents($root . '/static-mirror/index.html');
$mainOpen = is_string($mirror) ? strpos($mirror, '<main>') : false;
$mainClose = is_string($mirror) && false !== $mainOpen ? strpos($mirror, '</main>', $mainOpen) : false;
if (false === $mainOpen || false === $mainClose) {
    fwrite(STDERR, "FAIL could not locate the canonical homepage main element\n");
    exit(1);
}
$mainMarkup = substr($mirror, $mainOpen + strlen('<main>'), $mainClose - ($mainOpen + strlen('<main>')));
$sectionPattern = '~<(/?)section\\b[^>]*>~i';
preg_match_all($sectionPattern, $mainMarkup, $tokens, PREG_OFFSET_CAPTURE);
$depth = 0;
$sectionStart = null;
$sourceSections = [];
foreach ($tokens[0] as $index => $token) {
    $isClose = '' !== $tokens[1][$index][0];
    if (!$isClose) {
        if (0 === $depth) {
            $sectionStart = $token[1];
        }
        $depth++;
    } else {
        $depth--;
        if (0 === $depth && null !== $sectionStart) {
            $sourceSections[] = substr($mainMarkup, $sectionStart, $token[1] + strlen($token[0]) - $sectionStart);
            $sectionStart = null;
        }
    }
}
if (count($sourceSections) !== count($expected)) {
    fwrite(STDERR, 'FAIL canonical mirror has ' . count($sourceSections) . ' top-level sections; expected ' . count($expected) . "\n");
    $failed++;
}
$rootTags = [];
foreach ($expected as $index => $id) {
    $path = $root . '/template-parts/home/' . $id . '.php';
    if (!is_file($path) || !isset($sourceSections[$index])) {
        continue;
    }
    $part = file_get_contents($path);
    if (!is_string($part) || preg_match('~<section\\b[^>]*>~i', $sourceSections[$index], $sourceRoot) !== 1 || preg_match('~<section\\b[^>]*>~i', $part, $partRoot) !== 1) {
        fwrite(STDERR, "FAIL {$id} partial is missing its section root\n");
        $failed++;
        continue;
    }
    $rootTags[$id] = $sourceRoot[0];
    if ($partRoot[0] !== $sourceRoot[0]) {
        fwrite(STDERR, "FAIL {$id} partial changed the source section root attributes\n");
        $failed++;
    }
    if (!in_array($id, $dynamicSections, true) && $part !== $sourceSections[$index]) {
        fwrite(STDERR, "FAIL static {$id} partial no longer matches its source section markup\n");
        $failed++;
    }
}

$pageId = wp_insert_post([
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_title' => 'Task 5.1 Homepage QA',
    'post_name' => 'arena-task-51-home',
]);
if (is_wp_error($pageId) || !$pageId) {
    fwrite(STDERR, "FAIL could not create homepage QA page\n");
    exit(1);
}
update_option('show_on_front', 'page');
update_option('page_on_front', (int) $pageId);
$defaultSections = $class::get_renderable_sections((int) $pageId);
$defaultIds = array_values(array_map(static function (array $section): string {
    return (string) ($section['id'] ?? '');
}, $defaultSections));
if ($defaultIds !== $expected) {
    fwrite(STDERR, 'FAIL default homepage order/schema differs: ' . implode(', ', $defaultIds) . "\n");
    $failed++;
}
$legacyConfig = [
    ['id' => 'hero', 'enabled' => true, 'order' => 0, 'fields' => []],
    ['id' => 'projects', 'enabled' => true, 'order' => 1, 'fields' => []],
];
update_post_meta((int) $pageId, $class::META_KEY, $legacyConfig);
$migratedSections = $class::get_renderable_sections((int) $pageId);
$migratedIds = array_values(array_map(static function (array $section): string {
    return (string) ($section['id'] ?? '');
}, $migratedSections));
if ($migratedIds !== $expected) {
    fwrite(STDERR, "FAIL existing front-page settings were not extended with newly-added homepage sections\n");
    $failed++;
}
delete_post_meta((int) $pageId, $class::META_KEY);

$renderFrontPage = static function () use ($frontPagePath, $pageId): string {
    $GLOBALS['post'] = get_post($pageId);
    if ($GLOBALS['post'] instanceof WP_Post) {
        setup_postdata($GLOBALS['post']);
    }
    if (isset($GLOBALS['wp_query']) && is_object($GLOBALS['wp_query'])) {
        $GLOBALS['wp_query']->queried_object = $GLOBALS['post'];
        $GLOBALS['wp_query']->queried_object_id = (int) $pageId;
    }
    ob_start();
    include $frontPagePath;
    $html = (string) ob_get_clean();
    wp_reset_postdata();
    return $html;
};
$renderedPage = $renderFrontPage();
if (preg_match('~<main>(.*?)</main>~s', $renderedPage, $mainMatch) !== 1) {
    fwrite(STDERR, "FAIL front-page template did not render a main element\n");
    $failed++;
    $mainOutput = '';
} else {
    $mainOutput = $mainMatch[1];
}
$defaultPositions = [];
foreach ($expected as $id) {
    $position = isset($rootTags[$id]) ? strpos($mainOutput, $rootTags[$id]) : false;
    if (false === $position) {
        fwrite(STDERR, "FAIL default homepage is missing section root {$id}\n");
        $failed++;
    } else {
        $defaultPositions[] = $position;
    }
}
$sortedDefaultPositions = $defaultPositions;
sort($sortedDefaultPositions);
if ($defaultPositions !== $sortedDefaultPositions) {
    fwrite(STDERR, "FAIL default homepage section roots are not in source order\n");
    $failed++;
}
if (strpos($renderedPage, 'Lorem ipsum') !== false || strpos($renderedPage, 'Coming soon') !== false) {
    fwrite(STDERR, "FAIL homepage output contains placeholder content\n");
    $failed++;
}

// Reorder every section and hide one; actual section roots must follow the saved state.
$custom = [];
$hiddenId = 'project-archive';
$reversed = array_reverse($expected);
foreach ($reversed as $order => $id) {
    $custom[] = [
        'id' => $id,
        'enabled' => $id !== $hiddenId,
        'order' => $order,
        'fields' => [],
    ];
}
update_post_meta((int) $pageId, $class::META_KEY, $custom);
$customPage = $renderFrontPage();
if (preg_match('~<main>(.*?)</main>~s', $customPage, $customMatch) !== 1) {
    fwrite(STDERR, "FAIL custom front-page template did not render a main element\n");
    $failed++;
    $customOutput = '';
} else {
    $customOutput = $customMatch[1];
}
$customPositions = [];
foreach ($reversed as $id) {
    if ($id === $hiddenId) {
        if (isset($rootTags[$id]) && strpos($customOutput, $rootTags[$id]) !== false) {
            fwrite(STDERR, "FAIL hidden section {$id} was still rendered\n");
            $failed++;
        }
        continue;
    }
    $position = isset($rootTags[$id]) ? strpos($customOutput, $rootTags[$id]) : false;
    if (false === $position) {
        fwrite(STDERR, "FAIL custom homepage is missing visible section root {$id}\n");
        $failed++;
    } else {
        $customPositions[] = $position;
    }
}
$sortedCustomPositions = $customPositions;
sort($sortedCustomPositions);
if ($customPositions !== $sortedCustomPositions) {
    fwrite(STDERR, "FAIL homepage output did not apply the configured section order\n");
    $failed++;
}

if (0 === $failed) {
    fwrite(STDOUT, "PASS Task 5.1 section roots, static markup, defaults, ordering, and visibility\n");
}
exit($failed === 0 ? 0 : 1);
