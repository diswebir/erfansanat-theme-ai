<?php
/** Home 07 Featured products — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esQ = post_type_exists('product') ? new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 8]) : null;
?>
<section id="products" class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="flex flex-wrap items-end justify-between gap-4"><div><span class="text-xs font-extrabold text-emerald-700">فروشگاه</span><h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl"><?php echo esc_html($esField('title', 'منتخب محصولات عرفان صنعت اصفهان')); ?></h2></div><a href="<?php echo esc_url(home_url('/shop/')); ?>" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-extrabold text-emerald-800">همه محصولات ←</a></div>
<div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-home-featured-products><?php if ($esQ) : while ($esQ->have_posts()) : $esQ->the_post(); $GLOBALS['es_card_post'] = get_post(); get_template_part('template-parts/cards/card-product'); endwhile; wp_reset_postdata(); unset($GLOBALS['es_card_post']); endif; ?></div></section>
