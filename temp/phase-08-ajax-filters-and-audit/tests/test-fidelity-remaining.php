<?php
/** FIDELITY — REMAINING MIRROR PAGES: faq/services/recruitment/refund/licenses/cart/checkout/blog/blog-single/search/404. */
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$HERE = __DIR__;
$MIRROR = '/home/user/static-mirror/static-mirror';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

function dom_signature_set(string $html): array {
    $html = preg_replace('/<script\b.*?<\/script>/is', '', $html);
    $html = preg_replace('/<style\b.*?<\/style>/is', '', $html);
    if (!preg_match('/<body[^>]*>(.*)$/is', $html, $m)) { return []; }
    $sig = [];
    if (preg_match_all('/<([a-zA-Z][a-zA-Z0-9]*)\b([^>]*)>/', $m[1], $tags, PREG_SET_ORDER)) {
        foreach ($tags as $t) {
            $attrs = $t[2];
            $id = preg_match('/\bid="([^"]*)"/', $attrs, $im) ? $im[1] : '';
            $cls = preg_match('/\bclass="([^"]*)"/', $attrs, $cm) ? $cm[1] : '';
            $clsArr = array_filter(preg_split('/\s+/', trim($cls)));
            sort($clsArr);
            $data = [];
            if (preg_match_all('/\b(data-[a-zA-Z0-9-]+)="/', $attrs, $dm)) { $data = $dm[1]; sort($data); }
            $sig[strtolower($t[1]) . '|' . $id . '|' . implode(',', $clsArr) . '|' . implode(',', $data)] = true;
        }
    }
    return array_keys($sig);
}

$pages = [
    'faq'        => 'faq.html',
    'services'   => 'services.html',
    'recruitment'=> 'recruitment.html',
    'refund'     => 'refund_returns.html',
    'licenses'   => 'licenses-and-honors.html',
    'cart'       => 'cart.html',
    'checkout'   => 'checkout.html',
    'blog'       => 'blog.html',
    'blogsingle' => 'blog/baloti-seven-color-string.html',
    'search'     => 'blog.html',   // search page shares blog layout shell
];
foreach ($pages as $key => $mirrorFile) {
    $refPath = "$MIRROR/$mirrorFile";
    if (!is_file($refPath)) { check("mirror exists $mirrorFile", false); continue; }
    $ref = dom_signature_set((string) file_get_contents($refPath));
    $html = (string) shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg("$HERE/fidelity-render-one.php") . ' ' . escapeshellarg($key) . ' 2>/dev/null');
    $got = dom_signature_set($html);
    echo "[INFO] $mirrorFile rendered " . strlen($html) . "B, " . count($got) . " sigs\n";
    $gotSet = array_flip($got);
    preg_match('/<nav\b.*<\/nav>/is', (string) file_get_contents($refPath), $nm);
    $navSigs = $nm ? array_flip(dom_signature_set("<body>".$nm[0]."</body>")) : [];
    $missing = array_values(array_filter($ref, fn($s) => !isset($gotSet[$s]) && !isset($navSigs[$s]) && strpos($s, 'wp-admin-bar') === false));
    $pct = count($ref) ? round(100 * (count($ref) - count($missing)) / count($ref), 1) : 100;
    $thr = in_array($key, ['search','notfound','blogsingle']) ? 80.0 : 95.0;
    check("fidelity $mirrorFile ($key): $pct% of " . count($ref) . " sigs", count($missing) === 0 || $pct >= $thr);
    foreach (array_slice($missing, 0, 4) as $s) { echo "    missing: " . substr($s, 0, 120) . "\n"; }
}

// ---- 404: structural check (no 404.html in mirror — shares site shell) ----
$html404 = (string) shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg("$HERE/fidelity-render-one.php") . ' notfound 2>/dev/null');
check('404 renders with header nav', strpos($html404, '<nav') !== false);
check('404 renders with footer', strpos($html404, '<footer') !== false);
check('404 shows 404 message', strpos($html404, '404') !== false || strpos($html404, '۴۰۴') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
