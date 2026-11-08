<?php
/** RELEASE READINESS — theme package audit: required files, style.css header,
 *  no debug leftovers, text domain consistency, all templates lint-clean. */
$THEME = '/home/user/erfansanat-theme-ai';
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

// required theme files
foreach (['style.css','functions.php','index.php','header.php','footer.php','screenshot.png'] as $f) {
    check("required file: $f", is_file("$THEME/$f"));
}

// style.css header
$h = (string) file_get_contents("$THEME/style.css");
foreach (['Theme Name','Version','License','Text Domain','Requires at least','Requires PHP'] as $field) {
    check("style.css header has $field", preg_match('/^\s*\*?\s*'.preg_quote($field, '/').':/mi', $h) === 1);
}
check('text domain is erfansanat', strpos($h, 'Text Domain: erfansanat') !== false);
check('tested up to >= 7.2', preg_match('/Tested up to:\s*(\d+)\.(\d+)/', $h, $m) && ((int)$m[1] > 7 || ((int)$m[1] === 7 && (int)$m[2] >= 2)));

// no debug leftovers in shipped code (temp/ is dev tooling, excluded)
$leftovers = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($THEME, FilesystemIterator::SKIP_DOTS));
foreach ($rii as $f) {
    if ($f->getExtension() !== 'php') { continue; }
    $p = $f->getPathname();
    if (strpos($p, '/temp/') !== false) { continue; }
    $c = file_get_contents($p);
    foreach (['TODO', 'FIXME', 'var_dump(', 'print_r(', 'error_log('] as $needle) {
        if (strpos($c, $needle) !== false) { $leftovers[] = str_replace($THEME.'/', '', $p) . " :: $needle"; }
    }
}
check('no TODO/FIXME/debug leftovers in shipped PHP', $leftovers === []);
foreach (array_slice($leftovers, 0, 5) as $l) { echo "    $l\n"; }

// text domain consistency: every __/_e/_n/esc_html__ uses erfansanat
$bad = [];
foreach ($rii as $f) {
    if ($f->getExtension() !== 'php' || strpos($f->getPathname(), '/temp/') !== false) { continue; }
    $c = file_get_contents($f->getPathname());
    preg_match_all('/(?:__|_e|_x|_ex|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*[\'"](?:[^\'"\\\\]|\\\\.)*[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]/', $c, $m);
    foreach ($m[1] as $dom) { if ($dom !== 'erfansanat') { $bad[] = $f->getFilename() . " -> $dom"; } }
}
check('all translation calls use erfansanat domain', $bad === []);
foreach (array_slice($bad, 0, 5) as $b) { echo "    $b\n"; }

// every php file lints clean
$lintFail = [];
foreach ($rii as $f) {
    if ($f->getExtension() !== 'php' || strpos($f->getPathname(), '/temp/') !== false) { continue; }
    $out = (string) shell_exec(escapeshellarg($PHP) . ' -l ' . escapeshellarg($f->getPathname()) . ' 2>&1');
    if (strpos($out, 'No syntax errors') === false) { $lintFail[] = $f->getFilename(); }
}
check('all shipped PHP files lint clean', $lintFail === []);
foreach (array_slice($lintFail, 0, 5) as $l) { echo "    LINT: $l\n"; }

// required WP template hierarchy files exist
foreach (['404.php','archive.php','single.php','page.php','search.php','home.php','front-page.php','comments.php'] as $t) {
    check("template exists: $t", is_file("$THEME/$t"));
}

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
