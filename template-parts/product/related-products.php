<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$relatedIds = function_exists('wc_get_related_products') ? wc_get_related_products($postId, 4, [$postId]) : [];
if (!is_array($relatedIds) || $relatedIds === []) {
    $termIds = taxonomy_exists('product_cat') ? wp_get_post_terms($postId, 'product_cat', ['fields' => 'ids']) : [];
    $related = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 4, 'post__not_in' => [$postId], 'orderby' => 'date', 'tax_query' => $termIds ? [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $termIds]] : []]);
    $relatedIds = array_map(static fn(WP_Post $post): int => (int) $post->ID, $related);
}
if ($relatedIds === []) return;
$relatedPosts = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'post__in' => array_map('intval', $relatedIds), 'orderby' => 'post__in', 'posts_per_page' => 4]);
if ($relatedPosts === []) return;
?>
<section class="mt-12" data-related-products><div class="mb-5 flex items-end justify-between"><h2 class="text-xl font-black text-slate-900">محصولات مشابه و مکمل</h2><a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>" class="text-xs font-bold text-emerald-700">مشاهده همه محصولات</a></div><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><?php foreach ($relatedPosts as $relatedPost) : $GLOBALS['es_card_post'] = $relatedPost; get_template_part('template-parts/cards/card-product'); endforeach; unset($GLOBALS['es_card_post']); ?></div></section>
