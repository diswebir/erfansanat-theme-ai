<?php
/**
 * Erfan Sanat theme bootstrap.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/Constants.php';
require_once ES_INC_DIR . 'Autoloader.php';

\ErfanSanat\Theme\Autoloader::register();

// Global helpers (es_option) — P02-T002.
require_once ES_INC_DIR . 'Helpers.php';

// Navigation walkers (multiple classes in one file) — P04-T005+.
require_once ES_INC_DIR . 'Core/NavWalkers.php';
\ErfanSanat\Theme\Core\NavWalkers\LandingWalker::boot();

\ErfanSanat\Theme\Core\Theme::boot();
\ErfanSanat\Theme\Core\Menus::boot();
\ErfanSanat\Theme\Core\DeBloat::boot();
\ErfanSanat\Theme\Core\AssetManager::boot();
\ErfanSanat\Theme\Core\PostTypes::boot();
\ErfanSanat\Theme\Core\Taxonomies::boot();
\ErfanSanat\Theme\Core\Rewrites::boot();
\ErfanSanat\Theme\Core\LegacyRedirects::boot();
\ErfanSanat\Theme\Core\ViewCounter::boot();
\ErfanSanat\Theme\Core\Hreflang::boot();
\ErfanSanat\Theme\Admin\ProjectColumns::boot();
\ErfanSanat\Theme\Admin\ProductMetaboxes::boot();
\ErfanSanat\Theme\Admin\ProductQuickEdit::boot();
\ErfanSanat\Theme\Admin\ProjectMetaboxes::boot();
\ErfanSanat\Theme\Admin\TermMetaboxes::boot();
\ErfanSanat\Theme\Admin\HreflangMetabox::boot();

// Admin typography (Vazirmatn) — P02-T019. Boots before the dashboard so the
// shared font stylesheet is registered ahead of dashboard/metabox assets.
\ErfanSanat\Theme\Admin\Typography::boot();

// Custom admin dashboard — P02-T003.
\ErfanSanat\Theme\Admin\ThemeOptions::boot();

// Dashboard option tabs — P02-T010+.
\ErfanSanat\Theme\Options\Tabs\HomeTab::boot();
\ErfanSanat\Theme\Options\Tabs\HeaderTab::boot();
\ErfanSanat\Theme\Options\Tabs\MegaMenuTab::boot();
\ErfanSanat\Theme\Options\Tabs\FooterTab::boot();
\ErfanSanat\Theme\Options\Tabs\BrandTab::boot();
\ErfanSanat\Theme\Options\Tabs\ArchiveTab::boot();
\ErfanSanat\Theme\Admin\SectionMetabox::boot();
\ErfanSanat\Theme\Sections\Renderer::boot();

/*
 * Phase 6 — single-product template parts. Each part is hooked into the
 * standard WooCommerce summary hooks so the content-single-product shell
 * (P06-T002) renders it in place of its minimal fallback. Wired here as a
 * registered dependency; later Phase-6 tasks append their own hooks.
 */
add_action(
	'woocommerce_before_single_product_summary',
	static function () {
		get_template_part( 'template-parts/product/gallery' ); // P06-T003.
	},
	5
);
add_action(
	'es_product_specs_panel',
	static function () {
		get_template_part( 'template-parts/product/features' ); // P06-T009.
	},
	5
);
add_action(
	'es_product_specs_panel',
	static function () {
		get_template_part( 'template-parts/product/specs-table' ); // P06-T010.
	},
	10
);
add_action(
	'es_product_specs_panel',
	static function () {
		get_template_part( 'template-parts/product/legal-ids' ); // P06-T005.
	},
	20
);
add_action(
	'woocommerce_single_product_summary',
	static function () {
		get_template_part( 'template-parts/product/purchase' ); // P06-T006.
	},
	10
);
add_action(
	'es_product_summary_after',
	static function () {
		get_template_part( 'template-parts/product/notices' ); // P06-T008.
	},
	10
);
add_action(
	'es_product_usage',
	static function () {
		get_template_part( 'template-parts/product/usage' ); // P06-T011.
	},
	10
);

// P06-T007: additive max-quantity guard for the no-Woo fallback stepper only.
add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( ! is_singular( 'product' ) || defined( 'WC_VERSION' ) ) {
			return;
		}
		$js = ES_ASSETS_DIR . 'js/product-quantity.js';
		wp_enqueue_script(
			'es-product-quantity',
			ES_ASSETS_URI . 'js/product-quantity.js',
			array( 'es-main' ),
			is_file( $js ) ? (string) filemtime( $js ) : ES_VERSION,
			true
		);
	},
	20
);
