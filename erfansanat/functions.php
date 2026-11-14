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
\ErfanSanat\Theme\Core\CartFragments::boot(); // P06-T023: real Woo cart fragments + AJAX.
\ErfanSanat\Theme\Core\Ajax::boot(); // P08-T001: shared AJAX contract (search/filters/forms).
\ErfanSanat\Theme\Core\AjaxSearch::boot(); // P08-T002: live product search.
\ErfanSanat\Theme\Core\SmartFilters::boot(); // P08-T005/T006: product filter query + endpoint.
\ErfanSanat\Theme\Forms\Entries::boot(); // P08-T012: private form-entry CPT (dependency recorded in task-012).
\ErfanSanat\Theme\Forms\ContactForm::boot(); // P08-T013: public contact submission endpoint (dependency recorded in task-013).
\ErfanSanat\Theme\Forms\QuoteForm::boot(); // P08-T014: public quote submission endpoint (dependency recorded in task-014).
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
\ErfanSanat\Theme\Admin\ServiceMetabox::boot(); // P07-T027: service deliverables/steps.
\ErfanSanat\Theme\Admin\TermMetaboxes::boot();
\ErfanSanat\Theme\Admin\HreflangMetabox::boot();

// Map editable page templates to their section context (P07-T020+) so the
// section metabox and renderer agree without per-page manual configuration.
add_filter(
	'erfansanat_sections_context',
	static function ( $context, $post_id ) {
		if ( '' !== $context ) {
			return $context;
		}
		$template = (string) get_post_meta( $post_id, '_wp_page_template', true );
		$map = array(
			'page-templates/about.php'        => 'about',
			'page-templates/contact.php'      => 'contact',
			'page-templates/landing-arya.php' => 'arya',
		);
		return $map[ $template ] ?? $context;
	},
	10,
	2
);

// Blog comments (P07-T017): wire the T012 single-post hook to the core
// comments template so all WP comment hooks keep working.
add_action(
	'es_single_post_comments',
	static function () {
		if ( comments_open() || have_comments() ) {
			comments_template();
		}
	},
	10
);

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
\ErfanSanat\Theme\Options\Tabs\PagesTab::boot(); // P07-T022+: static-page repeaters.
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
add_action(
	'es_product_related_projects',
	static function () {
		get_template_part( 'template-parts/product/related-projects' ); // P06-T012.
	},
	10
);
add_action(
	'es_product_faq',
	static function () {
		get_template_part( 'template-parts/product/faq' ); // P06-T013.
	},
	10
);
add_action(
	'es_product_reviews_panel',
	static function () {
		get_template_part( 'template-parts/product/reviews' ); // P06-T014.
	},
	10
);
add_action(
	'es_product_related_products',
	static function () {
		get_template_part( 'template-parts/product/related-products' ); // P06-T015.
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

// P07-T022: certificates lightbox, only on the licenses & honors page.
add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( ! is_page_template( 'page-templates/licenses.php' ) ) {
			return;
		}
		$js = ES_ASSETS_DIR . 'js/certificates.js';
		wp_enqueue_script(
			'es-certificates',
			ES_ASSETS_URI . 'js/certificates.js',
			array(),
			is_file( $js ) ? (string) filemtime( $js ) : ES_VERSION,
			true
		);
	},
	20
);

// P07-T023: FAQ search filter, only on the FAQ page (accordion stays in main.js).
add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( ! is_page_template( 'page-templates/faq.php' ) ) {
			return;
		}
		$js = ES_ASSETS_DIR . 'js/faq-search.js';
		wp_enqueue_script(
			'es-faq-search',
			ES_ASSETS_URI . 'js/faq-search.js',
			array( 'es-main' ),
			is_file( $js ) ? (string) filemtime( $js ) : ES_VERSION,
			true
		);
	},
	20
);

// P06-T016: unify the shop page and the product CPT archive onto the theme's
// archive-product template (AC-02). Product taxonomy archives get their own
// templates (P06-T018 / T020).
add_filter(
	'template_include',
	static function ( $template ) {
		$es_is_shop = is_post_type_archive( 'product' )
			|| ( function_exists( 'is_shop' ) && is_shop() );
		if ( $es_is_shop ) {
			$es_archive = ES_THEME_DIR . 'woocommerce/archive-product.php';
			if ( is_file( $es_archive ) ) {
				return $es_archive;
			}
		}

		// Auth page (P06-T027).
		if ( is_page( 'auth' ) ) {
			$es_auth_template = ES_THEME_DIR . 'page-templates/auth.php';
			if ( is_file( $es_auth_template ) ) {
				return $es_auth_template;
			}
		}

		// Cart / checkout / account pages (P06-T022 / T024 / T026).
		$es_wc_pages = array(
			'cart'     => array( 'is_cart', 'woocommerce/cart/cart.php' ),
			'checkout' => array( 'is_checkout', 'woocommerce/checkout/form-checkout.php' ),
			'account'  => array( 'is_account_page', 'woocommerce/myaccount/my-account.php' ),
		);
		foreach ( $es_wc_pages as $es_slug => $es_conf ) {
			$es_is_wc_page = ( function_exists( $es_conf[0] ) && call_user_func( $es_conf[0] ) ) || is_page( $es_slug );
			if ( $es_is_wc_page ) {
				$es_wc_template = ES_THEME_DIR . $es_conf[1];
				if ( is_file( $es_wc_template ) ) {
					return $es_wc_template;
				}
			}
		}

		// Product taxonomy archives (P06-T018 / T020).
		$es_tax_map = array(
			'product_cat'   => 'taxonomy-product_cat.php',
			'product_tag'   => 'taxonomy-product_tag.php',
			'product_brand' => 'taxonomy-product_brand.php',
		);
		foreach ( $es_tax_map as $es_tax => $es_file ) {
			if ( is_tax( $es_tax ) ) {
				$es_tax_template = ES_THEME_DIR . 'woocommerce/' . $es_file;
				if ( is_file( $es_tax_template ) ) {
					return $es_tax_template;
				}
			}
		}
		return $template;
	}
);

/**
 * Single-project section callbacks (P07-T002..T005). The single-projects.php
 * shell fires es_project_* actions; each specialised part attaches here.
 */
add_action(
	'es_project_gallery',
	static function ( $project_id ) {
		get_template_part( 'template-parts/project/gallery', null, array( 'project_id' => (int) $project_id ) );
	},
	10,
	1
);
add_action(
	'es_project_details',
	static function ( $project_id ) {
		get_template_part( 'template-parts/project/meta', null, array( 'project_id' => (int) $project_id ) );
	},
	10,
	1
);
add_action(
	'es_project_used_products',
	static function ( $project_id ) {
		get_template_part( 'template-parts/project/used-products', null, array( 'project_id' => (int) $project_id ) );
	},
	10,
	1
);
add_action(
	'es_project_related',
	static function ( $project_id ) {
		get_template_part( 'template-parts/project/related-projects', null, array( 'project_id' => (int) $project_id ) );
	},
	10,
	1
);
