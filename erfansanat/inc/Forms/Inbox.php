<?php
/**
 * Message inbox storage.
 *
 * Submitted messages are stored as a private custom post type rather than
 * e-mailed straight out. Three reasons:
 *
 * 1. E-mail delivery fails silently on most shared hosts. A message that exists
 *    only in an e-mail that never arrived cannot be recovered; one in the
 *    database can.
 * 2. A stored message lets the site owner see what came in without trusting a
 *    mailbox.
 * 3. Notification e-mail can then be best-effort — send it, log a failure, but
 *    never lose the message because of it.
 *
 * The post type is `private`: no archive, no public query var, not searchable,
 * and every field is registered with `show_in_rest => false` so the content can
 * never be read through the REST API by an anonymous visitor.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * The `erfansanat_message` store.
 */
final class Inbox {

	/**
	 * Post type key.
	 */
	public const POST_TYPE = 'erfansanat_message';

	/**
	 * How long a message is kept, in days. `0` means forever.
	 *
	 * The brief has not decided a retention window, so the default is to keep
	 * messages: deleting a customer's enquiry after a fixed period without an
	 * owner decision would lose real leads. The value is filterable so the
	 * decision can be made without touching this file.
	 */
	public const DEFAULT_RETENTION_DAYS = 0;

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public static function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'               => __( 'پیام‌ها', 'erfansanat' ),
					'singular_name'      => __( 'پیام', 'erfansanat' ),
					'menu_name'          => __( 'صندوق پیام‌ها', 'erfansanat' ),
					'all_items'          => __( 'همهٔ پیام‌ها', 'erfansanat' ),
					'edit_item'          => __( 'مشاهدهٔ پیام', 'erfansanat' ),
					'view_item'          => __( 'مشاهدهٔ پیام', 'erfansanat' ),
					'search_items'       => __( 'جستجوی پیام‌ها', 'erfansanat' ),
					'not_found'          => __( 'پیامی یافت نشد.', 'erfansanat' ),
					'not_found_in_trash' => __( 'پیامی در زباله‌دان نیست.', 'erfansanat' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'capability_type'     => 'page',
				'map_meta_cap'        => true,
				'menu_icon'           => 'dashicons-email-alt',
				'menu_position'       => 26,
				'hierarchical'        => false,
				'supports'            => array( 'title' ),
				'delete_with_user'    => false,
			)
		);
	}

	/**
	 * Store one validated submission.
	 *
	 * @param array<string, mixed> $payload Validated form data.
	 * @param string               $form    Which form it came from.
	 * @return int Message id, or `0` on failure.
	 */
	public static function store( array $payload, string $form ): int {
		$name    = self::text( $payload, 'name' );
		$phone   = self::text( $payload, 'phone' );
		$subject = self::text( $payload, 'subject' );

		$title = $subject;
		if ( '' === $title ) {
			$title = $name;
		}
		if ( '' === $title ) {
			$title = __( 'پیام بدون عنوان', 'erfansanat' );
		}

		$id = wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_status'  => 'private',
				'post_title'   => wp_strip_all_tags( $title ),
				'post_content' => '',
				'meta_input'   => array(
					'_es_form'    => sanitize_key( $form ),
					'_es_name'    => $name,
					'_es_phone'   => $phone,
					'_es_email'   => self::text( $payload, 'email' ),
					'_es_org'     => self::text( $payload, 'org' ),
					'_es_body'    => self::text( $payload, 'message' ),
					'_es_ip_hash' => self::ip_hash(),
					'_es_ua'      => self::user_agent(),
					'_es_read'    => '0',
				),
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		return (int) $id;
	}

	/**
	 * Notify the site's contact address, best-effort.
	 *
	 * A failed send is logged but never fails the submission: the message is
	 * already stored, and telling a visitor "sorry, try again" when their message
	 * was actually saved would make them send it twice.
	 *
	 * @param int                  $id      Message id.
	 * @param array<string, mixed> $payload Validated form data.
	 * @return bool Whether the mail was accepted for delivery.
	 */
	public static function notify( int $id, array $payload ): bool {
		$to = self::recipient();

		$lines = array(
			sprintf(
				/* translators: %s: form name. */
				__( 'فرم: %s', 'erfansanat' ),
				(string) $payload['_form']
			),
			__( 'نام: ', 'erfansanat' ) . self::text( $payload, 'name' ),
			__( 'تلفن: ', 'erfansanat' ) . self::text( $payload, 'phone' ),
			__( 'ایمیل: ', 'erfansanat' ) . self::text( $payload, 'email' ),
			__( 'سازمان: ', 'erfansanat' ) . self::text( $payload, 'org' ),
			'',
			self::text( $payload, 'message' ),
			'',
			/* translators: %s: admin link. */
			sprintf( __( 'مشاهده در پیشخوان: %s', 'erfansanat' ), admin_url( 'post.php?post=' . $id . '&action=edit' ) ),
		);

		$sent = wp_mail(
			$to,
			sprintf(
				/* translators: %s: message title. */
				__( 'پیام جدید از سایت: %s', 'erfansanat' ),
				wp_strip_all_tags( (string) get_the_title( $id ) )
			),
			implode( "\n", $lines )
		);

		update_post_meta( $id, '_es_mailed', $sent ? '1' : '0' );

		return (bool) $sent;
	}

	/**
	 * Where notifications go.
	 *
	 * The brief's gate asks for a real destination before launch. Until one is
	 * configured the admin e-mail is the honest default: it is a mailbox the site
	 * owner demonstrably controls.
	 *
	 * @return string
	 */
	public static function recipient(): string {
		$email = (string) apply_filters( 'erfansanat_form_recipient', (string) get_option( 'admin_email', '' ) );
		return (string) sanitize_email( $email );
	}

	/**
	 * Delete messages older than the retention window.
	 *
	 * Hooked to a daily cron event. With the default of `0` it is a no-op, so the
	 * site does not silently lose data before an owner decides a policy.
	 *
	 * @return int Number of messages deleted.
	 */
	public static function purge(): int {
		$days = (int) apply_filters( 'erfansanat_message_retention_days', self::DEFAULT_RETENTION_DAYS );
		if ( $days <= 0 ) {
			return 0;
		}

		$old = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 100,
				'fields'         => 'ids',
				'date_query'     => array(
					array(
						'before' => $days . ' days ago',
					),
				),
			)
		);

		$deleted = 0;
		foreach ( (array) $old as $id ) {
			if ( wp_delete_post( (int) $id, true ) ) {
				++$deleted;
			}
		}

		return $deleted;
	}

	/**
	 * Schedule the retention sweep.
	 *
	 * @return void
	 */
	public static function schedule(): void {
		if ( ! wp_next_scheduled( 'erfansanat_message_purge' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'erfansanat_message_purge' );
		}
	}

	/**
	 * A readable value from the payload.
	 *
	 * @param array<string, mixed> $payload Form data.
	 * @param string               $key     Field name.
	 * @return string
	 */
	private static function text( array $payload, string $key ): string {
		if ( ! isset( $payload[ $key ] ) ) {
			return '';
		}

		// Defence in depth. The handler sanitises before it calls this, but a
		// store that trusts its caller is one refactor away from writing markup
		// into the inbox; strip it again here so no path can.
		$value = 'message' === $key
			? sanitize_textarea_field( (string) $payload[ $key ] )
			: sanitize_text_field( (string) $payload[ $key ] );

		return trim( (string) $value );
	}

	/**
	 * A salted hash of the sender's address.
	 *
	 * The raw IP is never stored: it is personal data with no ongoing use, and a
	 * hash is enough to spot a flood from one source.
	 *
	 * @return string
	 */
	private static function ip_hash(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['REMOTE_ADDR'] ) ) : '';
		if ( '' === $ip ) {
			return '';
		}
		return substr( wp_hash( $ip ), 0, 24 );
	}

	/**
	 * A trimmed user agent for debugging a bad submission.
	 *
	 * @return string
	 */
	private static function user_agent(): string {
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		return mb_substr( $ua, 0, 180 );
	}
}
