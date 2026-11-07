<?php
/** Shared special-page shell: editable SectionManager sections plus native page/shortcode content. */
global $post;
$pageId = get_queried_object_id();
$pageTitle = get_the_title($pageId);
$pageContent = (string) get_post_field('post_content', $pageId);
$pageSlug = $post instanceof WP_Post ? rawurldecode((string) $post->post_name) : '';
$isContactPage = is_page_template('page-templates/contact.php') || in_array($pageSlug, ['contact', 'تماس-با-ما'], true);
$isRecruitmentPage = is_page_template('page-templates/recruitment.php') || in_array($pageSlug, ['recruitment', 'فرصت-های-شغلی'], true);
?>
<main id="primary" class="bg-slate-50/60" data-special-page="<?php echo esc_attr(sanitize_key($pageSlug) ?: 'page'); ?>">
    <div class="mx-auto max-w-7xl px-5 pt-28 lg:px-8">
        <nav aria-label="<?php echo esc_attr__('مسیر صفحه', 'erfansanat-theme'); ?>" class="inline-flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-500"><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html($pageTitle); ?></span></nav>
    </div>
    <?php if (class_exists(\ErfanSanat\Theme\Admin\SectionManager::class)) : ?>
        <?php \ErfanSanat\Theme\Admin\SectionManager::render_sections((int) $pageId); ?>
    <?php endif; ?>
    <?php if ($isContactPage) : ?>
        <?php get_template_part('template-parts/forms/contact'); ?>
    <?php elseif ($isRecruitmentPage) : ?>
        <?php get_template_part('template-parts/forms/recruitment'); ?>
    <?php endif; ?>
    <?php if (trim(wp_strip_all_tags($pageContent)) !== '') : ?>
        <section class="mx-auto max-w-6xl px-5 pb-12 lg:px-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8"><div class="prose prose-slate max-w-none text-sm leading-8"><?php while (have_posts()) : the_post(); the_content(); endwhile; ?></div></div>
        </section>
    <?php elseif (!class_exists(\ErfanSanat\Theme\Admin\SectionManager::class)) : ?>
        <section class="mx-auto max-w-6xl px-5 py-12 lg:px-8"><h1 class="text-3xl font-black text-slate-950"><?php echo esc_html($pageTitle); ?></h1></section>
    <?php endif; ?>
</main>
