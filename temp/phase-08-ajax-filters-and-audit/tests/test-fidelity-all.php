<?php
/** FIDELITY — FULL MIRROR COVERAGE: every one of the 128 mirror .html files. */
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

/** Map a mirror path to a render key + threshold. */
function map_key(string $rel): array {
    if ($rel === 'index.html') return ['index', 95.0];
    if ($rel === 'shop.html' || $rel === 'products.html') return ['shop', 95.0];
    if ($rel === 'projects.html') return ['projects', 95.0];
    $top = ['about.html'=>'about','contact.html'=>'contact','faq.html'=>'faq','services.html'=>'services',
            'recruitment.html'=>'recruitment','refund_returns.html'=>'refund','licenses-and-honors.html'=>'licenses',
            'cart.html'=>'cart','checkout.html'=>'checkout'];
    if (isset($top[$rel])) return [$top[$rel], 95.0];
    if ($rel === 'blog.html') return ['blog', 95.0];
    if (str_starts_with($rel, 'blog/')) return ['blogsingle', 80.0];
    if (str_starts_with($rel, 'product/') || str_starts_with($rel, 'products/')) {
        if (str_contains($rel, 'category-')) return ['productcat', 85.0];
        return ['product', 90.0];
    }
    if (str_starts_with($rel, 'projects/')) {
        if (str_contains($rel, '/type-') || str_contains($rel, '/city-')) return ['projecttype', 80.0];
        return ['project', 90.0];
    }
    if (str_starts_with($rel, 'product_cat/')) return ['productcat', 85.0];
    if (str_starts_with($rel, 'city/')) return ['city', 85.0];
    if (str_starts_with($rel, 'project_type/')) return ['projecttype', 85.0];
    if (str_starts_with($rel, 'category/')) return ['blogcat', 85.0];
    if (str_starts_with($rel, 'services/')) return ['services', 80.0];
    return ['index', 80.0];
}

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($MIRROR, FilesystemIterator::SKIP_DOTS));
$list = [];
foreach ($files as $f) {
    if ($f->getExtension() === 'html') { $list[] = substr($f->getPathname(), strlen($MIRROR) + 1); }
}
sort($list);
echo "[INFO] mirror pages: " . count($list) . "\n";

$summary = [];
foreach ($list as $rel) {
    [$key, $thr] = map_key($rel);
    $ref = dom_signature_set((string) file_get_contents("$MIRROR/$rel"));
    $html = (string) shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg("$HERE/fidelity-render-one.php") . ' ' . escapeshellarg($key) . ' 2>/dev/null');
    $got = array_flip(dom_signature_set($html));
    preg_match('/<nav\b.*<\/nav>/is', (string) file_get_contents("$MIRROR/$rel"), $nm);
    $navSigs = $nm ? array_flip(dom_signature_set("<body>".$nm[0]."</body>")) : [];
    $missing = array_values(array_filter($ref, fn($s) => !isset($got[$s]) && !isset($navSigs[$s]) && strpos($s, 'wp-admin-bar') === false));
    $pct = count($ref) ? round(100 * (count($ref) - count($missing)) / count($ref), 1) : 100;
    $ok = count($missing) === 0 || $pct >= $thr;
    $summary[$key] = ($summary[$key] ?? '') . sprintf("%s %.1f%%  ", basename($rel), $pct);
    if (!$ok) {
        check("fidelity $rel ($key): $pct% >= $thr%", false);
        foreach (array_slice($missing, 0, 3) as $s) { echo "    missing: " . substr($s, 0, 110) . "\n"; }
    }
}
foreach ($summary as $k => $s) { echo "[SUMMARY $k] $s\n"; }
check('all ' . count($list) . ' mirror pages >= threshold', $fail === 0);
echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
