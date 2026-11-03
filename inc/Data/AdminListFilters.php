<?php
/**
 * فیلترها و ستون‌های ادمین لیست پروژه‌ها — دراپ‌داون city/project_type + ستون same_key_hrefl
 *
 * همه فقط در زمینه ادمین اجرا می‌شوند؛ روی فرانت اثری ندارند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

final class AdminListFilters {

    /** کلیدهای GET مجاز برای فیلتر */
    private const TAX_FILTERS = ['city', 'project_type'];

    public static function init(): void {
        if (is_admin()) {
            add_action('restrict_manage_posts', [self::class, 'render_filters']);
            add_action('parse_query', [self::class, 'apply_tax_filter']);
            add_filter('manage_projects_posts_columns', [self::class, 'columns']);
            add_action('manage_projects_posts_custom_column', [self::class, 'column_content'], 10, 2);
            add_filter('manage_edit-projects_sortable_columns', [self::class, 'sortable_columns']);
            add_action('pre_get_posts', [self::class, 'orderby_meta']);
        }
    }

    /** دراپ‌داون‌های فیلتر بالای لیست پروژه‌ها */
    public static function render_filters(string $post_type): void {
        if ($post_type !== 'projects') {
            return;
        }
        foreach (self::TAX_FILTERS as $taxonomy) {
            $terms   = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
            $current = isset($_GET[$taxonomy]) && is_string($_GET[$taxonomy]) ? sanitize_key($_GET[$taxonomy]) : '';

            printf('<select name="%1$s" class="postform"><option value="">%2$s</option>', esc_attr($taxonomy), esc_html__('همه', 'erfansanat'));
            foreach ((array) $terms as $term) {
                if (!is_object($term) || !isset($term->slug)) {
                    continue;
                }
                printf(
                    '<option value="%1$s"%2$s>%3$s</option>',
                    esc_attr((string) $term->slug),
                    selected($current, (string) $term->slug, false),
                    esc_html((string) ($term->name ?? $term->slug))
                );
            }
            echo '</select>';
        }
    }

    /** اعمال فیلتر تکسونومی روی کوئری لیست ادمین */
    public static function apply_tax_filter(object $query): void {
        if (!is_admin()) {
            return;
        }
        $post_type = $query->get('post_type');
        if ($post_type !== 'projects') {
            return;
        }

        $tax_query = [];
        foreach (self::TAX_FILTERS as $taxonomy) {
            $value = isset($_GET[$taxonomy]) && is_string($_GET[$taxonomy]) ? sanitize_key($_GET[$taxonomy]) : '';
            if ($value === '') {
                continue;
            }
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $value,
            ];
        }

        if ($tax_query !== []) {
            $existing = $query->get('tax_query');
            $merged   = is_array($existing) && $existing !== [] ? array_merge((array) $existing, $tax_query) : $tax_query;
            $query->set('tax_query', $merged);
        }
    }

    /** ستون‌های لیست پروژه‌ها: محل، نوع، کلید hreflang */
    public static function columns(array $columns): array {
        $new = [];
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ($key === 'title') {
                $new['es-city']        = __('محل پروژه', 'erfansanat');
                $new['es-project_type'] = __('نوع پروژه', 'erfansanat');
                $new['es-hrefkey']     = __('کلید hreflang', 'erfansanat');
            }
        }
        return $new;
    }

    /** محتوای ستون‌ها — escaping کامل */
    public static function column_content(string $column, int $post_id): void {
        switch ($column) {
            case 'es-city':
            case 'es-project_type':
                $taxonomy = str_replace('es-', '', $column);
                $terms    = get_the_terms($post_id, $taxonomy);
                if (!is_array($terms) || $terms === []) {
                    echo '—';
                    break;
                }
                $names = [];
                foreach ($terms as $term) {
                    if (is_object($term) && isset($term->name)) {
                        $names[] = (string) $term->name;
                    }
                }
                echo esc_html(implode('، ', $names));
                break;

            case 'es-hrefkey':
                $key = (string) get_post_meta($post_id, 'same_key_hrefl', true);
                echo $key !== '' ? '<code>' . esc_html($key) . '</code>' : '—';
                break;
        }
    }

    /** ستون کلید hreflang قابل مرتب‌سازی */
    public static function sortable_columns(array $columns): array {
        $columns['es-hrefkey'] = 'same_key_hrefl';
        return $columns;
    }

    /** اعمال مرتب‌سازی متا روی کوئری ادمین */
    public static function orderby_meta(object $query): void {
        if (!is_admin()) {
            return;
        }
        if (($query->get('orderby')) !== 'same_key_hrefl') {
            return;
        }
        $query->set('meta_key', 'same_key_hrefl');
        $query->set('orderby', 'meta_value');
    }
}
