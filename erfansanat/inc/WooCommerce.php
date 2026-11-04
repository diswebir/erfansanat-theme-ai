<?php
/**
 * WooCommerce integration.
 *
 * The theme replaces WooCommerce's content wrappers and templates with the
 * approved reference layout, so this class owns the small set of hook changes
 * that makes that safe. It never touches WooCommerce's data layer and never
 * removes a hook another plugin may rely on for behaviour; only the two
 * wrapper hooks that print markup are swapped.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Bridges WooCommerce's template system to the theme's templates.
 */
final class WooCommerce {

	/**
	 * Attach the integration hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( ! self::is_active() ) {
			return;
		}

		add_action( 'init', array( self::class, 'swap_content_wrappers' ), 20 );
		add_action( 'pre_get_posts', array( self::class, 'archive_paging' ) );
		add_filter( 'woocommerce_locate_template', array( self::class, 'locate_template' ), 10, 3 );
		add_filter( 'woocommerce_add_to_cart_fragments', array( self::class, 'cart_count_fragment' ) );
		add_filter( 'body_class', array( self::class, 'body_class' ) );
	}

	/**
	 * Give the product archives a real `?page=N` page number.
	 *
	 * WooCommerce's own archive is paginated by the main query; the theme's
	 * archive needs the same number for its pager markup and for the browser
	 * script's starting page. WordPress reads `paged` for archives and `page`
	 * for a static front page, so both are accepted.
	 *
	 * @param \WP_Query $query Query being prepared.
	 * @return void
	 */
	public static function archive_paging( $query ): void {
		if ( ! self::is_product_archive_query( $query ) ) {
			return;
		}

		self::paginate_archive_query( $query );
	}

	/**
	 * Whether a query is the main query of a product archive.
	 *
	 * Kept separate so the rule can be asserted without a full page request,
	 * which is the only place a real main query exists.
	 *
	 * @param mixed $query Candidate query.
	 * @return bool
	 */
	public static function is_product_archive_query( $query ): bool {
		if ( ! $query instanceof \WP_Query || is_admin() ) {
			return false;
		}
		if ( ! $query->is_main_query() ) {
			return false;
		}
		return $query->is_post_type_archive( 'product' ) || $query->is_tax( array( 'product_cat', 'product_tag' ) );
	}

	/**
	 * Apply the archive page size and the `?page=N` page number to a query.
	 *
	 * @param \WP_Query $query Product archive query.
	 * @return void
	 */
	public static function paginate_archive_query( \WP_Query $query ): void {
		$per_page = (int) apply_filters( 'erfansanat_archive_per_page', 9 );
		$query->set( 'posts_per_page', max( 1, $per_page ) );

		$page = max( 1, (int) $query->get( 'paged' ), (int) $query->get( 'page' ) );
		if ( $page > 1 && ! $query->get( 'paged' ) ) {
			$query->set( 'paged', $page );
		}
	}

	/**
	 * Whether WooCommerce is present.
	 *
	 * Checked at call time, because `boot_modules()` runs before the plugins
	 * that provide WooCommerce finish loading on some setups.
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		return class_exists( '\WooCommerce' ) || function_exists( 'WC' );
	}

	/**
	 * Replace WooCommerce's default `<div id="primary"><main id="main">` wrapper.
	 *
	 * The theme's `header.php` already opens the page `<main>`; WooCommerce's
	 * wrapper would nest a second one, which is invalid markup and breaks the
	 * landmark structure the accessibility checks look for. Both hooks are
	 * removed and the theme prints an inert div wrapper instead, so the CSS
	 * classes WooCommerce styles against still exist.
	 *
	 * The wrapper cannot reuse `#primary`: the theme's own `<main>` already
	 * owns that id, and two elements sharing it is invalid. `#woocommerce-primary`
	 * keeps the same class contract without the collision.
	 *
	 * @return void
	 */
	public static function swap_content_wrappers(): void {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

		add_action( 'woocommerce_before_main_content', array( self::class, 'open_content_wrapper' ), 10 );
		add_action( 'woocommerce_after_main_content', array( self::class, 'close_content_wrapper' ), 10 );
	}

	/**
	 * Print the theme's own content wrapper.
	 *
	 * @return void
	 */
	public static function open_content_wrapper(): void {
		echo '<div id="woocommerce-primary" class="content-area"><div id="woocommerce-content" class="site-content erfansanat-woocommerce">';
	}

	/**
	 * Close the theme's content wrapper.
	 *
	 * @return void
	 */
	public static function close_content_wrapper(): void {
		echo '</div></div>';
	}

	/**
	 * Resolve theme overrides from `woocommerce/` without losing plugin fallbacks.
	 *
	 * `woocommerce_locate_template()` already checks the theme first, so this
	 * filter only exists to keep the search honest when a child theme overrides
	 * the same file: the child wins, then this theme, then the plugin.
	 *
	 * @param string $template      Located template path.
	 * @param string $template_name Template name relative to the template path.
	 * @param string $template_path Template path.
	 * @return string
	 */
	public static function locate_template( $template, $template_name, $template_path ) {
		unset( $template_path );
		$theme_file = get_stylesheet_directory() . '/woocommerce/' . $template_name;
		if ( is_string( $template ) && '' !== $template && file_exists( $template ) ) {
			return $template;
		}
		return file_exists( $theme_file ) ? $theme_file : $template;
	}

	/**
	 * Keep the header cart badge in sync with WooCommerce's own AJAX add-to-cart.
	 *
	 * The transferred script writes into the same element, so the fragment must
	 * print the same wrapper the header uses; otherwise a cart update would
	 * replace the badge with a bare number.
	 *
	 * @param array<string, string> $fragments Fragment map.
	 * @return array<string, string>
	 */
	public static function cart_count_fragment( $fragments ) {
		if ( ! is_array( $fragments ) ) {
			return $fragments;
		}
		$count = function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;

		ob_start();
		?>
		<span class="absolute -left-1 -top-1 <?php echo 0 === $count ? 'hidden' : ''; ?> min-w-4 rounded-full bg-emerald-700 px-1 text-center text-[10px] leading-4 text-white" data-header-cart-badge="true">
			<?php echo esc_html( (string) $count ); ?>
		</span>
		<?php
		$fragments['span[data-header-cart-badge]'] = (string) ob_get_clean();

		return $fragments;
	}

	/**
	 * Mark WooCommerce views so page-level CSS can target them.
	 *
	 * @param array<int, string> $classes Body classes.
	 * @return array<int, string>
	 */
	public static function body_class( $classes ) {
		if ( ! is_array( $classes ) ) {
			return $classes;
		}
		if ( function_exists( 'is_product' ) && is_product() ) {
			$classes[] = 'erfansanat-product-page';
		}
		return $classes;
	}
}
