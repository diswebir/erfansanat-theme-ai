<?php
/**
 * single.php — blog post page: TOC, share, view counter (number2-viewc),
 * related posts, comments.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
get_header();
while (have_posts()) : the_post();
    $pid   = get_the_ID();
    $views = get_post_meta($pid, 'number2-viewc', true);
    if ($views === '' || $views === false) { $views = get_post_meta($pid, 'number-viewc', true); }
    $cats  = get_the_category($pid);
?>
<main id="main" class="site-main">
    <article class="relative bg-[#f6f8f7] pt-28 lg:pt-36">
        <div class="mx-auto max-w-[900px] px-4 sm:px-6 lg:px-8">
            <?php if ($cats) : ?><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700"><?php echo esc_html($cats[0]->name); ?></span><?php endif; ?>
            <h1 class="mt-3 text-2xl font-black leading-snug text-slate-900 sm:text-3xl lg:text-4xl"><?php the_title(); ?></h1>
            <div class="mt-3 flex flex-wrap items-center gap-4 text-xs font-bold text-slate-400">
                <span><?php echo esc_html(get_the_date('Y/m/d')); ?></span>
                <span><?php echo esc_html(sprintf(_n('%s بازدید', '%s بازدید', (int) $views, 'erfansanat'), number_format_i18n((int) $views))); ?></span>
            </div>
            <?php if (has_post_thumbnail()) : ?>
                <img src="<?php echo esc_url(get_the_post_thumbnail_url($pid, 'large')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="mt-6 h-72 w-full rounded-3xl object-cover" />
            <?php endif; ?>
        </div>
    </article>

    <div class="mx-auto max-w-[900px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-[220px_1fr]">
            <aside class="hidden lg:block">
                <div class="sticky top-28 rounded-3xl border border-slate-200 bg-white p-4">
                    <div class="text-xs font-black text-slate-900"><?php esc_html_e('فهرست محتوا', 'erfansanat'); ?></div>
                    <div id="es-toc" class="mt-2 text-xs font-bold text-slate-500"></div>
                    <div class="mt-4 border-t border-slate-100 pt-3 text-xs font-black text-slate-900"><?php esc_html_e('اشتراک‌گذاری', 'erfansanat'); ?></div>
                    <div class="mt-2 flex gap-2">
                        <a href="https://t.me/share/url?url=<?php echo esc_url(get_permalink()); ?>" target="_blank" rel="noopener" class="rounded-xl bg-slate-100 px-3 py-2">Telegram</a>
                        <a href="https://wa.me/?text=<?php echo esc_url(get_permalink()); ?>" target="_blank" rel="noopener" class="rounded-xl bg-slate-100 px-3 py-2">WhatsApp</a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo esc_url(get_permalink()); ?>" target="_blank" rel="noopener" class="rounded-xl bg-slate-100 px-3 py-2">X</a>
                    </div>
                </div>
            </aside>
            <div class="prose max-w-none text-slate-700"><?php the_content(); ?></div>
        </div>
    </div>

    <section class="mx-auto max-w-[900px] px-4 pb-10 sm:px-6 lg:px-8">
        <h2 class="text-lg font-black text-slate-900"><?php esc_html_e('مقالات مرتبط', 'erfansanat'); ?></h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            $rq = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'post__not_in'   => [$pid],
                'cat'            => $cats ? $cats[0]->term_id : 0,
                'no_found_rows'  => true,
            ]);
            while ($rq->have_posts()) : $rq->the_post();
                get_template_part('template-parts/cards/card', 'post');
            endwhile; wp_reset_postdata();
            ?>
        </div>
    </section>

    <section class="mx-auto max-w-[900px] px-4 pb-16 sm:px-6 lg:px-8"><?php comments_template(); ?></section>
</main>
<?php endwhile; ?>
<?php get_footer();
