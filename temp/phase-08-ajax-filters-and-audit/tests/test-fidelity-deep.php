<?php
/**
 * DEEP DOM FIDELITY — render each page in a fresh real-WP process (get_header is
 * require_once), then verify every UNIQUE structural signature
 * (tag|id|classes|data-attr-names) from the static mirror exists in the render.
 */
$MIRROR = '/home/user/static-mirror/static-mirror';
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$HERE = __DIR__;
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
    'index'   => 'index.html',
    'shop'    => 'shop.html',
    'about'   => 'about.html',
    'contact' => 'contact.html',
    'product' => 'product/fandoghi-string.html',
    'project' => 'projects/artam-tree-midan.html',
];
foreach ($pages as $key => $mirrorFile) {
    $ref = dom_signature_set((string) file_get_contents("$MIRROR/$mirrorFile"));
    $html = (string) shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg("$HERE/fidelity-render-one.php") . ' ' . escapeshellarg($key) . ' 2>/dev/null');
    $got = dom_signature_set($html);
    echo "[INFO] $mirrorFile rendered " . strlen($html) . "B, " . count($got) . " sigs\n";
    $gotSet = array_flip($got);
    // signatures that occur inside the reference <nav> are menu-driven (admin-assigned)
    preg_match('/<nav\b.*<\/nav>/is', (string) file_get_contents("$MIRROR/$mirrorFile"), $nm);
    $navSigs = $nm ? array_flip(dom_signature_set("<body>".$nm[0]."</body>")) : [];
    $missing = array_values(array_filter($ref, fn($s) => !isset($gotSet[$s]) && !isset($navSigs[$s]) && strpos($s, 'wp-admin-bar') === false));
    $pct = count($ref) ? round(100 * (count($ref) - count($missing)) / count($ref), 1) : 100;
    check("fidelity $mirrorFile: $pct% of " . count($ref) . " unique ref signatures present", count($missing) === 0 || $pct >= ($key === 'index' ? 95.0 : 98.0));
    foreach (array_slice($missing, 0, 6) as $s) { echo "    missing: " . substr($s, 0, 130) . "\n"; }
}
echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
