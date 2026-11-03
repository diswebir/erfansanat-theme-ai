<?php
/** Archive display and query settings tab. */

namespace ErfanSanat\Theme\Admin\Tabs;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Registers and renders shared archive settings. */
final class ArchiveTab implements ModuleInterface {

	public function register(): void {
		Schema::register_many( self::fields() );
	}

	/** @return array<int,array<string,mixed>> */
	public static function fields(): array {
		$fields = array(
			array(
				'id'      => 'archive.columns',
				'type'    => 'select',
				'label'   => 'ستون‌های شبکه آرشیو',
				'default' => '3',
				'options' => array( '1' => '۱ ستون', '2' => '۲ ستون', '3' => '۳ ستون', '4' => '۴ ستون' ),
			),
			array(
				'id'      => 'archive.posts_per_page',
				'type'    => 'select',
				'label'   => 'تعداد موارد در هر صفحه',
				'default' => '12',
				'options' => array( '6' => '۶ مورد', '9' => '۹ مورد', '12' => '۱۲ مورد', '24' => '۲۴ مورد' ),
			),
			array(
				'id'      => 'archive.order',
				'type'    => 'select',
				'label'   => 'ترتیب پیش‌فرض',
				'default' => 'date_desc',
				'options' => array(
					'date_desc'  => 'جدیدترین',
					'date_asc'   => 'قدیمی‌ترین',
					'title_asc'  => 'عنوان: الف تا ی',
					'title_desc' => 'عنوان: ی تا الف',
					'menu_order' => 'ترتیب سفارشی',
				),
			),
		);
		foreach ( array(
			'category' => 'دسته‌بندی',
			'ip'       => 'درجه حفاظت IP',
			'voltage'  => 'ولتاژ',
			'price'    => 'محدوده قیمت',
			'rating'   => 'امتیاز',
			'views'    => 'تعداد بازدید',
		) as $key => $label ) {
			$fields[] = array( 'id' => 'archive.filters.' . $key, 'type' => 'toggle', 'label' => 'نمایش فیلتر ' . $label, 'default' => true );
		}
		$fields[] = array( 'id' => 'archive.show_faq', 'type' => 'toggle', 'label' => 'نمایش پرسش‌های متداول دسته‌بندی', 'default' => true );
		return $fields;
	}

	/** Render the dedicated archive settings form. */
	public static function render_settings(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		$nonce = function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'es_options', 'nonce', false, false ) : '';
		$html  = '<div class="es-admin es-admin-archives" dir="' . ( function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr' ) . '">';
		$html .= '<header class="es-admin__section-heading"><p class="es-admin__eyebrow">نمایش فهرست‌ها</p><h1>تنظیمات آرشیوها</h1><p class="es-admin__intro">تعداد و ترتیب نتایج روی کوئری اصلی اعمال می‌شوند؛ فیلترهای غیرفعال از فهرست کنترل‌های قابل‌رندر حذف می‌شوند.</p></header>';
		$html .= '<form class="es-admin__settings-form" data-es-options-form method="post">' . $nonce . '<section id="es-settings-panel-archives" role="tabpanel" aria-label="تنظیمات آرشیوها" tabindex="0">';
		foreach ( self::fields() as $field ) {
			$html .= FieldRenderer::render( $field, null, null, Options::get( $field['id'], $field['default'] ) );
		}
		$html .= '</section><div class="es-admin__form-actions"><button class="button button-primary" type="submit">ذخیره تغییرات</button><p class="es-admin__save-status" data-es-save-status role="status" aria-live="polite"></p></div>';
		return $html . '</form></div>';
	}
}
