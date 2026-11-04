<?php
/** Product card — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esPost = $GLOBALS['es_card_post'] ?? get_post();
if (!$esPost instanceof WP_Post) return;
$esSale = (string) get_post_meta($esPost->ID, 'true_sale', true);
$esPrice = (string) get_post_meta($esPost->ID, '_price', true);
$esCats = get_the_terms($esPost->ID, taxonomy_exists('product_cat') ? 'product_cat' : 'category');
$esProduct = function_exists('wc_get_product') ? wc_get_product($esPost->ID) : null;
$esPriceValue = $esProduct instanceof \WC_Product ? (float) $esProduct->get_price() : (float) $esPrice;
$esPurchasable = $esSale !== 'false';
$esRating = $esProduct instanceof \WC_Product ? (float) $esProduct->get_average_rating() : (float) get_post_meta($esPost->ID, '_wc_average_rating', true);
$esViews = (int) get_post_meta($esPost->ID, 'number2-viewc', true);
if ($esViews <= 0) $esViews = (int) get_post_meta($esPost->ID, 'number-viewc', true);
$esIpTerms = taxonomy_exists('pa_ip-rating') ? get_the_terms($esPost->ID, 'pa_ip-rating') : false;
$esVoltageTerms = taxonomy_exists('pa_input-voltage') ? get_the_terms($esPost->ID, 'pa_input-voltage') : false;
$esIp = is_array($esIpTerms) ? implode(',', array_map(static fn(WP_Term $t): string => $t->name, $esIpTerms)) : '';
$esVoltage = is_array($esVoltageTerms) ? implode(',', array_map(static fn(WP_Term $t): string => $t->name, $esVoltageTerms)) : '';
$esSearch = trim(get_the_title($esPost) . ' ' . wp_strip_all_tags(get_the_excerpt($esPost)) . ' ' . $esIp . ' ' . $esVoltage);
$esAttributeMap = [];
foreach (\ErfanSanat\Theme\Filters\FilterRegistry::attributes() as $esTaxonomy => $_label) {
    if (!taxonomy_exists($esTaxonomy)) continue;
    $esTerms = get_the_terms($esPost->ID, $esTaxonomy);
    if (is_array($esTerms) && $esTerms !== []) $esAttributeMap[$esTaxonomy] = array_map(static fn(WP_Term $term): string => $term->slug, $esTerms);
}
?>
<article class="es-card-product group overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md" data-card-product="<?php echo esc_attr((string) $esPost->ID); ?>" data-product-item="<?php echo esc_attr((string) $esPost->ID); ?>" data-product-purchasable="<?php echo $esPurchasable ? 'true' : 'false'; ?>" data-product-price="<?php echo esc_attr((string) $esPriceValue); ?>" data-product-rating="<?php echo esc_attr((string) $esRating); ?>" data-product-views="<?php echo esc_attr((string) $esViews); ?>" data-product-ip="<?php echo esc_attr($esIp); ?>" data-product-voltage="<?php echo esc_attr($esVoltage); ?>" data-product-search-text="<?php echo esc_attr($esSearch); ?>" data-product-attributes="<?php echo esc_attr((string) wp_json_encode($esAttributeMap, JSON_UNESCAPED_UNICODE)); ?>">
<a href="<?php the_permalink($esPost); ?>" class="block overflow-hidden"><?php echo has_post_thumbnail($esPost) ? get_the_post_thumbnail($esPost, 'medium_large', ['class' => 'aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105']) : '<img src="' . esc_url(es_image_uri('hero.jpg')) . '" alt="" class="aspect-[4/3] w-full object-cover" />'; ?></a>
<div class="space-y-3 p-5">
<?php if (is_array($esCats) && $esCats) : ?><span class="text-[11px] font-extrabold text-emerald-700"><?php echo esc_html($esCats[0]->name); ?></span><?php endif; ?>
<h3 class="text-sm font-black leading-6 text-slate-900"><a href="<?php the_permalink($esPost); ?>"><?php echo esc_html(get_the_title($esPost)); ?></a></h3>
<?php if ($esSale === 'false') : ?><div class="flex items-center justify-between"><span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-700">استعلام قیمت</span><a class="text-xs font-extrabold text-emerald-700" href="<?php the_permalink($esPost); ?>">درخواست پیش‌فاکتور</a></div>
<?php else : ?><div class="flex items-center justify-between"><span class="text-sm font-black text-slate-900"><?php echo $esPrice !== '' ? esc_html(number_format_i18n($esPriceValue) . ' تومان') : ''; ?></span><a class="rounded-xl bg-emerald-700 px-3 py-2 text-[11px] font-extrabold text-white" href="<?php the_permalink($esPost); ?>">مشاهده محصول</a></div><?php endif; ?>
</div></article>
