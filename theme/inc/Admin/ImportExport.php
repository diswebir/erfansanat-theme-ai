<?php
/**
 * Administrator-only settings backup, import, and scoped reset tools.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Admin\Fields\Sanitizer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Defaults;
use ErfanSanat\Theme\Helpers\Options;
use ErfanSanat\Theme\Sections\Registry;
use ErfanSanat\Theme\Sections\Store;

defined( 'ABSPATH' ) || exit;

/** Secure export/import surface for the options tree and section meta. */
final class ImportExport implements ModuleInterface {

	private const FORMAT = 'erfansanat-theme-settings';
	private const VERSION = 1;
	private const MAX_IMPORT_BYTES = 1048576;

	/** Register authenticated WordPress admin-post actions. */
	public function register(): void {
		add_action( 'admin_post_es_export_settings', array( self::class, 'handle_export' ) );
		add_action( 'admin_post_es_import_settings', array( self::class, 'handle_import' ) );
		add_action( 'admin_post_es_reset_settings', array( self::class, 'handle_reset' ) );
	}

	/** Return a portable JSON snapshot of theme options and registered page section states. */
	public static function export_json(): string {
		$options = get_option( 'erfansanat_theme_options', array() );
		$safe_options = self::sanitize_payload( array( 'format' => self::FORMAT, 'version' => self::VERSION, 'options' => is_array( $options ) ? $options : array() ) );
		$options = $safe_options['options'] ?? array();
		$sections = array();
		foreach ( self::page_ids() as $page => $post_id ) {
			$sections[ $page ] = Store::get( $post_id, $page );
		}
		$payload = array(
			'format'   => self::FORMAT,
			'version'  => self::VERSION,
			'exported' => gmdate( 'c' ),
			'options'  => is_array( $options ) ? $options : array(),
			'sections' => $sections,
		);
		$json = function_exists( 'wp_json_encode' )
			? wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
			: json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		return is_string( $json ) ? $json : '{}';
	}

	/**
	 * Validate an import document and strip unknown pages/groups/fields.
	 *
	 * @return array{options:array<string,mixed>,sections:array<string,array<string,mixed>>}|null
	 */
	public static function sanitize_payload( $payload ): ?array {
		if ( ! is_array( $payload ) || self::FORMAT !== ( $payload['format'] ?? '' ) || self::VERSION !== (int) ( $payload['version'] ?? 0 ) ) {
			return null;
		}
		if ( isset( $payload['options'] ) && ! is_array( $payload['options'] ) ) {
			return null;
		}
		if ( isset( $payload['sections'] ) && ! is_array( $payload['sections'] ) ) {
			return null;
		}

		$raw_options = is_array( $payload['options'] ?? null ) ? $payload['options'] : array();
		$values = array();
		foreach ( Schema::all() as $id => $field ) {
			$exists = false;
			$value = self::read_path( $raw_options, explode( '.', $id ), $exists );
			if ( ! $exists || ! self::valid_value_type( $field, $value ) ) {
				continue;
			}
			$values[ $id ] = Sanitizer::sanitize( $field, $value );
		}
		$options = self::nest_values( $values );

		$sections = array();
		$registered_pages = array_fill_keys( Registry::pages(), true );
		foreach ( (array) ( $payload['sections'] ?? array() ) as $page => $state ) {
			if ( ! is_string( $page ) || ! isset( $registered_pages[ $page ] ) || ! is_array( $state ) ) {
				continue;
			}
			if ( ! is_array( $state['order'] ?? null ) || ! is_array( $state['enabled'] ?? null ) || ! is_array( $state['data'] ?? null ) ) {
				continue;
			}
			$sections[ $page ] = array(
				'order'     => $state['order'],
				'enabled'   => $state['enabled'],
				'data'      => $state['data'],
				'available' => is_array( $state['available'] ?? null ) ? $state['available'] : array(),
			);
		}
		return array( 'options' => $options, 'sections' => $sections );
	}

	/** Import sanitized JSON, snapshotting the current values before writing. */
	public static function import_json( string $json ): bool {
		if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		if ( strlen( $json ) > self::MAX_IMPORT_BYTES ) {
			return false;
		}
		$decoded = json_decode( $json, true );
		$clean = self::sanitize_payload( $decoded );
		if ( null === $clean ) {
			return false;
		}

		$current_options = get_option( 'erfansanat_theme_options', array() );
		update_option( 'erfansanat_theme_options_backup', is_array( $current_options ) ? $current_options : array(), false );
		$current_sections = array();
		foreach ( self::page_ids() as $page => $post_id ) {
			$current_sections[ $page ] = Store::get( $post_id, $page );
		}
		update_option( 'erfansanat_theme_sections_backup', $current_sections, false );

		$current_clean = self::sanitize_payload( array( 'format' => self::FORMAT, 'version' => self::VERSION, 'options' => is_array( $current_options ) ? $current_options : array() ) );
		$merged_options = self::merge_trees( $current_clean['options'] ?? array(), $clean['options'] );
		update_option( 'erfansanat_theme_options', $merged_options, false );
		Options::reset();
		if ( function_exists( 'do_action' ) ) {
			do_action( 'es_options_saved', $merged_options, self::registered_option_paths( $clean['options'] ) );
		}
		$page_ids = self::page_ids();
		foreach ( $clean['sections'] as $page => $state ) {
			$post_id = $page_ids[ $page ] ?? 0;
			if ( $post_id > 0 ) {
				Store::save( $post_id, $state, $page );
			}
		}
		return true;
	}

	/** Restore built-in defaults for exactly one registered settings group. */
	public static function reset_group( string $group ): bool {
		if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		if ( 'sections' === $group ) {
			if ( ! function_exists( 'delete_post_meta' ) ) {
				return false;
			}
			foreach ( self::page_ids() as $post_id ) {
				delete_post_meta( $post_id, Store::META_KEY );
			}
			return true;
		}
		$defaults = Defaults::all();
		if ( ! array_key_exists( $group, $defaults ) ) {
			return false;
		}
		Options::update( $group, $defaults[ $group ] );
		Options::reset();
		return true;
	}

	/** Render the Tools page's separate import/export and reset forms. */
	public static function render_settings(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		$html  = '<section class="es-admin__panel es-import-export" aria-labelledby="es-import-export-title">';
		$html .= '<h2 id="es-import-export-title">پشتیبان‌گیری و بازیابی</h2><p>نسخه JSON شامل تنظیمات ثبت‌شده و وضعیت سکشن‌های صفحات است.</p>';
		$html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		$html .= '<input type="hidden" name="action" value="es_export_settings">' . wp_nonce_field( 'es_export_settings', 'nonce', false, false );
		$html .= '<button class="button" type="submit">دانلود نسخه پشتیبان JSON</button></form>';
		$html .= '<form class="es-import-export__import" method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		$html .= '<input type="hidden" name="action" value="es_import_settings">' . wp_nonce_field( 'es_import_settings', 'nonce', false, false );
		$html .= '<label for="es-settings-file">انتخاب فایل JSON</label><input id="es-settings-file" type="file" name="settings_file" accept="application/json,.json">';
		$html .= '<label for="es-settings-json">یا جای‌گذاری محتوای JSON</label><textarea id="es-settings-json" name="import_json" rows="8" maxlength="1048576"></textarea>';
		$html .= '<button class="button button-primary" type="submit">اعتبارسنجی و واردکردن</button></form></section>';
		$html .= '<section class="es-admin__panel es-import-export__reset" aria-labelledby="es-settings-reset-title"><h2 id="es-settings-reset-title">بازنشانی محدود</h2>';
		$html .= '<p>فقط گروه انتخاب‌شده به مقدار پیش‌فرض برمی‌گردد؛ سایر تنظیمات و سکشن‌ها دست‌نخورده می‌مانند.</p>';
		$html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="es_reset_settings">' . wp_nonce_field( 'es_reset_settings', 'nonce', false, false );
		$html .= '<label for="es-reset-group">گروه موردنظر</label><select id="es-reset-group" name="group">';
		foreach ( array( 'header' => 'هدر', 'footer' => 'فوتر', 'social' => 'شبکه‌های اجتماعی', 'brand' => 'برند', 'archive' => 'آرشیوها', 'scripts' => 'اسکریپت‌ها', 'sections' => 'سکشن‌ها' ) as $key => $label ) {
			$html .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
		}
		$html .= '</select><label class="es-import-export__confirm"><input type="checkbox" name="confirm_reset" value="1" required> من تأیید می‌کنم فقط گروه انتخاب‌شده بازنشانی شود.</label>';
		$html .= '<button class="button button-secondary" type="submit">بازنشانی گروه انتخاب‌شده</button></form></section>';
		return $html;
	}

	/** Send the JSON snapshot as an attachment after a capability and nonce check. */
	public static function handle_export(): void {
		self::authorize_request( 'es_export_settings' );
		if ( function_exists( 'nocache_headers' ) ) { nocache_headers(); }
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="erfansanat-theme-settings-' . gmdate( 'Y-m-d' ) . '.json"' );
		echo self::export_json();
		exit;
	}

	/** Read a small JSON upload or pasted JSON and import its sanitized values. */
	public static function handle_import(): void {
		self::authorize_request( 'es_import_settings' );
		$json = isset( $_POST['import_json'] ) && is_string( $_POST['import_json'] ) ? wp_unslash( $_POST['import_json'] ) : '';
		if ( isset( $_FILES['settings_file'] ) && is_array( $_FILES['settings_file'] ) && UPLOAD_ERR_OK === (int) ( $_FILES['settings_file']['error'] ?? UPLOAD_ERR_NO_FILE ) ) {
			$file = $_FILES['settings_file'];
			if ( (int) ( $file['size'] ?? 0 ) > self::MAX_IMPORT_BYTES || 'json' !== strtolower( pathinfo( (string) ( $file['name'] ?? '' ), PATHINFO_EXTENSION ) ) || empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
				self::redirect( 'invalid' );
			}
			$contents = file_get_contents( $file['tmp_name'] );
			$json = is_string( $contents ) ? $contents : '';
		}
		self::redirect( self::import_json( $json ) ? 'imported' : 'invalid' );
	}

	/** Reset only the selected settings group after an explicit confirmation. */
	public static function handle_reset(): void {
		self::authorize_request( 'es_reset_settings' );
		$group = isset( $_POST['group'] ) && is_string( $_POST['group'] ) ? sanitize_key( wp_unslash( $_POST['group'] ) ) : '';
		$confirmed = isset( $_POST['confirm_reset'] ) && '1' === (string) wp_unslash( $_POST['confirm_reset'] );
		self::redirect( $confirmed && self::reset_group( $group ) ? 'reset' : 'invalid' );
	}

	/** Enforce manage_options and the action-specific referer nonce. */
	private static function authorize_request( string $action ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'اجازه انجام این عملیات را ندارید.', 'erfansanat' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( $action, 'nonce' );
	}

	/** Redirect back to tools with a short status code. */
	private static function redirect( string $notice ): void {
		$url = add_query_arg( array( 'es_tools_notice' => sanitize_key( $notice ) ), admin_url( 'admin.php?page=es-theme-tools' ) );
		wp_safe_redirect( $url );
		exit;
	}

	/** Map current site page keys to their post IDs; imports never trust foreign IDs. */
	private static function page_ids(): array {
		$paths = array(
			'home'       => '',
			'about'      => 'about',
			'contact'    => 'contact',
			'faq'        => 'faq',
			'licenses'   => 'licenses-and-honors',
			'warranty'   => 'refund_returns',
			'recruitment'=> 'recruitment',
		);
		$ids = array();
		foreach ( $paths as $page => $path ) {
			$post_id = 'home' === $page && function_exists( 'get_option' ) ? absint( get_option( 'page_on_front', 0 ) ) : 0;
			if ( $post_id < 1 && '' !== $path && function_exists( 'get_page_by_path' ) ) {
				$post = get_page_by_path( $path );
				$post_id = $post ? absint( $post->ID ?? 0 ) : 0;
			}
			if ( $post_id > 0 ) {
				$ids[ $page ] = $post_id;
			}
		}
		return $ids;
	}

	/** Reject a wrong PHP value type rather than turning it into a destructive default. */
	private static function valid_value_type( array $field, $value ): bool {
		$type = $field['type'] ?? '';
		if ( in_array( $type, array( 'checkbox-group', 'repeater' ), true ) ) {
			return is_array( $value ) || is_string( $value );
		}
		if ( 'media' === $type ) {
			return is_scalar( $value ) || is_array( $value );
		}
		return is_scalar( $value );
	}

	/** Return the registered option paths present in a sanitized settings tree. */
	private static function registered_option_paths( array $tree ): array {
		$paths = array();
		foreach ( Schema::all() as $id => $field ) {
			$exists = false;
			self::read_path( $tree, explode( '.', $id ), $exists );
			if ( $exists ) {
				$paths[] = $id;
			}
		}
		return $paths;
	}

	/** Read a nested setting path, keeping missing distinct from null. */
	private static function read_path( array $tree, array $segments, bool &$exists ) {
		$value = $tree;
		$exists = true;
		foreach ( $segments as $segment ) {
			if ( ! is_array( $value ) || ! array_key_exists( $segment, $value ) ) {
				$exists = false;
				return null;
			}
			$value = $value[ $segment ];
		}
		return $value;
	}

	/** Merge incoming whitelisted values over currently whitelisted values. */
	private static function merge_trees( array $base, array $overrides ): array {
		foreach ( $overrides as $key => $value ) {
			if ( is_array( $value ) && isset( $base[ $key ] ) && is_array( $base[ $key ] ) ) {
				$base[ $key ] = self::merge_trees( $base[ $key ], $value );
			} else {
				$base[ $key ] = $value;
			}
		}
		return $base;
	}

	/** Rebuild a nested options tree using only schema-validated paths. */
	private static function nest_values( array $values ): array {
		$tree = array();
		foreach ( $values as $path => $value ) {
			$segments = explode( '.', $path );
			$cursor =& $tree;
			$last = array_pop( $segments );
			foreach ( $segments as $segment ) {
				if ( ! isset( $cursor[ $segment ] ) || ! is_array( $cursor[ $segment ] ) ) {
					$cursor[ $segment ] = array();
				}
				$cursor =& $cursor[ $segment ];
			}
			$cursor[ $last ] = $value;
			unset( $cursor );
		}
		return $tree;
	}
}
