<?php
/** Native project taxonomies. */

namespace ErfanSanat\Theme\Content;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Registers public, REST-enabled project taxonomy archives. */
final class Taxonomies implements ModuleInterface {

	/** Register both taxonomies after their projects post type. */
	public function register(): void {
		add_action( 'init', array( self::class, 'register_project_taxonomies' ), 10 );
	}

	/** Register project_type and city with stable, non-nested public URLs. */
	public static function register_project_taxonomies(): void {
		self::register_project_taxonomy( 'project_type', 'انواع پروژه', 'نوع پروژه' );
		self::register_project_taxonomy( 'city', 'شهرها', 'شهر' );
	}

	/** Register one hierarchical project taxonomy without changing existing registrations. */
	private static function register_project_taxonomy( string $taxonomy, string $plural, string $singular ): void {
		if ( taxonomy_exists( $taxonomy ) ) {
			return;
		}

		$labels = array(
			'name'                       => sprintf( __( '%s', 'erfansanat-theme' ), $plural ),
			'singular_name'              => sprintf( __( '%s', 'erfansanat-theme' ), $singular ),
			'menu_name'                  => sprintf( __( '%s', 'erfansanat-theme' ), $plural ),
			'all_items'                  => sprintf( __( 'همه %s', 'erfansanat-theme' ), $plural ),
			'edit_item'                  => sprintf( __( 'ویرایش %s', 'erfansanat-theme' ), $singular ),
			'view_item'                  => sprintf( __( 'نمایش %s', 'erfansanat-theme' ), $singular ),
			'update_item'                => sprintf( __( 'به‌روزرسانی %s', 'erfansanat-theme' ), $singular ),
			'add_new_item'               => sprintf( __( 'افزودن %s', 'erfansanat-theme' ), $singular ),
			'new_item_name'              => sprintf( __( 'نام %s جدید', 'erfansanat-theme' ), $singular ),
			'parent_item'                => sprintf( __( '%s مادر', 'erfansanat-theme' ), $singular ),
			'parent_item_colon'          => sprintf( __( '%s مادر:', 'erfansanat-theme' ), $singular ),
			'search_items'               => sprintf( __( 'جست‌وجوی %s', 'erfansanat-theme' ), $plural ),
			'popular_items'              => sprintf( __( '%s پرکاربرد', 'erfansanat-theme' ), $plural ),
			'separate_items_with_commas' => sprintf( __( '%s را با ویرگول جدا کنید', 'erfansanat-theme' ), $plural ),
			'add_or_remove_items'        => sprintf( __( 'افزودن یا حذف %s', 'erfansanat-theme' ), $plural ),
			'choose_from_most_used'      => sprintf( __( 'انتخاب از %s پرکاربرد', 'erfansanat-theme' ), $plural ),
			'not_found'                  => sprintf( __( '%s پیدا نشد.', 'erfansanat-theme' ), $singular ),
			'no_terms'                   => sprintf( __( 'بدون %s', 'erfansanat-theme' ), $plural ),
			'items_list_navigation'      => sprintf( __( 'پیمایش فهرست %s', 'erfansanat-theme' ), $plural ),
			'items_list'                 => sprintf( __( 'فهرست %s', 'erfansanat-theme' ), $plural ),
		);

		register_taxonomy(
			$taxonomy,
			array( 'projects' ),
			array(
				'labels'            => $labels,
			'public'            => true,
			'publicly_queryable' => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'         => $taxonomy,
				'with_front'   => false,
				'hierarchical' => false,
			),
			)
		);
	}
}
