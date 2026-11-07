<?php
/**
 * Dynamic project detail template with legacy gallery, completion date, taxonomy and used products.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

get_header();
$projectId = absint(get_queried_object_id());
$projectPost = $projectId ? get_post($projectId) : null;
if (!$projectPost instanceof WP_Post || 'projects' !== $projectPost->post_type) :
    ?>
    <main id="project-details" class="mx-auto max-w-6xl px-5 py-24 lg:px-8"><p class="rounded-2xl border border-slate-200 bg-white p-6 text-center text-slate-600"><?php echo esc_html__('این پروژه در دسترس نیست.', 'erfansanat-theme'); ?></p></main>
    <?php
    get_footer();
    return;
endif;

$projectMeta = class_exists(\ErfanSanat\Theme\Admin\ProjectMetaboxes::class)
    ? \ErfanSanat\Theme\Admin\ProjectMetaboxes::get_project_data($projectId)
    : [];
$projectTitle = get_the_title($projectId);
$projectDate = isset($projectMeta['finish-pro-time']) && is_scalar($projectMeta['finish-pro-time'])
    ? trim(sanitize_text_field((string) $projectMeta['finish-pro-time']))
    : '';
$projectTypes = get_the_terms($projectId, 'project_type');
$projectCities = get_the_terms($projectId, 'city');
$projectTypes = is_array($projectTypes) ? $projectTypes : [];
$projectCities = is_array($projectCities) ? $projectCities : [];
$projectGalleryIds = class_exists(\ErfanSanat\Theme\Admin\ProjectMetaboxes::class)
    ? \ErfanSanat\Theme\Admin\ProjectMetaboxes::get_project_gallery_ids($projectId)
    : [];
$featuredImageId = (int) get_post_thumbnail_id($projectId);
if ($featuredImageId) {
    array_unshift($projectGalleryIds, $featuredImageId);
}
$projectGalleryIds = array_values(array_unique(array_filter(array_map('absint', $projectGalleryIds))));
$projectGallery = [];
foreach (array_slice($projectGalleryIds, 0, 20) as $imageId) {
    $imageUrl = wp_get_attachment_image_url($imageId, 'large');
    if (!$imageUrl) {
        $imageUrl = wp_get_attachment_url($imageId);
    }
    if (is_string($imageUrl) && '' !== $imageUrl) {
        $projectGallery[] = ['id' => $imageId, 'url' => $imageUrl];
    }
}

$rawRelatedProducts = isset($projectMeta['mortabet_mahsole_2']) && is_array($projectMeta['mortabet_mahsole_2'])
    ? $projectMeta['mortabet_mahsole_2']
    : [];
$relatedProductIds = [];
foreach ($rawRelatedProducts as $relatedProduct) {
    if (is_array($relatedProduct)) {
        $relatedProduct = $relatedProduct['ID'] ?? $relatedProduct['id'] ?? $relatedProduct['product_id'] ?? 0;
    } elseif (is_object($relatedProduct) && method_exists($relatedProduct, 'get_id')) {
        $relatedProduct = $relatedProduct->get_id();
    }
    $relatedProductId = absint($relatedProduct);
    $relatedProductPost = $relatedProductId ? get_post($relatedProductId) : null;
    if ($relatedProductPost instanceof WP_Post && 'product' === $relatedProductPost->post_type && 'publish' === $relatedProductPost->post_status) {
        $relatedProductIds[] = $relatedProductId;
    }
}
$relatedProductIds = array_values(array_unique($relatedProductIds));

$relatedProjectTaxQuery = [];
foreach (['project_type' => $projectTypes, 'city' => $projectCities] as $relatedTaxonomy => $relatedTerms) {
    $termIds = [];
    foreach ($relatedTerms as $relatedTerm) {
        if ($relatedTerm instanceof WP_Term) {
            $termIds[] = (int) $relatedTerm->term_id;
        }
    }
    if ($termIds) {
        $relatedProjectTaxQuery[] = [
            'taxonomy' => $relatedTaxonomy,
            'field' => 'term_id',
            'terms' => $termIds,
        ];
    }
}
$relatedProjectIds = [];
if ($relatedProjectTaxQuery) {
    if (count($relatedProjectTaxQuery) > 1) {
        $relatedProjectTaxQuery['relation'] = 'OR';
    }
    $relatedProjectsQuery = new WP_Query([
        'post_type' => 'projects',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'post__not_in' => [$projectId],
        'fields' => 'ids',
        'tax_query' => $relatedProjectTaxQuery,
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
    ]);
    $relatedProjectIds = array_values(array_filter(array_map('absint', $relatedProjectsQuery->posts)));
    wp_reset_postdata();
}
?>
<main id="project-details" class="bg-slate-50/60">
    <?php while (have_posts()) : the_post(); ?>
        <section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(900px_420px_at_80%_0%,rgba(0,177,64,0.09),transparent_62%)]"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
                <nav aria-label="<?php echo esc_attr__('مسیر صفحه', 'erfansanat-theme'); ?>" class="mb-6 inline-flex flex-wrap items-center gap-2 rounded-xl border border-emerald-100 bg-white/90 px-4 py-2 text-xs font-semibold text-slate-500">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a><span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url(get_post_type_archive_link('projects') ?: home_url('/projects/')); ?>" class="hover:text-emerald-700"><?php echo esc_html__('پروژه‌ها', 'erfansanat-theme'); ?></a>
                    <?php if (isset($projectTypes[0]) && $projectTypes[0] instanceof WP_Term) : $typeUrl = get_term_link($projectTypes[0]); if (!is_wp_error($typeUrl)) : ?><span aria-hidden="true">/</span><a href="<?php echo esc_url($typeUrl); ?>" class="hover:text-emerald-700"><?php echo esc_html($projectTypes[0]->name); ?></a><?php endif; endif; ?>
                    <span aria-hidden="true">/</span><span aria-current="page" class="max-w-[18rem] truncate"><?php echo esc_html($projectTitle); ?></span>
                </nav>

                <div class="grid items-start gap-7 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        <?php if ($projectGallery) : ?>
                            <div data-gallery-root="true" class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-2 shadow-sm sm:p-3">
                                <div class="group relative overflow-hidden rounded-2xl bg-slate-900">
                                    <img src="<?php echo esc_url($projectGallery[0]['url']); ?>" alt="<?php echo esc_attr($projectTitle); ?>" data-gallery-main-img="true" class="h-[17rem] w-full cursor-zoom-in object-cover sm:h-[25rem] lg:h-[31rem]" fetchpriority="high" />
                                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-gradient-to-t from-slate-950/75 to-transparent p-4 text-white">
                                        <span data-gallery-counter="true" class="rounded-lg bg-slate-950/50 px-2.5 py-1 text-xs font-bold"><?php echo esc_html('۱ / ' . count($projectGallery)); ?></span>
                                        <button type="button" data-gallery-expand="true" class="rounded-lg border border-white/40 bg-slate-950/45 px-3 py-1.5 text-xs font-bold"><?php echo esc_html__('نمایش بزرگ', 'erfansanat-theme'); ?></button>
                                    </div>
                                    <?php if (count($projectGallery) > 1) : ?>
                                        <button type="button" data-gallery-prev="true" aria-label="<?php echo esc_attr__('تصویر قبلی', 'erfansanat-theme'); ?>" class="absolute right-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full border border-white/30 bg-slate-950/55 text-xl text-white">›</button>
                                        <button type="button" data-gallery-next="true" aria-label="<?php echo esc_attr__('تصویر بعدی', 'erfansanat-theme'); ?>" class="absolute left-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full border border-white/30 bg-slate-950/55 text-xl text-white">‹</button>
                                    <?php endif; ?>
                                </div>
                                <?php if (count($projectGallery) > 1) : ?>
                                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                                        <?php foreach ($projectGallery as $galleryIndex => $image) : ?>
                                            <button type="button" data-gallery-thumb="<?php echo esc_attr((string) $galleryIndex); ?>" data-gallery-src="<?php echo esc_url($image['url']); ?>" aria-label="<?php echo esc_attr(sprintf(__('نمایش تصویر %d', 'erfansanat-theme'), $galleryIndex + 1)); ?>" class="relative h-16 w-20 shrink-0 overflow-hidden rounded-xl border-2 <?php echo 0 === $galleryIndex ? 'border-emerald-500' : 'border-transparent opacity-70'; ?> sm:h-20 sm:w-24">
                                                <?php echo wp_get_attachment_image($image['id'], 'thumbnail', false, ['alt' => $projectTitle, 'class' => 'h-full w-full object-cover', 'loading' => 'lazy']); ?>
                                                <span data-gallery-thumb-ring="true" class="pointer-events-none absolute inset-0 rounded-lg ring-2 ring-inset ring-emerald-500 <?php echo 0 === $galleryIndex ? 'block' : 'hidden'; ?>"></span>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div data-gallery-lightbox="true" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__('نمایش تصویر پروژه', 'erfansanat-theme'); ?>" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-sm">
                                    <button type="button" data-gallery-lightbox-close="true" aria-label="<?php echo esc_attr__('بستن', 'erfansanat-theme'); ?>" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-2xl text-white">×</button>
                                    <?php if (count($projectGallery) > 1) : ?><button type="button" data-gallery-lightbox-prev="true" aria-label="<?php echo esc_attr__('تصویر قبلی', 'erfansanat-theme'); ?>" class="absolute right-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-2xl text-white">‹</button><button type="button" data-gallery-lightbox-next="true" aria-label="<?php echo esc_attr__('تصویر بعدی', 'erfansanat-theme'); ?>" class="absolute left-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-2xl text-white">›</button><?php endif; ?>
                                    <img src="<?php echo esc_url($projectGallery[0]['url']); ?>" alt="<?php echo esc_attr($projectTitle); ?>" data-gallery-lightbox-img="true" class="max-h-[84vh] max-w-[90vw] rounded-2xl object-contain shadow-2xl" />
                                    <?php if (count($projectGallery) > 1) : ?><div class="absolute bottom-6 flex items-center gap-2"><?php foreach ($projectGallery as $galleryIndex => $image) : ?><button type="button" data-gallery-lightbox-dot="<?php echo esc_attr((string) $galleryIndex); ?>" aria-label="<?php echo esc_attr(sprintf(__('تصویر %d', 'erfansanat-theme'), $galleryIndex + 1)); ?>" class="h-2 rounded-full <?php echo 0 === $galleryIndex ? 'w-7 bg-emerald-400' : 'w-2 bg-white/40'; ?>"></button><?php endforeach; ?></div><?php endif; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="grid min-h-[17rem] place-items-center rounded-3xl border border-dashed border-emerald-200 bg-white p-8 text-center sm:min-h-[25rem]"><div><span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 text-2xl text-emerald-700" aria-hidden="true">✦</span><p class="mt-3 text-sm font-bold text-slate-500"><?php echo esc_html__('تصویری برای این پروژه ثبت نشده است.', 'erfansanat-theme'); ?></p></div></div>
                        <?php endif; ?>
                    </div>

                    <aside class="lg:col-span-5">
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                            <p class="text-xs font-extrabold text-emerald-700"><?php echo esc_html__('نمونه‌کار اجرایی عرفان صنعت', 'erfansanat-theme'); ?></p>
                            <h1 class="mt-2 text-2xl font-black leading-relaxed text-slate-950 sm:text-3xl"><?php echo esc_html($projectTitle); ?></h1>
                            <?php if ($projectPost->post_excerpt) : ?><p class="mt-3 text-sm leading-7 text-slate-600"><?php echo esc_html(get_the_excerpt($projectId)); ?></p><?php endif; ?>
                            <?php if ($projectTypes || $projectCities || $projectDate) : ?>
                                <dl class="mt-6 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                    <?php if ($projectCities) : ?><div class="rounded-xl border border-slate-100 bg-slate-50 p-3"><dt class="text-[11px] font-semibold text-slate-500"><?php echo esc_html__('شهر محل اجرا', 'erfansanat-theme'); ?></dt><dd class="mt-1 flex flex-wrap gap-2 text-xs font-extrabold text-slate-900"><?php foreach ($projectCities as $projectTerm) : if (!$projectTerm instanceof WP_Term) { continue; } $cityUrl = get_term_link($projectTerm); ?><span><?php if (!is_wp_error($cityUrl)) : ?><a href="<?php echo esc_url($cityUrl); ?>" class="hover:text-emerald-700"><?php echo esc_html($projectTerm->name); ?></a><?php else : echo esc_html($projectTerm->name); endif; ?></span><?php endforeach; ?></dd></div><?php endif; ?>
                                    <?php if ($projectTypes) : ?><div class="rounded-xl border border-slate-100 bg-slate-50 p-3"><dt class="text-[11px] font-semibold text-slate-500"><?php echo esc_html__('نوع پروژه', 'erfansanat-theme'); ?></dt><dd class="mt-1 flex flex-wrap gap-2 text-xs font-extrabold text-slate-900"><?php foreach ($projectTypes as $projectTerm) : if (!$projectTerm instanceof WP_Term) { continue; } $typeUrl = get_term_link($projectTerm); ?><span><?php if (!is_wp_error($typeUrl)) : ?><a href="<?php echo esc_url($typeUrl); ?>" class="hover:text-emerald-700"><?php echo esc_html($projectTerm->name); ?></a><?php else : echo esc_html($projectTerm->name); endif; ?></span><?php endforeach; ?></dd></div><?php endif; ?>
                                    <?php if ($projectDate) : ?><div class="rounded-xl border border-slate-100 bg-slate-50 p-3 sm:col-span-2"><dt class="text-[11px] font-semibold text-slate-500"><?php echo esc_html__('زمان تکمیل پروژه', 'erfansanat-theme'); ?></dt><dd class="mt-1 text-xs font-extrabold text-slate-900"><?php echo esc_html($projectDate); ?></dd></div><?php endif; ?>
                                </dl>
                            <?php endif; ?>
                            <a href="#project-description" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-xs font-extrabold text-white hover:bg-emerald-800"><?php echo esc_html__('مشاهده شناسنامه پروژه', 'erfansanat-theme'); ?><span aria-hidden="true">↓</span></a>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <?php if (trim((string) $projectPost->post_content) !== '' || $projectDate || $projectCities || $projectTypes) : ?>
            <section id="project-description" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-5 py-12 lg:px-8">
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <?php if (trim((string) $projectPost->post_content) !== '') : ?><div class="border-b border-slate-100 p-5 sm:p-8"><p class="mb-2 text-xs font-extrabold text-emerald-700"><?php echo esc_html__('درباره پروژه', 'erfansanat-theme'); ?></p><h2 class="mb-4 text-xl font-black leading-relaxed text-slate-950"><?php echo esc_html(sprintf(__('جزئیات اجرای %s', 'erfansanat-theme'), $projectTitle)); ?></h2><div class="prose prose-slate max-w-none text-sm leading-8"><?php the_content(); ?></div></div><?php endif; ?>
                    <?php if ($projectDate || $projectTypes || $projectCities) : ?>
                        <div class="p-5 sm:p-8"><h2 class="mb-4 text-lg font-black text-slate-900"><?php echo esc_html__('شناسنامه اجرایی پروژه', 'erfansanat-theme'); ?></h2><dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <?php if ($projectDate) : ?><div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><dt class="text-xs font-semibold text-slate-500"><?php echo esc_html__('زمان تکمیل', 'erfansanat-theme'); ?></dt><dd class="mt-1 text-sm font-extrabold text-slate-900"><?php echo esc_html($projectDate); ?></dd></div><?php endif; ?>
                            <?php foreach ($projectCities as $projectTerm) : if ($projectTerm instanceof WP_Term) : ?><div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><dt class="text-xs font-semibold text-slate-500"><?php echo esc_html__('شهر محل اجرا', 'erfansanat-theme'); ?></dt><dd class="mt-1 text-sm font-extrabold text-slate-900"><?php echo esc_html($projectTerm->name); ?></dd></div><?php endif; endforeach; ?>
                            <?php foreach ($projectTypes as $projectTerm) : if ($projectTerm instanceof WP_Term) : ?><div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><dt class="text-xs font-semibold text-slate-500"><?php echo esc_html__('نوع سازه', 'erfansanat-theme'); ?></dt><dd class="mt-1 text-sm font-extrabold text-slate-900"><?php echo esc_html($projectTerm->name); ?></dd></div><?php endif; endforeach; ?>
                        </dl></div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($relatedProductIds) : ?>
            <section id="used-products" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-14 lg:px-8">
                <div class="mb-6"><p class="text-xs font-extrabold text-emerald-700"><?php echo esc_html__('تولیدات کارخانه عرفان صنعت در این پروژه', 'erfansanat-theme'); ?></p><h2 class="mt-1 text-2xl font-black text-slate-900"><?php echo esc_html__('محصولات و تجهیزات استفاده‌شده', 'erfansanat-theme'); ?></h2></div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($relatedProductIds as $productIndex => $relatedProductId) : get_template_part('template-parts/cards/card-product', null, ['post_id' => $relatedProductId, 'variant' => 'archive', 'index' => $productIndex]); endforeach; ?></div>
            </section>
        <?php endif; ?>

        <?php if ($relatedProjectIds) : ?>
            <section class="border-t border-slate-200 bg-white py-12"><div class="mx-auto max-w-6xl px-5 lg:px-8"><div class="mb-6"><p class="text-xs font-extrabold text-emerald-700"><?php echo esc_html__('نمونه‌کارهای مرتبط', 'erfansanat-theme'); ?></p><h2 class="mt-1 text-2xl font-black text-slate-900"><?php echo esc_html__('سایر پروژه‌های شاخص عرفان صنعت', 'erfansanat-theme'); ?></h2></div><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($relatedProjectIds as $relatedProjectIndex => $relatedProjectId) : get_template_part('template-parts/cards/card-project', null, ['post_id' => $relatedProjectId, 'variant' => 'archive', 'index' => $relatedProjectIndex]); endforeach; ?></div></div></section>
        <?php endif; ?>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
