<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$query = $GLOBALS['es_blog_archive_query'] ?? $GLOBALS['wp_query'] ?? null;
if (!$query instanceof WP_Query) $query = new WP_Query(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 10]);
$term = $GLOBALS['es_blog_archive_term'] ?? null;
$heading = $term instanceof WP_Term ? $term->name : 'وبلاگ عرفان صنعت';
$categories = get_categories(['hide_empty' => true]);
$count = (int) $query->found_posts;
$perPage = 6;
$pageCount = max(1, (int) ceil($count / $perPage));
?>
<main id="primary" class="site-main"><section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" data-blog-archive="true">
<header class="mb-8 rounded-3xl bg-white p-6 md:p-8"><p class="mb-2 text-xs font-bold text-emerald-700">دانش و تجربه نورپردازی شهری</p><h1 data-blog-heading="true" class="text-2xl font-black text-slate-900 md:text-4xl"><?php echo esc_html($heading); ?></h1><p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">مقالات تخصصی طراحی، تولید، نصب و نگهداری سازه‌های نوری.</p></header>
<div class="mb-6 flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-4 md:flex-row md:items-center md:justify-between"><label class="flex-1 text-xs font-bold">جستجو در مقالات<input type="search" data-blog-search="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="عنوان یا موضوع مقاله"></label><div class="flex flex-wrap gap-2"><button type="button" data-blog-cat-btn="همه" class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white">همه</button><?php if (is_array($categories)) foreach ($categories as $category) : if ($category instanceof WP_Term) : ?><button type="button" data-blog-cat-btn="<?php echo esc_attr($category->name); ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600"><?php echo esc_html($category->name); ?></button><?php endif; endforeach; ?><button type="button" data-blog-reset="true" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold">پاک‌کردن فیلتر</button></div></div>
<div class="mb-4 text-sm text-slate-600"><strong data-blog-count="true"><?php echo esc_html(number_format_i18n($count)); ?></strong> مقاله</div>
<div data-blog-grid="true" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3"><?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); $GLOBALS['es_card_post'] = get_post(); get_template_part('template-parts/cards/card-post'); endwhile; wp_reset_postdata(); unset($GLOBALS['es_card_post']); endif; ?></div>
<div data-blog-empty="true" class="<?php echo $count > 0 ? 'hidden' : ''; ?> mt-6 rounded-2xl bg-slate-50 p-8 text-center text-sm text-slate-500">مقاله‌ای با این مشخصات پیدا نشد.</div>
<nav data-pagination-root="true" data-per-page="<?php echo esc_attr((string) $perPage); ?>" class="<?php echo $pageCount <= 1 ? 'hidden' : ''; ?> mt-8 flex items-center justify-center gap-2" aria-label="صفحه‌بندی مقالات"><button type="button" data-page-prev="true" class="rounded-xl border px-3 py-2" aria-label="صفحه قبل">قبلی</button><?php for ($page = 1; $page <= $pageCount; $page++) : ?><button type="button" data-page-btn="<?php echo esc_attr((string) $page); ?>" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"><?php echo esc_html(number_format_i18n($page)); ?></button><?php endfor; ?><button type="button" data-page-next="true" class="rounded-xl border px-3 py-2" aria-label="صفحه بعد">بعدی</button><span data-pagination-summary="true" class="sr-only"></span></nav>
</section></main>
