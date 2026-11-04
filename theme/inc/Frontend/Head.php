<?php
/** Head metadata, language alternatives, and structured data. */

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\SEO\JsonLd;

defined( 'ABSPATH' ) || exit;

/** Adds fallback social metadata without competing with SEO plugins. */
final class Head implements ModuleInterface {

	public function register(): void {
		add_action( 'wp_head', array( self::class, 'render' ), 5 );
	}

	/** Render metadata once in the document head. */
	public static function render(): void {
		if ( self::social_tags_enabled() ) {
			echo self::social_tags(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All values are escaped by social_tags().
		}
		echo self::hreflang_tags(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URLs are escaped by hreflang_tags().
		echo JsonLd::script(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON is encoded and script-filtered.
	}

	/** Whether this theme should provide social metadata rather than duplicate SEOPress output. */
	public static function social_tags_enabled( ?bool $seopress_active = null ): bool {
		if ( null === $seopress_active ) {
			$seopress_active = defined( 'SEOPRESS_VERSION' );
		}
		return ! $seopress_active;
	}

	/** Build Open Graph/Twitter fallback metadata. */
	public static function social_tags(): string {
		$title = function_exists( 'wp_get_document_title' ) ? (string) wp_get_document_title() : ( function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'name' ) : 'عرفان صنعت اصفهان (ESE)' );
		$description = self::description();
		$url = function_exists( 'get_permalink' ) && get_permalink() ? (string) get_permalink() : ( function_exists( 'home_url' ) ? (string) home_url( '/' ) : '/' );
		$image = function_exists( 'get_the_post_thumbnail_url' ) ? (string) get_the_post_thumbnail_url( get_the_ID(), 'large' ) : '';
		if ( '' === $image && function_exists( 'get_theme_file_uri' ) ) {
			$image = (string) get_theme_file_uri( '/assets/img/hero.jpg' );
		}
		$type = function_exists( 'is_singular' ) && is_singular() ? 'article' : 'website';
		$tags = array(
			'<meta property="og:locale" content="fa_IR" />',
			'<meta property="og:site_name" content="' . self::attr( function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'name' ) : 'شرکت دانش‌بنیان عرفان صنعت اصفهان' ) . '" />',
			'<meta property="og:title" content="' . self::attr( $title ) . '" />',
			'<meta property="og:description" content="' . self::attr( $description ) . '" />',
			'<meta property="og:type" content="' . self::attr( $type ) . '" />',
			'<meta property="og:url" content="' . self::attr( $url ) . '" />',
			'<meta name="twitter:card" content="summary_large_image" />',
			'<meta name="twitter:title" content="' . self::attr( $title ) . '" />',
			'<meta name="twitter:description" content="' . self::attr( $description ) . '" />',
		);
		if ( '' !== $image ) {
			$tags[] = '<meta property="og:image" content="' . self::attr( $image ) . '" />';
			$tags[] = '<meta name="twitter:image" content="' . self::attr( $image ) . '" />';
		}
		return implode( "\n", $tags ) . "\n";
	}

	/** Build exactly one alternate URL for each supported language. */
	public static function hreflang_tags(): string {
		$post_id = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
		$home = function_exists( 'home_url' ) ? (string) home_url( '/' ) : '/';
		$defaults = array( 'fa' => $home, 'en' => function_exists( 'home_url' ) ? (string) home_url( '/en/' ) : '/en/', 'ar' => function_exists( 'home_url' ) ? (string) home_url( '/ar/' ) : '/ar/' );
		$tags = array();
		foreach ( $defaults as $language => $fallback ) {
			$value = $post_id && function_exists( 'get_post_meta' ) ? get_post_meta( $post_id, 'hrefmeta_' . $language, true ) : '';
			$url = is_string( $value ) && '' !== trim( $value ) ? trim( $value ) : $fallback;
			$url = self::safe_url( $url, $fallback );
			$tags[] = '<link rel="alternate" hreflang="' . $language . '" href="' . self::attr( $url ) . '" />';
		}
		return implode( "\n", $tags ) . "\n";
	}

	private static function description(): string {
		if ( function_exists( 'has_excerpt' ) && has_excerpt() && function_exists( 'get_the_excerpt' ) ) {
			return trim( wp_strip_all_tags( (string) get_the_excerpt() ) );
		}
		return 'شرکت دانش‌بنیان عرفان صنعت اصفهان — پیشگام در طراحی، تولید و اجرای پروژه‌های نورپردازی شهری، المان‌های نوری، درختان نوری و خورشیدنما.';
	}

	private static function safe_url( string $url, string $fallback ): string {
		$scheme = strtolower( (string) parse_url( $url, PHP_URL_SCHEME ) );
		if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
			return $fallback;
		}
		return function_exists( 'esc_url' ) ? (string) esc_url( $url, array( 'http', 'https' ) ) : $url;
	}

	private static function attr( string $value ): string {
		return function_exists( 'esc_attr' ) ? esc_attr( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}
}
