<?php
/**
 * ثبت پست‌تایپ‌ها و تکسونومی‌های Native — جایگزین مستقیم JetEngine
 *
 * ⚠️ آرگومان‌ها بایت‌به‌بایت مطابق temp/DATA_BLUEPRINT.md §۲ و §۳ است؛
 * تغییر هر کلید = شکستن URLهای سئوشده سایت زنده.
 *
 * جدول URL نهایی:
 *   /projects/{slug}                  → سینگل پروژه
 *   /projects/                        → آرشیو پروژه‌ها
 *   /project_type/{term}              → آرشیو نوع پروژه
 *   /city/{term}                      → آرشیو محل پروژه
 *   /services/{slug}                  → سینگل خدمت (فهرست: برگه با قالب listing — فاز ۷)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

final class PostTypes {

    public static function init(): void {
        add_action('init', [self::class, 'register'], 10);
    }

    public static function register(): void {
        self::register_projects();
        self::register_services();
        self::register_project_type_taxonomy();
        self::register_city_taxonomy();
    }

    /** پست‌تایپ پروژه‌ها — مطابق بلوپرینت §۲.۱ */
    public static function register_projects(): void {
        register_post_type('projects', [
            'labels'        => [
                'name'          => __('پروژه‌ها', 'erfansanat'),
                'singular_name' => __('پروژه', 'erfansanat'),
                'add_new'       => __('افزودن پروژه', 'erfansanat'),
                'add_new_item'  => __('افزودن پروژه جدید', 'erfansanat'),
                'edit_item'     => __('ویرایش پروژه', 'erfansanat'),
                'menu_name'     => __('پروژه‌ها', 'erfansanat'),
            ],
            'public'        => true,
            'has_archive'   => true,
            'rewrite'       => [
                'slug'       => 'projects',
                'with_front' => false,
            ],
            'hierarchical'  => false,
            'menu_icon'     => 'dashicons-screenoptions',
            'menu_position' => 26,
            'supports'      => ['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions'],
            'show_in_rest'  => true,
            'map_meta_cap'  => true,
        ]);
    }

    /**
     * پست‌تایپ خدمات (تصمیم کارفرما Q2 / D-005).
     * has_archive=false تا با برگه فهرست خدمات تداخل rewrite نداشته باشد؛
     * فهرست خدمات از قالب برگه page-services رندر می‌شود (فاز ۷).
     */
    public static function register_services(): void {
        register_post_type('services', [
            'labels'        => [
                'name'          => __('خدمات', 'erfansanat'),
                'singular_name' => __('خدمت', 'erfansanat'),
                'add_new'       => __('افزودن خدمت', 'erfansanat'),
                'add_new_item'  => __('افزودن خدمت جدید', 'erfansanat'),
                'edit_item'     => __('ویرایش خدمت', 'erfansanat'),
                'menu_name'     => __('خدمات', 'erfansanat'),
            ],
            'public'        => true,
            'has_archive'   => false,
            'rewrite'       => [
                'slug'       => 'services',
                'with_front' => false,
            ],
            'hierarchical'  => false,
            'menu_icon'     => 'dashicons-lightbulb',
            'menu_position' => 27,
            'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
            'show_in_rest'  => true,
            'map_meta_cap'  => true,
        ]);
    }

    /** تکسونومی نوع پروژه — مطابق بلوپرینت §۳ */
    public static function register_project_type_taxonomy(): void {
        register_taxonomy('project_type', ['projects'], [
            'labels'            => [
                'name'          => __('نوع پروژه', 'erfansanat'),
                'singular_name' => __('نوع پروژه', 'erfansanat'),
                'menu_name'     => __('نوع پروژه', 'erfansanat'),
                'add_new_item'  => __('افزودن نوع پروژه', 'erfansanat'),
            ],
            'hierarchical'      => true,
            'rewrite'           => [
                'slug'         => 'project_type',
                'with_front'   => false,
                'hierarchical' => false,
            ],
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
        ]);
    }

    /** تکسونومی محل پروژه — مطابق بلوپرینت §۳ */
    public static function register_city_taxonomy(): void {
        register_taxonomy('city', ['projects'], [
            'labels'            => [
                'name'          => __('محل پروژه', 'erfansanat'),
                'singular_name' => __('محل پروژه', 'erfansanat'),
                'menu_name'     => __('محل پروژه', 'erfansanat'),
                'add_new_item'  => __('افزودن محل پروژه', 'erfansanat'),
            ],
            'hierarchical'      => true,
            'rewrite'           => [
                'slug'         => 'city',
                'with_front'   => false,
                'hierarchical' => false,
            ],
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
        ]);
    }
}
