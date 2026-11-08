<?php
/**
 * AJAX FUNCTIONAL TEST — every endpoint via subprocess on real WP 7.2+SQLite:
 * valid nonce → success JSON + sane payload; bad nonce → rejected;
 * form submissions persist to es_form_entry CPT.
 */
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$HERE = __DIR__;
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

function call(string $key, array $params): array {
    global $PHP, $HERE;
    $out = (string) shell_exec(escapeshellarg($PHP).' '.escapeshellarg("$HERE/ajax-call.php").' '.escapeshellarg($key).' '.escapeshellarg(json_encode($params)).' 2>/dev/null');
    return [$out, json_decode($out, true)];
}

[$out, $j] = call('filters_search', ['s'=>'والاشر']);
check('search: success JSON', is_array($j) && ($j['success'] ?? false) === true);
check('search: finds والاشر product', is_array($j) && strpos($j['data']['html'] ?? '', 'والاشر') !== false);

[$out, $j] = call('filters_products', ['product_cat'=>'point-light']);
check('filter_products by cat: success + html', is_array($j) && ($j['success'] ?? false) === true && strlen($j['data']['html'] ?? '') > 50);

[$out, $j] = call('filters_products', ['true_sale'=>'true']);
check('filter_products true_sale: sale item present', is_array($j) && strpos($j['data']['html'] ?? '', 'والاشر') !== false);

[$out, $j] = call('filters_projects', ['city'=>'esfahan']);
check('filter_projects by city: success + project', is_array($j) && ($j['success'] ?? false) === true && strpos($j['data']['html'] ?? '', 'تونل') !== false);

[$out, $j] = call('filters_search', ['s'=>'x', 'nonce'=>'bad']);
check('bad nonce rejected (no success payload)', !is_array($j) || ($j['success'] ?? false) !== true);

$formsOk = 0;
foreach (['contact','inquiry','recruitment','feedback'] as $form) {
    [$out, $j] = call('form', ['form_type'=>$form, 'name'=>'تست', 'phone'=>'09120000000', 'message'=>'پیام تست']);
    if (is_array($j) && ($j['success'] ?? false) === true) { $formsOk++; }
}
check("all 4 form endpoints accept valid submission ($formsOk/4)", $formsOk === 4);

// persistence check in a fresh boot
$persist = (string) shell_exec(escapeshellarg($PHP).' '.escapeshellarg("$HERE/ajax-call.php").' form '.escapeshellarg(json_encode(['form_type'=>'contact','name'=>'x'])).' 2>/dev/null');
$chk = (string) shell_exec(escapeshellarg($PHP).' -r '.escapeshellarg('
define("WP_INSTALLING", true);
require "/home/user/wp-test/wp-load.php";
$c = count(get_posts(["post_type"=>"es_form_entry","posts_per_page"=>-1]));
echo "ENTRIES:$c\n";
').' 2>/dev/null');
check('form entries persisted to es_form_entry CPT', strpos($chk, 'ENTRIES:') !== false && (int) filter_var($chk, FILTER_SANITIZE_NUMBER_INT) >= 1);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
