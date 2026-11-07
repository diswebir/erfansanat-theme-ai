<?php
/**
 * Admin inbox screens.
 *
 * The default list table for a custom post type shows a title and a date, which
 * for an enquiry hides the only two things that matter — who sent it and how to
 * reach them. This class replaces the columns, prints the message itself on the
 * edit screen, and marks it read on the first view.
 *
 * The message body is printed with `esc_html()` and `nl2br()`, never through
 * `wpautop()`/`the_content()`: it is untrusted visitor input, so it must never be
 * interpreted as markup.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * List and edit-screen presentation for stored messages.
 */
final class InboxAdmin {

	/**
	 * Attach the hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_filter( 'manage_' . Inbox::POST_TYPE . '_posts_columns', array( self::class, 'columns' ) );
		add_action( 'manage_' . Inbox::POST_TYPE . '_posts_custom_column', array( self::class, 'column' ), 10, 2 );
		add_action( 'add_meta_boxes', array( self::class, 'metabox' ) );
		add_filter( 'post_row_actions', array( self::class, 'row_actions' ), 10, 2 );
		add_action( 'admin_init', array( self::class, 'mark_read' ) );
	}

	/**
	 * Column set for the inbox list.
	 *
	 * @param array<string, string> $columns Existing columns.
	 * @return array<string, string>
	 */
	public static function columns( array $columns ): array {
		return array(
			'cb'         => $columns['cb'] ?? '<input type="checkbox" />',
			'es_sender'  => __( 'فرستنده', 'erfansanat' ),
			'es_contact' => __( 'تماس', 'erfansanat' ),
			'es_form'    => __( 'فرم', 'erfansanat' ),
			'es_excerpt' => __( 'متن پیام', 'erfansanat' ),
			'date'       => __( 'تاریخ', 'erfansanat' ),
		);
	}

	/**
	 * Print one cell.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Message id.
	 * @return void
	 */
	public static function column( string $column, int $post_id ): void {
		$name  = (string) get_post_meta( $post_id, '_es_name', true );
		$phone = (string) get_post_meta( $post_id, '_es_phone', true );
		$email = (string) get_post_meta( $post_id, '_es_email', true );
		$org   = (string) get_post_meta( $post_id, '_es_org', true );
		$body  = (string) get_post_meta( $post_id, '_es_body', true );

		switch ( $column ) {
			case 'es_sender':
				echo '<strong>' . esc_html( '' !== $name ? $name : __( 'بدون نام', 'erfansanat' ) ) . '</strong>';
				if ( '' !== $org ) {
					echo '<br /><span class="description">' . esc_html( $org ) . '</span>';
				}
				if ( '0' === (string) get_post_meta( $post_id, '_es_read', true ) ) {
					echo ' <span class="erfansanat-unread" style="display:inline-block;margin-inline-start:6px;padding:0 6px;border-radius:9999px;background:#047857;color:#fff;font-size:10px;line-height:18px;">' . esc_html__( 'جدید', 'erfansanat' ) . '</span>';
				}
				break;

			case 'es_contact':
				if ( '' !== $phone ) {
					echo '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '" dir="ltr">' . esc_html( $phone ) . '</a><br />';
				}
				if ( '' !== $email ) {
					echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
				}
				if ( '' === $phone && '' === $email ) {
					echo '&mdash;';
				}
				break;

			case 'es_form':
				$form   = (string) get_post_meta( $post_id, '_es_form', true );
				$labels = array(
					'contact'  => __( 'تماس', 'erfansanat' ),
					'quote'    => __( 'استعلام قیمت', 'erfansanat' ),
					'callback' => __( 'درخواست تماس', 'erfansanat' ),
				);
				echo esc_html( $labels[ $form ] ?? $form );
				break;

			case 'es_excerpt':
				echo esc_html( wp_trim_words( $body, 14, '…' ) );
				break;
		}
	}

	/**
	 * Replace the editor with a read-only message card.
	 *
	 * @return void
	 */
	public static function metabox(): void {
		remove_post_type_support( Inbox::POST_TYPE, 'editor' );

		add_meta_box(
			'erfansanat-message',
			__( 'متن پیام', 'erfansanat' ),
			array( self::class, 'render' ),
			Inbox::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Print the message.
	 *
	 * @param \WP_Post $post Message.
	 * @return void
	 */
	public static function render( \WP_Post $post ): void {
		$rows = array(
			__( 'نام', 'erfansanat' )    => (string) get_post_meta( $post->ID, '_es_name', true ),
			__( 'تلفن', 'erfansanat' )   => (string) get_post_meta( $post->ID, '_es_phone', true ),
			__( 'ایمیل', 'erfansanat' )  => (string) get_post_meta( $post->ID, '_es_email', true ),
			__( 'سازمان', 'erfansanat' ) => (string) get_post_meta( $post->ID, '_es_org', true ),
			__( 'فرم', 'erfansanat' )    => (string) get_post_meta( $post->ID, '_es_form', true ),
		);

		echo '<table class="widefat striped" style="margin-bottom:16px;">';
		foreach ( $rows as $label => $value ) {
			if ( '' === $value ) {
				continue;
			}
			echo '<tr><th style="width:120px;">' . esc_html( (string) $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
		}
		echo '</table>';

		echo '<div style="white-space:pre-wrap;font-size:14px;line-height:2;">'
			. esc_html( (string) get_post_meta( $post->ID, '_es_body', true ) )
			. '</div>';
	}

	/**
	 * Mark a message read the first time its edit screen opens.
	 *
	 * @return void
	 */
	public static function mark_read(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen identifier.
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
		if ( $post_id <= 0 || Inbox::POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_es_read', '1' );
	}

	/**
	 * Drop the "View" link: a message has no public page.
	 *
	 * @param array<string, string> $actions Row actions.
	 * @param \WP_Post              $post    Row post.
	 * @return array<string, string>
	 */
	public static function row_actions( array $actions, $post ): array {
		if ( $post instanceof \WP_Post && Inbox::POST_TYPE === $post->post_type ) {
			unset( $actions['view'], $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}
}
