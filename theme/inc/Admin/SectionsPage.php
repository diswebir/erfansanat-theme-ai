<?php
/**
 * Section Builder admin screen and authenticated save endpoint.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Sections\Registry;
use ErfanSanat\Theme\Sections\Store;

defined( 'ABSPATH' ) || exit;

/** Renders and persists homepage section order, visibility, and field data. */
final class SectionsPage implements ModuleInterface {

	/** Register the privileged sections save action. */
	public function register(): void {
		add_action( 'wp_ajax_es_save_sections', array( self::class, 'handle_save' ) );
	}

	/** Render the homepage section editor. */
	public static function render_settings(): string {
		if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		$post_id = function_exists( 'get_option' ) ? absint( get_option( 'page_on_front', 0 ) ) : 0;
		$sections = Registry::get( 'home' );
		$state = Store::get( $post_id, 'home' );
		$available_keys = array_fill_keys( $state['available'] ?? array(), true );
		$nonce = function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'es_sections', 'nonce', false, false ) : '';
		$rtl = function_exists( 'is_rtl' ) && is_rtl();
		$html  = '<div class="es-admin es-admin-sections" dir="' . ( $rtl ? 'rtl' : 'ltr' ) . '">';
		$html .= '<header class="es-admin__section-heading"><p class="es-admin__eyebrow">مدیریت ساختار صفحات</p><h1>سکشن‌های صفحه اصلی</h1><p class="es-admin__intro">ترتیب، وضعیت نمایش و محتوای هر سکشن را مدیریت کنید. برای تغییر ترتیب از دستگیره، کلیدهای جهت یا دکمه‌های جابه‌جایی استفاده کنید.</p></header>';
		if ( $post_id < 1 ) {
			$html .= '<p class="es-sections__notice" role="status">برای ذخیره تنظیمات، ابتدا یک برگه را به‌عنوان صفحه نخست در تنظیمات خواندن وردپرس انتخاب کنید.</p>';
		}
		$html .= '<form class="es-sections" data-es-sections-form method="post" data-ajax-url="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '">';
		$html .= $nonce;
		$html .= '<input type="hidden" name="post_id" value="' . esc_attr( (string) $post_id ) . '">';
		$html .= '<div class="es-sections__toolbar"><h2>ترتیب نمایش</h2><p>سکشن‌های حذف‌شده از چیدمان در فهرست «سکشن‌های آماده افزودن» می‌مانند و به‌صورت غیرفعال ذخیره می‌شوند.</p></div>';
		$html .= '<ol class="es-sections__list" data-es-section-list aria-label="ترتیب فعال سکشن‌ها">';
		foreach ( $state['order'] as $key ) {
			if ( isset( $sections[ $key ] ) && ! isset( $available_keys[ $key ] ) ) {
				$html .= self::render_item( $sections[ $key ], $state, false );
			}
		}
		$html .= '</ol>';
		$html .= '<section class="es-sections__available-wrap" aria-labelledby="es-sections-available-heading"><h2 id="es-sections-available-heading">سکشن‌های آماده افزودن</h2><p>سکشن‌های این فهرست غیرفعال‌اند و در فرانت‌اند نمایش داده نمی‌شوند.</p><ol class="es-sections__list es-sections__list--available" data-es-section-available aria-label="سکشن‌های آماده افزودن">';
		foreach ( $state['order'] as $key ) {
			if ( isset( $sections[ $key ] ) && isset( $available_keys[ $key ] ) ) {
				$html .= self::render_item( $sections[ $key ], $state, true );
			}
		}
		$html .= '</ol></section>';
		$html .= '<div class="es-sections__actions"><button class="button button-primary" type="submit">ذخیره تغییرات</button><p data-es-sections-status role="status" aria-live="polite"></p></div>';
		$html .= '</form></div>';
		return $html;
	}

	/** Render a single sortable section card with schema-backed editable fields. */
	private static function render_item( array $section, array $state, bool $available ): string {
		$key = $section['key'];
		$enabled = ! empty( $state['enabled'][ $key ] );
		$html  = '<li class="es-sections__item' . ( $available ? ' is-available' : '' ) . '" data-es-section-item data-es-section-key="' . esc_attr( $key ) . '" draggable="true">';
		$html .= '<div class="es-sections__item-header">';
		$html .= '<button class="button es-sections__handle" type="button" draggable="true" data-es-section-handle aria-label="' . esc_attr( 'جابه‌جایی سکشن: ' . $section['label'] ) . '">⠿</button>';
		$html .= '<strong class="es-sections__title">' . esc_html( $section['label'] ) . '</strong>';
		$html .= '<label class="es-sections__enabled"><input type="checkbox" data-es-section-enabled' . checked( $enabled, true, false ) . '><span>فعال</span></label>';
		$html .= '<button class="button" type="button" data-es-section-up aria-label="انتقال ' . esc_attr( $section['label'] ) . ' به بالا">↑</button>';
		$html .= '<button class="button" type="button" data-es-section-down aria-label="انتقال ' . esc_attr( $section['label'] ) . ' به پایین">↓</button>';
		$html .= '<button class="button" type="button" data-es-section-toggle-editor aria-expanded="false" aria-controls="es-section-editor-' . esc_attr( $key ) . '">ویرایش</button>';
		$html .= '<button class="button-link-delete" type="button" data-es-section-remove' . ( $available ? ' hidden' : '' ) . '>حذف از چیدمان</button>';
		$html .= '<button class="button" type="button" data-es-section-add' . ( $available ? '' : ' hidden' ) . '>افزودن به چیدمان</button>';
		$html .= '</div>';
		$html .= '<div class="es-sections__editor" id="es-section-editor-' . esc_attr( $key ) . '" data-es-section-editor hidden>';
		foreach ( $section['fields'] as $field ) {
			$field_id = $field['id'];
			$field_name = 'es_sections[data][' . $key . '][' . $field_id . ']';
			$field_html = FieldRenderer::render( $field, $field_name, 'es-sections-' . $key . '-' . $field_id, $state['data'][ $key ][ $field_id ] ?? ( $field['default'] ?? '' ) );
			if ( '' !== $field_html ) {
				$html .= '<div data-es-section-field="' . esc_attr( $field_id ) . '" data-es-section-field-type="' . esc_attr( $field['type'] ) . '">' . $field_html . '</div>';
			}
		}
		$html .= '</div></li>';
		return $html;
	}

	/** Validate the request and save only the selected front page's section state. */
	public static function handle_save(): void {
		if ( ! function_exists( 'check_ajax_referer' ) || ! check_ajax_referer( 'es_sections', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'درخواست معتبر نیست.' ), 403 );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'اجازه انجام این عملیات را ندارید.' ), 403 );
		}
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$front_page_id = absint( get_option( 'page_on_front', 0 ) );
		if ( $post_id < 1 || $post_id !== $front_page_id || ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_post', $post_id ) ) ) {
			wp_send_json_error( array( 'message' => 'برگه مقصد معتبر نیست.' ), 400 );
		}
		$raw_state = isset( $_POST['state'] ) && is_string( $_POST['state'] ) ? wp_unslash( $_POST['state'] ) : '';
		$state = json_decode( $raw_state, true );
		if ( ! is_array( $state ) || ! isset( $state['order'], $state['enabled'], $state['data'] ) || ! is_array( $state['order'] ) || ! is_array( $state['enabled'] ) || ! is_array( $state['data'] ) || ( isset( $state['available'] ) && ! is_array( $state['available'] ) ) ) {
			wp_send_json_error( array( 'message' => 'ساختار داده‌های سکشن معتبر نیست.' ), 400 );
		}
		Store::save( $post_id, $state, 'home' );
		wp_send_json_success( array( 'saved' => true, 'order' => Store::get( $post_id, 'home' )['order'] ), 200 );
	}
}
