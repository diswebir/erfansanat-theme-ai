<?php
/**
 * single-product.php — product detail page (replaces JetEngine template).
 * Connects every legacy meta key; hides empty optional blocks; supports
 * both online-buy (true_sale=true) and inquiry (true_sale=false) products.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

get_header();
while (have_posts()) : the_post();
    $pid        = get_the_ID();
    $zireonvan  = get_post_meta($pid, 'zireonvan', true);
    $english    = get_post_meta($pid, 'english_titl', true);
    $beforeSale = get_post_meta($pid, 'before-sale-c', true);
    $trueSale   = get_post_meta($pid, 'true_sale', true) === 'true';
    $catalog    = get_post_meta($pid, 'product-catalog-file', true);

    // Gallery: legacy galerry_produ + WooCommerce standard fallback
    $gallery = \ErfanSanat\Theme\Admin\ProductMetaboxes::read_gallery($pid);
    if (!$gallery) {
        $wc = get_post_meta($pid, '_product_image_gallery', true);
        $gallery = $wc ? array_filter(array_map('absint', explode(',', (string) $wc))) : [];
    }

    $legalIds = [
        'gs1_id'        => __('شناسه GS1 (GTIN)', 'erfansanat'),
        'national_code' => __('کد ملی کالا', 'erfansanat'),
        'hs_id'         => __('شناسه HS', 'erfansanat'),
        'isic_id'       => __('شناسه ISIC', 'erfansanat'),
        'cpc_id'        => __('شناسه CPC', 'erfansanat'),
        'product_id'    => __('شناسه محصول', 'erfansanat'),
    ];

    // 18 technical attributes (pa_*)
    $paKeys = ['pa_after-sales-support','pa_beam-angle','pa_body-material','pa_brand','pa_bulb-type','pa_cable-material','pa_certifications','pa_color','pa_diameter','pa_diffuser-color','pa_input-voltage','pa_intensity','pa_ip-rating','pa_led-chip-type','pa_lifespan','pa_size','pa_warranty','pa_wattage'];

    $features   = \ErfanSanat\Theme\Admin\ProductMetaboxes::read_repeater($pid, 'product-feature');
    $features2  = \ErfanSanat\Theme\Admin\ProductMetaboxes::read_repeater($pid, 'product-feature2_copy');
    $usages     = \ErfanSanat\Theme\Admin\ProductMetaboxes::read_repeater($pid, 'usage_product');
    $faqs       = \ErfanSanat\Theme\Admin\ProductMetaboxes::read_repeater($pid, 'faq');

    $usageTerms = get_post_meta($pid, 'usage_countr', true);
    $usageTerms = is_string($usageTerms) ? (json_decode($usageTerms, true) ?: []) : (array) $usageTerms;
    $usageNames = [];
    foreach ($usageTerms as $tid) {
        $term = get_term(absint($tid), 'project_type');
        if ($term && !is_wp_error($term)) { $usageNames[] = $term->name; }
    }

    $projectIds = get_post_meta($pid, 'project-ids', true);
    $projectIds = is_string($projectIds) ? (json_decode($projectIds, true) ?: []) : (array) $projectIds;
?>
<main id="main" class="site-main">
    <section class="relative bg-[#f6f8f7] pt-28 pb-10 sm:pt-32 lg:pt-36">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl lg:text-4xl"><?php the_title(); ?></h1>
            <?php if ($zireonvan) : ?><p class="mt-2 text-sm font-bold text-emerald-700 sm:text-base"><?php echo esc_html($zireonvan); ?></p><?php endif; ?>
            <?php if ($english) : ?><p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400" dir="ltr"><?php echo esc_html($english); ?></p><?php endif; ?>

            <?php if ($beforeSale) : ?>
                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800"><?php echo esc_html($beforeSale); ?></div>
            <?php endif; ?>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <?php if ($trueSale) : ?>
                    <?php woocommerce_template_single_add_to_cart(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/تماس-با-ما/')); ?>" class="rounded-2xl bg-emerald-700 px-6 py-3 text-sm font-black text-white"><?php esc_html_e('استعلام قیمت', 'erfansanat'); ?></a>
                <?php endif; ?>
                <?php if ($catalog) : ?>
                    <a href="<?php echo esc_url($catalog); ?>" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-800" download><?php esc_html_e('دانلود کاتالوگ', 'erfansanat'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ($gallery) : ?>
    <section class="py-10" data-gallery="true">
        <div class="mx-auto grid max-w-[1300px] gap-4 px-4 sm:grid-cols-2 sm:px-6 lg:px-8">
            <?php foreach ($gallery as $imgId) : ?>
                <img src="<?php echo esc_url(wp_get_attachment_image_url($imgId, 'large')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="h-64 w-full rounded-3xl object-cover" loading="lazy" />
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (array_filter(array_map(fn($k) => get_post_meta($pid, $k, true), array_keys($legalIds)))) : ?>
    <section class="py-10">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('شناسه‌های قانونی و صنعتی', 'erfansanat'); ?></h2>
            <table class="mt-4 w-full text-sm">
                <?php foreach ($legalIds as $key => $label) : $val = get_post_meta($pid, $key, true); if ($val) : ?>
                    <tr class="border-b border-slate-100"><th class="py-2 text-right font-bold text-slate-500"><?php echo esc_html($label); ?></th><td class="py-2 text-slate-900"><?php echo esc_html($val); ?></td></tr>
                <?php endif; endforeach; ?>
            </table>
        </div>
    </section>
    <?php endif; ?>

    <?php
    $specRows = [];
    foreach ($paKeys as $pa) {
        $terms = get_the_terms($pid, $pa);
        if ($terms && !is_wp_error($terms)) {
            $specRows[$pa] = implode('، ', wp_list_pluck($terms, 'name'));
        }
    }
    ?>
    <?php if ($specRows) : ?>
    <section class="py-10">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('جدول مشخصات فنی', 'erfansanat'); ?></h2>
            <table class="mt-4 w-full text-sm">
                <?php foreach ($specRows as $pa => $val) : ?>
                    <tr class="border-b border-slate-100"><th class="py-2 text-right font-bold text-slate-500"><?php echo esc_html($pa); ?></th><td class="py-2 text-slate-900"><?php echo esc_html($val); ?></td></tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($features || $features2) : ?>
    <section class="py-10">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('مشخصات محصول', 'erfansanat'); ?></h2>
            <ul class="mt-4 list-disc pr-5 text-sm text-slate-700">
                <?php foreach ($features as $row) : if (!empty($row['add-new-feature'])) : ?><li><?php echo esc_html($row['add-new-feature']); ?></li><?php endif; endforeach; ?>
            </ul>
            <?php if ($features2) : ?>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <?php foreach ($features2 as $row) : if (!empty($row['add-new-feature'])) : ?>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4"><div class="text-xs font-black text-slate-900"><?php echo esc_html($row['add-new-feature']); ?></div><div class="mt-1 text-sm font-bold text-emerald-700"><?php echo esc_html($row['vaule_of_variable'] ?? ''); ?></div></div>
                <?php endif; endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($usages || $usageNames) : ?>
    <section class="py-10">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('کاربردها و موارد مصرف', 'erfansanat'); ?></h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <?php foreach ($usageNames as $n) : ?><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700"><?php echo esc_html($n); ?></span><?php endforeach; ?>
            </div>
            <ul class="mt-4 list-disc pr-5 text-sm text-slate-700">
                <?php foreach ($usages as $row) : if (!empty($row['add-new-usage'])) : ?><li><?php echo esc_html($row['add-new-usage']); ?></li><?php endif; endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($projectIds) : ?>
    <section class="py-10">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('پروژه‌های اجراشده با این محصول', 'erfansanat'); ?></h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php
                $pq = new WP_Query(['post_type' => 'projects', 'post__in' => array_map('absint', $projectIds), 'posts_per_page' => -1, 'no_found_rows' => true]);
                while ($pq->have_posts()) : $pq->the_post();
                    get_template_part('template-parts/cards/card', 'project');
                endwhile; wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($faqs) : ?>
    <section class="py-10" data-faq="true">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('سوالات متداول', 'erfansanat'); ?></h2>
            <div class="mt-4 divide-y divide-slate-100 rounded-3xl border border-slate-200 bg-white">
                <?php foreach ($faqs as $row) : if (!empty($row['name_question'])) : ?>
                    <details class="group p-4">
                        <summary class="cursor-pointer text-sm font-black text-slate-900"><?php echo esc_html($row['name_question']); ?></summary>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600"><?php echo esc_html($row['answer_question'] ?? ''); ?></p>
                    </details>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="py-10">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('محصولات مشابه', 'erfansanat'); ?></h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php
                $cats = wp_get_post_terms($pid, 'product_cat', ['fields' => 'ids']);
                $rq = new WP_Query([
                    'post_type'      => 'product',
                    'posts_per_page' => 4,
                    'post__not_in'   => [$pid],
                    'tax_query'      => $cats ? [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cats]] : [],
                    'no_found_rows'  => true,
                ]);
                while ($rq->have_posts()) : $rq->the_post();
                    get_template_part('template-parts/cards/card', 'product');
                endwhile; wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

    <section class="pb-16"><?php comments_template(); ?></section>
</main>
<?php endwhile; ?>
<?php get_footer();
