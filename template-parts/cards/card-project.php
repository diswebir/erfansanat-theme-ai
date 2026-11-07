<?php
/** Project card — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esPost = $GLOBALS['es_card_post'] ?? get_post();
if (!$esPost instanceof WP_Post) return;
$esCities = get_the_terms($esPost->ID, 'city'); $esTypes = get_the_terms($esPost->ID, 'project_type');
$esYear = (string) get_post_meta($esPost->ID, 'finish-pro-time', true);
$esTypeNames = is_array($esTypes) ? implode(',', array_map(static fn(WP_Term $term): string => $term->name, $esTypes)) : '';
$esCityNames = is_array($esCities) ? implode(',', array_map(static fn(WP_Term $term): string => $term->name, $esCities)) : '';
$esProjectViews = \ErfanSanat\Theme\Helpers\ViewCounter::get($esPost->ID);
$esProjectSearch = trim(get_the_title($esPost) . ' ' . wp_strip_all_tags(get_the_excerpt($esPost)) . ' ' . $esTypeNames . ' ' . $esCityNames);
?>
<article class="es-card-project group overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md" data-card-project="<?php echo esc_attr((string) $esPost->ID); ?>" data-project-item="true" data-project-type="<?php echo esc_attr($esTypeNames); ?>" data-project-location="<?php echo esc_attr($esCityNames); ?>" data-project-search-text="<?php echo esc_attr($esProjectSearch); ?>" data-project-views="<?php echo esc_attr((string) $esProjectViews); ?>" data-project-rating="0" data-project-date="<?php echo esc_attr((string) get_post_time('U', true, $esPost)); ?>">
<a href="<?php the_permalink($esPost); ?>" class="block overflow-hidden" aria-label="<?php echo esc_attr('پروژه: ' . get_the_title($esPost)); ?>"><?php echo has_post_thumbnail($esPost) ? get_the_post_thumbnail($esPost, 'medium_large', ['class' => 'aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105']) : '<img src="' . esc_url(es_image_uri('factory.jpg')) . '" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async" />'; ?></a>
<div class="space-y-2 p-5">
<div class="flex flex-wrap gap-1.5"><?php if (is_array($esTypes) && $esTypes) : ?><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-800"><?php echo esc_html($esTypes[0]->name); ?></span><?php endif; ?><?php if (is_array($esCities) && $esCities) : ?><span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600"><?php echo esc_html($esCities[0]->name); ?></span><?php endif; ?><?php if ($esYear !== '') : ?><span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600"><?php echo esc_html($esYear); ?></span><?php endif; ?></div>
<h3 class="text-sm font-black leading-6 text-slate-900"><a href="<?php the_permalink($esPost); ?>"><?php echo esc_html(get_the_title($esPost)); ?></a></h3>
<a class="text-xs font-extrabold text-emerald-700" href="<?php the_permalink($esPost); ?>">مشاهده پروژه ←</a>
</div></article>
