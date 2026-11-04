<?php
/** Footer settings tab. Navigation items remain managed in Appearance → Menus. */

namespace ErfanSanat\Theme\Admin\Tabs;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Registers and renders footer content and display controls. */
final class FooterTab implements ModuleInterface {

	public function register(): void {
		Schema::register_many( self::fields() );
	}

	/** @return array<int,array<string,mixed>> */
	public static function fields(): array {
		return array(
			array( 'id' => 'footer.address', 'type' => 'textarea', 'label' => 'نشانی کارخانه و دفتر مرکزی', 'default' => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹', 'rows' => 3 ),
			array( 'id' => 'footer.work_hours', 'type' => 'text', 'label' => 'ساعات کاری', 'default' => 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر' ),
			array( 'id' => 'footer.copyright', 'type' => 'text', 'label' => 'متن حق نشر', 'default' => '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.' ),
			array( 'id' => 'footer.show_quick_links', 'type' => 'toggle', 'label' => 'نمایش فهرست پیوندهای سریع', 'default' => true, 'description' => 'آیتم‌ها از فهرست جایگاه Footer در نمایش ← فهرست‌ها خوانده می‌شوند.' ),
			array( 'id' => 'footer.show_categories', 'type' => 'toggle', 'label' => 'نمایش دسته‌بندی محصولات', 'default' => true, 'description' => 'دسته‌ها به‌صورت پویا از دسته‌بندی‌های منتشرشده ووکامرس خوانده می‌شوند.' ),
			array( 'id' => 'footer.back_to_top', 'type' => 'toggle', 'label' => 'نمایش دکمه بازگشت به بالا', 'default' => true ),
		);
	}

	/** Render footer fields with their persisted or static fallback values. */
	public static function render(): string {
		$html = '';
		foreach ( self::fields() as $field ) {
			$html .= FieldRenderer::render( $field, null, null, Options::get( $field['id'], $field['default'] ) );
		}
		return '<section id="es-settings-panel-footer" role="tabpanel" aria-labelledby="es-settings-tab-footer" tabindex="0" data-es-settings-panel="footer" hidden>' . $html . '</section>';
	}
}
