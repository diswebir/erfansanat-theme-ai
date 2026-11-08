<?php
/**
 * موتور مدیریت سکشن‌های صفحات — جایگزین کامل بخش Page Builder المنتور
 *
 * هر برگه می‌تواند سکشن‌های ثبت‌شده را روشن/خاموش کند، ترتیبشان را با درگ تغییر دهد
 * و فیلدهای هر سکشن (media/text/repeater/…) را ویرایش کند.
 * ذخیره: متای _es_sections به شکل [{id, enabled, order, fields}]
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class SectionManager {

    public const META_KEY     = '_es_sections';
    public const NONCE_ACTION = 'es_sections';

    /** [page_key => [id => ['label','schema'=>fields]]] به ترتیب ثبت */
    private static array $sections = [];
    private static int $sequence   = 0;

    /** نگاشت slug قالب برگه → گروه سکشن (فاز ۷.۷ — about/contact) */
    private const TEMPLATE_GROUPS = [
        'page-templates/page-about.php'   => 'about',
        'page-templates/page-contact.php' => 'contact',
        'page-templates/page-licenses.php'   => 'licenses',
        'page-templates/page-faq.php'        => 'faq',
        'page-templates/page-refund.php'     => 'refund',
        'page-templates/page-recruitment.php' => 'recruitment',
    ];

    public static function init(): void {
        add_action('add_meta_boxes', [self::class, 'register_metabox']);
        add_action('save_post', [self::class, 'save'], 10, 2);
    }

    /**
     * ثبت یک سکشن
     *
     * @param string $page_key کلید گروه صفحات (home/about/contact/landing/…)
     * @param string $id       شناسه یکتا (نام template-parts/sections/{id}.php)
     * @param string $label    برچسب نمایشی
     * @param array  $schema   اسکیم فیلدها (قالب فیلدهای FieldRenderer)
     */
    public static function register_section(string $page_key, string $id, string $label, array $schema = []): void {
        self::$sections[$page_key][$id] = [
            'label' => $label,
            'schema' => array_values($schema),
            'seq'    => self::$sequence++,
        ];
    }

    /** همه سکشن‌های ثبت‌شده */
    public static function registered(): array {
        return self::$sections;
    }

    /** سکشن‌های یک کلید صفحه (به ترتیب ثبت) */
    public static function sections_of(string $page_key): array {
        return self::$sections[$page_key] ?? [];
    }

    /** اسکیم فیلدهای یک سکشن (برای ادغام پیش‌فرض‌ها در رندر) */
    public static function schema_of(string $id): array {
        foreach (self::$sections as $items) {
            if (isset($items[$id]) && is_array($items[$id]['schema'] ?? null)) {
                return $items[$id]['schema'];
            }
        }
        return [];
    }

    /**
     * گروه سکشن‌های یک برگه بر اساس قالب انتخابی — پیش‌فرض: page
     *
     * @param int $post_id
     */
    public static function group_for_post(int $post_id): string {
        $slug  = function_exists('get_page_template_slug') ? (string) get_page_template_slug($post_id) : '';
        $map   = apply_filters('es_sections_template_groups', self::TEMPLATE_GROUPS);
        $group = is_array($map) ? (string) ($map[$slug] ?? '') : '';
        return $group !== '' ? $group : 'page';
    }

    /** انواع پست دارای متاباکس سکشن */
    public static function post_types(): array {
        $types = apply_filters('es_sections_post_types', ['page']);
        return is_array($types) ? $types : ['page'];
    }

    public static function register_metabox(): void {
        foreach (self::post_types() as $post_type) {
            add_meta_box(
                'es-sections',
                __('سکشن‌های صفحه (عرفان صنعت)', 'erfansanat'),
                [self::class, 'render_metabox'],
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * سکشن‌های فعال یک برگه به ترتیب ثبت/ذخیره — بدون متا: سکشن‌های گروه قالب برگه
     *
     * @return list<array{id:string,enabled:int,order:int,fields:array}>
     */
    public static function get_sections(int $post_id): array {
        $raw = get_post_meta($post_id, self::META_KEY, true);

        if (!is_array($raw) || $raw === []) {
            // پیش‌فرض: همه سکشن‌های ثبت‌شده، فعال، به ترتیب ثبت (ظاهر عین میرور)
            $out = [];
            // پیش‌فرض: سکشن‌های گروه قالب همین برگه، فعال، به ترتیب ثبت (ظاهر عین میرور)
            foreach (self::sections_of(self::group_for_post($post_id)) as $id => $info) {
                $out[] = ['id' => (string) $id, 'enabled' => 1, 'order' => count($out), 'fields' => []];
            }
            return $out;
        }

        $out = [];
        foreach ($raw as $row) {
            if (!is_array($row) || !isset($row['id'])) {
                continue;
            }
            $out[] = [
                'id'      => (string) $row['id'],
                'enabled' => (int) (bool) ($row['enabled'] ?? 1),
                'order'   => (int) ($row['order'] ?? 0),
                'fields'  => is_array($row['fields'] ?? null) ? $row['fields'] : [],
            ];
        }
        usort($out, static fn(array $a, array $b): int => $a['order'] <=> $b['order']);
        foreach ($out as $i => $row) {
            $out[$i]['order'] = $i;
        }
        return $out;
    }

    /** فهرست مسطح همه سکشن‌های ثبت‌شده به ترتیب ثبت — کلید «گروه|id» برای جلوگیری از تداخل id بین گروه‌ها */
    private static function registered_flat(): array {
        $flat = [];
        foreach (self::$sections as $page_key => $items) {
            foreach ($items as $id => $info) {
                $flat[$page_key . '|' . (string) $id] = $info;
            }
        }
        uasort($flat, static fn(array $a, array $b): int => $a['seq'] <=> $b['seq']);
        return $flat;
    }

    /** رندر UI متاباکس: لیست درگ‌پذیر + سوییچ + پنل فیلدها */
    public static function render_metabox(object $post): void {
        wp_nonce_field(self::NONCE_ACTION, 'es_sections_nonce');

        $saved = self::get_sections((int) $post->ID);

        // فقط سکشن‌های گروه قالب همین برگه (نگاشت قالب برگه → گروه، فاز ۷.۷)
        $by_id = self::sections_of(self::group_for_post((int) $post->ID));

        echo '<div class="es-sections" data-es-sections>';

        if ($by_id === []) {
            printf('<p class="es-sections__empty">%s</p>', esc_html__('هنوز سکشنی برای این قالب ثبت نشده است.', 'erfansanat'));
            echo '</div>';
            return;
        }

        // ردیف‌های ذخیره‌شده (به ترتیب ذخیره) — سکشن‌های ثبت‌شده جدید در انتها اضافه می‌شوند
        $rows = $saved;
        $known = array_column($rows, 'id');
        foreach ($by_id as $id => $info) {
            if (!in_array((string) $id, $known, true)) {
                $rows[] = ['id' => (string) $id, 'enabled' => 1, 'order' => count($rows), 'fields' => []];
            }
        }

        echo '<p class="es-sections__hint">' . esc_html__('با کشیدن ⠿ ترتیب سکشن‌ها را تغییر دهید؛ سوییچ، سکشن را فعال/غیرفعال می‌کند.', 'erfansanat') . '</p>';
        echo '<div class="es-sections__list" data-es-section-rows>';

        foreach ($rows as $index => $row) {
            $id       = (string) $row['id'];
            $enabled  = (int) (bool) $row['enabled'];
            $info     = $by_id[$id] ?? ['label' => $id, 'schema' => []];
            $schema   = is_array($info['schema'] ?? null) ? $info['schema'] : [];
            $fields   = is_array($row['fields'] ?? null) ? $row['fields'] : [];

            echo '<div class="es-sections__row" data-es-section-row data-section-id="' . esc_attr($id) . '">';
            echo '<div class="es-sections__row-head">';
            echo '<span class="es-sections__drag" data-es-section-move draggable="true" title="' . esc_attr__('جابه‌جایی', 'erfansanat') . '">⠿</span>';
            printf('<input type="hidden" name="opt_sections[%d][id]" value="%s">', (int) $index, esc_attr($id));
            printf('<input type="hidden" name="opt_sections[%d][enabled]" value="0">', (int) $index);
            printf('<input type="checkbox" class="es-switch" name="opt_sections[%d][enabled]" value="1"%s>', (int) $index, $enabled ? ' checked' : '');
            echo '<span class="es-sections__label">' . esc_html((string) $info['label']) . '</span>';
            echo '<code class="es-sections__id">#' . esc_html($id) . '</code>';
            echo '<button type="button" class="es-sections__toggle-fields" data-es-section-expand>⚙</button>';
            echo '</div>';
            echo '<div class="es-sections__fields" hidden data-es-section-fields>';

            foreach ($schema as $field) {
                if (!is_array($field) || !isset($field['key'], $field['type'])) {
                    continue;
                }
                $value            = $fields[$field['key']] ?? ($field['default'] ?? '');
                $field['name']    = sprintf('opt_sections[%d][fields][%s]', (int) $index, (string) $field['key']);
                echo FieldRenderer::render($field, $value, 'opt_sections');
            }

            echo '</div></div>';
        }

        echo '</div></div>';
    }

    /**
     * ذخیره متاباکس: نانس + کاپابیلیتی + سفید‌لیست ID + sanitize اسکیم
     * (کلیدهای ناشناخته قدیمی فیلدها برای سازگاری رو به جلو حفظ می‌شوند)
     */
    public static function save(int $post_id, object $post): void {
        if (!isset($_POST['es_sections_nonce'])) {
            return;
        }
        $nonce = is_string($_POST['es_sections_nonce']) ? $_POST['es_sections_nonce'] : '';
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
        if ($post_type !== '' && !in_array($post_type, self::post_types(), true)) {
            return;
        }

        $raw = isset($_POST['opt_sections']) && is_array($_POST['opt_sections']) ? wp_unslash($_POST['opt_sections']) : [];
        $registry = self::registered_flat();
        $previous = [];
        foreach (self::get_sections($post_id) as $prev_row) {
            $previous[$prev_row['id']] = $prev_row;
        }

        // نگاشت id خالص → تعریف (جستجو بین گروه‌ها)
        $by_id = [];
        foreach ($registry as $key => $info) {
            $by_id[(string) substr((string) $key, (int) strpos((string) $key, '|') + 1)] = $info;
        }

        $out = [];
        foreach (array_values($raw) as $index => $row) {
            if (!is_array($row) || !isset($row['id']) || !is_string($row['id'])) {
                continue;
            }
            $id = $row['id'];
            if (!isset($by_id[$id])) {
                continue; // سفید‌لیست: فقط سکشن‌های ثبت‌شده
            }
            $schema    = is_array($by_id[$id]['schema'] ?? null) ? $by_id[$id]['schema'] : [];
            $fields_in = is_array($row['fields'] ?? null) ? $row['fields'] : [];
            $fields    = Sanitizer::sanitize_fields($schema, $fields_in);

            // حفظ کلیدهای قدیمی خارج اسکیم (سازگاری رو به جلو)
            $old_fields = is_array($previous[$id]['fields'] ?? null) ? $previous[$id]['fields'] : [];
            foreach ($old_fields as $old_key => $old_val) {
                if (!isset($fields[$old_key]) && !self::in_schema($schema, (string) $old_key)) {
                    $fields[$old_key] = $old_val;
                }
            }

            $out[] = [
                'id'      => $id,
                'enabled' => (int) (bool) ($row['enabled'] ?? 0),
                'order'   => count($out),
                'fields'  => $fields,
            ];
        }

        update_post_meta($post_id, self::META_KEY, $out);
    }

    private static function in_schema(array $schema, string $key): bool {
        foreach ($schema as $field) {
            if (is_array($field) && ($field['key'] ?? null) === $key) {
                return true;
            }
        }
        return false;
    }
}
