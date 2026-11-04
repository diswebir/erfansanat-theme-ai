<?php
/** Structured data matching the ErfanSanat static mirror. */

namespace ErfanSanat\Theme\SEO;

defined( 'ABSPATH' ) || exit;

/** Builds safe Organization, WebSite, and Breadcrumb JSON-LD. */
final class JsonLd {

	/** @return array<int,array<string,mixed>> */
	public static function schema(): array {
		$site_url = function_exists( 'home_url' ) ? home_url( '/' ) : 'https://erfansanat.com/';
		$site_name = function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'name' ) : 'شرکت دانش‌بنیان عرفان صنعت اصفهان';
		$description = 'شرکت دانش‌بنیان عرفان صنعت اصفهان — پیشگام در طراحی، تولید و اجرای پروژه‌های نورپردازی شهری، المان‌های نوری، درختان نوری و خورشیدنما.';
		$site_root = rtrim( $site_url, '/' ) . '/';
		$organization_id = $site_root . '#organization';

		return array(
			array(
				'@context'       => 'https://schema.org',
				'@type'          => 'Organization',
				'@id'            => $organization_id,
				'name'           => $site_name,
				'alternateName'  => 'Erfan Sanat Esfahan (ESE)',
				'url'            => $site_url,
				'logo'           => function_exists( 'get_theme_file_uri' ) ? get_theme_file_uri( '/assets/img/ese-logo.svg' ) : $site_url . 'wp-content/themes/erfansanat/assets/img/ese-logo.svg',
				'telephone'      => function_exists( 'es_option' ) ? (string) es_option( 'header.phone', '031-91091011' ) : '031-91091011',
				'email'          => function_exists( 'es_option' ) ? (string) es_option( 'header.email', 'info@erfansanat.com' ) : 'info@erfansanat.com',
				'address'        => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => function_exists( 'es_option' ) ? (string) es_option( 'footer.address', 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹' ) : 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
					'addressLocality' => 'اصفهان',
					'addressRegion'   => 'اصفهان',
					'addressCountry'  => 'IR',
				),
				'sameAs'         => array( 'https://instagram.com/erfan_sanat_esfahan', 'https://t.me/erfansanat', 'https://linkedin.com/company/erfansanat' ),
			),
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => array(
					array(
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => 'صفحه اصلی',
						'item'     => $site_url,
					),
				),
			),
			array(
				'@context'       => 'https://schema.org',
				'@type'          => 'WebSite',
				'@id'            => $site_root . '#website',
				'url'            => $site_url,
				'name'           => $site_name,
				'description'    => $description,
				'inLanguage'     => 'fa-IR',
				'publisher'      => array( '@id' => $organization_id ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => $site_root . '?s={search_term_string}',
					'query-input' => 'required name=search_term_string',
				),
			),
		);
	}

	/** Return the encoded JSON payload, or an empty string on encoding failure. */
	public static function json(): string {
		$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
		$json  = function_exists( 'wp_json_encode' ) ? wp_json_encode( self::schema(), $flags ) : json_encode( self::schema(), $flags );
		return is_string( $json ) ? $json : '';
	}

	/** Render schema in the static mirror's stable script wrapper. */
	public static function script(): string {
		$json = self::json();
		if ( '' === $json ) {
			return '';
		}
		$html = '<script id="ese-jsonld-schema" type="application/ld+json">' . $json . '</script>';
		return function_exists( 'wp_kses' ) ? wp_kses( $html, array( 'script' => array( 'id' => true, 'type' => true ) ) ) : $html;
	}
}
