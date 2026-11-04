<?php
/**
 * Shared front-end markup fragments fed by the theme settings.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\AssetManager;
use ErfanSanat\Theme\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Prints the site-wide pieces that the header and footer share.
 *
 * These helpers exist so the Phase 4 header/footer templates and the temporary
 * Phase 2 shell print identical markup from one source. Every value is escaped
 * at print time and every list is filtered against the schema on save, so a
 * template never has to trust the option row.
 */
final class SiteParts {

	/**
	 * Print the optional announcement bar.
	 *
	 * The bar only appears when it is enabled and has text, so an empty notice
	 * never reserves layout space.
	 *
	 * @return void
	 */
	public static function notice_bar(): void {
		if ( ! Settings::get( 'notice_enabled', false ) ) {
			return;
		}
		$text = (string) Settings::get( 'notice_text', '' );
		if ( '' === trim( $text ) ) {
			return;
		}
		$url = (string) Settings::get( 'notice_link', '' );
		?>
		<div class="erfansanat-notice" role="region" aria-label="<?php esc_attr_e( 'اعلان سایت', 'erfansanat' ); ?>">
			<div class="mx-auto flex max-w-[1300px] items-center justify-center gap-2 px-4 py-2 text-center text-[12px] font-bold sm:text-[13px]">
				<?php if ( '' !== $url ) : ?>
					<a class="underline decoration-2 underline-offset-4" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $text ); ?></a>
				<?php else : ?>
					<span><?php echo esc_html( $text ); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the brand block: uploaded logo when present, the text mark otherwise.
	 *
	 * @param string $link        Optional wrapper link.
	 * @param string $extra_class Additional class names for the wrapper.
	 * @return void
	 */
	public static function brand( string $link = '', string $extra_class = '' ): void {
		$logo_id = (int) Settings::get( 'logo_id', 0 );
		// A deleted or non-image attachment falls back to the text mark rather
		// than printing a broken <img>.
		if ( $logo_id > 0 && ! wp_attachment_is_image( $logo_id ) ) {
			$logo_id = 0;
		}
		$mark       = (string) Settings::get( 'brand_mark', 'ESE' );
		$name       = (string) Settings::get( 'brand_name', '' );
		$tagline    = (string) Settings::get( 'brand_tagline', '' );
		$wrapper    = '' !== $link ? 'a' : 'div';
		$href_attr  = '' !== $link ? ' href="' . esc_url( $link ) . '"' : '';
		$class_attr = 'erfansanat-brand ' . $extra_class;
		echo '<' . esc_attr( $wrapper ) . ' class="' . esc_attr( $class_attr ) . '"' . $href_attr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Tag name and escaped attributes above.
		if ( $logo_id > 0 ) {
			echo wp_get_attachment_image(
				$logo_id,
				'full',
				false,
				array(
					'class'    => 'erfansanat-brand__logo h-10 w-auto',
					'alt'      => $name,
					'loading'  => 'eager',
					'decoding' => 'async',
				)
			);
		} else {
			printf( '<span class="erfansanat-brand__mark grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50/90 text-xs font-black tracking-tighter text-emerald-800 sm:h-10 sm:w-10 sm:text-sm">%s</span>', esc_html( $mark ) );
		}
		?>
		<span class="erfansanat-brand__text leading-tight">
			<span class="block text-[14px] font-black tracking-tight text-slate-900 sm:text-[15px]"><?php echo esc_html( $name ); ?></span>
			<?php if ( '' !== $tagline ) : ?>
				<span class="block text-[10px] font-semibold text-slate-500"><?php echo esc_html( $tagline ); ?></span>
			<?php endif; ?>
		</span>
		<?php
		echo '</' . esc_attr( $wrapper ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Closes the element opened above.
	}

	/**
	 * Print the desktop top strip (greeting text plus quick links).
	 *
	 * @return void
	 */
	public static function topbar(): void {
		$title    = (string) Settings::get( 'topbar_title', '' );
		$subtitle = (string) Settings::get( 'topbar_subtitle', '' );
		$links    = (array) Settings::get( 'topbar_links', array() );
		if ( '' === $title && '' === $subtitle && array() === $links ) {
			return;
		}
		?>
		<div class="erfansanat-topbar hidden border-b border-emerald-100/70 bg-emerald-50/55 text-[11px] text-slate-600 lg:block">
			<div class="mx-auto flex max-w-[1300px] items-center justify-between px-5 py-1.5 lg:px-8">
				<div class="flex items-center gap-2.5 font-medium">
					<?php if ( '' !== $title ) : ?>
						<span class="font-extrabold text-emerald-800"><?php echo esc_html( $title ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $title && '' !== $subtitle ) : ?>
						<span class="text-slate-300" aria-hidden="true">·</span>
					<?php endif; ?>
					<?php if ( '' !== $subtitle ) : ?>
						<span><?php echo esc_html( $subtitle ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( array() !== $links ) : ?>
					<div class="flex items-center gap-3.5">
						<?php foreach ( $links as $index => $item ) : ?>
							<?php if ( $index > 0 ) : ?>
								<span class="text-slate-300" aria-hidden="true">·</span>
							<?php endif; ?>
							<a href="<?php echo esc_url( (string) $item['url'] ); ?>" class="font-bold text-slate-600 transition-colors hover:text-emerald-700"><?php echo esc_html( (string) $item['title'] ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the footer contact column.
	 *
	 * @return void
	 */
	public static function contact_details(): void {
		$address       = (string) Settings::get( 'address', '' );
		$primary       = (string) Settings::get( 'phone_primary', '' );
		$primary_label = (string) Settings::get( 'phone_primary_label', '' );
		$sales         = (string) Settings::get( 'phone_sales', '' );
		$sales_label   = (string) Settings::get( 'phone_sales_label', '' );
		$email         = (string) Settings::get( 'email', '' );
		$hours         = (string) Settings::get( 'hours', '' );
		?>
		<ul class="erfansanat-contact space-y-3 text-[13px] text-slate-600">
			<?php if ( '' !== $address ) : ?>
				<li class="flex items-start gap-2.5">
					<span aria-hidden="true" class="mt-1 text-emerald-600">◈</span>
					<span class="leading-6"><?php echo esc_html( $address ); ?></span>
				</li>
			<?php endif; ?>
			<?php if ( '' !== $primary ) : ?>
				<li class="flex items-center gap-2.5">
					<span aria-hidden="true" class="text-emerald-600">☏</span>
					<a href="<?php echo esc_url( 'tel:' . self::tel_href( $primary ) ); ?>" class="font-bold text-slate-800 hover:text-emerald-700"><?php echo esc_html( '' !== $primary_label ? $primary_label : $primary ); ?></a>
				</li>
			<?php endif; ?>
			<?php if ( '' !== $sales ) : ?>
				<li class="flex items-center gap-2.5">
					<span aria-hidden="true" class="text-emerald-600">☏</span>
					<a href="<?php echo esc_url( 'tel:' . self::tel_href( $sales ) ); ?>" class="hover:text-emerald-700"><?php echo esc_html( '' !== $sales_label ? $sales_label : $sales ); ?></a>
				</li>
			<?php endif; ?>
			<?php if ( '' !== $email && is_email( $email ) ) : ?>
				<li class="flex items-center gap-2.5">
					<span aria-hidden="true" class="text-emerald-600">✉</span>
					<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="hover:text-emerald-700" dir="ltr"><?php echo esc_html( $email ); ?></a>
				</li>
			<?php endif; ?>
			<?php if ( '' !== $hours ) : ?>
				<li class="flex items-center gap-2.5">
					<span aria-hidden="true" class="text-emerald-600">◷</span>
					<span><?php echo esc_html( $hours ); ?></span>
				</li>
			<?php endif; ?>
		</ul>
		<?php
	}

	/**
	 * Print social links, but only the networks the site actually configured.
	 *
	 * @return void
	 */
	public static function social_links(): void {
		$social = (array) Settings::get( 'social', array() );
		if ( array() === $social ) {
			return;
		}
		?>
		<ul class="erfansanat-social flex flex-wrap gap-2 text-[12px] font-bold">
			<?php foreach ( $social as $item ) : ?>
				<li>
					<a class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-emerald-800 hover:bg-emerald-100"
						href="<?php echo esc_url( (string) $item['url'] ); ?>"
						rel="noopener noreferrer nofollow"
						target="_blank">
						<?php echo esc_html( (string) $item['title'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Print the footer product categories.
	 *
	 * Configured term ids win; when none are configured the eight categories the
	 * static reference lists are used, looked up by slug so the markup survives a
	 * data import that renumbers terms.
	 *
	 * @param string $css_class Class list applied to the category list.
	 * @return void
	 */
	public static function footer_categories( string $css_class = 'space-y-2.5 text-[13px] text-slate-600' ): void {
		$terms      = array();
		$configured = array_map( 'intval', (array) Settings::get( 'footer_categories', array() ) );
		if ( array() !== $configured ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'include'    => $configured,
					'orderby'    => 'include',
					'hide_empty' => false,
				)
			);
		} else {
			foreach ( self::reference_category_slugs() as $slug ) {
				$term = get_term_by( 'slug', $slug, 'product_cat' );
				if ( $term instanceof \WP_Term ) {
					$terms[] = $term;
				}
			}
		}

		if ( ! is_array( $terms ) || array() === $terms ) {
			return;
		}
		?>
		<ul class="erfansanat-footer-cats <?php echo esc_attr( $css_class ); ?>">
			<?php foreach ( $terms as $term ) : ?>
				<li>
					<a href="<?php echo esc_url( (string) get_term_link( $term ) ); ?>" class="transition-colors hover:text-emerald-700"><?php echo esc_html( $term->name ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Print a configured list of links.
	 *
	 * @param string $key      Settings key holding the rows.
	 * @param string $css_class List class names.
	 * @param bool   $fallback Whether to fall back to the footer menu when empty.
	 * @return void
	 */
	public static function link_list( string $key, string $css_class = 'space-y-2.5 text-[13px] text-slate-600', bool $fallback = false ): void {
		$links = (array) Settings::get( $key, array() );
		if ( array() === $links && $fallback ) {
			$locations = get_nav_menu_locations();
			if ( isset( $locations['tolidat-sherkat'] ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'tolidat-sherkat',
						'container'      => false,
						'items_wrap'     => '<ul class="erfansanat-links ' . esc_attr( $css_class ) . '">%3$s</ul>',
						'fallback_cb'    => false,
					)
				);
				return;
			}
		}
		if ( array() === $links ) {
			return;
		}
		?>
		<ul class="erfansanat-links <?php echo esc_attr( $css_class ); ?>">
			<?php foreach ( $links as $item ) : ?>
				<li><a href="<?php echo esc_url( (string) $item['url'] ); ?>" class="transition-colors hover:text-emerald-700"><?php echo esc_html( (string) $item['title'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Print the trust badges.
	 *
	 * @param string $css_class Class list applied to the badge list.
	 * @return void
	 */
	public static function footer_badges( string $css_class = 'flex flex-wrap gap-2 pt-1 text-[11px] font-bold text-emerald-800' ): void {
		$badges = (array) Settings::get( 'footer_badges', array() );
		if ( array() === $badges ) {
			return;
		}
		?>
		<ul class="erfansanat-badges <?php echo esc_attr( $css_class ); ?>">
			<?php foreach ( $badges as $badge ) : ?>
				<li class="rounded-full bg-emerald-50 px-3 py-1"><?php echo esc_html( (string) $badge ); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Fallback main navigation used until a real menu is assigned.
	 *
	 * The approved reference lists these eight destinations in its primary menu.
	 * Phase 4 replaces this with the configured mega menu; the fallback keeps the
	 * header usable in the meantime and is only printed when no menu is set.
	 *
	 * @param string $css_class Class list for the `<ul>`.
	 * @return void
	 */
	public static function primary_navigation( string $css_class = 'erfansanat-menu flex items-center gap-1' ): void {
		$locations = get_nav_menu_locations();
		if ( isset( $locations['main-mega-menu'] ) && (int) $locations['main-mega-menu'] > 0 ) {
			wp_nav_menu(
				array(
					'theme_location' => 'main-mega-menu',
					'container'      => false,
					'items_wrap'     => '<ul class="' . esc_attr( $css_class ) . '">%3$s</ul>',
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			return;
		}

		$fallback = array(
			array(
				'title' => __( 'صفحه اصلی', 'erfansanat' ),
				'url'   => home_url( '/' ),
			),
			array(
				'title' => __( 'فروشگاه', 'erfansanat' ),
				'url'   => home_url( '/shop/' ),
			),
			array(
				'title' => __( 'پروژه‌ها', 'erfansanat' ),
				'url'   => home_url( '/projects/' ),
			),
			array(
				'title' => __( 'خدمات', 'erfansanat' ),
				'url'   => home_url( '/services/' ),
			),
			array(
				'title' => __( 'وبلاگ', 'erfansanat' ),
				'url'   => home_url( '/blog/' ),
			),
			array(
				'title' => __( 'درباره ما', 'erfansanat' ),
				'url'   => home_url( '/about/' ),
			),
			array(
				'title' => __( 'تماس با ما', 'erfansanat' ),
				'url'   => home_url( '/contact/' ),
			),
		);
		?>
		<ul class="<?php echo esc_attr( $css_class ); ?>">
			<?php foreach ( $fallback as $item ) : ?>
				<li>
					<a class="rounded-xl px-3 py-2 text-[13px] font-extrabold text-slate-700 transition-colors hover:bg-slate-50 hover:text-emerald-700"
						href="<?php echo esc_url( (string) $item['url'] ); ?>"><?php echo esc_html( (string) $item['title'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Custom header or footer scripts, printed verbatim only for a capability holder.
	 *
	 * The value is stored only when the saving user held unfiltered_html, and the
	 * capability is re-checked here so a role change cannot leave stale script in
	 * the page for a user who may no longer edit it.
	 *
	 * @param string $position Either `header` or `footer`.
	 * @return string
	 */
	public static function custom_scripts( string $position ): string {
		$key        = 'footer' === $position ? 'footer_scripts' : 'header_scripts';
		$definition = Settings::fields()[ $key ] ?? array();
		$capability = isset( $definition['capability'] ) ? (string) $definition['capability'] : 'unfiltered_html';
		if ( ! current_user_can( $capability ) ) {
			return '';
		}
		return (string) Settings::get( $key, '' );
	}

	/**
	 * Slugs of the product categories the static footer lists.
	 *
	 * @return string[]
	 */
	public static function reference_category_slugs(): array {
		return array(
			'point-light',
			'strings-and-lighting-decorations',
			'acorn-thread',
			'needle-thread',
			'light-tree',
			'urban-lighting-elements',
			'wall-washer',
			'control-box',
		);
	}

	/**
	 * Digits of a phone number, keeping a leading plus.
	 *
	 * @param string $phone Stored phone value.
	 * @return string
	 */
	private static function tel_href( string $phone ): string {
		$plus   = str_starts_with( $phone, '+' );
		$digits = preg_replace( '/[^0-9]/', '', $phone );
		return ( $plus ? '+' : '' ) . (string) $digits;
	}

	/**
	 * Public `tel:` link for a stored phone number.
	 *
	 * @param string $phone Stored phone value.
	 * @return string
	 */
	public static function phone_link( string $phone ): string {
		$href = self::tel_href( $phone );
		return '' === $href ? '' : 'tel:' . $href;
	}

	/**
	 * Print the logo URL for the header, or an empty string when none is set.
	 *
	 * @return string
	 */
	public static function logo_url(): string {
		$logo_id = (int) Settings::get( 'logo_id', 0 );
		if ( $logo_id <= 0 ) {
			return '';
		}
		$url = wp_get_attachment_image_url( $logo_id, 'full' );
		return is_string( $url ) ? $url : '';
	}

	/**
	 * Asset URL used when the site has no uploaded logo yet.
	 *
	 * @return string
	 */
	public static function fallback_logo(): string {
		return AssetManager::uri( 'images/ese-logo.svg' );
	}
}
