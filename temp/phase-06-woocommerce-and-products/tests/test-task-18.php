<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$runner = file_get_contents( ES_REPO_ROOT . '/temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh' );
$seed_path = ES_REPO_ROOT . '/tools/harness/seed-products.php';
check( 'runner گیت فاز تمام test-task فایل‌ها را در یک PHP-WASM اجرا می‌کند', fn() => is_string( $runner ) && str_contains( $runner, 'tools/harness/run-phase.mjs' ) && str_contains( $runner, 'temp/phase-06-woocommerce-and-products/tests' ) );
check( 'سه fixture برای simple، variable و inquiry و ویژگی‌های pa_* تعریف شده‌اند', fn() => is_file( $seed_path ) && es_p6_has_all( (string) file_get_contents( $seed_path ), array( 'simple', 'variable', 'inquiry', 'pa_color', 'true_sale' ) ) );
check( 'گیت بدون placeholder اجرا می‌شود و JS/CSS محافظت‌شده دست‌نخورده مانده‌اند', fn() => str_contains( es_p6_source( 'assets/js/main.js' ), 'initProductsArchive' ) && md5_file( ES_REPO_ROOT . '/theme/assets/js/main.js' ) === '1253875641c093936de9b3452b7f8e91' && md5_file( ES_REPO_ROOT . '/theme/assets/css/styles.css' ) === 'fa57792cb884336ef8ef34f0699ccd73' && ! es_p6_has_any_pending_placeholders() );
summary();
