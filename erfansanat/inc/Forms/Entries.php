<?php
/**
 * Form entry storage (P08-T012).
 *
 * Persists validated form payloads as a private `es_form_entry` post type
 * with sanitized data, form type, read/unread status and submission context.
 * Admin listing/management is capability-gated (agent.md 8.2); the public
 * website can only create entries through the form endpoints (T013+).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

defined( 'ABSPATH' ) || exit;

final class Entries {

	private static bool $booted = false;

	/** Register the private entry CPT (also wired from functions.php boot). */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		if ( did_action( 'init' ) ) {
			self::register_cpt();
		} else {
			add_action( 'init', array( __CLASS__, 'register_cpt' ), 5 );
		}
	}

	/** Private, non-queryable post type — admin storage only (SC-02). */
	public static function register_cpt(): void {
		if ( post_type_exists( 'es_form_entry' ) ) {
			return;
		}
		register_post_type(
			'es_form_entry',
			array(
				'labels'             => array(
					'name'          => 'ورودی فرم‌ها',
					'singular_name' => 'ورودی فرم',
				),
				'public'             => false, // AC-03/SC-02: never public.
				'publicly_queryable' => false,
				'exclude_from_search'=> true,
				'show_ui'            => false, // T017 builds the admin UI.
				'show_in_menu'       => false,
				'show_in_rest'       => false,
				'supports'           => array( 'title', 'editor' ),
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
			)
		);
	}

	/** Admin-level gate for listing/reading/deleting entries (AC-03). */
	public static function can_manage(): bool {
		return is_user_logged_in() && current_user_can( 'manage_options' );
	}

	/** Public form submission path — always allowed (endpoints self-guard). */
	public static function can_store(): bool {
		return true;
	}

	/**
	 * Validate + persist an entry (SC-01).
	 *
	 * @param string              $type    Form type (contact|quote|recruitment|feedback).
	 * @param array<string,mixed> $data    Raw payload (sanitized here).
	 * @param array<string,mixed> $context Optional page/user context.
	 * @return int|\WP_Error Entry post id or error.
	 */
	public static function store( string $type, array $data, array $context = array() ) {
		if ( ! isset( Schema::types()[ $type ] ) ) {
			return new \WP_Error( 'es_form_unknown_type', 'نوع فرم شناخته‌شده نیست.' );
		}
		$clean = Schema::validate( $type, $data );
		if ( is_wp_error( $clean ) ) {
			return $clean;
		}
		$name  = isset( $clean['name'] ) ? (string) $clean['name'] : '';
		$label = Schema::types()[ $type ];
		$title = trim( $label . ' — ' . $name );
		if ( '' === $name ) {
			$title = $label . ' — ' . wp_date( 'Y-m-d H:i' );
		}
		$entry_id = (int) wp_insert_post(
			array(
				'post_type'    => 'es_form_entry',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_content' => isset( $clean['message'] ) ? (string) $clean['message'] : ( isset( $clean['background'] ) ? (string) $clean['background'] : '' ),
			),
			true
		);
		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}
		update_post_meta( $entry_id, '_es_entry_data', $clean );
		update_post_meta( $entry_id, '_es_entry_form', $type );
		update_post_meta( $entry_id, '_es_entry_status', 'unread' ); // AC-02: starts unread.
		update_post_meta(
			$entry_id,
			'_es_entry_meta',
			array(
				'page'     => isset( $context['page'] ) ? esc_url_raw( (string) $context['page'] ) : '',
				'user_id'  => get_current_user_id(),
				'submitted'=> (string) current_time( 'mysql' ),
			)
		);
		return $entry_id;
	}

	/** Current read/unread status (AC-02). */
	public static function status( int $entry_id ): string {
		$status = get_post_meta( $entry_id, '_es_entry_status', true );
		return 'read' === $status ? 'read' : 'unread';
	}

	public static function mark_read( int $entry_id ): bool {
		return (bool) update_post_meta( $entry_id, '_es_entry_status', 'read' );
	}

	public static function mark_unread( int $entry_id ): bool {
		return (bool) update_post_meta( $entry_id, '_es_entry_status', 'unread' );
	}

	/**
	 * List entry ids for admins only (AC-03 / SC-02).
	 *
	 * @param array{type?:string,status?:string,per_page?:int} $args Filters.
	 * @return array<int,int>|\WP_Error
	 */
	public static function query( array $args = array() ) {
		if ( ! self::can_manage() ) {
			return new \WP_Error( 'es_form_forbidden', 'دسترسی مجاز نیست.' );
		}
		$query_args = array(
			'post_type'      => 'es_form_entry',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( 200, (int) ( $args['per_page'] ?? 50 ) ) ),
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$meta_query = array();
		if ( ! empty( $args['type'] ) ) {
			$meta_query[] = array( 'key' => '_es_entry_form', 'value' => (string) $args['type'] );
		}
		if ( ! empty( $args['status'] ) ) {
			$meta_query[] = array( 'key' => '_es_entry_status', 'value' => (string) $args['status'] );
		}
		if ( $meta_query ) {
			$query_args['meta_query'] = $meta_query;
		}
		$q = new \WP_Query( $query_args );
		return array_map( 'intval', $q->posts );
	}

	/** Permanently delete an entry (admin only). */
	public static function delete( int $entry_id ): bool {
		if ( ! self::can_manage() ) {
			return false;
		}
		return false !== wp_delete_post( $entry_id, true );
	}
}
