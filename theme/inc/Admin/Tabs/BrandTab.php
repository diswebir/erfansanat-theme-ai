<?php
/** Brand color settings tab. */

namespace ErfanSanat\Theme\Admin\Tabs;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Defaults;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Registers and renders color overrides for the existing site shell. */
final class BrandTab implements ModuleInterface {

	/** Register the small allow-listed brand schema. */
	public function register(): void {
		Schema::register_many( self::fields() );
	}

	/** @return array<int,array<string,mixed>> */
	public static function fields(): array {
		return array(
			array(
				'id'                  => 'brand.color',
				'type'                => 'color',
				'label'               => 'رنگ اصلی برند',
				'default'             => Defaults::BRAND_COLOR,
				'description'         => 'پیش‌فرض قالب سبز زمردی است؛ رنگ سفارشی فقط به متغیر CSS برند اعمال می‌شود.',
				'reject_invalid_save' => true,
			),
		);
	}

	/** Render the dedicated brand settings form. */
	public static function render_settings(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		$nonce = function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'es_options', 'nonce', false, false ) : '';
		$value = Options::get( 'brand.color', Defaults::BRAND_COLOR );
		$html  = '<div class="es-admin es-admin-brand" dir="' . ( function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr' ) . '">';
		$html .= '<header class="es-admin__section-heading"><p class="es-admin__eyebrow">هویت بصری</p><h1>برند و رنگ</h1><p class="es-admin__intro">رنگ برند را تغییر دهید؛ ساختار، کلاس‌ها و دارایی‌های ثابت قالب دست‌نخورده می‌مانند.</p></header>';
		$html .= '<form class="es-admin__settings-form" data-es-options-form method="post">' . $nonce;
		$html .= '<section id="es-settings-panel-brand" role="tabpanel" aria-label="رنگ‌های برند" tabindex="0">';
		foreach ( self::fields() as $field ) {
			$html .= FieldRenderer::render( $field, null, null, $value );
		}
		$html .= '</section><div class="es-admin__form-actions"><button class="button button-primary" type="submit">ذخیره تغییرات</button><p class="es-admin__save-status" data-es-save-status role="status" aria-live="polite"></p></div>';
		return $html . '</form></div>';
	}
}
