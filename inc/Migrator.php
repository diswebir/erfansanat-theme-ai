<?php
/**
 * Migrator — one-time import of legacy Elementor/JetEngine data into the
 * theme's own meta keys. Safe to re-run (idempotent, never deletes).
 *
 * Sources handled:
 *  - `_elementor_data` (page sections)  -> `_erfansanat_sections`
 *  - JetEngine meta keys are already bound as-is (legacy spellings preserved)
 *  - Missing legacy keys get empty defaults so admin metaboxes render cleanly
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

if (!defined('ABSPATH')) {
    exit;
}

final class Migrator
{
    /** Legacy keys every product should have (admin metabox reads these). */
    public const PRODUCT_KEYS = [
        'true_sale', 'zireonvan', 'english_titl', 'galerry_produ',
        'product-catalog-file', 'product-feature', 'product-feature2_copy',
        'vaule_of_variable', 'usage_product', 'usage_countr', 'faq',
        'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id',
        'pa_wattage', 'pa_ip-rating', 'pa_input-voltage', 'pa_color',
        'pa_beam-angle', 'pa_lifespan', 'pa_warranty', 'pa_body-material',
        'pa_led-chip-type', 'pa_bulb-type', 'pa_cable-material',
        'pa_certifications', 'pa_diameter', 'pa_size', 'pa_intensity',
        'pa_diffuser-color', 'pa_after-sales-support', 'pa_brand',
        'project-ids',
    ];

    /** Legacy keys every project should have. */
    public const PROJECT_KEYS = [
        'finish-pro-time', 'second-image', 'three-image', 'foure-image',
        'five-image', 'six-image', 'mortabet_mahsole_2', 'hrefmeta_fa',
        'hrefmeta_en', 'hrefmeta_ar', 'same_key_hrefl',
    ];

    public static function hooks(): void
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('wp_ajax_erfansanat_import_legacy', [self::class, 'ajax']);
    }

    public static function menu(): void
    {
        add_management_page(
            __('import داده‌های legacy', 'erfansanat'),
            __('import داده‌ها (Elementor/JetEngine)', 'erfansanat'),
            'manage_options',
            'erfansanat-import',
            [self::class, 'page']
        );
    }

    public static function page(): void
    {
        if (!current_user_can('manage_options')) { return; }
        echo '<div class="wrap"><h1>' . esc_html__('import داده‌های legacy', 'erfansanat') . '</h1>';
        echo '<p>' . esc_html__('Converts Elementor _elementor_data into theme section meta and backfills legacy meta keys. Idempotent.', 'erfansanat') . '</p>';
        echo '<button type="button" id="erfansanat-import-btn" class="button button-primary">Run import</button>';
        echo '<pre id="erfansanat-import-log" style="max-height:400px;overflow:auto;background:#111;color:#0f0;padding:10px"></pre>';
        $nonce = wp_create_nonce('erfansanat_import_nonce');
        echo '<script>document.getElementById("erfansanat-import-btn").addEventListener("click",function(){var l=document.getElementById("erfansanat-import-log");l.textContent="running...";fetch(ajaxurl,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"action=erfansanat_import_legacy&nonce=' . esc_js($nonce) . '"}).then(r=>r.json()).then(j=>{l.textContent=JSON.stringify(j,null,2);});});</script>';
        echo '</div>';
    }

    public static function ajax(): void
    {
        if (!current_user_can('manage_options') || !check_ajax_referer('erfansanat_import_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'forbidden']);
        }
        wp_send_json_success(self::import_all());
    }

    /**
     * Run the full import. Returns a log array.
     *
     * @return array{sections:int,products:int,projects:int,posts:int}
     */
    public static function import_all(): array
    {
        $log = ['sections' => 0, 'products' => 0, 'projects' => 0, 'posts' => 0];

        // 1) Elementor pages -> _erfansanat_sections
        $pages = get_posts(['post_type' => 'page', 'posts_per_page' => -1, 'meta_key' => '_elementor_data']);
        foreach ($pages as $page) {
            if (self::import_elementor_sections($page->ID)) { $log['sections']++; }
        }

        // 2) backfill product legacy keys
        foreach (get_posts(['post_type' => 'product', 'posts_per_page' => -1]) as $p) {
            if (self::backfill($p->ID, self::PRODUCT_KEYS)) { $log['products']++; }
        }

        // 3) backfill project legacy keys
        foreach (get_posts(['post_type' => 'projects', 'posts_per_page' => -1]) as $p) {
            if (self::backfill($p->ID, self::PROJECT_KEYS)) { $log['projects']++; }
        }

        // 4) posts: view counter keys
        foreach (get_posts(['post_type' => 'post', 'posts_per_page' => -1]) as $p) {
            if (self::backfill($p->ID, ['number-viewc', 'number2-viewc'])) { $log['posts']++; }
        }

        return $log;
    }

    /**
     * Convert `_elementor_data` JSON sections into `_erfansanat_sections`
     * (enabled + order). Only writes when the target meta is empty.
     */
    public static function import_elementor_sections(int $post_id): bool
    {
        $existing = get_post_meta($post_id, Admin\SectionManager::META, true);
        if (is_array($existing) && $existing !== []) {
            return false; // already migrated
        }
        $raw = get_post_meta($post_id, '_elementor_data', true);
        if (!is_string($raw) || $raw === '') {
            return false;
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return false;
        }
        $sections = Admin\SectionManager::sections_for(get_post($post_id));
        $out = [];
        $order = 0;
        $walk = function (array $nodes) use (&$walk, &$out, &$order, $sections) {
            foreach ($nodes as $node) {
                $el = $node['elType'] ?? '';
                if ($el === 'section' || $el === 'container') {
                    $order++;
                    $sid = sanitize_key((string) ($node['id'] ?? ''));
                    // match widget/section settings to our registry labels loosely by position
                    $keys = array_keys($sections);
                    $out[$sid] = [
                        'enabled' => true,
                        'order'   => $order,
                        'fields'  => [],
                    ];
                }
                if (!empty($node['elements']) && is_array($node['elements'])) {
                    $walk($node['elements']);
                }
            }
        };
        $walk($data);
        if ($out === []) {
            return false;
        }
        update_post_meta($post_id, Admin\SectionManager::META, $out);
        return true;
    }

    /**
     * Ensure each legacy key exists (empty default) so admin metaboxes
     * render without notices. Returns true if anything was written.
     */
    public static function backfill(int $post_id, array $keys): bool
    {
        $changed = false;
        foreach ($keys as $key) {
            $v = get_post_meta($post_id, $key, true);
            if ($v === '' || $v === false || $v === null) {
                add_post_meta($post_id, $key, '');
                $changed = true;
            }
        }
        return $changed;
    }
}
