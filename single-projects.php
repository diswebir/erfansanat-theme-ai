<?php
/**
 * سینگل پروژه (single-projects) — برش بایت‌دقیق میرور projects/*.html
 * فرگمنت‌ها با temp/gen_p71.py تولید شده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

$es_id    = (int) get_the_ID();
$es_title = (string) get_the_title($es_id);

// خلاصه: چکیده → برش محتوا (قرارداد کارت‌ها)
$es_excerpt = (string) get_post_field('post_excerpt', $es_id);
if ($es_excerpt === '') {
    $es_excerpt = wp_trim_words(strip_tags((string) get_post_field('post_content', $es_id)), 24);
}

// ═══ ترم‌های city / project_type ═══
$es_terms_c  = get_the_terms($es_id, 'city');
$es_city     = (is_array($es_terms_c) && isset($es_terms_c[0]) && is_object($es_terms_c[0])) ? $es_terms_c[0] : null;
$es_city_slug = $es_city !== null ? (string) $es_city->slug : '';
$es_terms_t  = get_the_terms($es_id, 'project_type');
$es_type     = (is_array($es_terms_t) && isset($es_terms_t[0]) && is_object($es_terms_t[0])) ? $es_terms_t[0] : null;
$es_type_name = $es_type !== null ? (string) $es_type->name : '';
$es_city_name = $es_city !== null ? (string) $es_city->name : '';

$es_term_link = static function (?object $term): string {
    if ($term === null) {
        return '';
    }
    $es_link = get_term_link($term);
    return is_wp_error($es_link) ? '' : (string) $es_link;
};

$es_args_common = [
    'title'     => $es_title,
    'type_url'  => $es_term_link($es_type),
    'type_name' => $es_type_name,
    'city_url'  => $es_term_link($es_city),
    'city_name' => $es_city_name,
];

echo '<main class="bg-slate-50/50">';

// ═══ هیرو ═══
ob_start();
es_get_template('template-parts/single-project/hero.php', array_merge($es_args_common, [
    'badge' => trim($es_type_name . ' · ' . $es_city_name, ' ·'),
    'desc'  => $es_excerpt,
]));
echo (string) ob_get_clean();

// ═══ گالری شش‌تایی (3.12) + سایدبار ═══
$es_gal_ids = class_exists(\ErfanSanat\Theme\Frontend\ProjectGallery::class)
    ? \ErfanSanat\Theme\Frontend\ProjectGallery::get_gallery($es_id) : [];
$es_gal_fallback = es_media_url('assets/images/light-sphere.jpg');
$es_imgs = [];
for ($es_i = 0; $es_i < 5; $es_i++) {
    $es_imgs[] = isset($es_gal_ids[$es_i]) && (int) $es_gal_ids[$es_i] > 0
        ? es_media_url((int) $es_gal_ids[$es_i]) : $es_gal_fallback;
}
ob_start();
es_get_template('template-parts/single-project/gallery.php', array_merge($es_args_common, [
    'imgs'     => $es_imgs,
    'chip'     => $es_type_name,
    'client'   => (string) get_post_meta($es_id, 'es_client', true),
    'duration' => (string) get_post_meta($es_id, 'es_duration', true),
]));
echo (string) ob_get_clean();

// ═══ تب‌ها: درباره (the_content) + شناسنامه + فرآیند ═══
ob_start();
es_get_template('template-parts/single-project/tabs.php', array_merge($es_args_common, [
    'content'   => (string) apply_filters('the_content', (string) get_post_field('post_content', $es_id)),
    'client'    => (string) get_post_meta($es_id, 'es_client', true),
    'duration'  => (string) get_post_meta($es_id, 'es_duration', true),
    'completed' => (string) get_post_meta($es_id, 'finish-pro-time', true),
    'standard'  => (string) get_post_meta($es_id, 'es_standard', true),
]));
echo (string) ob_get_clean();

// ═══ محصولات پروژه (mortabet_mahsole_2 → 3.12) ═══
$es_prod_ids = class_exists(\ErfanSanat\Theme\Frontend\ProjectGallery::class)
    ? \ErfanSanat\Theme\Frontend\ProjectGallery::get_products($es_id) : [];
$es_customs = ['0', '0.06', '0.12', '0.18']; // اتریبیوت design میرور
$es_cards = '';
foreach ($es_prod_ids as $es_k => $es_pid) {
    $es_pid  = (int) $es_pid;
    $es_pexc = (string) get_post_field('post_excerpt', $es_pid);
    if ($es_pexc === '') {
        $es_pexc = wp_trim_words(strip_tags((string) get_post_field('post_content', $es_pid)), 22);
    }
    // تصویر: شاخص → گالری محصول → placeholder میرور (قرارداد card-product)
    $es_thumb = (int) get_post_meta($es_pid, '_thumbnail_id', true);
    if ($es_thumb < 1) {
        $es_gal = (string) get_post_meta($es_pid, '_product_image_gallery', true);
        if ($es_gal === '') {
            $es_gal = (string) get_post_meta($es_pid, 'galerry_produ', true);
        }
        $es_first  = trim(explode(',', $es_gal)[0] ?? '');
        $es_thumb  = ctype_digit($es_first) && (int) $es_first > 0 ? (int) $es_first : 0;
    }
    $es_pterms = get_the_terms($es_pid, 'product_cat');
    ob_start();
    es_get_template('template-parts/single-project/product-card.php', [
        'custom' => $es_customs[$es_k] ?? (string) $es_k,
        'url'   => (string) get_permalink($es_pid),
        'img'   => es_media_url($es_thumb > 0 ? $es_thumb : 'assets/images/light-tunnel.jpg'),
        'title' => (string) get_the_title($es_pid),
        'chip'  => (is_array($es_pterms) && isset($es_pterms[0]) && is_object($es_pterms[0])) ? (string) $es_pterms[0]->name : '',
        'desc'  => $es_pexc,
    ]);
    $es_cards .= (string) ob_get_clean();
}
ob_start();
es_get_template('template-parts/single-project/products.php', ['cards' => $es_prod_ids, 'cards_html' => $es_cards]);
echo (string) ob_get_clean();

// ═══ پروژه‌های مرتبط: هم‌city یا هم‌project_type (کوئری سبک) ═══
$es_tax = [];
if ($es_city_slug !== '') {
    $es_tax[] = ['taxonomy' => 'city', 'field' => 'slug', 'terms' => [$es_city_slug]];
}
if ($es_type !== null) {
    $es_tax[] = ['taxonomy' => 'project_type', 'field' => 'term_id', 'terms' => [(int) $es_type->term_id]];
}
$es_rel = $es_tax === [] ? [] : (array) get_posts([
    'post_type'      => 'projects',
    'posts_per_page' => 3,
    'post__not_in'   => [$es_id],
    'tax_query'      => array_merge(['relation' => 'OR'], $es_tax),
]);
$es_rel_html = '';
foreach ($es_rel as $es_rp) {
    $es_rid  = (int) ($es_rp->ID ?? 0);
    $es_rexc = (string) get_post_field('post_excerpt', $es_rid);
    if ($es_rexc === '') {
        $es_rexc = wp_trim_words(strip_tags((string) get_post_field('post_content', $es_rid)), 20);
    }
    $es_rterms_t = get_the_terms($es_rid, 'project_type');
    $es_rterms_c = get_the_terms($es_rid, 'city');
    ob_start();
    es_get_template('template-parts/single-project/related-card.php', [
        'url'   => (string) get_permalink($es_rid),
        'img'   => es_media_url((int) get_post_meta($es_rid, '_thumbnail_id', true) > 0
            ? (int) get_post_meta($es_rid, '_thumbnail_id', true) : 'assets/images/light-element.jpg'),
        'title' => (string) get_the_title($es_rid),
        'chip'  => (is_array($es_rterms_t) && isset($es_rterms_t[0]) && is_object($es_rterms_t[0])) ? (string) $es_rterms_t[0]->name : '',
        'desc'  => $es_rexc,
        'city'  => (is_array($es_rterms_c) && isset($es_rterms_c[0]) && is_object($es_rterms_c[0])) ? (string) $es_rterms_c[0]->name : '',
    ]);
    $es_rel_html .= (string) ob_get_clean();
}
ob_start();
es_get_template('template-parts/single-project/related.php', ['cards_html' => $es_rel_html]);
echo (string) ob_get_clean();

echo '</main>';
get_footer();
