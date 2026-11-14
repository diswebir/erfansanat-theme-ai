<?php
/**
 * Professional dashboard overview ("پیشخوان") panel.
 *
 * Renders a welcome hero, live content stat cards, quick actions and a system
 * status readout as the first tab of the theme dashboard. All counters are
 * localized to Persian digits and every value is escaped. The panel is
 * read-only — it never writes options — so it is safe for any manage_options
 * user and adds no AJAX surface.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

final class DashboardOverview {

	/** Content types surfaced as stat cards (post_type => dashicon). */
	private const CONTENT_TYPES = array(
		'product'  => 'dashicons-products',
		'projects' => 'dashicons-portfolio',
		'post'     => 'dashicons-admin-post',
		'page'     => 'dashicons-admin-page',
		'services' => 'dashicons-hammer',
	);

	/**
	 * Published (and pending) counts for the stat cards.
	 *
	 * @return array<string,array{label:string,icon:string,publish:int,pending:int,total:int,edit_link:string,add_link:string}>
	 */
	public static function stats(): array {
		$out = array();

		foreach ( self::CONTENT_TYPES as $type => $icon ) {
			if ( ! post_type_exists( $type ) ) {
				continue;
			}

			$object  = get_post_type_object( $type );
			$counts  = wp_count_posts( $type );
			$publish = isset( $counts->publish ) ? (int) $counts->publish : 0;
			$pending = isset( $counts->pending ) ? (int) $counts->pending : 0;

			$out[ $type ] = array(
				'label'     => $object ? $object->labels->name : $type,
				'icon'      => $icon,
				'publish'   => $publish,
				'pending'   => $pending,
				'total'     => $publish + $pending,
				'edit_link' => admin_url( 'edit.php?post_type=' . $type ),
				'add_link'  => admin_url( 'post-new.php?post_type=' . $type ),
			);
		}

		return $out;
	}

	/**
	 * Comment counts (approved / awaiting moderation).
	 *
	 * @return array{approved:int,moderated:int}
	 */
	public static function comment_stats(): array {
		$counts = wp_count_comments();

		return array(
			'approved'  => isset( $counts->approved ) ? (int) $counts->approved : 0,
			'moderated' => isset( $counts->moderated ) ? (int) $counts->moderated : 0,
		);
	}

	/**
	 * System / environment readout for the status card.
	 *
	 * @return array<int,array{label:string,value:string,status:string}>
	 */
	public static function system_info(): array {
		$theme = wp_get_theme();
		$woo   = class_exists( 'WooCommerce' ) || defined( 'WC_VERSION' );

		return array(
			array(
				'label'  => __( 'نسخه قالب', 'erfansanat' ),
				'value'  => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ),
				'status' => 'ok',
			),
			array(
				'label'  => __( 'نسخه وردپرس', 'erfansanat' ),
				'value'  => get_bloginfo( 'version' ),
				'status' => 'ok',
			),
			array(
				'label'  => __( 'نسخه PHP', 'erfansanat' ),
				'value'  => PHP_VERSION,
				'status' => version_compare( PHP_VERSION, '7.4', '>=' ) ? 'ok' : 'warn',
			),
			array(
				'label'  => __( 'ووکامرس', 'erfansanat' ),
				'value'  => $woo ? __( 'فعال', 'erfansanat' ) : __( 'غیرفعال', 'erfansanat' ),
				'status' => $woo ? 'ok' : 'warn',
			),
			array(
				'label'  => __( 'حافظه وردپرس', 'erfansanat' ),
				'value'  => WP_MEMORY_LIMIT,
				'status' => 'ok',
			),
			array(
				'label'  => __( 'زبان سایت', 'erfansanat' ),
				'value'  => get_bloginfo( 'language' ),
				'status' => 'ok',
			),
		);
	}

	/**
	 * Convert Latin digits to Persian digits for display.
	 */
	public static function fa_digits( $value ): string {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) {
			$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
		}

		return strtr( (string) $value, $map );
	}

	/**
	 * Render the overview panel (welcome + stats + quick actions + status).
	 */
	public static function render(): void {
		$stats    = self::stats();
		$comments = self::comment_stats();
		$system   = self::system_info();
		?>
		<div class="es-overview" data-es-overview="true">
			<div class="es-overview__hero">
				<div class="es-overview__hero-text">
					<h2 class="es-overview__welcome"><?php echo esc_html__( 'به داشبورد عرفان صنعت خوش آمدید', 'erfansanat' ); ?></h2>
					<p class="es-overview__lead"><?php echo esc_html__( 'از این پیشخوان می‌توانید محتوای سایت را رصد کنید، به‌سرعت به بخش‌های پرکاربرد بروید و تنظیمات قالب را مدیریت کنید.', 'erfansanat' ); ?></p>
				</div>
				<span class="es-overview__version"><?php echo esc_html( self::fa_digits( wp_get_theme()->get( 'Version' ) ) ); ?></span>
			</div>

			<h3 class="es-overview__section-title"><?php echo esc_html__( 'نمای کلی محتوا', 'erfansanat' ); ?></h3>
			<div class="es-overview__stats">
				<?php foreach ( $stats as $type => $stat ) : ?>
					<a class="es-stat-card" href="<?php echo esc_url( $stat['edit_link'] ); ?>">
						<span class="es-stat-card__icon dashicons <?php echo esc_attr( $stat['icon'] ); ?>" aria-hidden="true"></span>
						<span class="es-stat-card__body">
							<span class="es-stat-card__value"><?php echo esc_html( self::fa_digits( $stat['publish'] ) ); ?></span>
							<span class="es-stat-card__label"><?php echo esc_html( $stat['label'] ); ?></span>
							<?php if ( $stat['pending'] > 0 ) : ?>
								<span class="es-stat-card__pending"><?php echo esc_html( self::fa_digits( $stat['pending'] ) ); ?> <?php echo esc_html__( 'در انتظار بررسی', 'erfansanat' ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				<?php endforeach; ?>

				<a class="es-stat-card" href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>">
					<span class="es-stat-card__icon dashicons dashicons-admin-comments" aria-hidden="true"></span>
					<span class="es-stat-card__body">
						<span class="es-stat-card__value"><?php echo esc_html( self::fa_digits( $comments['approved'] ) ); ?></span>
						<span class="es-stat-card__label"><?php echo esc_html__( 'دیدگاه‌ها', 'erfansanat' ); ?></span>
						<?php if ( $comments['moderated'] > 0 ) : ?>
							<span class="es-stat-card__pending"><?php echo esc_html( self::fa_digits( $comments['moderated'] ) ); ?> <?php echo esc_html__( 'در انتظار تأیید', 'erfansanat' ); ?></span>
						<?php endif; ?>
					</span>
				</a>
			</div>

			<h3 class="es-overview__section-title"><?php echo esc_html__( 'دسترسی سریع', 'erfansanat' ); ?></h3>
			<div class="es-overview__actions">
				<?php
				$actions = array(
					array( 'label' => __( 'افزودن محصول', 'erfansanat' ), 'icon' => 'dashicons-plus-alt', 'url' => admin_url( 'post-new.php?post_type=product' ) ),
					array( 'label' => __( 'افزودن پروژه', 'erfansanat' ), 'icon' => 'dashicons-plus-alt', 'url' => admin_url( 'post-new.php?post_type=projects' ) ),
					array( 'label' => __( 'افزودن نوشته', 'erfansanat' ), 'icon' => 'dashicons-plus-alt', 'url' => admin_url( 'post-new.php' ) ),
					array( 'label' => __( 'رسانه‌ها', 'erfansanat' ), 'icon' => 'dashicons-admin-media', 'url' => admin_url( 'upload.php' ) ),
					array( 'label' => __( 'سفارشی‌سازی', 'erfansanat' ), 'icon' => 'dashicons-admin-customizer', 'url' => admin_url( 'customize.php' ) ),
					array( 'label' => __( 'فهرست‌ها', 'erfansanat' ), 'icon' => 'dashicons-menu', 'url' => admin_url( 'nav-menus.php' ) ),
				);
				foreach ( $actions as $action ) :
					?>
					<a class="es-quick-action" href="<?php echo esc_url( $action['url'] ); ?>">
						<span class="dashicons <?php echo esc_attr( $action['icon'] ); ?>" aria-hidden="true"></span>
						<?php echo esc_html( $action['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>

			<h3 class="es-overview__section-title"><?php echo esc_html__( 'وضعیت سیستم', 'erfansanat' ); ?></h3>
			<div class="es-overview__status">
				<table class="es-status-table">
					<tbody>
						<?php foreach ( $system as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
								<td>
									<span class="es-status-dot es-status-dot--<?php echo esc_attr( $row['status'] ); ?>" aria-hidden="true"></span>
									<?php echo esc_html( $row['value'] ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
}
