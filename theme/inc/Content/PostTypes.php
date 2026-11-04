<?php
/** Native content types for the theme. */

namespace ErfanSanat\Theme\Content;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Registers the native projects post type. */
final class PostTypes implements ModuleInterface {

	/** Hook content-type registration at WordPress's standard init priority. */
	public function register(): void {
		add_action( 'init', array( self::class, 'register_projects' ), 10 );
	}

	/** Register the projects type with the stable public URL contract. */
	public static function register_projects(): void {
		if ( post_type_exists( 'projects' ) ) {
			return;
		}

		$labels = array(
			'name'                     => __( 'پروژه‌ها', 'erfansanat-theme' ),
			'singular_name'            => __( 'پروژه', 'erfansanat-theme' ),
			'menu_name'                => __( 'پروژه‌ها', 'erfansanat-theme' ),
			'name_admin_bar'           => __( 'پروژه', 'erfansanat-theme' ),
			'add_new'                  => __( 'افزودن', 'erfansanat-theme' ),
			'add_new_item'             => __( 'افزودن پروژه', 'erfansanat-theme' ),
			'new_item'                 => __( 'پروژه جدید', 'erfansanat-theme' ),
			'edit_item'                => __( 'ویرایش پروژه', 'erfansanat-theme' ),
			'view_item'                => __( 'نمایش پروژه', 'erfansanat-theme' ),
			'all_items'                => __( 'همه پروژه‌ها', 'erfansanat-theme' ),
			'search_items'             => __( 'جست‌وجوی پروژه‌ها', 'erfansanat-theme' ),
			'parent_item_colon'        => __( 'پروژه مادر:', 'erfansanat-theme' ),
			'not_found'                => __( 'پروژه‌ای پیدا نشد.', 'erfansanat-theme' ),
			'not_found_in_trash'       => __( 'پروژه‌ای در زباله‌دان پیدا نشد.', 'erfansanat-theme' ),
			'archives'                 => __( 'بایگانی پروژه‌ها', 'erfansanat-theme' ),
			'attributes'               => __( 'ویژگی‌های پروژه', 'erfansanat-theme' ),
			'featured_image'           => __( 'تصویر شاخص پروژه', 'erfansanat-theme' ),
			'set_featured_image'       => __( 'تنظیم تصویر شاخص پروژه', 'erfansanat-theme' ),
			'remove_featured_image'    => __( 'حذف تصویر شاخص پروژه', 'erfansanat-theme' ),
			'use_featured_image'       => __( 'استفاده به‌عنوان تصویر شاخص پروژه', 'erfansanat-theme' ),
			'insert_into_item'         => __( 'درج در پروژه', 'erfansanat-theme' ),
			'uploaded_to_this_item'    => __( 'بارگذاری‌شده برای این پروژه', 'erfansanat-theme' ),
			'filter_items_list'        => __( 'فیلتر فهرست پروژه‌ها', 'erfansanat-theme' ),
			'items_list_navigation'    => __( 'پیمایش فهرست پروژه‌ها', 'erfansanat-theme' ),
			'items_list'               => __( 'فهرست پروژه‌ها', 'erfansanat-theme' ),
			'item_published'           => __( 'پروژه منتشر شد.', 'erfansanat-theme' ),
			'item_published_privately' => __( 'پروژه به‌صورت خصوصی منتشر شد.', 'erfansanat-theme' ),
			'item_reverted_to_draft'   => __( 'پروژه به پیش‌نویس برگشت.', 'erfansanat-theme' ),
			'item_scheduled'           => __( 'پروژه زمان‌بندی شد.', 'erfansanat-theme' ),
			'item_updated'             => __( 'پروژه به‌روزرسانی شد.', 'erfansanat-theme' ),
		);

		register_post_type(
			'projects',
			array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => true,
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_icon'          => 'dashicons-screenoptions',
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'rewrite'            => array(
					'slug'       => 'projects',
					'with_front' => false,
				),
				'supports'           => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions' ),
			)
		);
	}
}
