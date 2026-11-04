<?php
/** Final audit contract for Task 8.3. The master runner executes all suites and DOM/browser E2E. */
$root = dirname(__DIR__, 3);
$checks = [
    'temp/run-all-phases-e2e.sh' => [
        'phase-01-core-and-assets', 'phase-08-ajax-filters-and-audit', 'e2e-ui.test.js', 'browser-e2e.test.js', 'axe-core', 'WP_DEBUG', 'diff --check',
    ],
    'temp/setup-wordpress-test.sh' => ['define(\'WP_DEBUG\', true)', 'WP_DEBUG_LOG'],
    'inc/TemplateParts.php' => ['seopress_is_active', 'hreflang_markup'],
    'inc/Admin/ContentMeta.php' => ['hreflang'],
    'header.php' => ['wp_head()'],
    'footer.php' => ['wp_footer()'],
    'temp/phase-08-ajax-filters-and-audit/audit-report.md' => [
        'Rollout', 'Rollback', 'SEOPress', 'WP_DEBUG', 'browser', 'latency',
    ],
];
$failed = 0;
foreach ($checks as $relative => $needles) {
    $source = is_file($root . '/' . $relative) ? file_get_contents($root . '/' . $relative) : false;
    if (false === $source) {
        fwrite(STDERR, "FAIL final audit artifact missing: {$relative}\n");
        $failed++;
        continue;
    }
    foreach ($needles as $needle) {
        if (false === strpos($source, $needle)) {
            fwrite(STDERR, "FAIL audit contract `{$needle}` missing in {$relative}\n");
            $failed++;
        }
    }
}
foreach (range(1, 8) as $phaseNumber) {
    $matches = glob($root . '/temp/phase-' . sprintf('%02d', $phaseNumber) . '-*/tests/run-phase-tests.sh');
    if (!$matches || !is_file($matches[0])) {
        fwrite(STDERR, "FAIL executable phase runner missing for phase {$phaseNumber}\n");
        $failed++;
    }
}
$e2eFiles = [
    $root . '/temp/phase-08-ajax-filters-and-audit/tests/e2e-ui.test.js',
    $root . '/temp/phase-08-ajax-filters-and-audit/tests/browser-e2e.test.js',
    $root . '/node_modules/jsdom/package.json',
    $root . '/node_modules/puppeteer-core/package.json',
    $root . '/node_modules/@sparticuz/chromium/package.json',
    $root . '/node_modules/axe-core/package.json',
];
foreach ($e2eFiles as $e2eFile) {
    if (!is_file($e2eFile)) {
        fwrite(STDERR, "FAIL DOM/browser E2E artifact missing: {$e2eFile}\n");
        $failed++;
    }
}
if ($failed) {
    exit(1);
}
fwrite(STDOUT, "PASS final audit artifacts, eight phase runners, SEO guard contracts, WP_DEBUG harness, and DOM/browser E2E availability\n");
