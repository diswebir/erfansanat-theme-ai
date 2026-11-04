<?php
/** Admin-facing permalink audit and privacy-conscious 404 log. */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Exposes the stable route map and keeps a bounded, query-free 404 history. */
final class UrlAudit implements ModuleInterface {

	private const PAGE_SLUG = 'es-theme-url-audit';
	private const LOG_OPTION = 'es_404_audit';
	private const MAX_LOGS = 100;

	/** Register admin screen, public 404 capture and clear-log action. */
	public function register(): void {
		add_action( 'admin_menu', array( self::class, 'register_page' ) );
		add_action( 'template_redirect', array( self::class, 'record_404' ), 1 );
		add_action( 'admin_post_es_clear_404_audit', array( self::class, 'clear_log' ) );
	}

	/** Add the audit page below the theme dashboard. */
	public static function register_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		add_submenu_page(
			'erfansanat-theme',
			__( 'ممیزی URL', 'erfansanat-theme' ),
			__( 'ممیزی URL', 'erfansanat-theme' ),
			'manage_options',
			self::PAGE_SLUG,
			array( self::class, 'render_page' )
		);
	}

	/** Return the route-family table for output and automated checks. */
	public static function route_families(): array {
		return array(
			'shop'        => array( 'label' => 'فروشگاه', 'path' => '/shop/' ),
			'product_cat' => array( 'label' => 'دسته محصول', 'path' => '/product_cat/sample-category/' ),
			'projects'    => array( 'label' => 'پروژه‌ها', 'path' => '/projects/' ),
			'city'        => array( 'label' => 'شهر', 'path' => '/city/esfahan/' ),
			'project_type'=> array( 'label' => 'نوع پروژه', 'path' => '/project_type/bridge-lighting/' ),
			'product'     => array( 'label' => 'محصول', 'path' => '/product/sample-product/' ),
		);
	}

	/** Render the safe URL map and recent 404 paths. */
	public static function render(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}

		$html  = '<div class="wrap es-url-audit"><h1>' . esc_html__( 'ممیزی URL', 'erfansanat-theme' ) . '</h1>';
		$html .= '<p>' . esc_html__( 'مسیرهای پایدار قالب و درخواست‌های ۴۰۴ اخیر؛ این گزارش IP، کوکی یا query string ذخیره نمی‌کند.', 'erfansanat-theme' ) . '</p>';
		$html .= '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'خانواده', 'erfansanat-theme' ) . '</th><th>' . esc_html__( 'نمونه مسیر', 'erfansanat-theme' ) . '</th><th>' . esc_html__( 'آدرس', 'erfansanat-theme' ) . '</th></tr></thead><tbody>';
		foreach ( self::route_families() as $family => $route ) {
			$url = home_url( $route['path'] );
			$html .= '<tr><th scope="row">' . esc_html( $family ) . ' — ' . esc_html( $route['label'] ) . '</th><td><code>' . esc_html( $route['path'] ) . '</code></td><td><a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a></td></tr>';
		}
		$html .= '</tbody></table><h2>' . esc_html__( 'درخواست‌های ۴۰۴ اخیر', 'erfansanat-theme' ) . '</h2>';
		$logs = get_option( self::LOG_OPTION, array() );
		$logs = is_array( $logs ) ? array_slice( $logs, 0, 50, true ) : array();
		if ( ! $logs ) {
			$html .= '<p>' . esc_html__( 'موردی ثبت نشده است.', 'erfansanat-theme' ) . '</p>';
		} else {
			$html .= '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'مسیر', 'erfansanat-theme' ) . '</th><th>' . esc_html__( 'تعداد', 'erfansanat-theme' ) . '</th><th>' . esc_html__( 'آخرین درخواست (UTC)', 'erfansanat-theme' ) . '</th></tr></thead><tbody>';
			foreach ( $logs as $entry ) {
				if ( ! is_array( $entry ) ) { continue; }
				$html .= '<tr><td><code>' . esc_html( $entry['path'] ?? '' ) . '</code></td><td>' . absint( $entry['hits'] ?? 0 ) . '</td><td>' . esc_html( $entry['last_seen'] ?? '' ) . '</td></tr>';
			}
			$html .= '</tbody></table>';
		}
		$html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="es_clear_404_audit">' . wp_nonce_field( 'es_clear_404_audit', '_wpnonce', false, false ) . '<button class="button" type="submit">' . esc_html__( 'پاک‌کردن گزارش ۴۰۴', 'erfansanat-theme' ) . '</button></form></div>';
		return $html;
	}

	/** Render the page callback after a capability check. */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		echo self::render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render() escapes all dynamic output.
	}

	/** Store only normalized paths, with no query string, IP address, or cookies. */
	public static function record_404(): void {
		if ( ! function_exists( 'is_404' ) || ! is_404() || ( function_exists( 'is_admin' ) && is_admin() ) ) {
			return;
		}
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) && is_string( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		$path        = parse_url( $request_uri, PHP_URL_PATH );
		if ( ! is_string( $path ) || '' === $path ) {
			return;
		}
		$path = rawurldecode( $path );
		$path = preg_replace( '/[\x00-\x1F\x7F]/u', '', $path ) ?? '';
		$path = substr( trim( sanitize_text_field( $path ) ), 0, 255 );
		if ( '' === $path ) { return; }

		$logs = get_option( self::LOG_OPTION, array() );
		$logs = is_array( $logs ) ? $logs : array();
		$entry = is_array( $logs[ $path ] ?? null ) ? $logs[ $path ] : array( 'path' => $path, 'hits' => 0 );
		$entry['hits']      = min( 1000000, absint( $entry['hits'] ?? 0 ) + 1 );
		$entry['last_seen'] = gmdate( 'Y-m-d H:i:s' );
		$logs[ $path ]      = $entry;
		uasort( $logs, static function ( $left, $right ) { return strcmp( (string) ( $right['last_seen'] ?? '' ), (string) ( $left['last_seen'] ?? '' ) ); } );
		$logs = array_slice( $logs, 0, self::MAX_LOGS, true );
		update_option( self::LOG_OPTION, $logs, false );
	}

	/** Clear the bounded log from an authorized nonce-protected admin request. */
	public static function clear_log(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'اجازه انجام این عملیات را ندارید.', 'erfansanat-theme' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'es_clear_404_audit' );
		delete_option( self::LOG_OPTION );
		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) );
		exit;
	}
}
