<?php
/**
 * متاباکس «اطلاعات پروژه» — گالری کلیدهای تاریخی + محصولات پروژه + تاریخ تکمیل
 *
 * ⚠️ املای دقیق کلیدها: second-image, three-image, foure-image (عمداً foure!),
 *   five-image, six-image, finish-pro-time, mortabet_mahsole_2
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class ProjectMetaboxes {

    public const NONCE_ACTION = 'es_project_meta';
    public const NONCE_FIELD  = 'es_project_meta_nonce';
    public const GALLERY_KEY  = '_es_project_gallery';

    /** کلیدهای گالری تاریخی به ترتیب تصویر */
    public const LEGACY_IMAGES = ['second-image', 'three-image', 'foure-image', 'five-image', 'six-image'];

    public static function init(): void {
        add_action('add_meta_boxes', [self::class, 'register_metabox']);
        add_action('save_post_projects', [self::class, 'save'], 10, 2);
        add_filter('manage_projects_posts_columns', [self::class, 'columns']);
        add_action('manage_projects_posts_custom_column', [self::class, 'column_content'], 10, 2);
    }

    public static function register_metabox(): void {
        add_meta_box(
            'es-project-info',
            __('اطلاعات پروژه (عرفان صنعت)', 'erfansanat'),
            [self::class, 'render'],
            'projects',
            'normal',
            'high'
        );
    }

    /** رندر متاباکس */
    public static function render(object $post): void {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);
        $post_id = (int) $post->ID;

        $legacy_values = [];
        foreach (self::LEGACY_IMAGES as $key) {
            $legacy_values[$key] = (string) get_post_meta($post_id, $key, true);
        }
        $modern = (string) get_post_meta($post_id, self::GALLERY_KEY, true);

        echo '<div class="es-pmeta" data-es-pmeta>';
        echo '<nav class="es-pmeta__tabs">';
        echo '<button type="button" class="button button-primary" data-es-pmeta-tab="gallery">' . esc_html__('گالری پروژه', 'erfansanat') . '</button>';
        echo '<button type="button" class="button" data-es-pmeta-tab="info">' . esc_html__('مشخصات پروژه', 'erfansanat') . '</button>';
        echo '</nav>';

        // ---- تب گالری ----
        echo '<section data-es-pmeta-panel="gallery" class="es-pmeta__panel">';
        echo '<h4>' . esc_html__('تصاویر گالری (کلیدهای تاریخی دیتابیس)', 'erfansanat') . '</h4>';
        $labels = [
            'second-image' => 'تصویر ۲',
            'three-image'  => 'تصویر ۳',
            'foure-image'  => 'تصویر ۴ (کلید foure-image)',
            'five-image'   => 'تصویر ۵',
            'six-image'    => 'تصویر ۶',
        ];
        foreach (self::LEGACY_IMAGES as $key) {
            printf(
                '<p class="es-pmeta__row"><label>%1$s<br><input type="text" class="widefat" name="opt_project[%2$s]" value="%3$s" placeholder="ID"></label></p>',
                esc_html((string) $labels[$key]),
                esc_attr((string) $key),
                esc_attr($legacy_values[$key])
            );
        }

        echo '<h4>' . esc_html__('گالری مدرن (چند تصویر — همگام با کلیدهای بالا)', 'erfansanat') . '</h4>';
        RelatedPicker::render('opt_project[_es_project_gallery]', 'attachment', RelatedPicker::source_posts(), 'انتخاب تصاویر گالری', (string) self::GALLERY_KEY, $post_id);
        echo '<p class="description">' . esc_html__('با ذخیره، ۵ آیدی اول به ترتیب در کلیدهای تاریخی همگام می‌شوند؛ داده قدیمی هرگز حذف نمی‌شود.', 'erfansanat') . '</p>';
        echo '</section>';

        // ---- تب مشخصات ----
        echo '<section data-es-pmeta-panel="info" class="es-pmeta__panel" hidden>';
        printf(
            '<p class="es-pmeta__row"><label>%1$s<br><input type="text" class="widefat" name="opt_project[finish-pro-time]" value="%2$s" placeholder="1404/01/01"></label><span class="description">%3$s</span></p>',
            esc_html__('تاریخ تکمیل پروژه', 'erfansanat'),
            esc_attr((string) get_post_meta($post_id, 'finish-pro-time', true)),
            esc_html__('متنی (شمسی) — سازگار با WP-Parsidate', 'erfansanat')
        );
        RelatedPicker::render('opt_project[mortabet_mahsole_2]', 'product', RelatedPicker::source_posts(), __('محصولات استفاده‌شده در پروژه', 'erfansanat'), 'mortabet_mahsole_2', $post_id);
        echo '</section>';

        echo '</div>';
    }

    /** ذخیره — گارد کامل + همگام‌سازی گالری مدرن → کلیدهای تاریخی */
    public static function save(int $post_id, object $post): void {
        if (!isset($_POST[self::NONCE_FIELD])) {
            return;
        }
        $nonce = is_string($_POST[self::NONCE_FIELD]) ? $_POST[self::NONCE_FIELD] : '';
        if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (function_exists('wp_is_post_autosave') && wp_is_post_autosave($post_id)) {
            return;
        }
        if (function_exists('wp_is_post_revision') && wp_is_post_revision($post_id)) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        $post_type = is_object($post) ? (string) ($post->post_type ?? '') : '';
        if ($post_type !== 'projects') {
            return;
        }

        $meta = isset($_POST['opt_project']) && is_array($_POST['opt_project']) ? wp_unslash($_POST['opt_project']) : [];

        // کلیدهای تاریخی گالری (فقط عدد)
        foreach (self::LEGACY_IMAGES as $key) {
            if (!array_key_exists($key, $meta)) {
                continue;
            }
            $value = is_scalar($meta[$key]) ? trim((string) $meta[$key]) : '';
            $value = ctype_digit($value) ? (string) (int) $value : '';
            update_post_meta($post_id, $key, $value);
        }

        // تاریخ تکمیل
        if (array_key_exists('finish-pro-time', $meta)) {
            update_post_meta($post_id, 'finish-pro-time', sanitize_text_field(is_scalar($meta['finish-pro-time']) ? (string) $meta['finish-pro-time'] : ''));
        }

        // گالری مدرن → همگام‌سازی ۵ آیدی اول با کلیدهای تاریخی
        if (array_key_exists(self::GALLERY_KEY, $meta)) {
            $ids = ProductMetaboxes::sanitize_id_list($meta[self::GALLERY_KEY]);
            update_post_meta($post_id, self::GALLERY_KEY, implode(',', $ids));
            $to_sync = array_slice($ids, 0, 5);
            foreach (self::LEGACY_IMAGES as $i => $key) {
                if (isset($to_sync[$i])) {
                    update_post_meta($post_id, $key, (string) $to_sync[$i]);
                }
            }
        }

        // محصولات پروژه (حفظ فرمت آرایه/JSON)
        if (array_key_exists('mortabet_mahsole_2', $meta)) {
            $old_parse = \ErfanSanat\Theme\Data\RepeaterEngine::parse(get_post_meta($post_id, 'mortabet_mahsole_2', true));
            $ids       = ProductMetaboxes::sanitize_id_list($meta['mortabet_mahsole_2']);
            $format    = $old_parse['format'] === \ErfanSanat\Theme\Data\RepeaterEngine::FORMAT_JSON
                ? \ErfanSanat\Theme\Data\RepeaterEngine::FORMAT_JSON
                : \ErfanSanat\Theme\Data\RepeaterEngine::FORMAT_ARRAY;
            \ErfanSanat\Theme\Data\RepeaterEngine::write($post_id, 'mortabet_mahsole_2', array_map(static fn($id): array => ['id' => $id], $ids), $format);
        }

        do_action('es_project_meta_saved', $post_id, $meta);
    }

    /** ستون‌های لیست پروژه‌ها: تاریخ تکمیل */
    public static function columns(array $columns): array {
        $columns['es-finish-time'] = __('تاریخ تکمیل', 'erfansanat');
        return $columns;
    }

    public static function column_content(string $column, int $post_id): void {
        if ($column !== 'es-finish-time') {
            return;
        }
        $time = (string) get_post_meta($post_id, 'finish-pro-time', true);
        echo $time !== '' ? esc_html($time) : '—';
    }
}
