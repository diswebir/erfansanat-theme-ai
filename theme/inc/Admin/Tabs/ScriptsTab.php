<?php
/** Custom head/footer script settings tab. */

namespace ErfanSanat\Theme\Admin\Tabs;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Registers restricted administrator-only script fields. */
final class ScriptsTab implements ModuleInterface {

	public function register(): void {
		Schema::register_many( self::fields() );
	}

	/** @return array<int,array<string,mixed>> */
	public static function fields(): array {
		return array(
			array( 'id' => 'scripts.head', 'type' => 'code', 'label' => 'کد مجاز در بخش head', 'default' => '', 'rows' => 8, 'description' => 'تگ‌های script و صفات مجاز از KSES عبور می‌کنند؛ کد تنها برای مدیر دارای manage_options قابل ذخیره است.' ),
			array( 'id' => 'scripts.footer', 'type' => 'code', 'label' => 'کد مجاز پیش از پایان body', 'default' => '', 'rows' => 8, 'description' => 'خروجی در wp_footer، پیش از بسته‌شدن body قرار می‌گیرد.' ),
		);
	}

	/** Render the tools page with a clear security notice and nonce-protected form. */
	public static function render_settings(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		$nonce = function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'es_options', 'nonce', false, false ) : '';
		$html  = '<div class="es-admin es-admin-tools" dir="' . ( function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr' ) . '">';
		$html .= '<header class="es-admin__section-heading"><p class="es-admin__eyebrow">ابزارهای پیشرفته</p><h1>اسکریپت‌های سفارشی</h1><p class="es-admin__intro">کدهای تأییدشده فقط در فرانت‌اند تزریق می‌شوند؛ فقط مدیران مجاز می‌توانند آن‌ها را ذخیره کنند.</p></header>';
		$html .= '<div class="notice notice-warning inline"><p>کد سفارشی می‌تواند رفتار سایت را تغییر دهد. فقط کدهای مورداعتماد را وارد کنید؛ تگ‌ها و صفات غیرمجاز حذف می‌شوند.</p></div>';
		$html .= '<form class="es-admin__settings-form" data-es-options-form method="post">' . $nonce;
		foreach ( self::fields() as $field ) {
			$html .= FieldRenderer::render( $field, null, null, Options::get( $field['id'], $field['default'] ) );
		}
		$html .= '<div class="es-admin__form-actions"><button class="button button-primary" type="submit">ذخیره تغییرات</button><p class="es-admin__save-status" data-es-save-status role="status" aria-live="polite"></p></div>';
		return $html . '</form></div>';
	}
}
