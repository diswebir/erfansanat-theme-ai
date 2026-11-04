<?php
/** Shared dynamic blog/article archive using the active WordPress main query. */

global $wp_query;
$archiveTitle = is_category() ? single_cat_title('', false) : (is_home() ? __('وبلاگ و مقالات تخصصی', 'erfansanat-theme') : get_the_archive_title());
$archiveDescription = is_category() ? category_description() : get_the_archive_description();
$allCategories = get_categories(['hide_empty' => true, 'number' => 30, 'orderby' => 'name', 'order' => 'ASC']);
$allCategories = is_array($allCategories) ? $allCategories : [];
$totalPosts = $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;
?>
<main id="primary" class="bg-slate-50/60">
    <section class="border-b border-emerald-100 bg-gradient-to-l from-emerald-50 via-white to-slate-50 pb-10 pt-28 lg:pb-12">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <nav aria-label="<?php echo esc_attr__('مسیر صفحه', 'erfansanat-theme'); ?>" class="mb-5 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-500"><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html(wp_strip_all_tags((string) $archiveTitle)); ?></span></nav>
            <p class="text-xs font-extrabold text-emerald-700"><?php echo esc_html__('مجله تخصصی نورپردازی شهری', 'erfansanat-theme'); ?></p>
            <h1 class="mt-2 text-3xl font-black leading-relaxed text-slate-950 sm:text-4xl"><?php echo esc_html(wp_strip_all_tags((string) $archiveTitle)); ?></h1>
            <?php if (trim(wp_strip_all_tags((string) $archiveDescription)) !== '') : ?><div class="mt-3 max-w-3xl text-sm leading-7 text-slate-600"><?php echo wp_kses_post($archiveDescription); ?></div><?php endif; ?>
        </div>
    </section>
    <section id="articles" class="mx-auto grid max-w-7xl gap-8 px-5 py-10 lg:grid-cols-12 lg:px-8 lg:py-14">
        <aside class="lg:col-span-3">
            <div class="sticky top-28 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-black text-slate-900"><?php echo esc_html__('دسته‌بندی مقالات', 'erfansanat-theme'); ?></h2>
                <p class="mt-1 text-xs text-slate-500"><?php echo esc_html(number_format_i18n($totalPosts)); ?> <?php echo esc_html__('مقاله در این آرشیو', 'erfansanat-theme'); ?></p>
                <nav aria-label="<?php echo esc_attr__('دسته‌بندی مقالات', 'erfansanat-theme'); ?>" class="mt-4 space-y-1">
                    <a href="<?php echo esc_url(get_post_type_archive_link('post') ?: home_url('/blog/')); ?>" class="block rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800"><?php echo esc_html__('همه مقالات', 'erfansanat-theme'); ?></a>
                    <?php foreach ($allCategories as $category) : if (!$category instanceof WP_Term) { continue; } $categoryUrl = get_category_link($category->term_id); ?><a href="<?php echo esc_url($categoryUrl); ?>" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-bold <?php echo is_category($category->term_id) ? 'bg-emerald-700 text-white' : 'text-slate-700 hover:bg-emerald-50 hover:text-emerald-800'; ?>"><span><?php echo esc_html($category->name); ?></span><span class="text-[10px] opacity-75"><?php echo esc_html(number_format_i18n((int) $category->count)); ?></span></a><?php endforeach; ?>
                </nav>
            </div>
        </aside>
        <div class="min-w-0 lg:col-span-9">
            <?php if (have_posts()) : ?>
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <?php while (have_posts()) : the_post(); ?>
                        <article <?php post_class('overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md'); ?>>
                            <?php get_template_part('template-parts/cards/card-post', null, ['post_id' => get_the_ID()]); ?>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => __('مقالات جدیدتر', 'erfansanat-theme'), 'next_text' => __('مقالات قدیمی‌تر', 'erfansanat-theme'), 'screen_reader_text' => __('صفحه‌بندی مقالات', 'erfansanat-theme'), 'class' => 'mt-8 flex flex-wrap justify-center gap-2']); ?>
            <?php else : ?>
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><h2 class="text-lg font-black text-slate-900"><?php echo esc_html__('هنوز مقاله‌ای در این بخش منتشر نشده است.', 'erfansanat-theme'); ?></h2><a href="<?php echo esc_url(home_url('/')); ?>" class="mt-4 inline-flex rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-extrabold text-white"><?php echo esc_html__('بازگشت به صفحه اصلی', 'erfansanat-theme'); ?></a></div>
            <?php endif; ?>
        </div>
    </section>
</main>
