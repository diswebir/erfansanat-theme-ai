<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$ids = \ErfanSanat\Theme\Helpers\LegacyMeta::getIds($postId, 'project-ids');
if ($ids === []) return;
$projects = get_posts(['post_type' => 'projects', 'post_status' => 'publish', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => count($ids)]);
if ($projects === []) return;
?>
<section class="mt-12" data-related-projects><div class="mb-5 flex items-end justify-between"><h2 class="text-xl font-black text-slate-900">پروژه‌های اجراشده با این محصول</h2><a href="<?php echo esc_url(get_post_type_archive_link('projects')); ?>" class="text-xs font-bold text-emerald-700">همه پروژه‌ها</a></div><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($projects as $project) : $GLOBALS['es_card_post'] = $project; get_template_part('template-parts/cards/card-project'); endforeach; unset($GLOBALS['es_card_post']); ?></div></section>
