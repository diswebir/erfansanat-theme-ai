<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_project_id'] ?? get_the_ID());
if ($postId < 1) return;
$content = (string) get_post_field('post_content', $postId);
$productIds = \ErfanSanat\Theme\Helpers\LegacyMeta::getIds($postId, 'mortabet_mahsole_2');
$products = $productIds !== [] ? get_posts(['post_type' => 'product', 'post_status' => 'publish', 'post__in' => $productIds, 'orderby' => 'post__in', 'posts_per_page' => count($productIds)]) : [];
$typeIds = taxonomy_exists('project_type') ? wp_get_post_terms($postId, 'project_type', ['fields' => 'ids']) : [];
$taxQuery = is_array($typeIds) && $typeIds !== [] ? [['taxonomy' => 'project_type', 'field' => 'term_id', 'terms' => $typeIds]] : [];
$relatedProjects = get_posts(['post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => 3, 'post__not_in' => [$postId], 'orderby' => 'date', 'tax_query' => $taxQuery]);
?>
<section id="project-details" class="mt-10 rounded-3xl bg-white p-5 leading-8 text-slate-700 md:p-8"><h2 class="mb-4 text-xl font-black text-slate-900">شرح پروژه</h2><div class="prose prose-slate max-w-none text-sm leading-8"><?php echo apply_filters('the_content', $content); ?></div></section>
<?php if ($products !== []) : ?><section id="used-products" class="mt-12"><h2 class="mb-5 text-xl font-black text-slate-900">محصولات و تجهیزات استفاده‌شده در این پروژه</h2><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($products as $product) : $GLOBALS['es_card_post'] = $product; get_template_part('template-parts/cards/card-product'); endforeach; unset($GLOBALS['es_card_post']); ?></div></section><?php endif; ?>
<?php if ($relatedProjects !== []) : ?><section class="mt-12" data-related-projects><h2 class="mb-5 text-xl font-black text-slate-900">سایر پروژه‌های شاخص عرفان صنعت</h2><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($relatedProjects as $project) : $GLOBALS['es_card_post'] = $project; get_template_part('template-parts/cards/card-project'); endforeach; unset($GLOBALS['es_card_post']); ?></div></section><?php endif; ?>
