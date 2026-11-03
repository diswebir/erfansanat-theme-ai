<?php
/**
 * Native custom post types (replaces JetEngine registrations).
 *
 * `projects` matches the live blueprint exactly (slug, rewrite, supports,
 * menu icon, REST). Registration is guarded so it never duplicates an
 * existing post type.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class PostTypes {

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
	 * Register the theme's native post types (idempotent).
	 */
	public static function register(): void {
		self::register_projects();
	}

	/**
	 * Projects CPT — exact live blueprint (P03-T001).
	 */
	private static function register_projects(): void {
		if ( post_type_exists( 'projects' ) ) {
			return; // Never duplicate an existing registration.
		}

		register_post_type(
			'projects',
			array(
				'labels'       => array(
					'name'               => __( 'پروژه‌ها', 'erfansanat' ),
					'singular_name'      => __( 'پروژه', 'erfansanat' ),
					'add_new'            => __( 'افزودن پروژه', 'erfansanat' ),
					'add_new_item'       => __( 'افزودن پروژه جدید', 'erfansanat' ),
					'edit_item'          => __( 'ویرایش پروژه', 'erfansanat' ),
					'new_item'           => __( 'پروژه جدید', 'erfansanat' ),
					'view_item'          => __( 'نمایش پروژه', 'erfansanat' ),
					'search_items'       => __( 'جستجوی پروژه‌ها', 'erfansanat' ),
					'not_found'          => __( 'پروژه‌ای یافت نشد.', 'erfansanat' ),
					'not_found_in_trash' => __( 'پروژه‌ای در زباله‌دان نیست.', 'erfansanat' ),
					'all_items'          => __( 'همه پروژه‌ها', 'erfansanat' ),
					'menu_name'          => __( 'پروژه‌ها', 'erfansanat' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array(
					'slug'       => 'projects',
					'with_front' => false,
				),
				'hierarchical' => false,
				'menu_icon'    => 'dashicons-screenoptions',
				'supports'     => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions' ),
				'show_in_rest' => true,
			)
		);
	}
}
