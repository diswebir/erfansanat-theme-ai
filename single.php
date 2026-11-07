<?php
/**
 * Standard WordPress post detail with a generated table of contents, sharing, related posts and comments.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;
get_header();
?>
<main id="primary" class="bg-slate-50/60">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post();
            $postId = get_the_ID();
            $postTitle = get_the_title();
            $postUrl = get_permalink();
            $postCategories = get_the_category();
            $postCategories = is_array($postCategories) ? $postCategories : [];
            $postViews = class_exists(\ErfanSanat\Theme\Admin\ContentMeta::class) ? \ErfanSanat\Theme\Admin\ContentMeta::get_view_count($postId) : 0;
            $renderedContent = apply_filters('the_content', (string) get_post_field('post_content', $postId));
            $tocItems = [];
            $headingIds = [];
            $renderedContent = preg_replace_callback(
                '~<h([23])\b([^>]*)>(.*?)</h\1\s*>~is',
                static function (array $matches) use (&$tocItems, &$headingIds): string {
                    $level = (int) $matches[1];
                    $attributes = $matches[2];
                    $headingHtml = $matches[3];
                    $headingText = trim(wp_strip_all_tags($headingHtml));
                    if ('' === $headingText) {
                        return $matches[0];
                    }
                    if (preg_match('~\bid\s*=\s*(["\'])(.*?)\1~i', $attributes, $idMatch)) {
                        $id = sanitize_title($idMatch[2]);
                    } else {
                        $id = sanitize_title($headingText);
                    }
                    if ('' === $id) {
                        $id = 'section';
                    }
                    $baseId = $id;
                    $suffix = 2;
                    while (isset($headingIds[$id])) {
                        $id = $baseId . '-' . $suffix++;
                    }
                    $headingIds[$id] = true;
                    if (!preg_match('~\bid\s*=~i', $attributes)) {
                        $attributes .= ' id="' . esc_attr($id) . '"';
                    }
                    $tocItems[] = ['id' => $id, 'text' => $headingText, 'level' => $level];
                    return '<h' . $level . $attributes . '>' . $headingHtml . '</h' . $level . '>';
                },
                $renderedContent
            );
            if (!is_string($renderedContent)) {
                $renderedContent = '';
            }
            $shareUrl = rawurlencode($postUrl);
            $shareTitle = rawurlencode($postTitle);
            $categories = [];
            foreach ($postCategories as $postCategory) {
                if ($postCategory instanceof WP_Term) {
                    $categories[] = $postCategory;
                }
            }
            $relatedPostIds = [];
            if ($categories) {
                $categoryIds = array_map(static function (WP_Term $category): int { return (int) $category->term_id; }, $categories);
                $relatedQuery = new WP_Query([
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 3,
                    'post__not_in' => [$postId],
                    'category__in' => $categoryIds,
                    'fields' => 'ids',
                    'no_found_rows' => true,
                    'ignore_sticky_posts' => true,
                ]);
                $relatedPostIds = array_values(array_filter(array_map('absint', $relatedQuery->posts)));
                wp_reset_postdata();
            }
            ?>
            <article <?php post_class('mx-auto max-w-7xl px-5 pb-14 pt-28 lg:px-8'); ?>>
                <nav aria-label="<?php echo esc_attr__('مسیر صفحه', 'erfansanat-theme'); ?>" class="mb-5 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500"><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a><span aria-hidden="true">/</span><a href="<?php echo esc_url(home_url('/blog/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('مقالات', 'erfansanat-theme'); ?></a><?php if ($categories && !is_wp_error(get_category_link($categories[0]->term_id))) : ?><span aria-hidden="true">/</span><a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="hover:text-emerald-700"><?php echo esc_html($categories[0]->name); ?></a><?php endif; ?></nav>
                <header class="mx-auto max-w-4xl text-center">
                    <?php if ($categories) : ?><div class="mb-3 flex flex-wrap justify-center gap-2"><?php foreach ($categories as $postCategory) : ?><a href="<?php echo esc_url(get_category_link($postCategory->term_id)); ?>" class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-800"><?php echo esc_html($postCategory->name); ?></a><?php endforeach; ?></div><?php endif; ?>
                    <h1 class="text-3xl font-black leading-relaxed text-slate-950 sm:text-4xl"><?php echo esc_html($postTitle); ?></h1>
                    <div class="mt-4 flex flex-wrap justify-center gap-x-5 gap-y-2 text-xs font-semibold text-slate-500"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time><span><?php echo esc_html(number_format_i18n($postViews)); ?> <?php echo esc_html__('بازدید', 'erfansanat-theme'); ?></span><span><?php echo esc_html(number_format_i18n((int) get_comments_number())); ?> <?php echo esc_html__('دیدگاه', 'erfansanat-theme'); ?></span></div>
                </header>

                <?php if (has_post_thumbnail()) : ?><div class="mx-auto mt-8 max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"><?php the_post_thumbnail('large', ['class' => 'max-h-[34rem] w-full object-cover', 'loading' => 'eager']); ?></div><?php endif; ?>

                <div class="mx-auto mt-8 grid max-w-6xl items-start gap-7 lg:grid-cols-12">
                    <aside class="space-y-5 lg:sticky lg:top-28 lg:col-span-3">
                        <?php if ($tocItems) : ?><nav aria-label="<?php echo esc_attr__('فهرست مطالب', 'erfansanat-theme'); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-black text-slate-900"><?php echo esc_html__('فهرست مطالب', 'erfansanat-theme'); ?></h2><ol class="mt-3 space-y-2"><?php foreach ($tocItems as $tocItem) : ?><li class="<?php echo 3 === $tocItem['level'] ? 'pr-3' : ''; ?>"><a href="<?php echo esc_url('#' . $tocItem['id']); ?>" class="text-xs font-semibold leading-6 text-slate-600 hover:text-emerald-700"><?php echo esc_html($tocItem['text']); ?></a></li><?php endforeach; ?></ol></nav><?php endif; ?>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-black text-slate-900"><?php echo esc_html__('اشتراک‌گذاری مقاله', 'erfansanat-theme'); ?></h2><div class="mt-3 grid grid-cols-2 gap-2"><a rel="noopener noreferrer" target="_blank" href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl); ?>" class="rounded-xl bg-slate-100 px-3 py-2 text-center text-xs font-bold text-slate-700 hover:bg-emerald-50"><?php echo esc_html__('Facebook', 'erfansanat-theme'); ?></a><a rel="noopener noreferrer" target="_blank" href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareTitle); ?>" class="rounded-xl bg-slate-100 px-3 py-2 text-center text-xs font-bold text-slate-700 hover:bg-emerald-50"><?php echo esc_html__('X / Twitter', 'erfansanat-theme'); ?></a><a rel="noopener noreferrer" target="_blank" href="<?php echo esc_url('https://wa.me/?text=' . $shareTitle . '%20' . $shareUrl); ?>" class="col-span-2 rounded-xl bg-emerald-50 px-3 py-2 text-center text-xs font-bold text-emerald-800 hover:bg-emerald-100"><?php echo esc_html__('اشتراک در WhatsApp', 'erfansanat-theme'); ?></a></div></div>
                    </aside>
                    <div class="min-w-0 lg:col-span-9">
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8"><div class="prose prose-slate max-w-none text-sm leading-8"><?php echo $renderedContent; // Content has passed through the normal WordPress content filters. ?></div></div>
                        <?php if ($relatedPostIds) : ?><section class="mt-10"><div class="mb-5"><p class="text-xs font-extrabold text-emerald-700"><?php echo esc_html__('ادامه مطالعه', 'erfansanat-theme'); ?></p><h2 class="mt-1 text-xl font-black text-slate-900"><?php echo esc_html__('مقالات مرتبط', 'erfansanat-theme'); ?></h2></div><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"><?php foreach ($relatedPostIds as $relatedIndex => $relatedPostId) : get_template_part('template-parts/cards/card-post', null, ['post_id' => $relatedPostId]); endforeach; ?></div></section><?php endif; ?>
                    </div>
                </div>
                <section id="comments" class="mx-auto mt-10 max-w-6xl rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8"><h2 class="mb-5 text-xl font-black text-slate-900"><?php echo esc_html__('دیدگاه‌ها', 'erfansanat-theme'); ?></h2><?php comments_template(); ?></section>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <section class="mx-auto max-w-5xl px-5 py-28 text-center"><h1 class="text-2xl font-black text-slate-900"><?php echo esc_html__('مقاله در دسترس نیست.', 'erfansanat-theme'); ?></h1></section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
