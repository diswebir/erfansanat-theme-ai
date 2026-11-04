<?php
/** Template Name: خدمت تخصصی */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$postId = (int) get_the_ID();
$parentId = (int) get_post_field('post_parent', $postId);
$defaults = [
    'service-hero' => ['title' => get_the_title($postId), 'body' => (string) get_the_excerpt($postId), 'order' => 0],
    'service-details' => ['title' => 'جزئیات خدمت', 'body' => '', 'items' => [], 'projects' => [], 'products' => [], 'order' => 1],
    'service-contact' => ['title' => 'برای این خدمت مشاوره بگیرید', 'body' => 'نیاز پروژه خود را با کارشناسان ما در میان بگذارید.', 'order' => 2],
];
$byType = [];
foreach (es_get_sections($postId) as $section) if (isset($section['type'], $defaults[$section['type']])) $byType[$section['type']] = $section;
$sections = [];
foreach ($defaults as $type => $fields) {
    $section = $byType[$type] ?? ['type' => $type, 'enabled' => true, 'order' => $fields['order'], 'fields' => []];
    $section['fields'] = array_merge($fields, is_array($section['fields'] ?? null) ? $section['fields'] : []);
    $sections[] = $section;
}
usort($sections, static fn(array $a, array $b): int => (int) ($a['order'] ?? 0) <=> (int) ($b['order'] ?? 0));
$detailFields = is_array($byType['service-details']['fields'] ?? null) ? array_merge($defaults['service-details'], $byType['service-details']['fields']) : $defaults['service-details'];
$projectIds = is_array($detailFields['projects'] ?? null) ? array_values(array_filter(array_map('absint', $detailFields['projects']))) : [];
$productIds = is_array($detailFields['products'] ?? null) ? array_values(array_filter(array_map('absint', $detailFields['products']))) : [];
$relatedProjects = $projectIds !== [] ? get_posts(['post_type' => 'projects', 'post_status' => 'publish', 'post__in' => $projectIds, 'orderby' => 'post__in', 'posts_per_page' => count($projectIds)]) : [];
$relatedProducts = $productIds !== [] ? get_posts(['post_type' => 'product', 'post_status' => 'publish', 'post__in' => $productIds, 'orderby' => 'post__in', 'posts_per_page' => count($productIds)]) : [];
?>
<main id="primary" class="site-main mx-auto max-w-7xl space-y-7 px-4 py-8 sm:px-6 lg:px-8" data-service-page="true">
<?php foreach ($sections as $section) : if (empty($section['enabled'])) continue; $type = (string) $section['type']; $fields = $section['fields']; ?>
<?php if ($type === 'service-hero') : ?><section data-service-hero="true" class="rounded-3xl bg-slate-900 p-7 text-white md:p-11"><nav aria-label="breadcrumb" class="mb-5 text-xs text-slate-300"><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-white">خانه</a><span class="mx-2">/</span><?php if ($parentId > 0) : ?><a href="<?php echo esc_url(get_permalink($parentId)); ?>" class="hover:text-white"><?php echo esc_html(get_the_title($parentId)); ?></a><span class="mx-2">/</span><?php endif; ?><span aria-current="page"><?php echo esc_html((string) $fields['title']); ?></span></nav><p class="text-xs font-bold text-emerald-300">خدمات تخصصی نورپردازی</p><h1 class="mt-3 text-3xl font-black leading-relaxed md:text-5xl"><?php echo esc_html((string) $fields['title']); ?></h1><?php if (trim((string) $fields['body']) !== '') : ?><div class="mt-4 max-w-3xl text-sm leading-8 text-slate-200"><?php echo wp_kses_post((string) $fields['body']); ?></div><?php endif; ?></section>
<?php elseif ($type === 'service-details') : ?><section data-service-details="true" class="rounded-3xl bg-white p-6 md:p-9"><h2 class="text-2xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><?php if (trim((string) $fields['body']) !== '') : ?><div class="mt-3 text-sm leading-8 text-slate-600"><?php echo wp_kses_post((string) $fields['body']); ?></div><?php endif; ?><?php if (is_array($fields['items'] ?? null) && $fields['items'] !== []) : ?><ul class="mt-5 grid gap-3 sm:grid-cols-2"><?php foreach ($fields['items'] as $item) if (is_scalar($item) && trim((string) $item) !== '') : ?><li class="rounded-xl bg-slate-50 p-4 text-sm font-bold text-slate-700"><?php echo esc_html((string) $item); ?></li><?php endif; ?></ul><?php endif; ?>
<?php if ($relatedProjects !== []) : ?><div class="mt-8"><h3 class="mb-4 text-lg font-black text-slate-900">پروژه‌های مرتبط</h3><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($relatedProjects as $related) : $GLOBALS['es_card_post'] = $related; get_template_part('template-parts/cards/card-project'); endforeach; unset($GLOBALS['es_card_post']); ?></div></div><?php endif; ?>
<?php if ($relatedProducts !== []) : ?><div class="mt-8"><h3 class="mb-4 text-lg font-black text-slate-900">محصولات مرتبط</h3><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($relatedProducts as $related) : $GLOBALS['es_card_post'] = $related; get_template_part('template-parts/cards/card-product'); endforeach; unset($GLOBALS['es_card_post']); ?></div></div><?php endif; ?></section>
<?php elseif ($type === 'service-contact') : ?><section data-service-contact="true" class="rounded-3xl bg-emerald-50 p-6 text-center md:p-9"><h2 class="text-2xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><p class="mt-3 text-sm leading-7 text-slate-600"><?php echo esc_html((string) $fields['body']); ?></p><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">درخواست مشاوره برای این خدمت</a></section><?php endif; ?>
<?php endforeach; ?>
</main>
<?php get_footer(); ?>
