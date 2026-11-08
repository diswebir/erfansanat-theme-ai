<?php
/** ASSET BUDGET TEST — render key pages, audit every <script>/<link> tag:
 *  all local, deferred, no jQuery, total size budget, image lazy-loading. */
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$HERE = __DIR__;
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

$keys = ['index' => 'index.html', 'shop' => 'shop.html', 'product' => 'product/fandoghi-string.html', 'blog' => 'blog.html'];
$totalMaxKb = 600;   // CSS+JS budget per page
$imgLazyMin = 0.5;   // fraction of content imgs with loading=lazy

foreach ($keys as $key => $mirrorFile) {
    $outFile = tempnam(sys_get_temp_dir(), 'ab') . '.html';
    shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg('/home/user/erfansanat-theme-ai/temp/phase-08-ajax-filters-and-audit/tests/fidelity-render-one.php') . ' ' . escapeshellarg($key) . ' > ' . escapeshellarg($outFile) . ' 2>/dev/null');
    $html = (string) file_get_contents($outFile);
    @unlink($outFile);
    check("$key renders", strlen($html) > 20000);

    preg_match_all('/<script[^>]*src="([^"]+)"[^>]*>/i', $html, $sm);
    preg_match_all('/<link[^>]*rel="stylesheet"[^>]*href="([^"]+)"[^>]*>/i', $html, $lm);
    preg_match_all('/<link[^>]*href="([^"]+)"[^>]*rel="stylesheet"[^>]*>/i', $html, $lm2);
    $lm[1] = array_merge($lm[1], $lm2[1]);
    $ext = array_filter(array_merge($sm[1], $lm[1]), fn($u) => preg_match('#^https?://(?!localhost)#', $u) && strpos($u, 'erfansanat') === false && strpos($u, 'w.org') === false);
    check("$key: no external script/css", count($ext) === 0);
    if ($ext) { echo "    ext: " . implode(',', array_slice($ext, 0, 3)) . "\n"; }

    $noDefer = 0;
    foreach ($sm[0] as $tag) { if (!preg_match('/\b(defer|async)\b/i', $tag)) $noDefer++; }
    check("$key: all scripts deferred/async", $noDefer === 0);

    $jq = array_filter($sm[1], fn($u) => stripos($u, 'jquery') !== false);
    check("$key: no jQuery", count($jq) === 0);

    // local asset size budget
    $kb = 0;
    foreach (array_merge($sm[1], $lm[1]) as $u) {
        if (preg_match('#^https?://#', $u)) { continue; }
        $path = preg_replace('#^.*?/wp-content/themes/erfansanat-theme-ai/#', '', $u);
        $path = explode('?', $path)[0];
        $f = '/home/user/erfansanat-theme-ai/' . $path;
        if (is_file($f)) { $kb += filesize($f) / 1024; }
    }
    check("$key: asset payload {$kb}KB <= {$totalMaxKb}KB", $kb <= $totalMaxKb);

    preg_match_all('/<img\b[^>]*>/i', $html, $im);
    $lazy = 0; $total = isset($im[1]) ? count($im[1]) : count($im[0]);
    foreach ($im[0] as $tag) { if (preg_match('/loading="lazy"/i', $tag)) $lazy++; }
    $ratio = $total ? $lazy / $total : 1;
    // fidelity: theme lazy ratio must match the static mirror (hero imgs eager there too)
    $mh = (string) file_get_contents('/home/user/static-mirror/static-mirror/' . $mirrorFile);
    preg_match_all('/<img\b[^>]*>/i', $mh, $mim);
    $mt = isset($mim[0]) ? count($mim[0]) : 0;
    $ml = 0; foreach (($mim[0] ?? []) as $tag) { if (preg_match('/loading="lazy"/i', $tag)) $ml++; }
    $mratio = $mt ? $ml / $mt : 1;
    check("$key: lazy ratio " . round($ratio, 2) . " >= mirror " . round($mratio, 2) . " - 0.05", $ratio >= $mratio - 0.05 || $total === 0);
}

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
