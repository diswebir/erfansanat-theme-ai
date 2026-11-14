<?php
/**
 * product_cat term meta: thumbnail (media id) + FAQ repeater
 * (name_question / answer_question). Applies only to product_cat; the FAQ is
 * stored via the legacy codec so the JetEngine item-0 format is preserved.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\LegacyMeta;

final class TermMetaboxes {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'product_cat_add_form_fields', array( __CLASS__, 'render_add_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'render_edit_fields' ), 10, 2 );
		add_action( 'created_product_cat', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'edited_product_cat', array( __CLASS__, 'save' ), 10, 2 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Fields on the "add category" form.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 */
	public static function render_add_fields( $taxonomy ): void {
		wp_nonce_field( 'es_term_meta', 'es_term_meta_nonce' );
		self::fields( 0, array() );
	}

	/**
	 * Fields on the "edit category" form.
	 *
	 * @param \WP_Term $term     Term.
	 * @param string   $taxonomy Taxonomy slug.
	 */
	public static function render_edit_fields( $term, $taxonomy ): void {
		wp_nonce_field( 'es_term_meta', 'es_term_meta_nonce' );
		$thumbnail = absint( get_term_meta( $term->term_id, 'thumbnail', true ) );
		$faq       = LegacyMeta::get_for_term( $term->term_id, 'faq' );
		echo '<table class="form-table"><tbody>';
		self::fields( $thumbnail, $faq );
		echo '</tbody></table>';
	}

	/**
	 * @param int                  $thumbnail Media id.
	 * @param array<int,mixed>     $faq       FAQ rows.
	 */
	private static function fields( int $thumbnail, array $faq ): void {
		printf(
			'<tr class="form-field"><th scope="row"><label for="es-cat-thumbnail">%s</label></th><td><input type="text" id="es-cat-thumbnail" name="es_term_meta[thumbnail]" value="%s" class="regular-text" /></td></tr>',
			esc_html__( 'تصویر دسته (شناسه رسانه)', 'erfansanat' ),
			esc_attr( (string) $thumbnail )
		);

		$faq[] = array( 'name_question' => '', 'answer_question' => '' ); // New empty row.
		echo '<tr class="form-field"><th scope="row">' . esc_html__( 'سوالات متداول دسته', 'erfansanat' ) . '</th><td>';
		foreach ( $faq as $i => $row ) {
			$q = is_array( $row ) ? (string) ( $row['name_question'] ?? '' ) : '';
			$a = is_array( $row ) ? (string) ( $row['answer_question'] ?? '' ) : '';
			printf( '<p><input type="text" name="es_term_meta[faq][%d][name_question]" value="%s" class="widefat" placeholder="%s" /></p>', (int) $i, esc_attr( $q ), esc_attr__( 'سوال', 'erfansanat' ) );
			printf( '<p><textarea name="es_term_meta[faq][%d][answer_question]" class="widefat" rows="2" placeholder="%s">%s</textarea></p>', (int) $i, esc_attr__( 'پاسخ', 'erfansanat' ), esc_textarea( $a ) );
		}
		echo '</td></tr>';
	}

	/**
	 * Read the FAQ rows that have a non-empty question (for rendering).
	 *
	 * @return array<int,array{name_question:string,answer_question:string}>
	 */
	public static function get_faq( int $term_id ): array {
		$out = array();
		foreach ( LegacyMeta::get_for_term( $term_id, 'faq' ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$question = trim( (string) ( $row['name_question'] ?? '' ) );
			if ( '' === $question ) {
				continue;
			}
			$out[] = array(
				'name_question'   => (string) $row['name_question'],
				'answer_question' => (string) ( $row['answer_question'] ?? '' ),
			);
		}
		return $out;
	}

	/**
	 * Front-end FAQ markup; returns an empty string when there is no FAQ (SC-02).
	 */
	public static function render_faq_html( int $term_id ): string {
		$faq = self::get_faq( $term_id );
		if ( empty( $faq ) ) {
			return '';
		}

		$html = '<div class="es-term-faq">';
		foreach ( $faq as $row ) {
			$html .= '<div class="es-faq-item"><h4>' . esc_html( $row['name_question'] ) . '</h4><p>' . esc_html( $row['answer_question'] ) . '</p></div>';
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Save thumbnail + FAQ (nonce + capability guarded, product_cat only).
	 *
	 * @param int $term_id Term id.
	 * @param int $tt_id   Term taxonomy id.
	 */
	public static function save( $term_id, $tt_id ): void {
		if ( ! isset( $_POST['es_term_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_term_meta_nonce'] ) ), 'es_term_meta' ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_term_meta'] ) || ! is_array( $_POST['es_term_meta'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_term_meta'] );

		if ( array_key_exists( 'thumbnail', $raw ) ) {
			update_term_meta( $term_id, 'thumbnail', absint( $raw['thumbnail'] ) );
		}

		if ( array_key_exists( 'faq', $raw ) ) {
			$rows  = LegacyMeta::decode( $raw['faq'] );
			$clean = array();
			foreach ( $rows as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$question = sanitize_text_field( (string) ( $row['name_question'] ?? '' ) );
				$answer   = sanitize_textarea_field( (string) ( $row['answer_question'] ?? '' ) );
				if ( '' === trim( $question ) && '' === trim( $answer ) ) {
					continue; // Drop fully empty rows.
				}
				$clean[] = array( 'name_question' => $question, 'answer_question' => $answer );
			}
			LegacyMeta::set_for_term( $term_id, 'faq', $clean );
		}
	}
}
