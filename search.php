<?php
/** Search results for WordPress posts and public content types. */
defined('ABSPATH') || exit;
get_header();
$searchTerm = get_search_query();
?>
<main id="primary" class="min-h-[65vh] bg-slate-50/60 px-5 pb-14 pt-28 lg:px-8">
    <section class="mx-auto max-w-7xl">
        <nav aria-label="<?php echo esc_attr__('مسیر صفحه', 'erfansanat-theme'); ?>" class="mb-5 flex items-center gap-2 text-xs font-semibold text-slate-500"><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html__('نتایج جستجو', 'erfansanat-theme'); ?></span></nav>
        <header class="mb-7 rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm sm:p-8"><p class="text-xs font-extrabold text-emerald-700"><?php echo esc_html__('جستجو در وب‌سایت', 'erfansanat-theme'); ?></p><h1 class="mt-2 text-2xl font-black leading-relaxed text-slate-950 sm:text-3xl"><?php echo esc_html(sprintf(__('نتایج برای «%s»', 'erfansanat-theme'), $searchTerm)); ?></h1>
            <form role="search" method="get" class="mt-5 flex flex-col gap-2 sm:flex-row" action="<?php echo esc_url(home_url('/')); ?>"><label class="sr-only" for="site-search-query"><?php echo esc_html__('عبارت جستجو', 'erfansanat-theme'); ?></label><input id="site-search-query" type="search" name="s" value="<?php echo esc_attr($searchTerm); ?>" placeholder="<?php echo esc_attr__('نام محصول، مقاله یا پروژه...', 'erfansanat-theme'); ?>" class="min-h-12 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /><button type="submit" class="min-h-12 rounded-xl bg-emerald-700 px-6 text-sm font-extrabold text-white hover:bg-emerald-800"><?php echo esc_html__('جستجو', 'erfansanat-theme'); ?></button></form>
        </header>
        <?php if (have_posts()) : ?>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <?php while (have_posts()) : the_post(); $resultType = get_post_type(); ?>
                    <article <?php post_class('overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm'); ?>>
                        <?php if ('projects' === $resultType) : get_template_part('template-parts/cards/card-project', null, ['post_id' => get_the_ID(), 'variant' => 'archive']); ?>
                        <?php elseif ('product' === $resultType) : get_template_part('template-parts/cards/card-product', null, ['post_id' => get_the_ID(), 'variant' => 'archive']); ?>
                        <?php elseif ('post' === $resultType) : get_template_part('template-parts/cards/card-post', null, ['post_id' => get_the_ID()]); ?>
                        <?php else : ?><div class="p-5"><h2 class="text-lg font-black text-slate-900"><a href="<?php the_permalink(); ?>" class="hover:text-emerald-700"><?php the_title(); ?></a></h2><p class="mt-2 text-sm leading-7 text-slate-600"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p><a href="<?php the_permalink(); ?>" class="mt-4 inline-flex text-xs font-extrabold text-emerald-700"><?php echo esc_html__('ادامه مطلب', 'erfansanat-theme'); ?></a></div><?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => __('نتایج قبلی', 'erfansanat-theme'), 'next_text' => __('نتایج بعدی', 'erfansanat-theme'), 'screen_reader_text' => __('صفحه‌بندی نتایج جستجو', 'erfansanat-theme'), 'class' => 'mt-8 flex flex-wrap justify-center gap-2']); ?>
        <?php else : ?>
            <section class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center sm:p-12"><span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 text-2xl text-emerald-700" aria-hidden="true">⌕</span><h2 class="mt-4 text-xl font-black text-slate-900"><?php echo esc_html__('نتیجه‌ای پیدا نشد.', 'erfansanat-theme'); ?></h2><p class="mt-2 text-sm leading-7 text-slate-600"><?php echo esc_html__('عبارت دیگری را امتحان کنید یا از دسته‌بندی‌های سایت دیدن کنید.', 'erfansanat-theme'); ?></p><a href="<?php echo esc_url(home_url('/')); ?>" class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-xs font-extrabold text-white"><?php echo esc_html__('بازگشت به صفحه اصلی', 'erfansanat-theme'); ?></a></section>
        <?php endif; ?>
    </section>
</main>
<?php get_footer(); ?>
