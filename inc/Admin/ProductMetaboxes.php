<?php
/**
 * متاباکس «اطلاعات محصولات» — تمام کلیدهای متای دیتابیس زنده با املای دقیق
 *
 * ⚠️ املای کلیدها عیناً مطابق بلوپرینت است (بعضی عمداً بدون e/با املای تاریخی):
 *   gs1_id, national_code, hs_id, isic_id, cpc_id, product_id,
 *   zireonvan, english_titl (بدون e!), before-sale-c, true_sale,
 *   product-catalog-file, galerry_produ (عمداً با غلط تایپی تاریخی),
 *   project-ids, usage_countr (بدون y!),
 *   product-feature, product-feature2_copy, vaule_of_variable, usage_product, faq
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Data\ProductGallery;
use ErfanSanat\Theme\Data\RepeaterEngine;

final class ProductMetaboxes {

    public const NONCE_ACTION = 'es_product_meta';
    public const NONCE_FIELD  = 'es_product_meta_nonce';
    public const META_PREFIX  = 'opt_meta';

    public static function init(): void {
        add_action('add_meta_boxes', [self::class, 'register_metabox']);
        add_action('save_post_product', [self::class, 'save'], 10, 2);
        add_filter('manage_product_posts_columns', [self::class, 'columns']);
        add_action('manage_product_posts_custom_column', [self::class, 'column_content'], 10, 2);
    }

    public static function register_metabox(): void {
        add_meta_box(
            'es-product-info',
            __('اطلاعات محصولات (عرفان صنعت)', 'erfansanat'),
            [self::class, 'render'],
            'product',
            'normal',
            'high'
        );
    }

    /** اسکیم فیلدهای متنی/ساده به تفکیک گروه */
    public static function text_fields(): array {
        return [
            'gs1_id'              => ['type' => 'text', 'label' => __('شناسه GS1 (برابر GTIN)', 'erfansanat')],
            'national_code'       => ['type' => 'text', 'label' => __('کد ملی کالا', 'erfansanat')],
            'hs_id'               => ['type' => 'text', 'label' => __('شناسه HS', 'erfansanat')],
            'isic_id'             => ['type' => 'text', 'label' => __('شناسه ISIC', 'erfansanat')],
            'cpc_id'              => ['type' => 'text', 'label' => __('شناسه CPC', 'erfansanat')],
            'product_id'          => ['type' => 'text', 'label' => __('شناسه محصول', 'erfansanat')],
            'zireonvan'           => ['type' => 'text', 'label' => __('زیرعنوان محصول', 'erfansanat'), 'help' => __('زیر H1 سینگل محصول نمایش داده می‌شود', 'erfansanat')],
            'english_titl'        => ['type' => 'text', 'label' => __('عنوان انگلیسی محصول', 'erfansanat'), 'help' => __('⚠️ کلید دیتابیس english_titl است (بدون e پایانی) — تغییری ندهید', 'erfansanat')],
            'before-sale-c'       => ['type' => 'textarea', 'label' => __('اطلاعیه قبل از خرید', 'erfansanat'), 'help' => __('همگام با پنل «اطلاعات محصول» ووکامرس', 'erfansanat')],
        ];
    }

    /** اسکیم فیلدهای تکرارشونده (کلید → زیرفیلدها) */
    public static function repeater_schemas(): array {
        return [
            'product-feature'      => [
                'label' => __('مشخصات محصول', 'erfansanat'),
                'add'   => __('افزودن مشخصه', 'erfansanat'),
                'fields' => [
                    ['key' => 'add-new-feature', 'type' => 'text', 'label' => __('ویژگی', 'erfansanat')],
                ],
            ],
            'product-feature2_copy' => [
                'label' => __('ویژگی‌های کلیدی (دو بخشی)', 'erfansanat'),
                'add'   => __('افزودن ویژگی', 'erfansanat'),
                'help'  => __('⚠️ کلید مقدار vaule_of_variable است (املای دیتابیس) — تغییری ندهید', 'erfansanat'),
                'fields' => [
                    ['key' => 'add-new-feature', 'type' => 'text', 'label' => __('عنوان (مثل گارانتی)', 'erfansanat')],
                    ['key' => 'vaule_of_variable', 'type' => 'text', 'label' => __('مقدار (مثل ۲۴ ماهه)', 'erfansanat')],
                ],
            ],
            'usage_product'        => [
                'label' => __('کاربردها و موارد مصرف', 'erfansanat'),
                'add'   => __('افزودن کاربرد', 'erfansanat'),
                'fields' => [
                    ['key' => 'add-new-usage', 'type' => 'text', 'label' => __('کاربرد', 'erfansanat')],
                ],
            ],
            'faq'                  => [
                'label' => __('سوالات متداول محصول', 'erfansanat'),
                'add'   => __('افزودن سوال', 'erfansanat'),
                'fields' => [
                    ['key' => 'name_question', 'type' => 'text', 'label' => __('سوال', 'erfansanat')],
                    ['key' => 'answer_question', 'type' => 'textarea', 'label' => __('پاسخ', 'erfansanat')],
                ],
            ],
        ];
    }

    /** خواندن مقدار متنی متا */
    private static function value(int $post_id, string $key): string {
        return (string) get_post_meta($post_id, $key, true);
    }

    /** رندر متاباکس تب‌دار */
    public static function render(object $post): void {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);
        $post_id = (int) $post->ID;

        $true_sale = self::value($post_id, 'true_sale');
        if ($true_sale === '') {
            $true_sale = 'true'; // پیش‌فرض: قابل خرید آنلاین
        }

        $gallery_ids = ProductGallery::get_ids($post_id);

        $tabs = [
            'ids'       => __('شناسه‌های قانونی', 'erfansanat'),
            'titles'    => __('عناوین و وضعیت فروش', 'erfansanat'),
            'gallery'   => __('فایل و گالری', 'erfansanat'),
            'relations' => __('روابط', 'erfansanat'),
            'repeaters' => __('مشخصات و FAQ', 'erfansanat'),
        ];

        echo '<div class="es-pmeta" data-es-pmeta>';
        echo '<nav class="es-pmeta__tabs">';
        $first = true;
        foreach ($tabs as $key => $label) {
            printf('<button type="button" class="button%1$s" data-es-pmeta-tab="%2$s">%3$s</button>', $first ? ' button-primary' : '', esc_attr((string) $key), esc_html($label));
            $first = false;
        }
        echo '</nav>';

        // ---- تب شناسه‌ها ----
        echo '<section data-es-pmeta-panel="ids" class="es-pmeta__panel">';
        foreach (self::text_fields() as $key => $spec) {
            if (!in_array($key, ['gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id'], true)) {
                continue;
            }
            printf(
                '<p class="es-pmeta__row"><label>%1$s<br><input type="text" class="widefat" name="%2$s[%3$s]" value="%4$s"></label></p>',
                esc_html((string) $spec['label']),
                esc_attr(self::META_PREFIX),
                esc_attr((string) $key),
                esc_attr(self::value($post_id, (string) $key))
            );
        }
        echo '<p class="description">' . esc_html__('این شناسه‌ها در Quick Edit لیست محصولات هم قابل ویرایش‌اند.', 'erfansanat') . '</p>';
        echo '</section>';

        // ---- تب عناوین و وضعیت فروش ----
        echo '<section data-es-pmeta-panel="titles" class="es-pmeta__panel" hidden>';
        foreach (['zireonvan', 'english_titl', 'before-sale-c'] as $key) {
            $spec = self::text_fields()[$key];
            $tag  = $spec['type'] === 'textarea' ? 'textarea' : 'input';
            $body = $spec['type'] === 'textarea'
                ? sprintf('<textarea class="widefat" rows="4" name="%s[%s]">%s</textarea>', esc_attr(self::META_PREFIX), esc_attr($key), esc_textarea(self::value($post_id, $key)))
                : sprintf('<input type="text" class="widefat" name="%s[%s]" value="%s">', esc_attr(self::META_PREFIX), esc_attr($key), esc_attr(self::value($post_id, $key)));
            printf(
                '<p class="es-pmeta__row"><label>%1$s<br>%2$s</label>%3$s</p>',
                esc_html((string) $spec['label']),
                $body, // phpcs:ignore WordPress.Security.EscapeOutput -- اینپوت امن
                isset($spec['help']) ? '<span class="description">' . esc_html((string) $spec['help']) . '</span>' : ''
            );
        }
        printf(
            '<p class="es-pmeta__row"><label><strong>%1$s</strong><br>
            <label><input type="radio" name="%2$s[true_sale]" value="true"%3$s> %4$s</label>
            <label><input type="radio" name="%2$s[true_sale]" value="false"%5$s> %6$s</label></label></p>',
            esc_html__('وضعیت خرید آنلاین', 'erfansanat'),
            esc_attr(self::META_PREFIX),
            $true_sale === 'true' ? ' checked' : '',
            esc_html__('قابل خرید آنلاین (true_sale=true)', 'erfansanat'),
            $true_sale === 'false' ? ' checked' : '',
            esc_html__('نیازمند استعلام/تماس (true_sale=false)', 'erfansanat')
        );
        echo '</section>';

        // ---- تب فایل و گالری ----
        echo '<section data-es-pmeta-panel="gallery" class="es-pmeta__panel" hidden>';
        printf(
            '<p class="es-pmeta__row"><label>%1$s<br><input type="text" class="widefat" name="%2$s[product-catalog-file]" value="%3$s" placeholder="https://…"></label><span class="description">%4$s</span></p>',
            esc_html__('فایل کاتالوگ محصول (URL)', 'erfansanat'),
            esc_attr(self::META_PREFIX),
            esc_attr(self::value($post_id, 'product-catalog-file')),
            esc_html__('آدرس PDF کاتالوگ — ذخیره به صورت url', 'erfansanat')
        );
        printf(
            '<p class="es-pmeta__row"><label>%1$s<br><input type="text" class="widefat" name="%2$s[galerry_produ]" value="%3$s" placeholder="101,102,103"></label><span class="description">%4$s</span></p>',
            esc_html__('گالری محصول (آیدی‌های تصویر جدا با کاما)', 'erfansanat'),
            esc_attr(self::META_PREFIX),
            esc_attr(self::value($post_id, 'galerry_produ')),
            esc_html__('⚠️ کلید galerry_produ (املای تاریخی دیتابیس) — با گالری ووکامرس همگام می‌شود', 'erfansanat')
        );
        echo '<p class="description">' . sprintf(esc_html__('گالری فعلی (ادغام منابع): %s'), '<code>' . esc_html(implode(', ', $gallery_ids)) . '</code>') . '</p>';
        echo '</section>';

        // ---- تب روابط ----
        echo '<section data-es-pmeta-panel="relations" class="es-pmeta__panel" hidden>';
        RelatedPicker::render(self::META_PREFIX . '[project-ids]', 'projects', RelatedPicker::source_posts(), __('پروژه‌های مرتبط با این محصول', 'erfansanat'), 'project-ids', $post_id);
        RelatedPicker::render(self::META_PREFIX . '[usage_countr]', 'project_type', RelatedPicker::source_terms(), __('کاربردهای محصول (نوع پروژه)', 'erfansanat'), 'usage_countr', $post_id);
        echo '</section>';

        // ---- تب Repeaterها ----
        echo '<section data-es-pmeta-panel="repeaters" class="es-pmeta__panel" hidden>';
        foreach (self::repeater_schemas() as $key => $spec) {
            $read  = RepeaterEngine::read($post_id, (string) $key);
            $field = [
                'key'       => (string) $key,
                'type'      => 'repeater',
                'label'     => (string) $spec['label'],
                'add_label' => (string) $spec['add'],
                'help'      => (string) ($spec['help'] ?? ''),
                'fields'    => $spec['fields'],
                'value'     => $read['rows'],
            ];
            printf('<div class="es-pmeta__group"><h4>%s</h4>', esc_html((string) $spec['label']));
            echo FieldRenderer::render($field, $read['rows'], self::META_PREFIX . '_rep');
            echo '</div>';
        }
        echo '</section>';

        echo '</div>';
        echo '<style>.es-pmeta__tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}.es-pmeta__panel{border:1px solid #e2e8f0;border-radius:8px;padding:14px}.es-pmeta__row{margin:0 0 14px}.es-pmeta__group h4{margin:12px 0 6px}</style>';
        echo '<script>document.addEventListener("click",function(e){var b=e.target.closest("[data-es-pmeta-tab]");if(!b)return;var root=b.closest("[data-es-pmeta]"),id=b.getAttribute("data-es-pmeta-tab");root.querySelectorAll("[data-es-pmeta-tab]").forEach(function(x){x.classList.toggle("button-primary",x===b)});root.querySelectorAll("[data-es-pmeta-panel]").forEach(function(p){p.hidden=p.getAttribute("data-es-pmeta-panel")!==id;});});</script>';
    }

    /** ذخیره متا — نانس/کاپابیلیتی/اتوسِیو + حفظ فرمت Repeaterها */
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
        if ($post_type !== 'product') {
            return;
        }

        $meta = isset($_POST[self::META_PREFIX]) && is_array($_POST[self::META_PREFIX]) ? wp_unslash($_POST[self::META_PREFIX]) : [];

        // ---- فیلدهای متنی ----
        foreach (self::text_fields() as $key => $spec) {
            if (!array_key_exists((string) $key, $meta)) {
                continue;
            }
            $value = $meta[(string) $key];
            $value = $spec['type'] === 'textarea' ? wp_kses_post(is_scalar($value) ? (string) $value : '') : sanitize_text_field(is_scalar($value) ? (string) $value : '');
            update_post_meta($post_id, (string) $key, $value);
        }

        // ---- وضعیت خرید ----
        if (array_key_exists('true_sale', $meta)) {
            $true_sale = is_scalar($meta['true_sale']) ? (string) $meta['true_sale'] : 'true';
            update_post_meta($post_id, 'true_sale', $true_sale === 'false' ? 'false' : 'true');
        }

        // ---- فایل کاتالوگ (url) ----
        if (array_key_exists('product-catalog-file', $meta)) {
            $catalog = is_scalar($meta['product-catalog-file']) ? (string) $meta['product-catalog-file'] : '';
            if (ctype_digit($catalog) && (int) $catalog > 0 && function_exists('wp_get_attachment_url')) {
                $catalog = (string) wp_get_attachment_url((int) $catalog);
            }
            update_post_meta($post_id, 'product-catalog-file', esc_url_raw($catalog));
        }

        // ---- گالری (رشته کاما + همگام‌سازی ووکامرس) ----
        if (array_key_exists('galerry_produ', $meta)) {
            $ids_raw = is_scalar($meta['galerry_produ']) ? (string) $meta['galerry_produ'] : '';
            $ids     = ProductGallery::normalize($ids_raw);
            ProductGallery::sync($post_id, $ids);
        }

        // ---- روابط (حفظ فرمت آرایه/JSON) ----
        foreach (['project-ids', 'usage_countr'] as $rel_key) {
            if (!array_key_exists($rel_key, $meta)) {
                continue;
            }
            $old_parse = RepeaterEngine::parse(get_post_meta($post_id, $rel_key, true));
            $ids       = self::sanitize_id_list($meta[$rel_key]);
            $format    = $old_parse['format'] === RepeaterEngine::FORMAT_JSON ? RepeaterEngine::FORMAT_JSON : RepeaterEngine::FORMAT_ARRAY;
            RepeaterEngine::write($post_id, $rel_key, array_map(static fn($id): array => ['id' => $id], $ids), $format);
        }

        // ---- Repeaterها با حفظ فرمت ----
        $rep_input = isset($_POST[self::META_PREFIX . '_rep']) && is_array($_POST[self::META_PREFIX . '_rep']) ? wp_unslash($_POST[self::META_PREFIX . '_rep']) : [];
        foreach (self::repeater_schemas() as $key => $spec) {
            if (!array_key_exists((string) $key, $rep_input)) {
                continue;
            }
            $old_parse  = RepeaterEngine::read($post_id, (string) $key);
            $rows_input = is_array($rep_input[$key]) ? $rep_input[$key] : [];
            // sanitize هر ردیف به‌ازای اسکیم زیرفیلدها (نوع repeater)
            $rows = \ErfanSanat\Theme\Admin\Sanitizer::field(
                ['type' => 'repeater', 'fields' => is_array($spec['fields'] ?? null) ? $spec['fields'] : []],
                $rows_input
            );
            RepeaterEngine::write($post_id, (string) $key, array_values(is_array($rows) ? $rows : []), $old_parse['format'] === RepeaterEngine::FORMAT_EMPTY ? null : $old_parse['format']);
        }

        do_action('es_product_meta_saved', $post_id, $meta);
    }

    /** پاکسازی لیست آیدی (آرایه یا رشته کاما) → لیست int */
    public static function sanitize_id_list(mixed $input): array {
        if (is_string($input)) {
            $parts = explode(',', $input);
        } elseif (is_array($input)) {
            $parts = $input;
        } else {
            return [];
        }
        $out = [];
        foreach ($parts as $part) {
            if (is_array($part) && isset($part['id'])) {
                $part = $part['id'];
            }
            $id = absint(is_scalar($part) ? $part : 0);
            if ($id > 0) {
                $out[] = $id;
            }
        }
        return array_values(array_unique($out));
    }

    /** ستون‌های لیست محصولات: شناسه‌ها + وضعیت خرید */
    public static function columns(array $columns): array {
        $columns['es-identifiers'] = __('شناسه‌ها', 'erfansanat');
        $columns['es-sale-mode']   = __('وضعیت خرید', 'erfansanat');
        return $columns;
    }

    public static function column_content(string $column, int $post_id): void {
        if ($column === 'es-identifiers') {
            $gs1 = (string) get_post_meta($post_id, 'gs1_id', true);
            $nc  = (string) get_post_meta($post_id, 'national_code', true);
            echo $gs1 !== '' || $nc !== ''
                ? '<span class="es-quickedit-data" data-gs1="' . esc_attr($gs1) . '" data-national="' . esc_attr($nc) . '" data-hs="' . esc_attr((string) get_post_meta($post_id, 'hs_id', true)) . '" data-isic="' . esc_attr((string) get_post_meta($post_id, 'isic_id', true)) . '" data-cpc="' . esc_attr((string) get_post_meta($post_id, 'cpc_id', true)) . '" data-product-id="' . esc_attr((string) get_post_meta($post_id, 'product_id', true)) . '">' . esc_html($gs1 !== '' ? 'GS1: ' . $gs1 : 'کد ملی: ' . $nc) . '</span>'
                : '—';
            return;
        }
        if ($column === 'es-sale-mode') {
            $true_sale = (string) get_post_meta($post_id, 'true_sale', true);
            if ($true_sale === 'false') {
                echo '<span style="color:#b45309;font-weight:600">استعلامی</span>';
            } else {
                echo '<span style="color:#047857;font-weight:600">آنلاین</span>';
            }
        }
    }
}
