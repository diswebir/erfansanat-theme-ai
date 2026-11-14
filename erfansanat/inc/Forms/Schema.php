<?php
/**
 * Form schema definitions (P08-T012).
 *
 * The four live forms (agent.md 8.2): contact, quote (استعلام), recruitment,
 * client feedback. Each type declares its fields with sanitization rules so
 * Entries::store() can validate + clean payloads before persisting them.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

defined( 'ABSPATH' ) || exit;

final class Schema {

	/**
	 * The four reference form types (AC-01) with their Persian labels.
	 *
	 * @return array<string,string>
	 */
	public static function types(): array {
		return array(
			'contact'     => 'فرم تماس',
			'quote'       => 'استعلام قیمت',
			'recruitment' => 'فرم استخدام',
			'feedback'    => 'نظرات مشتریان',
		);
	}

	/**
	 * Field schema per type: name → [type, required, label].
	 * type ∈ text|tel|email|textarea — mirrors the static reference forms.
	 *
	 * @param string $type Form type.
	 * @return array<string,array{type:string,required:bool,label:string}>|null
	 */
	public static function fields( string $type ): ?array {
		$fields = array(
			'contact'     => array(
				'name'         => array( 'type' => 'text', 'required' => true, 'label' => 'نام شما' ),
				'phone'        => array( 'type' => 'tel', 'required' => true, 'label' => 'شماره تماس' ),
				'organization' => array( 'type' => 'text', 'required' => false, 'label' => 'نام سازمان یا شرکت (اختیاری)' ),
				'message'      => array( 'type' => 'textarea', 'required' => true, 'label' => 'توضیحات پروژه' ),
			),
			'quote'       => array(
				'name'     => array( 'type' => 'text', 'required' => true, 'label' => 'نام شما' ),
				'phone'    => array( 'type' => 'tel', 'required' => true, 'label' => 'شماره تماس' ),
				'product'  => array( 'type' => 'text', 'required' => false, 'label' => 'محصول مورد نظر' ),
				'quantity' => array( 'type' => 'text', 'required' => false, 'label' => 'تعداد' ),
				'message'  => array( 'type' => 'textarea', 'required' => true, 'label' => 'توضیحات استعلام' ),
			),
			'recruitment' => array(
				'name'       => array( 'type' => 'text', 'required' => true, 'label' => 'نام و نام خانوادگی' ),
				'phone'      => array( 'type' => 'tel', 'required' => true, 'label' => 'شماره تماس' ),
				'email'      => array( 'type' => 'email', 'required' => false, 'label' => 'ایمیل (اختیاری)' ),
				'unit'       => array( 'type' => 'text', 'required' => false, 'label' => 'واحد سازمانی' ),
				'experience' => array( 'type' => 'text', 'required' => false, 'label' => 'میزان سابقه' ),
				'background' => array( 'type' => 'textarea', 'required' => true, 'label' => 'تحصیلات، مهارت‌ها و سوابق' ),
			),
			'feedback'    => array(
				'name'    => array( 'type' => 'text', 'required' => true, 'label' => 'نام شما' ),
				'rating'  => array( 'type' => 'text', 'required' => false, 'label' => 'امتیاز' ),
				'message' => array( 'type' => 'textarea', 'required' => true, 'label' => 'متن دیدگاه' ),
			),
		);
		return $fields[ $type ] ?? null;
	}

	/**
	 * Validate + sanitize a raw payload against the schema (AC-02).
	 * Unknown fields are dropped; missing required fields → \WP_Error; values
	 * are cleaned with field-type-appropriate sanitizers.
	 *
	 * @param string               $type  Form type.
	 * @param array<string,mixed>  $input Raw payload.
	 * @return array<string,string>|\WP_Error
	 */
	public static function validate( string $type, array $input ) {
		if ( ! isset( self::types()[ $type ] ) ) {
			return new \WP_Error( 'es_form_unknown_type', 'نوع فرم شناخته‌شده نیست.' );
		}
		$schema  = self::fields( $type ) ?? array();
		$missing = array();
		$clean   = array();
		foreach ( $schema as $name => $def ) {
			$raw = isset( $input[ $name ] ) && is_scalar( $input[ $name ] ) ? (string) $input[ $name ] : '';
			$raw = trim( $raw );
			if ( '' === $raw ) {
				if ( $def['required'] ) {
					$missing[] = $def['label'];
				}
				continue;
			}
			switch ( $def['type'] ) {
				case 'email':
					$email = sanitize_email( $raw );
					if ( ! is_email( $email ) ) {
						return new \WP_Error( 'es_form_bad_email', 'ایمیل واردشده معتبر نیست: ' . $def['label'] );
					}
					$clean[ $name ] = $email;
					break;
				case 'textarea':
					$clean[ $name ] = sanitize_textarea_field( wp_strip_all_tags( $raw ) );
					break;
				case 'tel':
					$clean[ $name ] = sanitize_text_field( preg_replace( '/[^\d\+\-\s\(\)۰-۹]/u', '', $raw ) ?? '' );
					break;
				default:
					$clean[ $name ] = sanitize_text_field( wp_strip_all_tags( $raw ) );
					break;
			}
		}
		if ( $missing ) {
			return new \WP_Error( 'es_form_missing', implode( '، ', $missing ) );
		}
		return $clean;
	}
}
