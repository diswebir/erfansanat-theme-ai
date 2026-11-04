<?php
/**
 * Footer / contact / social tab.
 *
 * Defaults mirror static-mirror/index.html. Social links are rendered with a
 * safe rel and empty links are skipped.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\FieldRenderer;
use ErfanSanat\Theme\Options\Schema;

final class FooterTab {

	public const ID = 'footer';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
	}

	public static function id(): string {
		return self::ID;
	}

	/** @return array<int,string> */
	public static function fields(): array {
		return array_keys(
			array_filter(
				Schema::fields(),
				static function ( $field ) {
					return ( $field['tab'] ?? '' ) === self::ID;
				}
			)
		);
	}

	/** @return array<string,mixed> */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $key ) {
			$defaults[ $key ] = Schema::default( $key );
		}
		return $defaults;
	}

	/**
	 * Contact data source for footer + global CTA.
	 *
	 * @return array{phone:string,mobile:string,email:string,address:string,postal_code:string}
	 */
	public static function contact(): array {
		return array(
			'phone'       => (string) es_option( 'contact_phone' ),
			'mobile'      => (string) es_option( 'contact_mobile' ),
			'email'       => (string) es_option( 'contact_email' ),
			'address'     => (string) es_option( 'contact_address' ),
			'postal_code' => (string) es_option( 'contact_postal_code' ),
		);
	}

	/**
	 * Social links with safe rel/target; empty URLs are skipped (SC-02).
	 *
	 * @return array<int,array{network:string,url:string,target:string,rel:string}>
	 */
	public static function social_links(): array {
		$out = array();
		foreach ( (array) es_option( 'social_links' ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$url = FieldRenderer::safe_url( (string) ( $item['url'] ?? '' ) );
			if ( '' === $url ) {
				continue;
			}
			$out[] = array(
				'network' => (string) ( $item['network'] ?? '' ),
				'url'     => $url,
				'target'  => '_blank',
				'rel'     => 'noopener noreferrer',
			);
		}
		return $out;
	}

	public static function copyright(): string {
		return (string) es_option( 'copyright_text' );
	}

	public static function description(): string {
		return (string) es_option( 'footer_description' );
	}

	/** @return array<int,mixed> */
	public static function badges(): array {
		return (array) es_option( 'footer_badges' );
	}

	/**
	 * Global contact CTA content (P04-T016). Phone/email reuse contact().
	 *
	 * @return array<string,mixed>
	 */
	public static function cta(): array {
		return array(
			'enabled'           => (bool) es_option( 'cta_enabled' ),
			'eyebrow'           => (string) es_option( 'cta_eyebrow' ),
			'heading'           => (string) es_option( 'cta_heading' ),
			'heading_highlight' => (string) es_option( 'cta_heading_highlight' ),
			'description'       => (string) es_option( 'cta_description' ),
			'phone_label'       => (string) es_option( 'cta_phone_label' ),
			'quote_label'       => (string) es_option( 'cta_quote_label' ),
			'quote_url'         => (string) es_option( 'cta_quote_url' ),
			'hours'             => (string) es_option( 'cta_hours' ),
		);
	}
}
