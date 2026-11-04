<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$query = $GLOBALS['es_project_archive_query'] ?? $GLOBALS['wp_query'] ?? null;
if (!$query instanceof WP_Query) $query = new WP_Query(['post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => 12]);
$types = get_terms(['taxonomy' => 'project_type', 'hide_empty' => true]);
$cities = get_terms(['taxonomy' => 'city', 'hide_empty' => true]);
$count = (int) $query->found_posts;
$term = $GLOBALS['es_project_archive_term'] ?? null;
$initialType = $term instanceof WP_Term && $term->taxonomy === 'project_type' ? $term->name : 'همه';
$initialLocation = $term instanceof WP_Term && $term->taxonomy === 'city' ? $term->name : 'همه';
$title = $term instanceof WP_Term ? ($term->taxonomy === 'city' ? 'پروژه‌های اجراشده در ' . $term->name : 'پروژه‌های ' . $term->name) : 'پروژه‌های نورپردازی شهری';
$description = $term instanceof WP_Term ? trim((string) $term->description) : '';
$perPage = 6;
$pageCount = max(1, (int) ceil($count / $perPage));
?>
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" data-projects-archive="true" data-initial-type="<?php echo esc_attr($initialType); ?>" data-initial-location="<?php echo esc_attr($initialLocation); ?>">
    <header class="mb-8 rounded-3xl bg-white p-6 md:p-8"><p class="mb-2 text-xs font-bold text-emerald-700">نمونه‌کارهای عرفان صنعت اصفهان</p><h1 class="text-2xl font-black text-slate-900 md:text-4xl"><?php echo esc_html($title); ?></h1><p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600"><?php echo esc_html($description !== '' ? $description : 'کارنامه اجرایی پروژه‌های طراحی، تولید و اجرای سازه‌های نوری در شهرهای ایران.'); ?></p></header>
    <div class="mb-4 flex justify-end lg:hidden"><button type="button" data-archive-drawer-open="true" aria-expanded="false" aria-controls="project-archive-filter-drawer" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm">فیلتر پروژه‌ها</button></div>
    <div class="mb-6 hidden gap-4 rounded-3xl border border-slate-200 bg-white p-4 lg:grid lg:grid-cols-4">
        <label class="text-xs font-bold">جستجو در پروژه‌ها<input type="search" data-project-search="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="نام پروژه، شهر یا نوع سازه"></label>
        <label class="text-xs font-bold">نوع پروژه<select data-project-type-filter="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><option value="همه">همه انواع</option><?php if (is_array($types)) foreach ($types as $term) : if ($term instanceof WP_Term) : ?><option value="<?php echo esc_attr($term->name); ?>" <?php selected($initialType, $term->name); ?>><?php echo esc_html($term->name); ?></option><?php endif; endforeach; ?></select></label>
        <label class="text-xs font-bold">شهر / محل اجرا<select data-project-location-filter="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><option value="همه">همه شهرها</option><?php if (is_array($cities)) foreach ($cities as $term) : if ($term instanceof WP_Term) : ?><option value="<?php echo esc_attr($term->name); ?>" <?php selected($initialLocation, $term->name); ?>><?php echo esc_html($term->name); ?></option><?php endif; endforeach; ?></select></label>
        <label class="text-xs font-bold">مرتب‌سازی<select data-project-sort="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><option value="newest">جدیدترین</option><option value="popular">پربازدیدترین</option><option value="rating">بالاترین امتیاز</option></select></label>
    </div>
    <div class="mb-4 text-sm text-slate-600"><strong data-project-count="true"><?php echo esc_html(number_format_i18n($count)); ?></strong> پروژه</div>
    <div data-project-grid="true" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3"><?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); $GLOBALS['es_card_post'] = get_post(); get_template_part('template-parts/cards/card-project'); endwhile; wp_reset_postdata(); unset($GLOBALS['es_card_post']); endif; ?></div>
    <div data-project-empty="true" class="<?php echo $count > 0 ? 'hidden' : ''; ?> mt-6 rounded-2xl bg-slate-50 p-8 text-center text-sm text-slate-500">پروژه‌ای با این مشخصات پیدا نشد.</div>
    <nav data-pagination-root="true" data-per-page="<?php echo esc_attr((string) $perPage); ?>" class="<?php echo $pageCount <= 1 ? 'hidden' : ''; ?> mt-8 flex items-center justify-center gap-2" aria-label="صفحه‌بندی پروژه‌ها"><button type="button" data-page-prev="true" class="rounded-xl border px-3 py-2" aria-label="صفحه قبل">قبلی</button><?php for ($page = 1; $page <= $pageCount; $page++) : ?><button type="button" data-page-btn="<?php echo esc_attr((string) $page); ?>" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"><?php echo esc_html(number_format_i18n($page)); ?></button><?php endfor; ?><button type="button" data-page-next="true" class="rounded-xl border px-3 py-2" aria-label="صفحه بعد">بعدی</button><span data-pagination-summary="true" class="sr-only"></span></nav>
    <section class="mt-12 rounded-3xl bg-emerald-50 p-6 text-center"><h2 class="text-xl font-black text-slate-900">برای پروژه نورپردازی خود مشاوره بگیرید</h2><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="mt-4 inline-block rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">ارتباط با کارشناسان پروژه</a></section>
    <?php get_template_part('template-parts/global/archive-filter-drawer', null, ['type' => 'project', 'types' => is_array($types) ? $types : [], 'cities' => is_array($cities) ? $cities : [], 'initial_type' => $initialType, 'initial_location' => $initialLocation]); ?>
</section>
