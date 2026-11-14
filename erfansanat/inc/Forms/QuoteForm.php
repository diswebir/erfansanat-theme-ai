<?php
/**
 * Quote form handler (P08-T014).
 *
 * Inquiry-mode quote popup: resolves an optional product/project id to a real
 * published post (invalid ids are rejected), sanitizes the reference fields
 * and stores an unread es_form_entry of type quote linked to the content.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Core\Ajax;

defined( 'ABSPATH' ) || exit;

final class QuoteForm {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		if ( did_action( 'init' ) ) {
			self::register_endpoint();
		} else {
			add_action( 'init', array( __CLASS__, 'register_endpoint' ), 20 );
		}
	}

	public static function register_endpoint(): void {
		Ajax::register(
			'quote_submit',
			array( __CLASS__, 'ajax_submit' ),
			array(
				'public' => true,
				'inputs' => array(
					'es_qf_name'       => array( 'type' => 'text', 'default' => '' ),
					'es_qf_phone'      => array( 'type' => 'text', 'default' => '' ),
					'es_qf_product'    => array( 'type' => 'text', 'default' => '' ),
					'es_qf_quantity'   => array( 'type' => 'text', 'default' => '' ),
					'es_qf_message'    => array( 'type' => 'text', 'default' => '' ),
					'es_qf_product_id' => array( 'type' => 'text', 'default' => '' ),
					'es_qf_project_id' => array( 'type' => 'text', 'default' => '' ),
					'es_qf_hp'         => array( 'type' => 'text', 'default' => '' ),
					'page'             => array( 'type' => 'text', 'default' => '' ),
				),
			)
		);
	}

	/**
	 * Endpoint callback → submit().
	 *
	 * @param array<string,mixed> $input Whitelisted input.
	 * @return array<string,mixed>
	 */
	public static function ajax_submit( array $input ): array {
		return self::submit( $input, array( 'page' => $input['page'] ?? '' ) );
	}

	/**
	 * Resolve an optional content id to a published post permalink.
	 *
	 * @param string $raw       Raw id (empty = no content).
	 * @param string $post_type Expected post type.
	 * @return array{url:string,id:int}|WP_Error
	 */
	private static function resolve_content( string $raw, string $post_type ) {
		$raw = trim( $raw );
		if ( '' === $raw ) {
			return array( 'url' => '', 'id' => 0 );
		}
		if ( ! preg_match( '/^\d{1,20}$/', $raw ) ) {
			return new \WP_Error( 'es_quote_bad_id', 'شناسه محتوا معتبر نیست.' );
		}
		$id    = (int) $raw;
		$title = get_post_status( $id );
		$type  = get_post_type( $id );
		if ( false === $title || 'publish' !== $title || $type !== $post_type ) {
			return new \WP_Error( 'es_quote_bad_id', 'محصول یا پروژه مورد نظر در دسترس نیست.' );
		}
		$url = get_permalink( $id );
		if ( ! is_string( $url ) || '' === $url ) {
			return new \WP_Error( 'es_quote_bad_id', 'لینک محتوا در دسترس نیست.' );
		}
		return array( 'url' => $url, 'id' => $id );
	}

	/**
	 * Full submission pipeline: honeypot → content id resolution → schema
	 * validation (AC-03 sanitizes quantity/product text) → store (SC-01).
	 *
	 * @param array<string,mixed> $payload Raw payload.
	 * @param array<string,mixed> $context  Optional context.
	 * @return array{entry:int,message:string,content_url:string}|WP_Error
	 */
	public static function submit( array $payload, array $context = array() ) {
		// P08-T018: shared anti-spam — per-IP rate limit then the honeypot.
		$es_rate = SpamGuard::enforce_rate( 'quote' );
		if ( is_wp_error( $es_rate ) ) {
			return $es_rate;
		}
		if ( ! SpamGuard::honeypot_ok( $payload['es_qf_hp'] ?? '' ) ) {
			return new \WP_Error( 'es_form_honeypot', 'ارسال درخواست ممکن نیست. لطفاً صفحه را دوباره بارگذاری کنید.' );
		}
		$content = self::resolve_content( (string) ( $payload['es_qf_product_id'] ?? '' ), 'product' );
		if ( is_wp_error( $content ) ) {
			return $content;
		}
		$content_project = self::resolve_content( (string) ( $payload['es_qf_project_id'] ?? '' ), 'projects' );
		if ( is_wp_error( $content_project ) ) {
			return $content_project;
		}
		if ( ! $content['id'] && $content_project['id'] ) {
			$content = $content_project;
		}
		$data = Schema::validate(
			'quote',
			array(
				'name'     => $payload['es_qf_name'] ?? '',
				'phone'    => $payload['es_qf_phone'] ?? '',
				'product'  => $payload['es_qf_product'] ?? '',
				'quantity' => $payload['es_qf_quantity'] ?? '',
				'message'  => $payload['es_qf_message'] ?? '',
			)
		);
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$entry_id = Entries::store( 'quote', $data, $context );
		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}
		SpamGuard::hit( 'quote' );
		// SC-03: notify after storing — an email failure keeps the entry.
		Mailer::send_entry( 'quote', (int) $entry_id, $data );
		$content_url = (string) ( $content['url'] ?? '' );
		if ( '' !== $content_url ) {
			update_post_meta( (int) $entry_id, '_es_entry_meta', array_merge(
				(array) get_post_meta( (int) $entry_id, '_es_entry_meta', true ),
				array( 'content_url' => esc_url_raw( $content_url ), 'content_id' => (int) $content['id'] )
			) );
		}
		return array(
			'entry'       => (int) $entry_id,
			'message'     => 'استعلام شما ثبت شد؛ کارشناسان فروش با شما تماس می‌گیرند.',
			'content_url' => $content_url,
		);
	}
}
