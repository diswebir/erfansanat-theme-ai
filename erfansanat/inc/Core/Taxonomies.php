<?php
/**
 * Native taxonomies for projects (replaces JetEngine taxonomies).
 *
 * Slugs are agnostic: live terms use Persian slugs (decision D3), so nothing
 * here hardcodes an English term slug. Rewrite bases match the live blueprint.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Taxonomies {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'init', array( __CLASS__, 'register' ), 5 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Register the theme's native taxonomies (idempotent).
	 */
	public static function register(): void {
		self::register_project_type();
		self::register_city();
	}

	/**
	 * City taxonomy — Persian-slug friendly, flat rewrite (P03-T004).
	 */
	private static function register_city(): void {
		if ( taxonomy_exists( 'city' ) ) {
			return; // Never duplicate an existing registration.
		}

		register_taxonomy(
			'city',
			array( 'projects' ),
			array(
				'labels'            => array(
					'name'          => __( 'شهرها', 'erfansanat' ),
					'singular_name' => __( 'شهر', 'erfansanat' ),
					'search_items'  => __( 'جستجوی شهرها', 'erfansanat' ),
					'all_items'     => __( 'همه شهرها', 'erfansanat' ),
					'parent_item'   => __( 'شهر والد', 'erfansanat' ),
					'edit_item'     => __( 'ویرایش شهر', 'erfansanat' ),
					'add_new_item'  => __( 'افزودن شهر', 'erfansanat' ),
					'menu_name'     => __( 'شهرها', 'erfansanat' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'         => 'city',
					'with_front'   => false,
					'hierarchical' => false,
				),
			)
		);
	}

	/**
	 * Project type taxonomy — hierarchical, flat rewrite (P03-T003).
	 */
	private static function register_project_type(): void {
		if ( taxonomy_exists( 'project_type' ) ) {
			return; // Never duplicate an existing registration.
		}

		register_taxonomy(
			'project_type',
			array( 'projects' ),
			array(
				'labels'            => array(
					'name'          => __( 'انواع پروژه', 'erfansanat' ),
					'singular_name' => __( 'نوع پروژه', 'erfansanat' ),
					'search_items'  => __( 'جستجوی انواع پروژه', 'erfansanat' ),
					'all_items'     => __( 'همه انواع پروژه', 'erfansanat' ),
					'parent_item'   => __( 'نوع والد', 'erfansanat' ),
					'edit_item'     => __( 'ویرایش نوع پروژه', 'erfansanat' ),
					'add_new_item'  => __( 'افزودن نوع پروژه', 'erfansanat' ),
					'menu_name'     => __( 'انواع پروژه', 'erfansanat' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'         => 'project_type',
					'with_front'   => false,
					'hierarchical' => false,
				),
			)
		);
	}
}
