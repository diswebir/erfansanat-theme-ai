<?php
/**
 * Shared dynamic archive view for all projects, project types, and cities.
 * Uses the WordPress main query so native archive conditionals and pagination remain active.
 *
 * @package ErfanSanat\Theme
 */

global $wp_query;
$currentTerm = get_queried_object();
$currentTerm = $currentTerm instanceof WP_Term ? $currentTerm : null;
$taxonomy = $currentTerm instanceof WP_Term ? (string) $currentTerm->taxonomy : '';
$isProjectTypeArchive = 'project_type' === $taxonomy;
$isCityArchive = 'city' === $taxonomy;
$projectTypeTerms = get_terms([
    'taxonomy' => 'project_type',
    'hide_empty' => true,
    'number' => 100,
    'orderby' => 'name',
    'order' => 'ASC',
]);
$cityTerms = get_terms([
    'taxonomy' => 'city',
    'hide_empty' => true,
    'number' => 100,
    'orderby' => 'name',
    'order' => 'ASC',
]);
$projectTypeTerms = is_array($projectTypeTerms) ? $projectTypeTerms : [];
$cityTerms = is_array($cityTerms) ? $cityTerms : [];
$archiveUrl = get_post_type_archive_link('projects') ?: home_url('/projects/');
$archiveTitle = $currentTerm instanceof WP_Term
    ? $currentTerm->name
    : __('پروژه‌های نورپردازی شهری', 'erfansanat-theme');
$archiveDescription = $currentTerm instanceof WP_Term
    ? term_description($currentTerm->term_id, $currentTerm->taxonomy)
    : '';
$archiveImageUrl = '';
if ($wp_query instanceof WP_Query && !empty($wp_query->posts)) {
    foreach ($wp_query->posts as $archivePost) {
        $archivePostId = $archivePost instanceof WP_Post ? (int) $archivePost->ID : absint($archivePost);
        if ($archivePostId && class_exists(\ErfanSanat\Theme\HomepageData::class)) {
            $archiveImageUrl = \ErfanSanat\Theme\HomepageData::get_post_image_url($archivePostId, 'large');
            if ($archiveImageUrl) {
                break;
            }
        }
    }
}
$initialType = $isProjectTypeArchive && $currentTerm instanceof WP_Term ? $currentTerm->name : 'همه';
$initialCity = $isCityArchive && $currentTerm instanceof WP_Term ? $currentTerm->name : 'همه';
$totalProjects = $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;
$renderedProjectCount = $wp_query instanceof WP_Query ? max(1, (int) $wp_query->post_count) : 1;
?>
<main id="primary" class="bg-slate-50/60">
    <section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
        <?php if ($archiveImageUrl) : ?><img src="<?php echo esc_url($archiveImageUrl); ?>" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover opacity-[0.12]" loading="eager" /><?php endif; ?>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-l from-white/85 via-white/90 to-emerald-50/85" aria-hidden="true"></div>
        <div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
            <nav aria-label="<?php echo esc_attr__('مسیر صفحه', 'erfansanat-theme'); ?>" class="mb-5 inline-flex flex-wrap items-center gap-2 rounded-xl border border-emerald-100 bg-white/90 px-4 py-2 text-xs font-semibold text-slate-500">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url($archiveUrl); ?>" class="hover:text-emerald-700"><?php echo esc_html__('پروژه‌ها', 'erfansanat-theme'); ?></a>
                <?php if ($currentTerm instanceof WP_Term) : ?><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html($currentTerm->name); ?></span><?php endif; ?>
            </nav>
            <p class="text-sm font-extrabold text-emerald-700"><?php echo esc_html__('آرشیو پروژه‌های اجراشده عرفان صنعت', 'erfansanat-theme'); ?></p>
            <h1 class="mt-2 max-w-4xl text-3xl font-black leading-relaxed text-slate-950 sm:text-4xl"><?php echo esc_html($archiveTitle); ?></h1>
            <?php if ('' !== trim(wp_strip_all_tags((string) $archiveDescription))) : ?><div class="mt-3 max-w-3xl text-sm leading-7 text-slate-600"><?php echo wp_kses_post($archiveDescription); ?></div><?php endif; ?>
        </div>
    </section>

    <section id="archive" data-projects-archive="true" data-initial-type="<?php echo esc_attr($initialType); ?>" data-initial-location="<?php echo esc_attr($initialCity); ?>" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-10 lg:px-8 lg:py-14">
        <div class="grid items-start gap-7 lg:grid-cols-12">
            <aside aria-label="<?php echo esc_attr__('فیلتر و آرشیو پروژه‌ها', 'erfansanat-theme'); ?>" class="hidden lg:col-span-4 lg:block xl:col-span-3">
                <div class="sticky top-28 space-y-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-sm font-black text-slate-900"><?php echo esc_html__('فیلتر و آرشیو پروژه‌ها', 'erfansanat-theme'); ?></h2>
                            <p class="mt-1 text-xs font-semibold text-slate-500"><span data-project-count="true"><?php echo esc_html(number_format_i18n($totalProjects)); ?></span> <?php echo esc_html__('پروژه در آرشیو', 'erfansanat-theme'); ?></p>
                        </div>
                        <?php if ($currentTerm instanceof WP_Term) : ?><a href="<?php echo esc_url($archiveUrl); ?>" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-emerald-700"><?php echo esc_html__('همه پروژه‌ها', 'erfansanat-theme'); ?></a><?php endif; ?>
                    </div>
                    <label for="project-sidebar-search" class="block text-xs font-extrabold text-slate-700"><?php echo esc_html__('جستجو در پروژه‌ها', 'erfansanat-theme'); ?></label>
                    <input id="project-sidebar-search" type="search" data-project-search="true" placeholder="<?php echo esc_attr__('نام پروژه، شهر یا نوع سازه...', 'erfansanat-theme'); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none" />
                    <nav aria-label="<?php echo esc_attr__('آرشیو نوع پروژه', 'erfansanat-theme'); ?>" class="border-t border-slate-100 pt-4">
                        <h3 class="mb-2 text-xs font-black text-slate-800"><?php echo esc_html__('نوع پروژه', 'erfansanat-theme'); ?></h3>
                        <a href="<?php echo esc_url($archiveUrl); ?>" data-project-filter-type="all" class="mb-1 flex items-center justify-between rounded-lg px-3 py-2 text-xs font-bold <?php echo !$isProjectTypeArchive ? 'bg-emerald-700 text-white' : 'text-slate-700 hover:bg-emerald-50'; ?>"><span><?php echo esc_html__('همه انواع پروژه‌ها', 'erfansanat-theme'); ?></span><span><span data-project-count="true"><?php echo esc_html(number_format_i18n($totalProjects)); ?></span></span></a>
                        <?php foreach ($projectTypeTerms as $filterTerm) :
                            if (!$filterTerm instanceof WP_Term) { continue; }
                            $filterUrl = get_term_link($filterTerm);
                            if (is_wp_error($filterUrl)) { continue; }
                            $active = $isProjectTypeArchive && (int) $currentTerm->term_id === (int) $filterTerm->term_id;
                            ?>
                            <a href="<?php echo esc_url($filterUrl); ?>" class="mb-1 flex items-center justify-between rounded-lg px-3 py-2 text-xs font-bold <?php echo $active ? 'bg-emerald-700 text-white' : 'text-slate-700 hover:bg-emerald-50'; ?>"><span><?php echo esc_html($filterTerm->name); ?></span><span><?php echo esc_html(number_format_i18n((int) $filterTerm->count)); ?></span></a>
                        <?php endforeach; ?>
                    </nav>
                    <nav aria-label="<?php echo esc_attr__('آرشیو شهر پروژه', 'erfansanat-theme'); ?>" class="border-t border-slate-100 pt-4">
                        <h3 class="mb-2 text-xs font-black text-slate-800"><?php echo esc_html__('شهر محل اجرا', 'erfansanat-theme'); ?></h3>
                        <a href="<?php echo esc_url($archiveUrl); ?>" class="mb-1 flex items-center justify-between rounded-lg px-3 py-2 text-xs font-bold <?php echo !$isCityArchive ? 'bg-emerald-700 text-white' : 'text-slate-700 hover:bg-emerald-50'; ?>"><span><?php echo esc_html__('همه شهرها', 'erfansanat-theme'); ?></span><span><span data-project-count="true"><?php echo esc_html(number_format_i18n($totalProjects)); ?></span></span></a>
                        <?php foreach ($cityTerms as $filterTerm) :
                            if (!$filterTerm instanceof WP_Term) { continue; }
                            $filterUrl = get_term_link($filterTerm);
                            if (is_wp_error($filterUrl)) { continue; }
                            $active = $isCityArchive && (int) $currentTerm->term_id === (int) $filterTerm->term_id;
                            ?>
                            <a href="<?php echo esc_url($filterUrl); ?>" class="mb-1 flex items-center justify-between rounded-lg px-3 py-2 text-xs font-bold <?php echo $active ? 'bg-emerald-700 text-white' : 'text-slate-700 hover:bg-emerald-50'; ?>"><span><?php echo esc_html($filterTerm->name); ?></span><span><?php echo esc_html(number_format_i18n((int) $filterTerm->count)); ?></span></a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </aside>

            <div class="min-w-0 lg:col-span-8 xl:col-span-9">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                    <div><h2 class="text-base font-black text-slate-900"><?php echo esc_html($currentTerm instanceof WP_Term ? sprintf(__('پروژه‌های اجراشده در %s', 'erfansanat-theme'), $currentTerm->name) : __('همه پروژه‌های نورپردازی شهری', 'erfansanat-theme')); ?></h2><p class="mt-1 text-xs text-slate-500"><span data-project-count="true"><?php echo esc_html(number_format_i18n($totalProjects)); ?></span> <?php echo esc_html__('پروژه', 'erfansanat-theme'); ?></p></div>
                    <label for="project-sort-select" class="flex items-center gap-2 text-xs font-bold text-slate-600"><span><?php echo esc_html__('مرتب‌سازی:', 'erfansanat-theme'); ?></span><select id="project-sort-select" data-project-sort="true" class="rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs font-bold text-slate-800"><option value="newest"><?php echo esc_html__('جدیدترین پروژه‌ها', 'erfansanat-theme'); ?></option><option value="popular"><?php echo esc_html__('پربازدیدترین', 'erfansanat-theme'); ?></option><option value="rating"><?php echo esc_html__('بالاترین امتیاز', 'erfansanat-theme'); ?></option></select></label>
                    <button type="button" data-project-filter-clear="true" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:border-emerald-300 hover:text-emerald-800"><?php echo esc_html__('پاک‌کردن فیلترها', 'erfansanat-theme'); ?></button>
                    <span class="sr-only" role="status" aria-live="polite" data-project-ajax-status="true"></span>
                    <button type="button" data-archive-drawer-open="true" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-xs font-extrabold text-white lg:hidden"><?php echo esc_html__('فیلتر و آرشیو', 'erfansanat-theme'); ?></button>
                </div>

                <div data-project-grid="true" class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post();
                            $projectId = get_the_ID();
                            $projectTypeTermsForCard = get_the_terms($projectId, 'project_type');
                            $projectCityTermsForCard = get_the_terms($projectId, 'city');
                            $projectTypeTermsForCard = is_array($projectTypeTermsForCard) ? $projectTypeTermsForCard : [];
                            $projectCityTermsForCard = is_array($projectCityTermsForCard) ? $projectCityTermsForCard : [];
                            $typeNames = array_values(array_map(static function (WP_Term $projectTerm): string { return $projectTerm->name; }, array_filter($projectTypeTermsForCard, static function ($projectTerm): bool { return $projectTerm instanceof WP_Term; })));
                            $cityNames = array_values(array_map(static function (WP_Term $projectTerm): string { return $projectTerm->name; }, array_filter($projectCityTermsForCard, static function ($projectTerm): bool { return $projectTerm instanceof WP_Term; })));
                            $typeLabel = $isProjectTypeArchive && $currentTerm instanceof WP_Term ? $currentTerm->name : (string) reset($typeNames);
                            $cityLabel = $isCityArchive && $currentTerm instanceof WP_Term ? $currentTerm->name : (string) reset($cityNames);
                            $searchText = trim(wp_strip_all_tags(get_the_title() . ' ' . get_the_excerpt() . ' ' . implode(' ', $typeNames) . ' ' . implode(' ', $cityNames)));
                            $viewCount = class_exists(\ErfanSanat\Theme\Admin\ContentMeta::class) ? \ErfanSanat\Theme\Admin\ContentMeta::get_view_count($projectId) : 0;
                            $projectRating = get_post_meta($projectId, 'project-rating', true);
                            $projectRating = is_numeric($projectRating) ? (float) $projectRating : 0;
                            ?>
                            <article <?php post_class('project-archive-item'); ?> data-project-item="true" data-project-type="<?php echo esc_attr($typeLabel); ?>" data-project-types="<?php echo esc_attr(implode('|', $typeNames)); ?>" data-project-location="<?php echo esc_attr($cityLabel); ?>" data-project-views="<?php echo esc_attr((string) $viewCount); ?>" data-project-rating="<?php echo esc_attr((string) $projectRating); ?>" data-project-date="<?php echo esc_attr(get_post_time('c', true, $projectId)); ?>" data-project-search-text="<?php echo esc_attr($searchText); ?>">
                                <?php get_template_part('template-parts/cards/card-project', null, ['post_id' => $projectId, 'variant' => 'archive', 'index' => 0]); ?>
                            </article>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                <div data-project-empty="true" class="<?php echo $wp_query instanceof WP_Query && !empty($wp_query->posts) ? 'hidden ' : ''; ?>mt-5 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-sm font-bold text-slate-600">
                    <p><?php echo esc_html__('پروژه‌ای با این مشخصات یافت نشد.', 'erfansanat-theme'); ?></p>
                    <?php if ($currentTerm instanceof WP_Term) : ?><a href="<?php echo esc_url($archiveUrl); ?>" class="mt-3 inline-flex rounded-lg bg-emerald-700 px-4 py-2 text-xs font-extrabold text-white"><?php echo esc_html__('نمایش همه پروژه‌ها', 'erfansanat-theme'); ?></a><?php endif; ?>
                </div>
                <?php
                $pagination = get_the_posts_pagination([
                    'mid_size' => 1,
                    'prev_text' => __('پروژه‌های جدیدتر', 'erfansanat-theme'),
                    'next_text' => __('پروژه‌های قدیمی‌تر', 'erfansanat-theme'),
                    'screen_reader_text' => __('صفحه‌بندی پروژه‌ها', 'erfansanat-theme'),
                    'class' => 'mt-8 flex flex-wrap items-center justify-center gap-2',
                ]);
                ?>
                <div data-project-server-pagination="true"><?php if ($pagination) { echo wp_kses_post($pagination); } ?></div>
                <div class="hidden mt-7 flex flex-wrap items-center justify-center gap-2" data-project-ajax-pagination="true" aria-label="<?php echo esc_attr__('صفحه‌بندی پروژه‌ها', 'erfansanat-theme'); ?>"></div>
            </div>
        </div>

        <div data-archive-drawer="true" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__('فیلتر و آرشیو پروژه‌ها', 'erfansanat-theme'); ?>" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/60 p-0 backdrop-blur-sm sm:items-center sm:p-4 lg:hidden">
            <div data-archive-drawer-backdrop="true" class="absolute inset-0"></div>
            <div class="relative z-10 max-h-[86vh] w-full max-w-lg overflow-y-auto rounded-t-3xl bg-white p-5 shadow-2xl sm:rounded-3xl">
                <div class="mb-4 flex items-center justify-between"><h2 class="text-base font-black text-slate-900"><?php echo esc_html__('فیلتر پروژه‌ها', 'erfansanat-theme'); ?></h2><button type="button" data-archive-drawer-close="true" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 text-slate-600" aria-label="<?php echo esc_attr__('بستن فیلتر', 'erfansanat-theme'); ?>">×</button></div>
                <label for="project-modal-search" class="mb-2 block text-xs font-bold text-slate-700"><?php echo esc_html__('جستجو در پروژه‌ها', 'erfansanat-theme'); ?></label>
                <input id="project-modal-search" type="search" data-project-search="true" placeholder="<?php echo esc_attr__('نام پروژه، شهر یا نوع سازه...', 'erfansanat-theme'); ?>" class="mb-5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none" />
                <div class="grid gap-5 sm:grid-cols-2">
                    <nav aria-label="<?php echo esc_attr__('نوع پروژه', 'erfansanat-theme'); ?>"><h3 class="mb-2 text-xs font-black text-slate-800"><?php echo esc_html__('نوع پروژه', 'erfansanat-theme'); ?></h3><a href="<?php echo esc_url($archiveUrl); ?>" class="mb-1 block rounded-lg px-3 py-2 text-xs font-bold hover:bg-emerald-50"><?php echo esc_html__('همه پروژه‌ها', 'erfansanat-theme'); ?></a><?php foreach ($projectTypeTerms as $filterTerm) : if (!$filterTerm instanceof WP_Term) { continue; } $filterUrl = get_term_link($filterTerm); if (is_wp_error($filterUrl)) { continue; } ?><a href="<?php echo esc_url($filterUrl); ?>" class="mb-1 block rounded-lg px-3 py-2 text-xs font-bold hover:bg-emerald-50"><?php echo esc_html($filterTerm->name); ?> <span class="text-slate-400"><?php echo esc_html(number_format_i18n((int) $filterTerm->count)); ?></span></a><?php endforeach; ?></nav>
                    <nav aria-label="<?php echo esc_attr__('شهر پروژه', 'erfansanat-theme'); ?>"><h3 class="mb-2 text-xs font-black text-slate-800"><?php echo esc_html__('شهر محل اجرا', 'erfansanat-theme'); ?></h3><a href="<?php echo esc_url($archiveUrl); ?>" class="mb-1 block rounded-lg px-3 py-2 text-xs font-bold hover:bg-emerald-50"><?php echo esc_html__('همه شهرها', 'erfansanat-theme'); ?></a><?php foreach ($cityTerms as $filterTerm) : if (!$filterTerm instanceof WP_Term) { continue; } $filterUrl = get_term_link($filterTerm); if (is_wp_error($filterUrl)) { continue; } ?><a href="<?php echo esc_url($filterUrl); ?>" class="mb-1 block rounded-lg px-3 py-2 text-xs font-bold hover:bg-emerald-50"><?php echo esc_html($filterTerm->name); ?> <span class="text-slate-400"><?php echo esc_html(number_format_i18n((int) $filterTerm->count)); ?></span></a><?php endforeach; ?></nav>
                </div>
            </div>
        </div>
    </section>
</main>
