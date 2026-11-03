<?php
/**
 * Test — Task 1.7: انتقال CSS/JS/فونت با تطابق بای‌به‌بایت
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\theme_md5;
use function EsTest\ref_md5;

echo "== Task 1.7: css/js/fonts transfer ==\n";

$map = array(
	'assets/css/styles.css',
	'assets/js/main.js',
	'assets/fonts/vazirmatn-arabic.woff2',
	'assets/fonts/vazirmatn-latin.woff2',
	'assets/fonts/vazirmatn-latin-ext.woff2',
);

foreach ( $map as $rel ) {
	$md5 = theme_md5( $rel );
	check( "$rel exists", fn() => null !== $md5 );
	check( "$rel matches the static mirror byte-for-byte", function () use ( $md5, $rel ) {
		$ref = ref_md5( $rel );
		return null !== $md5 && null !== $ref && $md5 === $ref;
	} );
}

check( 'styles.css is the only stylesheet payload (188K+)', fn() => filesize( EsTest\tpath( 'assets/css/styles.css' ) ) > 180000 );
check( 'main.js is the only script payload (100K+)', fn() => filesize( EsTest\tpath( 'assets/js/main.js' ) ) > 100000 );
check( 'no second css file was introduced', fn() => 1 === count( (array) glob( EsTest\tpath( 'assets/css/*' ) ) ) );
check( 'no second js file was introduced', fn() => 1 === count( (array) glob( EsTest\tpath( 'assets/js/*' ) ) ) );
check( 'no minified duplicate of the sources', fn() => array() === glob( EsTest\tpath( 'assets/*' ) . '/*.min.*' ) );
check( 'font files are woff2 only', function () {
	foreach ( (array) glob( EsTest\tpath( 'assets/fonts/*' ) ) as $f ) {
		if ( '.woff2' !== substr( $f, -6 ) ) { return false; }
	}
	return true;
} );

summary();
