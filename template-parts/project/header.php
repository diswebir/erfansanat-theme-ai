<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_project_id'] ?? get_the_ID());
if ($postId < 1) return;
$title = get_the_title($postId);
$summary = trim((string) get_post_field('post_excerpt', $postId));
if ($summary === '') $summary = wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $postId)), 35);
$typeTerms = get_the_terms($postId, 'project_type');
$cityTerms = get_the_terms($postId, 'city');
?>
<header class="mb-6 space-y-4" data-project-header>
    <nav class="text-xs font-medium text-slate-500" aria-label="breadcrumb"><ol class="flex flex-wrap items-center gap-2"><li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700">خانه</a></li><li aria-hidden="true">/</li><li><a href="<?php echo esc_url(get_post_type_archive_link('projects')); ?>" class="hover:text-emerald-700">پروژه‌ها</a></li><?php if (is_array($typeTerms) && $typeTerms !== []) : ?><li aria-hidden="true">/</li><li><?php echo esc_html($typeTerms[0]->name); ?></li><?php endif; ?><li aria-hidden="true">/</li><li aria-current="page" class="font-bold text-slate-800"><?php echo esc_html($title); ?></li></ol></nav>
    <div class="flex flex-wrap gap-2"><?php if (is_array($typeTerms)) foreach ($typeTerms as $term) : ?><a href="<?php echo esc_url(get_term_link($term)); ?>" class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800"><?php echo esc_html($term->name); ?></a><?php endforeach; ?><?php if (is_array($cityTerms)) foreach ($cityTerms as $term) : ?><a href="<?php echo esc_url(get_term_link($term)); ?>" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700"><?php echo esc_html($term->name); ?></a><?php endforeach; ?></div>
    <h1 class="text-2xl font-black leading-tight text-slate-900 md:text-4xl"><?php echo esc_html($title); ?></h1>
    <?php if ($summary !== '') : ?><p class="max-w-4xl text-sm leading-7 text-slate-600"><?php echo esc_html($summary); ?></p><?php endif; ?>
</header>
