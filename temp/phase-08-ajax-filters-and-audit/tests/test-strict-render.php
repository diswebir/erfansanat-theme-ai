<?php
/** STRICT RENDER TEST — render every key page with E_ALL and capture any
 *  warning / notice / deprecation emitted by the theme. One page per process. */
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$HERE = __DIR__;
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

// wrapper: enables E_ALL + error log capture, then includes the standard renderer
$wrapper = $HERE . '/strict-render-one.php';
file_put_contents($wrapper, <<<'PHP'
<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
$__log = tempnam(sys_get_temp_dir(), 'err') ;
ini_set('error_log', $__log);
$key = $argv[1] ?? '';
ob_start();
include __DIR__ . '/fidelity-render-one.php';
$html = ob_get_clean();
$errs = file_exists($__log) ? file_get_contents($__log) : '';
// keep only errors originating from the theme
$themeErrs = [];
foreach (preg_split('/\r?\n/', $errs) as $line) {
    if ($line === '') { continue; }
    if (strpos($line, 'erfansanat-theme-ai') !== false && strpos($line, '/temp/') === false) {
        $themeErrs[] = $line;
    }
}
echo "===THEME_ERRORS===\n" . implode("\n", array_slice($themeErrs, 0, 10)) . "\n===END===\n";
echo "===HTML_LEN=== " . strlen($html) . "\n";
PHP
);

$keys = ['index','shop','products','projects','about','contact','faq','services','recruitment','refund','licenses','cart','checkout','blog','blogsingle','product','project','productcat','city','projecttype','blogcat','search','notfound'];
foreach ($keys as $key) {
    $out = (string) shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg($wrapper) . ' ' . escapeshellarg($key) . ' 2>/dev/null');
    preg_match('/===THEME_ERRORS===\n(.*?)\n===END===/s', $out, $m);
    $errs = trim($m[1] ?? '');
    preg_match('/===HTML_LEN=== (\d+)/', $out, $lm);
    $len = (int) ($lm[1] ?? 0);
    check("strict render '$key' (html {$len}B): no theme warnings/notices/deprecations", $errs === '' && $len > 1000);
    if ($errs !== '') { echo "    " . substr($errs, 0, 300) . "\n"; }
}
@unlink($wrapper);
echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
