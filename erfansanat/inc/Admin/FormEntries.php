<?php
/**
 * Admin UI for form entries (P08-T017).
 *
 * Submenu under the ErfanSanat dashboard: filtered list (form type / status /
 * date range), escaped detail rendering and nonce-gated read/unread/approve/
 * delete actions. Every path is capability-gated to manage_options.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Forms\Entries;
use ErfanSanat\Theme\Forms\FeedbackForm;
use ErfanSanat\Theme\Forms\Schema;

defined( 'ABSPATH' ) || exit;

final class FormEntries {

	private static bool $booted = false;

	public const SLUG        = 'erfansanat-form-entries';
	public const CAP         = 'manage_options';
	public const NONCE       = 'es_entries_nonce';
	public const POST_ACTION = 'es_entries_action';

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_post_' . self::POST_ACTION, array( __CLASS__, 'handle_action' ) );
	}

	public static function register_menu(): void {
		add_submenu_page(
			ThemeOptions::SLUG,
			__( 'ورودی فرم‌ها', 'erfansanat' ),
			__( 'ورودی فرم‌ها', 'erfansanat' ),
			self::CAP,
			self::SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	public static function page_url( array $filters = array() ): string {
		$url = admin_url( 'admin.php?page=' . self::SLUG );
		if ( $filters ) {
			$url = add_query_arg( array_map( 'strval', $filters ), $url );
		}
		return $url;
	}

	/** Whether the current user may manage entries (AC-03 / SC-02). */
	public static function can_manage(): bool {
		return is_user_logged_in() && current_user_can( self::CAP );
	}

	/**
	 * Filtered entry ids (AC-01). Dates are plain post_date comparisons so no
	 * Entries.php change is needed (T012 file is not a T017 target).
	 *
	 * @param array{type?:string,status?:string,date_from?:string,date_to?:string} $filters Filters.
	 * @return array<int,int>|WP_Error
	 */
	public static function get_entries( array $filters = array() ) {
		if ( ! self::can_manage() ) {
			return new \WP_Error( 'es_entries_forbidden', 'دسترسی مجاز نیست.' );
		}
		$raw = Entries::query( array(
			'type'     => isset( $filters['type'] ) ? (string) $filters['type'] : '',
			'status'   => isset( $filters['status'] ) && in_array( $filters['status'], array( 'read', 'unread' ), true ) ? (string) $filters['status'] : '',
			'per_page' => 200,
		) );
		if ( is_wp_error( $raw ) ) {
			return $raw;
		}
		$from = isset( $filters['date_from'] ) ? (string) $filters['date_from'] : '';
		$to   = isset( $filters['date_to'] ) ? (string) $filters['date_to'] : '';
		if ( '' === $from && '' === $to ) {
			return $raw;
		}
		$from_ts = '' !== $from ? strtotime( $from . ' 00:00:00' ) : 0;
		$to_ts   = '' !== $to ? strtotime( $to . ' 23:59:59' ) : 0;
		return array_values( array_filter( $raw, static function ( $id ) use ( $from_ts, $to_ts ) {
			$time = (int) get_post_timestamp( $id );
			if ( $from_ts && $time < $from_ts ) {
				return false;
			}
			if ( $to_ts && $time > $to_ts ) {
				return false;
			}
			return true;
		} ) );
	}

	/**
	 * Nonce + capability-gated entry action (AC-03 / SC-01 / SC-02).
	 *
	 * @param string $do       read|unread|approve|delete.
	 * @param int    $entry_id Entry id.
	 * @param string $nonce    Nonce token.
	 * @return true|WP_Error
	 */
	public static function perform_action( string $do, int $entry_id, string $nonce ) {
		if ( ! self::can_manage() ) {
			return new \WP_Error( 'es_entries_forbidden', 'دسترسی مجاز نیست.' );
		}
		if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return new \WP_Error( 'es_entries_bad_nonce', 'توکن امنیتی نامعتبر است.' );
		}
		$entry = get_post( $entry_id );
		if ( null === $entry || 'es_form_entry' !== $entry->post_type ) {
			return new \WP_Error( 'es_entries_bad_entry', 'ورودی یافت نشد.' );
		}
		switch ( $do ) {
			case 'read':
				Entries::mark_read( $entry_id );
				break;
			case 'unread':
				Entries::mark_unread( $entry_id );
				break;
			case 'approve':
				FeedbackForm::approve( $entry_id );
				break;
			case 'delete':
				if ( ! Entries::delete( $entry_id ) ) {
					return new \WP_Error( 'es_entries_delete', 'حذف ورودی ناموفق بود.' );
				}
				break;
			default:
				return new \WP_Error( 'es_entries_bad_action', 'اکشن شناخته‌شده نیست.' );
		}
		return true;
	}

	/** admin-post wrapper: perform + safe redirect. */
	public static function handle_action(): void {
		$do       = isset( $_POST['do'] ) ? sanitize_key( (string) $_POST['do'] ) : '';
		$entry_id = isset( $_POST['entry_id'] ) ? absint( (string) $_POST['entry_id'] ) : 0;
		$nonce    = isset( $_POST['_wpnonce'] ) ? (string) $_POST['_wpnonce'] : '';
		$result   = self::perform_action( $do, $entry_id, $nonce );
		$notice   = is_wp_error( $result ) ? 'error' : 'done';
		wp_safe_redirect( add_query_arg( 'es_entries_notice', $notice, self::page_url() ) );
		exit;
	}

	private static function render_filters(): void {
		$type   = isset( $_GET['type'] ) ? sanitize_key( (string) $_GET['type'] ) : '';
		$status = isset( $_GET['status'] ) ? sanitize_key( (string) $_GET['status'] ) : '';
		$from   = isset( $_GET['date_from'] ) ? (string) $_GET['date_from'] : '';
		$to     = isset( $_GET['date_to'] ) ? (string) $_GET['date_to'] : '';
		?>
		<form method="get" class="es-entries-filters">
			<input type="hidden" name="page" value="<?php echo esc_attr( self::SLUG ); ?>" />
			<select name="type">
				<option value=""><?php esc_html_e( 'همه انواع فرم', 'erfansanat' ); ?></option>
				<?php foreach ( Schema::types() as $es_key => $es_label ) : ?>
					<option value="<?php echo esc_attr( $es_key ); ?>" <?php selected( $type, $es_key ); ?>><?php echo esc_html( $es_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<select name="status">
				<option value=""><?php esc_html_e( 'همه وضعیت‌ها', 'erfansanat' ); ?></option>
				<option value="unread" <?php selected( $status, 'unread' ); ?>><?php esc_html_e( 'ناخوانده', 'erfansanat' ); ?></option>
				<option value="read" <?php selected( $status, 'read' ); ?>><?php esc_html_e( 'خوانده‌شده', 'erfansanat' ); ?></option>
			</select>
			<label><?php esc_html_e( 'از تاریخ', 'erfansanat' ); ?> <input type="date" name="date_from" value="<?php echo esc_attr( $from ); ?>" /></label>
			<label><?php esc_html_e( 'تا تاریخ', 'erfansanat' ); ?> <input type="date" name="date_to" value="<?php echo esc_attr( $to ); ?>" /></label>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'اعمال فیلتر', 'erfansanat' ); ?></button>
		</form>
		<?php
	}

	private static function render_row_actions( int $entry_id ): void {
		$nonce = wp_create_nonce( self::NONCE );
		$post  = admin_url( 'admin-post.php' );
		$btn   = static function ( string $do, string $label ) use ( $entry_id, $nonce, $post ) {
			return '<form method="post" action="' . esc_url( $post ) . '" style="display:inline">'
				. '<input type="hidden" name="action" value="' . esc_attr( self::POST_ACTION ) . '" />'
				. '<input type="hidden" name="_wpnonce" value="' . esc_attr( $nonce ) . '" />'
				. '<input type="hidden" name="entry_id" value="' . esc_attr( (string) $entry_id ) . '" />'
				. '<input type="hidden" name="do" value="' . esc_attr( $do ) . '" />'
				. '<button type="submit" class="button button-small">' . esc_html( $label ) . '</button></form>';
		};
		echo $btn( 'read', __( 'خوانده شد', 'erfansanat' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $btn escapes everything.
		echo $btn( 'unread', __( 'ناخوانده', 'erfansanat' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $btn( 'approve', __( 'تأیید بازخورد', 'erfansanat' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $btn( 'delete', __( 'حذف', 'erfansanat' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render the admin page (AC-02: everything escaped).
	 *
	 * @param array<string,string>|null $filters Optional filter override (tests).
	 */
	public static function render_page( ?array $filters = null ): void {
		if ( ! self::can_manage() ) {
			wp_die( esc_html__( 'دسترسی مجاز نیست.', 'erfansanat' ), 403 );
		}
		$f = null === $filters ? array(
			'type'      => isset( $_GET['type'] ) ? sanitize_key( (string) $_GET['type'] ) : '',
			'status'    => isset( $_GET['status'] ) ? sanitize_key( (string) $_GET['status'] ) : '',
			'date_from' => isset( $_GET['date_from'] ) ? (string) $_GET['date_from'] : '',
			'date_to'   => isset( $_GET['date_to'] ) ? (string) $_GET['date_to'] : '',
		) : $filters;
		$entries = self::get_entries( $f );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ورودی فرم‌های عرفان صنعت', 'erfansanat' ); ?></h1>
			<?php self::render_filters(); ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'تاریخ', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'نوع فرم', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'نام', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'تماس', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'پیام', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'وضعیت', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'عملیات', 'erfansanat' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( is_wp_error( $entries ) || ! $entries ) : ?>
					<tr><td colspan="7"><?php esc_html_e( 'ورودی‌ای یافت نشد.', 'erfansanat' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $entries as $entry_id ) : ?>
						<?php
						$data  = (array) get_post_meta( $entry_id, '_es_entry_data', true );
						$type  = (string) get_post_meta( $entry_id, '_es_entry_form', true );
						$types = Schema::types();
						?>
						<tr>
							<td><?php echo esc_html( get_date_from_gmt( (string) get_post_field( 'post_date_gmt', $entry_id ), 'Y-m-d H:i' ) ); ?></td>
							<td><?php echo esc_html( $types[ $type ] ?? $type ); ?></td>
							<td><?php echo esc_html( (string) ( $data['name'] ?? '' ) ); ?></td>
							<td dir="ltr"><?php echo esc_html( (string) ( $data['phone'] ?? '' ) ); ?></td>
							<td><?php echo esc_html( wp_html_excerpt( (string) ( $data['message'] ?? ( $data['background'] ?? '' ) ), 120, '…' ) ); ?></td>
							<td>
								<?php if ( 'quote' === $type ) : ?>
									<span><?php esc_html_e( 'استعلام', 'erfansanat' ); ?></span>
								<?php elseif ( 'feedback' === $type ) : ?>
									<span><?php echo esc_html( 'approve' === FeedbackForm::moderation( $entry_id ) ? __( 'تأییدشده', 'erfansanat' ) : __( 'در انتظار بررسی', 'erfansanat' ) ); ?></span>
								<?php else : ?>
									<span><?php echo esc_html( 'read' === Entries::status( $entry_id ) ? __( 'خوانده‌شده', 'erfansanat' ) : __( 'ناخوانده', 'erfansanat' ) ); ?></span>
								<?php endif; ?>
							</td>
							<td><?php self::render_row_actions( $entry_id ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
