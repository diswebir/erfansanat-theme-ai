<?php
/**
 * Bespoke RTL settings panel and its AJAX save endpoint.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Erfan Sanat" admin page and saves the option over AJAX.
 *
 * The screen is a single RTL page with a tab rail. Every request is guarded by
 * `manage_options` and a nonce; the save endpoint re-checks both, then hands the
 * payload to Settings::sanitize() before writing the single option row.
 */
final class ThemeOptions {

	/**
	 * Admin page slug.
	 */
	public const SLUG = 'erfansanat-options';

	/**
	 * AJAX action name.
	 */
	public const AJAX_ACTION = 'erfansanat_save_options';

	/**
	 * Nonce action name.
	 */
	public const NONCE_ACTION = 'erfansanat_options_nonce';

	/**
	 * Capability required for the screen and the endpoint.
	 */
	public const CAPABILITY = 'manage_options';

	/**
	 * Optional JSON sender used by the acceptance suite.
	 *
	 * When null, the standard wp_send_json_* helpers end the request as usual.
	 *
	 * @var callable|null
	 */
	private static $sender = null;

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	/**
	 * Replace the JSON sender (tests only).
	 *
	 * @param callable|null $sender Receives (array $payload, int $status, bool $success).
	 * @return void
	 */
	public static function set_sender( ?callable $sender ): void {
		self::$sender = $sender;
	}

	/**
	 * Send a JSON success or error envelope.
	 *
	 * @param array<string, mixed> $payload Payload for the `data` member.
	 * @param int                  $status  HTTP status for the error case.
	 * @param bool                 $success Whether the request succeeded.
	 * @return void
	 */
	private static function respond( array $payload, int $status = 200, bool $success = true ): void {
		if ( null !== self::$sender ) {
			call_user_func( self::$sender, $payload, $status, $success );
			return;
		}
		if ( $success ) {
			wp_send_json_success( $payload, $status );
		}
		wp_send_json_error( $payload, $status );
	}

	/**
	 * Register the screen, assets and AJAX endpoint.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'admin_menu', array( self::class, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_admin_font' ), 1 );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_meta_boxes' ), 20 );
		// `wp-login.php` is not an admin screen, so the class is registered from
		// `Theme::register()` for it separately; this hook only fires when it is.
		add_action( 'login_enqueue_scripts', array( self::class, 'enqueue_login_font' ) );
	}

	/**
	 * Register the AJAX endpoint.
	 *
	 * `wp_ajax_*` handlers are only dispatched for authenticated requests, so the
	 * hook is added on admin_init and can also be called directly by the tests.
	 *
	 * @return void
	 */
	public static function register_ajax(): void {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( self::class, 'handle_save' ) );
	}

	/**
	 * Register the top-level menu entry.
	 *
	 * @return void
	 */
	public static function add_menu(): void {
		add_menu_page(
			__( 'داشبورد عرفان صنعت', 'erfansanat' ),
			__( 'عرفان صنعت', 'erfansanat' ),
			self::CAPABILITY,
			self::SLUG,
			array( self::class, 'render' ),
			'dashicons-art',
			59
		);
	}

	/**
	 * Whether the current request is the theme panel.
	 *
	 * @param string $hook Current admin page hook.
	 * @return bool
	 */
	public static function is_panel( string $hook ): bool {
		return false !== strpos( $hook, self::SLUG );
	}

	/**
	 * Load the panel styles and script only on the panel screen.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public static function enqueue_assets( string $hook ): void {
		if ( ! self::is_panel( $hook ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style(
			'erfansanat-admin',
			\ErfanSanat\Theme\AssetManager::uri( 'admin/options.css' ),
			array( 'wp-components' ),
			\ErfanSanat\Theme\AssetManager::version( 'admin/options.css' )
		);
		wp_enqueue_script(
			'erfansanat-admin',
			\ErfanSanat\Theme\AssetManager::uri( 'admin/options.js' ),
			array( 'jquery', 'wp-util' ),
			\ErfanSanat\Theme\AssetManager::version( 'admin/options.js' ),
			true
		);
		wp_localize_script(
			'erfansanat-admin',
			'erfansanatAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'action'  => self::AJAX_ACTION,
				'strings' => array(
					'saving'        => __( 'در حال ذخیره…', 'erfansanat' ),
					'saved'         => __( 'ذخیره شد.', 'erfansanat' ),
					'error'         => __( 'ذخیره نشد. دوباره تلاش کنید.', 'erfansanat' ),
					'confirmRemove' => __( 'این ردیف حذف شود؟', 'erfansanat' ),
					'chooseImage'   => __( 'انتخاب تصویر', 'erfansanat' ),
					'useImage'      => __( 'استفاده از این تصویر', 'erfansanat' ),
				),
			)
		);
	}

	/**
	 * Load the theme's font on every admin screen.
	 *
	 * Unconditional on purpose: the dashboard, the editor, the media library,
	 * WooCommerce and every taxonomy screen belong to the same product, and a
	 * screen-by-screen list leaves gaps that only appear as an inconsistent
	 * typeface somewhere in the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_font(): void {
		// Unconditional. The dashboard, every post type, every taxonomy, the
		// media library and the WooCommerce screens all use the theme's font,
		// so gating this on a screen list would leave holes that only show up
		// as an inconsistent typeface somewhere in the admin.
		if ( wp_style_is( 'erfansanat-admin-font', 'enqueued' ) ) {
			return;
		}

		wp_enqueue_style(
			'erfansanat-admin-font',
			\ErfanSanat\Theme\AssetManager::uri( 'admin/font.css' ),
			array(),
			\ErfanSanat\Theme\AssetManager::version( 'admin/font.css' )
		);
	}

	/**
	 * The login form is not an admin screen, so it needs its own hook.
	 *
	 * A login form in a different typeface from the site is the first thing an
	 * editor sees, and `admin_enqueue_scripts` never fires for it.
	 *
	 * @return void
	 */
	public static function enqueue_login_font(): void {
		wp_enqueue_style(
			'erfansanat-admin-font',
			\ErfanSanat\Theme\AssetManager::uri( 'admin/font.css' ),
			array(),
			\ErfanSanat\Theme\AssetManager::version( 'admin/font.css' )
		);
	}

	/**
	 * Load the meta-box layout on the editors that need it.
	 *
	 * The font is not this method's job any more; it is loaded on every screen
	 * by `enqueue_admin_font()`.
	 *
	 * @param string $hook Current admin page hook, supplied by the action.
	 * @return void
	 */
	public static function enqueue_meta_boxes( string $hook ): void {
		unset( $hook ); // The screen object carries everything this needs.
		if ( wp_style_is( 'erfansanat-admin', 'enqueued' ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen instanceof \WP_Screen ) {
			return;
		}

		$post_types = array( 'product', 'projects', 'services', 'post', 'page' );
		$taxonomies = array( 'product_cat', 'product_tag', 'project_type', 'city' );
		$supported  = in_array( (string) $screen->base, array( 'post' ), true )
			&& in_array( (string) $screen->post_type, $post_types, true );
		$supported  = $supported
			|| (
				in_array( (string) $screen->base, array( 'edit-tags', 'term' ), true )
				&& in_array( (string) $screen->taxonomy, $taxonomies, true )
			);

		if ( ! $supported ) {
			return;
		}

		wp_enqueue_style(
			'erfansanat-admin',
			\ErfanSanat\Theme\AssetManager::uri( 'admin/options.css' ),
			array( 'erfansanat-admin-font' ),
			\ErfanSanat\Theme\AssetManager::version( 'admin/options.css' )
		);
	}

	/**
	 * Render the admin overview with live content counts and safe shortcuts.
	 *
	 * Counts are derived from WordPress post tables; nothing is stored as a
	 * second source of truth, and private form messages are represented only by
	 * an unread count (their contents stay in the inbox screen).
	 *
	 * @return void
	 */
	private static function render_overview(): void {
		$stats = array(
			array(
				'label' => __( 'محصولات منتشرشده', 'erfansanat' ),
				'count' => self::published_count( 'product' ),
				'icon'  => 'dashicons-products',
				'href'  => admin_url( 'edit.php?post_type=product' ),
			),
			array(
				'label' => __( 'پروژه‌های منتشرشده', 'erfansanat' ),
				'count' => self::published_count( 'projects' ),
				'icon'  => 'dashicons-building',
				'href'  => admin_url( 'edit.php?post_type=projects' ),
			),
			array(
				'label' => __( 'خدمات منتشرشده', 'erfansanat' ),
				'count' => self::published_count( 'services' ),
				'icon'  => 'dashicons-admin-tools',
				'href'  => admin_url( 'edit.php?post_type=services' ),
			),
			array(
				'label' => __( 'پیام‌های خوانده‌نشده', 'erfansanat' ),
				'count' => self::unread_message_count(),
				'icon'  => 'dashicons-email-alt',
				'href'  => admin_url( 'edit.php?post_type=erfansanat_message' ),
			),
		);
		?>
		<section class="erfansanat-admin__overview" aria-label="<?php esc_attr_e( 'نمای کلی وب‌سایت', 'erfansanat' ); ?>">
			<div class="erfansanat-admin__overview-heading">
				<div>
					<h2><?php esc_html_e( 'نمای کلی', 'erfansanat' ); ?></h2>
					<p><?php esc_html_e( 'خلاصه‌ای زنده از محتوای سایت و میان‌برهای مدیریت.', 'erfansanat' ); ?></p>
				</div>
				<span class="erfansanat-admin__live"><span aria-hidden="true"></span><?php esc_html_e( 'آمار فعلی', 'erfansanat' ); ?></span>
			</div>
			<div class="erfansanat-admin__stats">
				<?php foreach ( $stats as $stat ) : ?>
					<a class="erfansanat-admin__stat" href="<?php echo esc_url( $stat['href'] ); ?>">
						<span class="erfansanat-admin__stat-icon dashicons <?php echo esc_attr( $stat['icon'] ); ?>" aria-hidden="true"></span>
						<span class="erfansanat-admin__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
						<strong class="erfansanat-admin__stat-count"><?php echo esc_html( number_format_i18n( (int) $stat['count'] ) ); ?></strong>
						<span class="erfansanat-admin__stat-link"><?php esc_html_e( 'مدیریت', 'erfansanat' ); ?> <span aria-hidden="true">←</span></span>
					</a>
				<?php endforeach; ?>
			</div>
			<div class="erfansanat-admin__quicklinks">
				<h3><?php esc_html_e( 'دسترسی سریع', 'erfansanat' ); ?></h3>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'افزودن محصول', 'erfansanat' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=projects' ) ); ?>"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'ثبت پروژه', 'erfansanat' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=services' ) ); ?>"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'افزودن خدمت', 'erfansanat' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=erfansanat_message' ) ); ?>"><span class="dashicons dashicons-inbox" aria-hidden="true"></span><?php esc_html_e( 'رفتن به صندوق پیام‌ها', 'erfansanat' ); ?></a>
			</div>
			<?php self::render_setup_checklist(); ?>
		</section>
		<?php
	}

	/**
	 * Render a short configuration checklist with direct repair links.
	 *
	 * @return void
	 */
	private static function render_setup_checklist(): void {
		$front_id   = (int) get_option( 'page_on_front', 0 );
		$front_page = $front_id > 0 ? get_post( $front_id ) : null;
		$front_ok   = 'page' === get_option( 'show_on_front' ) && $front_page instanceof \WP_Post && 'publish' === $front_page->post_status;

		$locations = get_nav_menu_locations();
		$menu_id   = isset( $locations['main-mega-menu'] ) ? (int) $locations['main-mega-menu'] : 0;
		$menu_ok   = $menu_id > 0 && false !== wp_get_nav_menu_object( $menu_id );

		$contact_pages = get_pages(
			array(
				'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- One published contact page; this is an admin-only checklist.
				'meta_value' => 'page-templates/contact.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- See above.
				'number'     => 1,
			)
		);
		$contact_page  = is_array( $contact_pages ) && isset( $contact_pages[0] ) && $contact_pages[0] instanceof \WP_Post ? $contact_pages[0] : null;
		$contact_ok    = $contact_page instanceof \WP_Post && 'publish' === $contact_page->post_status;

		$checks = array(
			array(
				'label' => __( 'صفحهٔ خانهٔ منتشرشده', 'erfansanat' ),
				'ok'    => $front_ok,
				'href'  => $front_page instanceof \WP_Post ? get_edit_post_link( $front_page->ID, 'raw' ) : admin_url( 'options-reading.php' ),
			),
			array(
				'label' => __( 'فهرست اصلی به جایگاه هدر متصل است', 'erfansanat' ),
				'ok'    => $menu_ok,
				'href'  => admin_url( 'nav-menus.php' ),
			),
			array(
				'label' => __( 'برگهٔ تماس با قالب تماس منتشر شده است', 'erfansanat' ),
				'ok'    => $contact_ok,
				'href'  => $contact_page instanceof \WP_Post ? get_edit_post_link( $contact_page->ID, 'raw' ) : admin_url( 'edit.php?post_type=page' ),
			),
			array(
				'label' => __( 'ووکامرس برای فروشگاه فعال است', 'erfansanat' ),
				'ok'    => class_exists( 'WooCommerce' ),
				'href'  => admin_url( 'plugins.php' ),
			),
		);
		?>
		<div class="erfansanat-admin__checklist">
			<div class="erfansanat-admin__checklist-heading">
				<div>
					<h3><?php esc_html_e( 'چک‌لیست راه‌اندازی', 'erfansanat' ); ?></h3>
					<p><?php esc_html_e( 'این موارد از وضعیت فعلی وردپرس بررسی می‌شوند و در هر زمان قابل اصلاح‌اند.', 'erfansanat' ); ?></p>
				</div>
				<span><?php echo esc_html( sprintf( /* translators: 1: passing checks, 2: total checks. */ __( '%1$d از %2$d آماده', 'erfansanat' ), count( array_filter( $checks, static fn( array $check ): bool => $check['ok'] ) ), count( $checks ) ) ); ?></span>
			</div>
			<ul>
				<?php foreach ( $checks as $check ) : ?>
					<li data-check-status="<?php echo $check['ok'] ? 'ready' : 'attention'; ?>">
						<span class="erfansanat-admin__check-icon dashicons <?php echo $check['ok'] ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>" aria-hidden="true"></span>
						<span><?php echo esc_html( $check['label'] ); ?></span>
						<strong><?php echo $check['ok'] ? esc_html__( 'آماده', 'erfansanat' ) : esc_html__( 'نیاز به بررسی', 'erfansanat' ); ?></strong>
						<a href="<?php echo esc_url( (string) $check['href'] ); ?>"><?php esc_html_e( 'بررسی', 'erfansanat' ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Count published entries for a registered post type.
	 *
	 * @param string $post_type Post type slug.
	 * @return int
	 */
	private static function published_count( string $post_type ): int {
		$counts = wp_count_posts( $post_type );
		return is_object( $counts ) && isset( $counts->publish ) ? (int) $counts->publish : 0;
	}

	/**
	 * Count private messages whose read marker is still zero.
	 *
	 * @return int
	 */
	private static function unread_message_count(): int {
		$query = new \WP_Query(
			array(
				'post_type'              => 'erfansanat_message',
				'post_status'            => 'private',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- The inbox is a private, low-volume post type and this count runs only on the admin dashboard.
					array(
						'key'     => '_es_read',
						'value'   => '0',
						'compare' => '=',
					),
				),
			)
		);
		return (int) $query->found_posts;
	}

	/**
	 * Render the settings screen.
	 *
	 * @return void
	 */
	public static function render(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'دسترسی به این صفحه مجاز نیست.', 'erfansanat' ), 403 );
		}

		$tabs   = Settings::tabs();
		$schema = Settings::schema();
		$values = Settings::all();
		$active = 'brand';
		?>
		<div class="wrap erfansanat-admin" dir="rtl">
			<header class="erfansanat-admin__welcome">
				<div class="erfansanat-admin__welcome-copy">
					<p class="erfansanat-admin__eyebrow"><?php esc_html_e( 'مرکز مدیریت وب‌سایت', 'erfansanat' ); ?></p>
					<h1><?php esc_html_e( 'داشبورد عرفان صنعت', 'erfansanat' ); ?></h1>
					<p><?php esc_html_e( 'وضعیت محتوا را ببینید، به کارهای روزمره دسترسی سریع داشته باشید و تنظیمات سراسری قالب را مدیریت کنید.', 'erfansanat' ); ?></p>
				</div>
				<a class="erfansanat-admin__view-site" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-external" aria-hidden="true"></span>
					<?php esc_html_e( 'مشاهدهٔ وب‌سایت', 'erfansanat' ); ?>
				</a>
			</header>

			<?php self::render_overview(); ?>

			<div class="erfansanat-admin__status" role="status" aria-live="polite" hidden></div>

			<div class="erfansanat-admin__layout">
				<nav class="erfansanat-admin__tabs" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'erfansanat' ); ?>">
					<?php foreach ( $tabs as $slug => $label ) : ?>
						<button type="button"
							class="erfansanat-admin__tab<?php echo $slug === $active ? ' is-active' : ''; ?>"
							data-tab="<?php echo esc_attr( $slug ); ?>"
							aria-controls="erfansanat-panel-<?php echo esc_attr( $slug ); ?>"
							aria-selected="<?php echo $slug === $active ? 'true' : 'false'; ?>"
							role="tab">
							<?php echo esc_html( $label ); ?>
						</button>
					<?php endforeach; ?>
				</nav>

				<form id="erfansanat-options-form" class="erfansanat-admin__form" novalidate>
					<?php foreach ( $schema as $tab => $fields ) : ?>
						<?php
						if ( 'sections' === $tab ) {
							// The layout field is rendered by the section manager so it
							// can offer an ordered list instead of a bare text input.
							continue;
						}
						?>
						<section id="erfansanat-panel-<?php echo esc_attr( $tab ); ?>"
							class="erfansanat-admin__panel<?php echo $tab === $active ? ' is-active' : ''; ?>"
							data-panel="<?php echo esc_attr( $tab ); ?>"
							role="tabpanel"
							<?php echo $tab === $active ? '' : 'hidden'; ?>>
							<?php foreach ( $fields as $name => $field ) : ?>
								<?php self::render_field( $name, $field, $values[ $name ] ?? $field['default'] ); ?>
							<?php endforeach; ?>
						</section>
					<?php endforeach; ?>

					<?php SectionManager::render_panel(); ?>

					<p class="erfansanat-admin__actions">
						<button type="submit" class="button button-primary button-large">
							<?php esc_html_e( 'ذخیره تغییرات', 'erfansanat' ); ?>
						</button>
						<span class="erfansanat-admin__hint">
							<?php esc_html_e( 'تغییرات فقط پس از ذخیره اعمال می‌شوند.', 'erfansanat' ); ?>
						</span>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render one field.
	 *
	 * @param string               $name  Field name.
	 * @param array<string, mixed> $field Field definition.
	 * @param mixed                $value Current value.
	 * @return void
	 */
	private static function render_field( string $name, array $field, $value ): void {
		$id          = 'erfansanat-' . str_replace( '_', '-', $name );
		$type        = (string) $field['type'];
		$label       = (string) ( $field['label'] ?? $name );
		$description = (string) ( $field['description'] ?? '' );
		$locked      = isset( $field['capability'] ) && ! current_user_can( (string) $field['capability'] );
		?>
		<div class="erfansanat-field erfansanat-field--<?php echo esc_attr( $type ); ?>" data-name="<?php echo esc_attr( $name ); ?>">
			<label class="erfansanat-field__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>

			<?php if ( 'bool' === $type ) : ?>
				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( (bool) $value ); ?> <?php disabled( $locked ); ?> />
			<?php elseif ( 'textarea' === $type || 'code' === $type ) : ?>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="<?php echo 'code' === $type ? 6 : 3; ?>" class="<?php echo 'code' === $type ? 'large-text code' : 'large-text'; ?>" dir="<?php echo 'code' === $type ? 'ltr' : 'rtl'; ?>" <?php disabled( $locked ); ?>><?php echo esc_textarea( (string) $value ); ?></textarea>
			<?php elseif ( 'media' === $type ) : ?>
				<?php $preview = (int) $value > 0 ? wp_get_attachment_image_url( (int) $value, 'thumbnail' ) : ''; ?>
				<span class="erfansanat-media">
					<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) (int) $value ); ?>" data-media-input />
					<img src="<?php echo esc_url( (string) $preview ); ?>" alt="" class="erfansanat-media__preview" data-media-preview <?php echo $preview ? '' : 'hidden'; ?> />
					<button type="button" class="button" data-media-select><?php esc_html_e( 'انتخاب تصویر', 'erfansanat' ); ?></button>
					<button type="button" class="button-link" data-media-clear><?php esc_html_e( 'حذف', 'erfansanat' ); ?></button>
				</span>
			<?php elseif ( 'links' === $type ) : ?>
				<div class="erfansanat-repeater" data-repeater="links">
					<div class="erfansanat-repeater__rows" data-repeater-rows>
						<?php foreach ( (array) $value as $index => $row ) : ?>
							<div class="erfansanat-repeater__row">
								<input type="text" name="<?php echo esc_attr( $name ); ?>[<?php echo (int) $index; ?>][title]" value="<?php echo esc_attr( (string) ( $row['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'عنوان', 'erfansanat' ); ?>" />
								<input type="url" name="<?php echo esc_attr( $name ); ?>[<?php echo (int) $index; ?>][url]" value="<?php echo esc_attr( (string) ( $row['url'] ?? '' ) ); ?>" placeholder="https://" dir="ltr" />
								<button type="button" class="button-link erfansanat-repeater__remove" data-repeater-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button>
							</div>
						<?php endforeach; ?>
					</div>
					<template data-repeater-template>
						<div class="erfansanat-repeater__row">
							<input type="text" name="<?php echo esc_attr( $name ); ?>[__INDEX__][title]" value="" placeholder="<?php esc_attr_e( 'عنوان', 'erfansanat' ); ?>" />
							<input type="url" name="<?php echo esc_attr( $name ); ?>[__INDEX__][url]" value="" placeholder="https://" dir="ltr" />
							<button type="button" class="button-link erfansanat-repeater__remove" data-repeater-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button>
						</div>
					</template>
					<button type="button" class="button" data-repeater-add><?php esc_html_e( 'افزودن ردیف', 'erfansanat' ); ?></button>
				</div>
			<?php elseif ( 'list' === $type ) : ?>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="4" class="large-text" aria-describedby="<?php echo esc_attr( $id ); ?>-help"><?php echo esc_textarea( implode( "\n", array_map( 'strval', (array) $value ) ) ); ?></textarea>
				<p class="description" id="<?php echo esc_attr( $id ); ?>-help"><?php esc_html_e( 'هر مورد در یک خط.', 'erfansanat' ); ?></p>
			<?php elseif ( 'terms' === $type ) : ?>
				<?php
				$taxonomy = (string) ( $field['taxonomy'] ?? 'product_cat' );
				$terms    = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => false,
					)
				);
				$selected = array_map( 'intval', (array) $value );
				?>
				<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>[]" multiple size="6" class="erfansanat-select--multi">
					<?php if ( is_array( $terms ) ) : ?>
						<?php foreach ( $terms as $term ) : ?>
							<option value="<?php echo esc_attr( (string) $term->term_id ); ?>" <?php echo in_array( (int) $term->term_id, $selected, true ) ? 'selected' : ''; ?>>
								<?php echo esc_html( $term->name ); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			<?php elseif ( 'int' === $type ) : ?>
				<input type="number" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) (int) $value ); ?>" min="<?php echo esc_attr( (string) ( $field['min'] ?? 0 ) ); ?>" max="<?php echo esc_attr( (string) ( $field['max'] ?? 100 ) ); ?>" class="small-text" />
			<?php elseif ( 'email' === $type ) : ?>
				<input type="email" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" dir="ltr" />
			<?php elseif ( 'url' === $type ) : ?>
				<input type="url" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" dir="ltr" />
			<?php elseif ( 'phone' === $type ) : ?>
				<input type="tel" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" dir="ltr" />
			<?php else : ?>
				<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" <?php echo isset( $field['max'] ) ? 'maxlength="' . esc_attr( (string) $field['max'] ) . '"' : ''; ?> />
			<?php endif; ?>

			<?php if ( '' !== $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
			<?php if ( $locked ) : ?>
				<p class="description erfansanat-field__locked"><?php esc_html_e( 'برای ویرایش این فیلد مجوز unfiltered_html لازم است.', 'erfansanat' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * AJAX endpoint: verify capability and nonce, sanitise, save.
	 *
	 * @return void
	 */
	public static function handle_save(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			self::respond( array( 'message' => __( 'دسترسی مجاز نیست.', 'erfansanat' ) ), 403, false );
			return;
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			self::respond( array( 'message' => __( 'نشست منقضی شده است. صفحه را دوباره بارگذاری کنید.', 'erfansanat' ) ), 403, false );
			return;
		}

		$payload = isset( $_POST['options'] ) ? wp_unslash( $_POST['options'] ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised per field in Settings::sanitize().
		if ( ! is_array( $payload ) ) {
			self::respond( array( 'message' => __( 'داده‌ای برای ذخیره ارسال نشد.', 'erfansanat' ) ), 400, false );
			return;
		}

		$result = Settings::sanitize( $payload, true );
		update_option( Settings::OPTION, $result['values'], false );
		Settings::flush();

		self::respond(
			array(
				'values'  => $result['values'],
				'errors'  => $result['errors'],
				'message' => __( 'تنظیمات ذخیره شد.', 'erfansanat' ),
			)
		);
	}
}
