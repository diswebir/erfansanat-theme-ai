<?php
/**
 * Compatibility map of the 18 live WooCommerce technical attributes (pa_*).
 *
 * WooCommerce owns attribute registration (from the DB). This class is a
 * read-only map of taxonomy slug → Persian label + swatch type, plus a guarded
 * register_missing() helper for environments where an attribute taxonomy is
 * absent (e.g. the QA shim). It never re-registers an existing attribute.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class AttributeMap {

	/**
	 * The 18 live attributes: taxonomy => [label, type].
	 *
	 * @return array<string,array{label:string,type:string}>
	 */
	public static function all(): array {
		return array(
			'pa_after-sales-support' => array( 'label' => 'خدمات پس از فروش', 'type' => 'select' ),
			'pa_beam-angle'          => array( 'label' => 'زاویه تابش', 'type' => 'select' ),
			'pa_body-material'       => array( 'label' => 'جنس بدنه', 'type' => 'select' ),
			'pa_brand'               => array( 'label' => 'برند', 'type' => 'select' ),
			'pa_bulb-type'           => array( 'label' => 'جنس حباب', 'type' => 'select' ),
			'pa_cable-material'      => array( 'label' => 'جنس سیم', 'type' => 'select' ),
			'pa_certifications'      => array( 'label' => 'گواهینامه‌ها', 'type' => 'select' ),
			'pa_color'               => array( 'label' => 'رنگ نور', 'type' => 'color' ),
			'pa_diameter'            => array( 'label' => 'قطر', 'type' => 'select' ),
			'pa_diffuser-color'      => array( 'label' => 'رنگ حباب', 'type' => 'select' ),
			'pa_input-voltage'       => array( 'label' => 'ولتاژ ورودی', 'type' => 'select' ),
			'pa_intensity'           => array( 'label' => 'شدت نور', 'type' => 'select' ),
			'pa_ip-rating'           => array( 'label' => 'درجه حفاظت IP', 'type' => 'select' ),
			'pa_led-chip-type'       => array( 'label' => 'نوع چیپ LED', 'type' => 'select' ),
			'pa_lifespan'            => array( 'label' => 'طول عمر مفید', 'type' => 'select' ),
			'pa_size'                => array( 'label' => 'طول/تراکم', 'type' => 'select' ),
			'pa_warranty'            => array( 'label' => 'گارانتی', 'type' => 'select' ),
			'pa_wattage'             => array( 'label' => 'توان مصرفی وات', 'type' => 'select' ),
		);
	}

	/**
	 * @return array{label:string,type:string}|null
	 */
	public static function get( string $taxonomy ): ?array {
		$all = self::all();
		return $all[ $taxonomy ] ?? null;
	}

	public static function is_mapped( string $taxonomy ): bool {
		return null !== self::get( $taxonomy );
	}

	public static function label( string $taxonomy ): string {
		$def = self::get( $taxonomy );
		return $def['label'] ?? '';
	}

	public static function type( string $taxonomy ): string {
		$def = self::get( $taxonomy );
		return $def['type'] ?? 'select';
	}

	/**
	 * Register only the attribute taxonomies that do not already exist.
	 * Existing attributes (registered by WooCommerce) are never touched.
	 */
	public static function register_missing(): void {
		foreach ( self::all() as $taxonomy => $def ) {
			if ( taxonomy_exists( $taxonomy ) ) {
				continue; // AC-03: never duplicate an existing attribute.
			}

			register_taxonomy(
				$taxonomy,
				'product',
				array(
					'label'        => $def['label'],
					'public'       => true,
					'hierarchical' => false,
					'show_ui'      => false,
					'show_in_rest' => true,
					'rewrite'      => array( 'slug' => $taxonomy ),
				)
			);
		}
	}
}
