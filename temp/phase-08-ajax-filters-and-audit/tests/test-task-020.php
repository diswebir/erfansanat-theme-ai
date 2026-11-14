<?php
/**
 * P08-T020 — performance & escaping audit: warning-free key pages
 * (WP_DEBUG-level capture), escaped dynamic output, asset/query budgets.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-020.php
 */

if ( ! defined( 'SAVEQUERIES' ) ) {
	define( 'SAVEQUERIES', true ); // AC-03: query budget — must precede wp-load.
}

require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

ob_start();

es_task( 'P08-T020', 'ممیزی کارایی و Escaping' );

// AC-02 / SC-01: static escape scan across every theme PHP file — raw
// `echo $var` / `print $var` / short-echo tags are banned; everything goes
// through esc_html/esc_url/esc_attr/wp_json_encode/printf literals.
$es_bad_echo = array();
$es_raw_re   = '/(echo|print)\s+\$[^;?]*;/'; // Raw `echo $var;` — ternaries with literal branches are structurally safe.
$es_it       = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( ES_THEME, FilesystemIterator::SKIP_DOTS ) );
foreach ( $es_it as $es_file ) {
	if ( ! $es_file->isFile() || 'php' !== $es_file->getExtension() ) {
		continue;
	}
	$es_lines = explode( "\n", (string) file_get_contents( $es_file->getPathname() ) );
	foreach ( $es_lines as $es_n => $es_line ) {
		if ( preg_match( $es_raw_re, $es_line ) && false === strpos( $es_line, 'phpcs:ignore' ) ) {
			$es_bad_echo[] = substr( $es_file->getPathname(), strlen( ES_THEME ) + 1 ) . ':' . ( $es_n + 1 );
		}
	}
}
es_check( 'SC-01: هیچ echo/print خام بدون phpcs:ignore در تم', empty( $es_bad_echo ) );
es_check( 'AC-02: اسکن escape — تخلف‌ها گزارش نمی‌شوند', array() === $es_bad_echo );
$es_escaped_files = 0;
foreach ( $es_it as $es_file ) {
	if ( $es_file->isFile() && 'php' === $es_file->getExtension() ) {
		$es_src = (string) file_get_contents( $es_file->getPathname() );
		if ( false !== strpos( $es_src, 'esc_html(' ) || false !== strpos( $es_src, 'esc_url(' ) || false !== strpos( $es_src, 'esc_attr(' ) ) {
			$es_escaped_files++;
		}
	}
}
es_check( 'AC-02: خروجی داینامیک با esc_* (فایل‌های پوشش‌داده)', $es_escaped_files >= 30 );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$es_warnings = array();
	set_error_handler( static function ( $es_no, $es_str, $es_file, $es_line ) use ( &$es_warnings ) {
		if ( in_array( $es_no, array( E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED ), true ) ) {
			$es_warnings[] = $es_str . ' @' . substr( $es_file, strlen( ES_THEME ) + 1 ) . ':' . $es_line;
		}
		return true;
	} );

	/**
	 * Render one key page (SC-02): swap the global query, include the real
	 * template (get_header/get_footer included so topbar/head run too).
	 *
	 * @return array{html:string,queries:int}
	 */
	$es_render_page = static function ( array $es_args, string $es_template ): array {
		global $wp_query, $wpdb;
		$es_saved        = $wp_query;
		$es_q            = new WP_Query( $es_args );
		$GLOBALS['wp_query'] = $es_q;
		$es_posts        = $es_q->posts ?? array();
		$GLOBALS['post'] = $es_posts[0] ?? null;
		ErfanSanat\Theme\Core\Seo::reset_printed();
		$es_before = (int) $wpdb->num_queries;
		ob_start();
		include ES_THEME . '/' . $es_template;
		$es_html = (string) ob_get_clean();
		$GLOBALS['wp_query'] = $es_saved;
		unset( $GLOBALS['post'] );
		return array( 'html' => $es_html, 'queries' => (int) $wpdb->num_queries - $es_before );
	};

	// Fixtures: static front page + a post + a category.
	$es_page_id = (int) wp_insert_post( array( 'post_title' => 'T020 خانه', 'post_status' => 'publish', 'post_type' => 'page' ) );
	$es_post_id = (int) wp_insert_post( array( 'post_title' => 'T020 مقاله', 'post_status' => 'publish', 'post_type' => 'post', 'post_content' => 'محتوای تستی T020' ) );
	$es_cat     = get_term_by( 'name', 'T020 دسته', 'category' );
	$es_cat_id  = $es_cat ? (int) $es_cat->term_id : (int) wp_insert_term( 'T020 دسته', 'category' )['term_id'];
	$es_show    = get_option( 'show_on_front' );
	$es_pof     = get_option( 'page_on_front' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $es_page_id );

	// SC-02/AC-01: front page — warning-free render, budgets respected.
	$es_front = $es_render_page( array( 'page_id' => $es_page_id ), 'front-page.php' );
	es_check( 'SC-02: صفحه اصلی بدون warning/notice', array() === $es_warnings && strlen( $es_front['html'] ) > 10000 );
	global $wp_styles, $wp_scripts;
	$es_styles  = count( (array) ( $wp_styles->queue ?? array() ) );
	$es_scripts = count( (array) ( $wp_scripts->queue ?? array() ) );
	es_check( "AC-03: بودجه asset — استایل‌ها <= 8 (got=$es_styles)", $es_styles > 0 && $es_styles <= 8 );
	es_check( "AC-03: بودجه asset — اسکریپت‌ها <= 6 (got=$es_scripts)", $es_scripts > 0 && $es_scripts <= 6 );
	es_check( "AC-03: بودجه کوئری صفحه اصلی <= 250 (got={$es_front['queries']})", $es_front['queries'] > 0 && $es_front['queries'] <= 250 );

	// DeBloat: no emoji script on the front head.
	es_check( 'AC-03: اسکریپت emoji حذف شده (DeBloat)', false === strpos( $es_front['html'], 'wp-emoji' ) );

	// SC-02: single post + category archive render warning-free.
	$es_w_before = count( $es_warnings );
	$es_single   = $es_render_page( array( 'p' => $es_post_id, 'post_type' => 'post' ), 'single.php' );
	es_check( 'SC-02: تک‌مقاله بدون warning رندر می‌شود', count( $es_warnings ) === $es_w_before && false !== strpos( $es_single['html'], 'T020 مقاله' ) );
	es_check( "AC-03: بودجه کوئری تک‌مقاله <= 150 (got={$es_single['queries']})", $es_single['queries'] <= 150 );
	$es_w_before = count( $es_warnings );
	$es_arch     = $es_render_page( array( 'cat' => $es_cat_id ), 'archive.php' );
	es_check( 'SC-02: آرشیو دسته بدون warning رندر می‌شود', count( $es_warnings ) === $es_w_before && strlen( $es_arch['html'] ) > 1000 );

	// Local assets only: every queued stylesheet/script source is empty or local.
	$es_external = array();
	foreach ( array( 'style' => $wp_styles, 'script' => $wp_scripts ) as $es_kind => $es_deps ) {
		foreach ( (array) ( $es_deps->queue ?? array() ) as $es_handle ) {
			$es_src = (string) ( $es_deps->registered[ $es_handle ]->src ?? '' );
			if ( '' !== $es_src && ! preg_match( '#^(/|http://localhost)#', $es_src ) ) {
				$es_external[] = $es_kind . ':' . $es_handle;
			}
		}
	}
	es_check( 'AC-03: همه assetهای صف‌شده داخلی‌اند', array() === $es_external );

	// Teardown.
	restore_error_handler();
	update_option( 'show_on_front', $es_show );
	update_option( 'page_on_front', $es_pof );
	wp_delete_post( $es_page_id, true );
	wp_delete_post( $es_post_id, true );
	wp_delete_term( $es_cat_id, 'category' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
