<?php
/**
 * Theme administration menu and isolated dashboard screens.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Admin\Tabs\ArchiveTab;
use ErfanSanat\Theme\Admin\Tabs\BrandTab;
use ErfanSanat\Theme\Admin\Tabs\FooterTab;
use ErfanSanat\Theme\Admin\Tabs\HeaderTab;
use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Adds the theme's admin area without changing unrelated WordPress screens.
 */
final class AdminMenu implements ModuleInterface {

	private const ROOT_SLUG = 'erfansanat-theme';

	/** @var array<string,array{title:string,description:string}> */
	private const PAGES = array(
		'es-theme-settings' => array(
			'title'       => 'تنظیمات سراسری',
			'description' => 'مدیریت تنظیمات مشترک سایت از یک مخزن واحد؛ شامل هدر، فوتر، اطلاعات تماس و شبکه‌های اجتماعی.',
		),
		'es-theme-brand' => array(
			'title'       => 'برند و رنگ',
			'description' => 'تنظیم رنگ‌های برند و هویت بصری قالب، بدون تغییر کلاس‌ها یا دارایی‌های ثابت فرانت‌اند.',
		),
		'es-theme-archives' => array(
			'title'       => 'آرشیوها',
			'description' => 'تنظیم تعداد، ترتیب و فیلترهای قابل‌نمایش در آرشیو محصولات، پروژه‌ها و نوشته‌ها.',
		),
		'es-theme-sections' => array(
			'title'       => 'سکشن‌های صفحات',
			'description' => 'مدیریت ترتیب، وضعیت نمایش و محتوای ساختارمند سکشن‌های صفحات سایت.',
		),
		'es-theme-forms' => array(
			'title'       => 'فرم‌ها',
			'description' => 'دسترسی به تنظیمات فرم‌های اختصاصی سایت و مقصد اطلاع‌رسانی پیام‌ها.',
		),
		'es-theme-tools' => array(
			'title'       => 'ابزارها',
			'description' => 'ابزارهای پشتیبان‌گیری، بازیابی تنظیمات و نگهداری قالب.',
		),
	);

	/** Register WordPress admin hooks. */
	public function register(): void {
		add_action( 'admin_menu', array( self::class, 'register_pages' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
	}

	/** Register top-level and child pages for administrators only. */
	public static function register_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_menu_page(
			'عرفان صنعت',
			'عرفان صنعت',
			'manage_options',
			self::ROOT_SLUG,
			array( self::class, 'render_dashboard' ),
			'dashicons-lightbulb',
			58
		);

		add_submenu_page(
			self::ROOT_SLUG,
			'داشبورد عرفان صنعت',
			'داشبورد',
			'manage_options',
			self::ROOT_SLUG,
			array( self::class, 'render_dashboard' )
		);

		foreach ( self::PAGES as $slug => $page ) {
			add_submenu_page(
				self::ROOT_SLUG,
				$page['title'] . ' — عرفان صنعت',
				$page['title'],
				'manage_options',
				$slug,
				static function () use ( $slug ): void {
					self::render_section( $slug );
				}
			);
		}
	}

	/** Enqueue the small dashboard bundle only on theme-owned screens. */
	public static function enqueue_assets( string $hook_suffix ): void {
		if ( ! current_user_can( 'manage_options' ) || ! self::is_theme_screen( $hook_suffix ) ) {
			return;
		}

		$base    = function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() : '';
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		wp_enqueue_media();
		wp_enqueue_style( 'es-admin', $base . '/assets/admin/admin.css', array(), $version );
		wp_enqueue_script( 'es-admin', $base . '/assets/admin/admin.js', array(), $version, true );
		wp_enqueue_script( 'es-admin-media', $base . '/assets/admin/media.js', array(), $version, true );
		wp_enqueue_script( 'es-admin-repeater', $base . '/assets/admin/repeater.js', array(), $version, true );
	}

	/** Check a complete WordPress hook suffix, not a loose substring. */
	private static function is_theme_screen( string $hook_suffix ): bool {
		return 'toplevel_page_' . self::ROOT_SLUG === $hook_suffix
			|| 0 === strpos( $hook_suffix, self::ROOT_SLUG . '_page_es-theme-' );
	}

	/** Render the dashboard landing page. */
	public static function render_dashboard(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$rtl = function_exists( 'is_rtl' ) && is_rtl();
		?>
		<div class="es-admin es-admin-dashboard" dir="<?php echo $rtl ? 'rtl' : 'ltr'; ?>">
			<header class="es-admin__hero">
				<div class="es-admin__brand-mark" aria-hidden="true">ESE</div>
				<div>
					<p class="es-admin__eyebrow">مدیریت قالب اختصاصی</p>
					<h1>عرفان صنعت اصفهان</h1>
					<p class="es-admin__intro">تنظیمات اصلی وب‌سایت را از یک فضای امن و یکپارچه مدیریت کنید.</p>
				</div>
			</header>
			<nav class="es-admin__cards" aria-label="بخش‌های مدیریت قالب">
				<?php foreach ( self::PAGES as $slug => $page ) : ?>
					<a class="es-admin__card" data-es-admin-nav href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>">
						<span class="es-admin__card-icon" aria-hidden="true">›</span>
						<span class="es-admin__card-title"><?php echo esc_html( $page['title'] ); ?></span>
						<span class="es-admin__card-description"><?php echo esc_html( $page['description'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
			<p class="es-admin__note">ساختار محتوایی و آیتم‌های منو همچنان از ابزارهای بومی وردپرس مدیریت می‌شوند.</p>
		</div>
		<?php
	}

	/** Render the editable Header/Footer settings workspace. */
	public static function render_settings(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		$rtl   = function_exists( 'is_rtl' ) && is_rtl();
		$nonce = function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'es_options', 'nonce', false, false ) : '';
		$html  = '<div class="es-admin es-admin-settings" dir="' . ( $rtl ? 'rtl' : 'ltr' ) . '">';
		$html .= '<header class="es-admin__section-heading"><p class="es-admin__eyebrow">تنظیمات سراسری</p><h1>هدر و فوتر</h1><p class="es-admin__intro">محتوای تماس و نمایش سایت را ویرایش کنید؛ ساختار و آیتم‌های فهرست همچنان در بخش بومی فهرست‌های وردپرس مدیریت می‌شوند.</p></header>';
		$html .= '<div class="es-admin__settings-tabs" role="tablist" aria-label="تنظیمات هدر و فوتر">';
		$html .= '<button class="es-admin__tab" id="es-settings-tab-header" type="button" role="tab" aria-selected="true" aria-controls="es-settings-panel-header" data-es-settings-tab="header">هدر و اطلاعات تماس</button>';
		$html .= '<button class="es-admin__tab" id="es-settings-tab-footer" type="button" role="tab" aria-selected="false" aria-controls="es-settings-panel-footer" data-es-settings-tab="footer" tabindex="-1">فوتر</button></div>';
		$html .= '<form class="es-admin__settings-form" data-es-options-form method="post">' . $nonce;
		$html .= HeaderTab::render();
		$html .= FooterTab::render();
		$html .= '<div class="es-admin__form-actions"><button class="button button-primary" type="submit">ذخیره تغییرات</button><p class="es-admin__save-status" data-es-save-status role="status" aria-live="polite"></p></div>';
		$html .= '</form></div>';
		return $html;
	}

	/** Render an accessible, consistent landing screen for a dashboard area. */
	public static function render_section( string $slug ): void {
		if ( ! current_user_can( 'manage_options' ) || ! isset( self::PAGES[ $slug ] ) ) {
			return;
		}

		if ( 'es-theme-settings' === $slug ) {
			echo self::render_settings(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The renderer escapes field values and emits trusted tab markup.
			return;
		}
		if ( 'es-theme-brand' === $slug ) {
			echo BrandTab::render_settings(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The renderer escapes field values and emits trusted tab markup.
			return;
		}
		if ( 'es-theme-archives' === $slug ) {
			echo ArchiveTab::render_settings(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The renderer escapes field values and emits trusted tab markup.
			return;
		}

		$page = self::PAGES[ $slug ];
		$rtl  = function_exists( 'is_rtl' ) && is_rtl();
		?>
		<div class="es-admin es-admin-section" dir="<?php echo $rtl ? 'rtl' : 'ltr'; ?>">
			<header class="es-admin__section-heading">
				<p class="es-admin__eyebrow">تنظیمات قالب</p>
				<h1><?php echo esc_html( $page['title'] ); ?></h1>
				<p class="es-admin__intro"><?php echo esc_html( $page['description'] ); ?></p>
			</header>
			<div class="es-admin__panel">
				<p><?php echo esc_html( $page['description'] ); ?></p>
				<a class="es-admin__back" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::ROOT_SLUG ) ); ?>">بازگشت به داشبورد</a>
			</div>
		</div>
		<?php
	}
}
