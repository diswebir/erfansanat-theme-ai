<?php
/** Home 13 Latest posts — sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esSection = is_array($GLOBALS['es_section'] ?? null) ? $GLOBALS['es_section'] : [];
$esF = is_array($esSection['fields'] ?? null) ? $esSection['fields'] : [];
$esField = static fn(string $k, string $d = ''): string => isset($esF[$k]) && is_scalar($esF[$k]) ? (string) $esF[$k] : $d;

$esQ = new WP_Query(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3]);
?>
<section id="blog-preview" class="mx-auto max-w-[1300px] px-5 pt-20 lg:px-8"><div class="flex flex-wrap items-end justify-between gap-4"><h2 class="text-2xl font-black text-slate-900 sm:text-3xl"><?php echo esc_html($esField('title', 'جدیدترین مقالات و راهنماهای تخصصی نورپردازی')); ?></h2><a href="<?php echo esc_url(home_url('/blog/')); ?>" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-extrabold text-emerald-800">همه مقالات ←</a></div>
<div class="mt-8 grid gap-5 md:grid-cols-3" data-home-latest-posts><?php while ($esQ->have_posts()) : $esQ->the_post(); $GLOBALS['es_card_post'] = get_post(); get_template_part('template-parts/cards/card-post'); endwhile; wp_reset_postdata(); unset($GLOBALS['es_card_post']); ?></div></section>
