<?php
/** Post card — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esPost = $GLOBALS['es_card_post'] ?? get_post();
if (!$esPost instanceof WP_Post) return;
$esCats = get_the_category($esPost->ID);
$esBlogCategory = is_array($esCats) && isset($esCats[0]) ? (string) $esCats[0]->name : '';
$esBlogSearch = trim(get_the_title($esPost) . ' ' . wp_strip_all_tags(get_the_excerpt($esPost)) . ' ' . $esBlogCategory);
?>
<article class="es-card-post group overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md" data-card-post="<?php echo esc_attr((string) $esPost->ID); ?>" data-blog-item="true" data-blog-category="<?php echo esc_attr($esBlogCategory); ?>" data-blog-search-text="<?php echo esc_attr($esBlogSearch); ?>">
<a href="<?php the_permalink($esPost); ?>" class="block overflow-hidden" aria-label="<?php echo esc_attr('مقاله: ' . get_the_title($esPost)); ?>"><?php echo has_post_thumbnail($esPost) ? get_the_post_thumbnail($esPost, 'medium_large', ['class' => 'aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105']) : '<img src="' . esc_url(es_image_uri('sun-light.jpg')) . '" alt="" class="aspect-[16/10] w-full object-cover" loading="lazy" decoding="async" />'; ?></a>
<div class="space-y-2 p-5">
<div class="flex items-center gap-2 text-[11px] font-bold text-slate-600"><?php if ($esCats) : ?><span class="text-emerald-700"><?php echo esc_html($esCats[0]->name); ?></span><span>·</span><?php endif; ?><time datetime="<?php echo esc_attr(get_the_date('c', $esPost)); ?>"><?php echo esc_html(get_the_date('', $esPost)); ?></time></div>
<h3 class="text-sm font-black leading-6 text-slate-900"><a href="<?php the_permalink($esPost); ?>"><?php echo esc_html(get_the_title($esPost)); ?></a></h3>
<p class="text-xs leading-6 text-slate-500"><?php echo esc_html(wp_trim_words((string) get_the_excerpt($esPost), 18)); ?></p>
</div></article>
