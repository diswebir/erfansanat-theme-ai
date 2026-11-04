<?php
/** Header and shared-contact settings tab. */

namespace ErfanSanat\Theme\Admin\Tabs;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Registers and renders editable header/contact/social settings. */
final class HeaderTab implements ModuleInterface {

	/** Add this tab's field definitions to the save allow-list. */
	public function register(): void {
		Schema::register_many( self::fields() );
	}

	/** Declarative fields, with values sourced from the current static mirror. */
	public static function fields(): array {
		return array(
			array( 'id' => 'header.announcement.text', 'type' => 'text', 'label' => 'متن نوار اعلان', 'default' => 'فروشگاه آنلاین (ارسال سریع)' ),
			array( 'id' => 'header.announcement.url', 'type' => 'url', 'label' => 'لینک نوار اعلان', 'default' => '' ),
			array( 'id' => 'header.tagline', 'type' => 'text', 'label' => 'شعار کوتاه', 'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)' ),
			array( 'id' => 'header.phone', 'type' => 'tel', 'label' => 'تلفن کارخانه', 'default' => '031-91091011' ),
			array( 'id' => 'header.sales_phone', 'type' => 'tel', 'label' => 'تلفن مدیر فروش', 'default' => '09137976915' ),
			array( 'id' => 'header.email', 'type' => 'email', 'label' => 'ایمیل', 'default' => 'info@erfansanat.com' ),
			array( 'id' => 'header.logo_id', 'type' => 'media', 'label' => 'لوگوی سایت', 'default' => 0, 'media_kind' => 'image' ),
			array(
				'id'      => 'social.links',
				'type'    => 'repeater',
				'label'   => 'شبکه‌های اجتماعی',
				'default' => array(),
				'fields'  => array(
					array( 'id' => 'name', 'type' => 'text', 'label' => 'نام نمایشی', 'default' => '' ),
					array(
						'id'      => 'icon',
						'type'    => 'select',
						'label'   => 'شناسه آیکون مجاز',
						'default' => 'instagram',
						'options' => array(
							'instagram' => 'Instagram',
							'linkedin'  => 'LinkedIn',
							'telegram'  => 'Telegram',
							'aparat'    => 'Aparat',
							'youtube'   => 'YouTube',
						),
					),
					array( 'id' => 'url', 'type' => 'url', 'label' => 'نشانی شبکه اجتماعی', 'default' => '', 'absolute' => true ),
				),
			),
		);
	}

	/** Render this tab's fields and a page-specific nonce. */
	public static function render(): string {
		$html = '';
		foreach ( self::fields() as $field ) {
			$html .= FieldRenderer::render( $field, null, null, Options::get( $field['id'], $field['default'] ) );
		}
		return '<section id="es-settings-panel-header" role="tabpanel" aria-labelledby="es-settings-tab-header" tabindex="0" data-es-settings-panel="header">' . $html . '</section>';
	}

	/** Shared front-end predicate: false means omit the whole announcement block. */
	public static function announcement_enabled(): bool {
		return (bool) Options::get( 'header.announcement.enabled', true );
	}

	/** Render an announcement with template-owned markup, or return nothing when disabled. */
	public static function render_announcement( callable $render ): string {
		if ( ! self::announcement_enabled() ) {
			return '';
		}
		$text = Options::get( 'header.announcement.text', '' );
		$url  = Options::get( 'header.announcement.url', '' );
		return (string) call_user_func( $render, is_scalar( $text ) ? (string) $text : '', is_scalar( $url ) ? (string) $url : '' );
	}
}
