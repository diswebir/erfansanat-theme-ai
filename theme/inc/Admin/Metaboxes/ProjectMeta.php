<?php
/** Project-specific legacy metadata box. */

namespace ErfanSanat\Theme\Admin\Metaboxes;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Meta\MetaRepository;

defined( 'ABSPATH' ) || exit;

/** Preserves historic project image keys, Jalali completion date and product links. */
final class ProjectMeta implements ModuleInterface {

	private const IMAGE_KEYS = array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image' );

	public function register(): void {
		add_action( 'add_meta_boxes_projects', array( self::class, 'register_box' ) );
		add_action( 'save_post_projects', array( self::class, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_media' ) );
	}

	public static function register_box(): void {
		add_meta_box( 'es-project-meta', __( 'اطلاعات تکمیلی پروژه', 'erfansanat-theme' ), array( self::class, 'render' ), 'projects', 'normal', 'high' );
	}

	public static function render( $post ): void {
		if ( is_object( $post ) && ! empty( $post->ID ) && current_user_can( 'edit_post', (int) $post->ID ) ) { echo self::render_content( (int) $post->ID ); }
	}

	public static function render_content( int $post_id ): string {
		$html = (string) wp_nonce_field( 'es_project_meta_' . $post_id, 'es_project_meta_nonce', false, false );
		foreach ( self::IMAGE_KEYS as $key ) {
			$id = absint( get_post_meta( $post_id, $key, true ) );
			$html .= '<p><label>' . esc_html( $key ) . '</label><div class="es-media" data-es-media data-es-media-mode="single" data-es-media-kind="image"><input class="es-media__value widefat" type="text" name="es_project_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( (string) $id ) . '"><div class="es-media__preview">' . ( $id ? wp_get_attachment_image( $id, 'thumbnail' ) : '' ) . '</div><button type="button" class="button" data-es-media-select>' . esc_html__( 'انتخاب تصویر', 'erfansanat-theme' ) . '</button><button type="button" class="button" data-es-media-remove>' . esc_html__( 'حذف', 'erfansanat-theme' ) . '</button></div></p>';
		}
		$date = get_post_meta( $post_id, 'finish-pro-time', true );
		$date = is_scalar( $date ) ? (string) $date : '';
		$html .= '<p><label for="es-finish-pro-time">' . esc_html__( 'تاریخ تکمیل (جلالی YYYY/MM/DD)', 'erfansanat-theme' ) . '</label><input id="es-finish-pro-time" type="text" name="es_project_meta[finish-pro-time]" value="' . esc_attr( $date ) . '"></p>';
		$selected = self::product_ids( es_meta( $post_id, 'mortabet_mahsole_2', array() ) );
		$products = get_posts( array( 'post_type' => 'product', 'post_status' => array( 'publish', 'draft' ), 'numberposts' => 200, 'orderby' => 'title', 'order' => 'ASC' ) );
		$html .= '<p><label for="es-project-products">' . esc_html__( 'محصولات مرتبط', 'erfansanat-theme' ) . '</label><select id="es-project-products" name="es_project_meta[mortabet_mahsole_2][]" multiple class="widefat">';
		foreach ( is_array( $products ) ? $products : array() as $product ) { if ( ! is_object( $product ) || empty( $product->ID ) ) { continue; } $id = (int) $product->ID; $sel = in_array( $id, $selected, true ) ? ' selected="selected"' : ''; $html .= '<option value="' . esc_attr( (string) $id ) . '"' . $sel . '>' . esc_html( get_the_title( $id ) ) . '</option>'; }
		return $html . '</select></p>';
	}

	public static function save( int $post_id, $post = null ): bool {
		if ( $post_id < 1 || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['es_project_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['es_project_meta_nonce'] ), 'es_project_meta_' . $post_id ) || ! isset( $_POST['es_project_meta'] ) || ! is_array( $_POST['es_project_meta'] ) ) { return false; }
		$input = wp_unslash( $_POST['es_project_meta'] );
		foreach ( self::IMAGE_KEYS as $key ) {
			if ( ! array_key_exists( $key, $input ) ) { continue; }
			$id = absint( $input[$key] );
			if ( 0 === $id ) { delete_post_meta( $post_id, $key ); }
			elseif ( 'attachment' === get_post_type( $id ) && wp_attachment_is_image( $id ) ) { update_post_meta( $post_id, $key, $id ); }
		}
		if ( isset( $input['finish-pro-time'] ) && is_scalar( $input['finish-pro-time'] ) ) {
			$date = sanitize_text_field( (string) $input['finish-pro-time'] );
			if ( '' === $date ) { delete_post_meta( $post_id, 'finish-pro-time' ); }
			elseif ( self::valid_jalali_date( $date ) ) { update_post_meta( $post_id, 'finish-pro-time', $date ); }
		}
		if ( isset( $input['mortabet_mahsole_2'] ) && is_array( $input['mortabet_mahsole_2'] ) ) {
			$ids = array(); foreach ( $input['mortabet_mahsole_2'] as $raw ) { $id = absint( $raw ); if ( $id && 'product' === get_post_type( $id ) && ! in_array( $id, $ids, true ) ) { $ids[] = $id; } }
			if ( $ids ) {
				$existing = MetaRepository::get( $post_id, 'mortabet_mahsole_2', array() );
				if ( is_array( $existing ) && array_key_exists( 'item-0', $existing ) ) { $rows = array(); foreach ( $ids as $index => $id ) { $rows['item-' . $index] = $id; } $ids = $rows; }
				MetaRepository::set( $post_id, 'mortabet_mahsole_2', $ids );
			} else { MetaRepository::delete( $post_id, 'mortabet_mahsole_2' ); }
		}
		return true;
	}

	/** Validate Jalali month/day ranges while accepting leap-day 30 Esfand. */
	public static function valid_jalali_date( string $date ): bool {
		if ( ! preg_match( '/^([1-9][0-9]{3})\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])$/', $date, $m ) ) { return false; }
		$month = (int) $m[2]; $day = (int) $m[3];
		if ( $month <= 6 ) { return $day <= 31; }
		if ( $month <= 11 ) { return $day <= 30; }
		$leap_residues = array( 0, 4, 8, 12, 16, 21, 25, 29 );
		return $day <= 29 || ( 30 === $day && in_array( ( ( (int) $m[1] - 1 ) % 33 ), $leap_residues, true ) );
	}

	private static function product_ids( $value ): array {
		if ( ! is_array( $value ) ) { return array(); }
		$ids = array(); foreach ( $value as $item ) { if ( is_array( $item ) ) { $item = $item['id'] ?? $item['value'] ?? 0; } $id = absint( $item ); if ( $id && ! in_array( $id, $ids, true ) ) { $ids[] = $id; } } return $ids;
	}

	public static function enqueue_media( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! function_exists( 'get_current_screen' ) ) { return; }
		$screen = get_current_screen(); if ( ! is_object( $screen ) || 'projects' !== $screen->post_type ) { return; }
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		wp_enqueue_style( 'es-admin-fonts', get_template_directory_uri() . '/assets/admin/fonts.css', array(), $version );
		wp_enqueue_media(); wp_enqueue_script( 'es-admin-media', get_template_directory_uri() . '/assets/admin/media.js', array(), $version, true );
	}
}
