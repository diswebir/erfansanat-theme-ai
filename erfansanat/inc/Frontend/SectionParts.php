<?php
/**
 * Shared home-section rendering pieces.
 *
 * Every reference section repeats the same eyebrow → title → subtitle block and
 * the same asset lookups. Keeping them here means a section part only contains
 * the markup that is genuinely unique to it, which is what makes the port
 * reviewable against `static-mirror/`.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\AssetManager;
use ErfanSanat\Theme\Sections;
use ErfanSanat\Theme\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Section markup helpers.
 */
final class SectionParts {

	/**
	 * Reference asset shipped with the theme, as an absolute URL.
	 *
	 * The URL is resolved from the asset manifest directory so a static file can
	 * never escape the theme, and a missing file degrades to an empty string
	 * instead of a broken image request.
	 *
	 * @param string $relative Path such as `images/hero.jpg`.
	 * @return string
	 */
	public static function asset( string $relative ): string {
		$relative = ltrim( $relative, '/' );
		if ( '' === $relative || str_contains( $relative, '..' ) || ! file_exists( AssetManager::path( $relative ) ) ) {
			return '';
		}
		return AssetManager::uri( $relative );
	}

	/**
	 * Reference hero video URL.
	 *
	 * The video is never enqueued; it is only referenced by the hero markup so
	 * the browser fetches it when the visitor asks for it.
	 *
	 * @return string
	 */
	public static function hero_video(): string {
		return self::asset( 'video/hero-video.mp4' );
	}

	/**
	 * Render the repeated eyebrow → heading → description block.
	 *
	 * Titles may contain one highlighted fragment wrapped in `[...]`, which is
	 * printed in the reference emerald accent. Submitting plain text keeps a
	 * single plain heading, so an editor can never emit broken markup.
	 *
	 * @param array<string, mixed> $args Arguments for the heading block.
	 *
	 *     @type string $eyebrow     Small label above the title.
	 *     @type string $title       Heading text, optionally with one [accent].
	 *     @type string $subtitle    Paragraph under the heading.
	 *     @type string $id          Heading id, when something points at it.
	 *     @type string $align       Either `start` or `center`, like the reference.
	 *     @type string $level       Heading tag, `h2` (the default) or `h3`.
	 *     @type string $title_class Extra classes for the heading.
	 * }
	 * @return void
	 */
	public static function heading( array $args ): void {
		$args = array_merge(
			array(
				'eyebrow'     => '',
				'title'       => '',
				'subtitle'    => '',
				'id'          => '',
				'align'       => 'start',
				'level'       => 'h2',
				'title_class' => 'text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]',
			),
			$args
		);

		$centered = 'center' === $args['align'];
		$level    = in_array( $args['level'], array( 'h2', 'h3' ), true ) ? $args['level'] : 'h2';

		if ( '' === trim( (string) $args['eyebrow'] ) && '' === trim( (string) $args['title'] ) && '' === trim( (string) $args['subtitle'] ) ) {
			return;
		}
		?>
		<div class="flex max-w-2xl flex-col gap-3 <?php echo $centered ? 'items-center text-center mx-auto' : 'items-start text-right'; ?>">
			<?php if ( '' !== trim( (string) $args['eyebrow'] ) ) : ?>
				<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
					<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( (string) $args['eyebrow'] ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $args['title'] ) ) : ?>
				<<?php echo esc_attr( $level ); ?><?php echo '' !== $args['id'] ? ' id="' . esc_attr( (string) $args['id'] ) . '"' : ''; ?> class="<?php echo esc_attr( (string) $args['title_class'] ); ?>">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped fragments below.
					echo self::title_markup( (string) $args['title'] );
					?>
				</<?php echo esc_attr( $level ); ?>>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $args['subtitle'] ) ) : ?>
				<p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( (string) $args['subtitle'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Escape a title and wrap its single `[accent]` fragment.
	 *
	 * @param string $title Raw title.
	 * @return string
	 */
	public static function title_markup( string $title ): string {
		$safe = esc_html( $title );
		if ( ! str_contains( $safe, '[' ) || ! str_contains( $safe, ']' ) ) {
			return $safe;
		}
		return (string) preg_replace(
			'/\[([^\]]+)\]/u',
			'<span class="text-emerald-700">$1</span>',
			$safe,
			1
		);
	}

	/**
	 * Print a reference-styled card link arrow.
	 *
	 * @param string $css Extra classes.
	 * @return void
	 */
	public static function arrow( string $css = 'h-4 w-4' ): void {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="<?php echo esc_attr( $css ); ?>" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>
		<?php
	}

	/**
	 * Site phone as the reference prints it, falling back to the raw setting.
	 *
	 * @return string
	 */
	public static function phone(): string {
		return (string) Settings::get( 'phone_primary_label', Settings::get( 'phone_primary', '' ) );
	}

	/**
	 * Raw primary phone number, for `tel:` links.
	 *
	 * @return string
	 */
	public static function phone_number(): string {
		return (string) Settings::get( 'phone_primary', '' );
	}

	/**
	 * `tel:` link for the primary phone.
	 *
	 * @return string
	 */
	public static function phone_href(): string {
		return SiteParts::phone_link( self::phone_number() );
	}

	/**
	 * The site logo, as a URL.
	 *
	 * The panel setting wins; a theme-mod logo is the fallback, and an empty
	 * string means "no logo configured", which structured data must be able to
	 * tell apart from a logo it simply could not resolve.
	 *
	 * @param string $size Image size.
	 * @return string
	 */
	public static function logo_url( string $size = 'full' ): string {
		$logo_id = (int) Settings::get( 'logo_id', 0 );
		if ( $logo_id <= 0 ) {
			$logo_id = (int) get_theme_mod( 'custom_logo', 0 );
		}
		if ( $logo_id <= 0 ) {
			return '';
		}

		$url = wp_get_attachment_image_url( $logo_id, $size );
		return is_string( $url ) ? $url : '';
	}

	/**
	 * Contact e-mail.
	 *
	 * @return string
	 */
	public static function email(): string {
		return (string) Settings::get( 'email', '' );
	}

	/**
	 * Persian digits for a number the theme prints itself.
	 *
	 * Deliberately narrow: it is used for counters, ratings and review totals
	 * that the template builds. WooCommerce's own price and date HTML is left
	 * alone, because WP-Parsidate already converts plugin output on the live
	 * site and converting twice would mangle the currency markup.
	 *
	 * @param string|int $value Number as stored.
	 * @return string
	 */
	public static function fa_digits( $value ): string {
		return strtr(
			(string) $value,
			array(
				'0' => '۰',
				'1' => '۱',
				'2' => '۲',
				'3' => '۳',
				'4' => '۴',
				'5' => '۵',
				'6' => '۶',
				'7' => '۷',
				'8' => '۸',
				'9' => '۹',
			)
		);
	}

	/**
	 * Section field value with a typed fallback.
	 *
	 * @param array<string, mixed> $fields Section fields.
	 * @param string               $key    Field name.
	 * @param mixed                $fallback Value when the field is empty.
	 * @return mixed
	 */
	public static function field( array $fields, string $key, $fallback = '' ) {
		$value = $fields[ $key ] ?? null;
		if ( null === $value || '' === $value || array() === $value ) {
			return $fallback;
		}
		return $value;
	}

	/**
	 * Default icon markup for a repeater row.
	 *
	 * @param string $icon Icon name from the closed list.
	 * @param string $css  Classes for the inline SVG.
	 * @return void
	 */
	public static function icon( string $icon, string $css = 'h-5 w-5' ): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Whitelisted inline SVG built by Sections::icon_svg().
		echo Sections::icon_svg( $icon, $css );
	}
}
