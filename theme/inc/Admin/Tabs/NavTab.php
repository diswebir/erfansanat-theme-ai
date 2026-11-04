<?php
/** Display-only navigation controls; menu structure remains in WordPress Menus. */

namespace ErfanSanat\Theme\Admin\Tabs;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

final class NavTab implements ModuleInterface {

	public function register(): void { Schema::register_many( self::fields() ); }

	/** @return array<int,array<string,mixed>> */
	public static function fields(): array {
		return array(
			array( 'id' => 'header.announcement.enabled', 'type' => 'toggle', 'label' => 'نمایش نوار اعلان', 'default' => true ),
			array( 'id' => 'nav.mega.enabled', 'type' => 'toggle', 'label' => 'فعال‌بودن مگامنوها', 'default' => true ),
			array( 'id' => 'nav.open_mode', 'type' => 'select', 'label' => 'نحوه بازشدن مگامنو', 'default' => 'hover', 'options' => array( 'hover' => 'با قرارگرفتن نشانگر (hover)', 'click' => 'با کلیک' ) ),
			array( 'id' => 'nav.header_height', 'type' => 'select', 'label' => 'ارتفاع هدر', 'default' => 'standard', 'options' => array( 'standard' => 'استاندارد', 'compact' => 'فشرده' ) ),
			array( 'id' => 'nav.sticky', 'type' => 'toggle', 'label' => 'چسبندگی هدر هنگام پیمایش', 'default' => true ),
		);
	}

	/** Render navigation display controls only; no menu structure editor appears here. */
	public static function render(): string {
		$html = '';
		foreach ( self::fields() as $field ) {
			$html .= FieldRenderer::render( $field, null, null, Options::get( $field['id'], $field['default'] ) );
		}
		return '<section id="es-settings-panel-nav" role="tabpanel" aria-labelledby="es-settings-tab-nav" tabindex="0" data-es-settings-panel="nav" hidden>' . $html . '</section>';
	}
}
