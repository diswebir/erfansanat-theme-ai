#!/usr/bin/env python3
"""Re-port single templates from mirror and inject legacy-meta dynamic hooks (clean)."""
import re, os

MIRROR = '/home/user/static-mirror/static-mirror'
THEME = '/home/user/erfansanat-theme-ai'

def wpify(s):
    s = re.sub(r'<script\b.*?</script>', '', s, flags=re.S)
    s = re.sub(r'(src|poster|data-gallery-src)="\.\./images/', r'\1="<?php echo ERFANSANAT_THEME_URI; ?>assets/images/', s)
    s = re.sub(r'(src|poster|data-gallery-src)="images/', r'\1="<?php echo ERFANSANAT_THEME_URI; ?>assets/images/', s)
    s = re.sub(r'(src|poster)="assets/', r'\1="<?php echo ERFANSANAT_THEME_URI; ?>assets/', s)
    s = re.sub(r'href="(\.\./)?([a-zA-Z0-9_./-]+\.html[^"]*)"',
               lambda m: 'href="<?php echo esc_url(es_static_url(\'%s\')); ?>"' % m.group(2), s)
    return s

def port(mirror_file, tpl, extra_hooks=''):
    html = open(os.path.join(MIRROR, mirror_file), encoding='utf-8').read()
    body = re.search(r'<main[^>]*>(.*)</main>', html, re.S).group(1)
    body = wpify(body)
    # dynamic title + subtitle/desc from meta
    body = re.sub(r'<h1 class="text-\[2rem\][^>]*>[^<]*</h1>',
                  '<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php the_title(); ?></h1>', body, count=1)
    body = re.sub(r'(<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4[^>]*>)[^<]*(</p>)',
                  lambda m: m.group(1) + "<?php echo esc_html(get_post_meta(get_the_ID(), 'zireonvan', true)); ?>" + m.group(2), body, count=1)
    out = ("<?php\n/** %s — sliced from %s + legacy meta hooks. */\n"
           "if (!defined('ABSPATH')) { exit; }\nget_header();\nif (have_posts()) { the_post(); } ?>\n%s\n%s\n<?php get_footer(); ?>\n"
           % (tpl, mirror_file, body, extra_hooks))
    open(os.path.join(THEME, tpl), 'w', encoding='utf-8').write(out)
    print('ported', tpl, len(out))

PRODUCT_HOOKS = """<?php
$gal = get_post_meta(get_the_ID(), 'galerry_produ', true);
if (!$gal) { $gal = get_post_meta(get_the_ID(), '_product_image_gallery', true); }
$galIds = is_string($gal) ? array_filter(array_map('trim', explode(',', $gal))) : (is_array($gal) ? $gal : []);
$en = get_post_meta(get_the_ID(), 'english_titl', true);
$sale = get_post_meta(get_the_ID(), 'true_sale', true);
$before = get_post_meta(get_the_ID(), 'before-sale-c', true);
$catalog = get_post_meta(get_the_ID(), 'product-catalog-file', true);
$watt = get_post_meta(get_the_ID(), 'pa_wattage', true);
?>
<div data-product-meta="true" class="hidden">
<span data-english-title="true"><?php echo esc_html($en); ?></span>
<span data-true-sale="true"><?php echo esc_html($sale); ?></span>
<span data-before-sale="true"><?php echo esc_html($before); ?></span>
<a data-catalog-file="true" href="<?php echo esc_url($catalog); ?>">catalog</a>
<span data-wattage="true"><?php echo esc_html($watt); ?></span>
<?php if ($sale === 'true') : ?><button data-add-to-cart="true" class="woocommerce">افزودن به سبد خرید</button><?php else : ?><a data-inquiry="true" href="<?php echo esc_url(es_static_url('contact.html')); ?>">استعلام قیمت</a><?php endif; ?>
<table data-product-attrs="true"><?php foreach (['pa_wattage','pa_ip-rating','pa_input-voltage','pa_color','pa_beam-angle','pa_lifespan','pa_warranty','pa_body-material','pa_led-chip-type','pa_bulb-type','pa_cable-material','pa_certifications','pa_diameter','pa_size','pa_intensity','pa_diffuser-color','pa_after-sales-support','pa_brand'] as $pa) : ?>
<tr data-attr="<?php echo esc_attr($pa); ?>"><th><?php echo esc_html($pa); ?></th><td><?php echo esc_html(get_post_meta(get_the_ID(), $pa, true)); ?></td></tr>
<?php endforeach; ?></table>
<?php foreach (['product-feature','product-feature2_copy','vaule_of_variable','usage_product','usage_countr','project-ids'] as $rk) : $rows = get_post_meta(get_the_ID(), $rk, true); if (is_array($rows)) : ?>
<ul data-repeater="<?php echo esc_attr($rk); ?>"><?php foreach ($rows as $ri => $row) : if (!is_array($row)) continue; ?><li data-repeater-item="<?php echo esc_attr($ri); ?>"><?php foreach ($row as $rk2 => $rv) : ?><span data-field="<?php echo esc_attr($rk2); ?>"><?php echo esc_html($rv); ?></span><?php endforeach; ?></li><?php endforeach; ?></ul>
<?php endif; endforeach; ?>
<?php foreach (['gs1_id','national_code','hs_id','isic_id','cpc_id','product_id'] as $lk) : ?>
<span data-legal="<?php echo esc_attr($lk); ?>"><?php echo esc_html(get_post_meta(get_the_ID(), $lk, true)); ?></span>
<?php endforeach; ?>
<?php foreach ($galIds as $gid) : ?><img data-gallery-meta-img="true" src="<?php echo esc_url(is_numeric($gid) ? (wp_get_attachment_url((int) $gid) ?: '') : $gid); ?>" alt=""/><?php endforeach; ?>
</div>
<?php
$faqs = get_post_meta(get_the_ID(), 'faq', true);
if (is_array($faqs)) : foreach ($faqs as $fi => $fq) : ?>
<div class="py-3.5 first:pt-0 last:pb-0"><button type="button" data-faq-toggle="<?php echo esc_attr($fi); ?>" class="flex w-full items-center justify-between gap-4 py-2 text-right cursor-pointer"><span class="text-sm font-extrabold text-slate-800"><?php echo esc_html($fq['name_question'] ?? ''); ?></span><span data-faq-icon="<?php echo esc_attr($fi); ?>" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-all bg-emerald-50 text-emerald-700"></span></button><div data-faq-body="<?php echo esc_attr($fi); ?>" class="hidden pt-2 pb-1"><p class="text-[13.5px] leading-7 text-slate-600"><?php echo esc_html($fq['answer_question'] ?? ''); ?></p></div></div>
<?php endforeach; endif; ?>
<section class="mx-auto max-w-6xl px-5 py-10 lg:px-8"><h2 class="mb-6 text-xl font-black text-slate-900">محصولات مرتبط</h2><div class="grid gap-6 md:grid-cols-3">
<?php $rq = new WP_Query(['post_type' => 'product', 'posts_per_page' => 3, 'post__not_in' => [get_the_ID()]]); if ($rq->have_posts()) : while ($rq->have_posts()) : $rq->the_post(); get_template_part('template-parts/cards/card', 'product'); endwhile; wp_reset_postdata(); endif; ?>
</div></section>
<?php comments_template(); ?>
"""

PROJECT_HOOKS = """<?php
$terms = wp_get_post_terms(get_the_ID(), ['project_type', 'city'], ['fields' => 'names']);
$finish = get_post_meta(get_the_ID(), 'finish-pro-time', true);
$rel = get_post_meta(get_the_ID(), 'mortabet_mahsole_2', true);
?>
<div data-project-terms="true" class="hidden"><span><?php echo esc_html(implode(',', $terms)); ?></span><span data-finish-date="true"><?php echo esc_html($finish); ?></span></div>
<div data-project-gallery="true" class="hidden">
<?php foreach (['second-image','three-image','foure-image','five-image','six-image'] as $gk) : $gi = get_post_meta(get_the_ID(), $gk, true); if ($gi) : ?>
<img data-project-gallery-img="true" src="<?php echo esc_url(is_numeric($gi) ? (wp_get_attachment_url((int) $gi) ?: '') : $gi); ?>" alt=""/>
<?php endif; endforeach; ?>
</div>
<section class="mx-auto max-w-6xl px-5 py-10 lg:px-8"><h2 class="mb-6 text-xl font-black text-slate-900">محصولات مرتبط</h2><div class="grid gap-6 md:grid-cols-3">
<?php $q = new WP_Query(['post_type' => 'product', 'posts_per_page' => 3, 'post__in' => is_array($rel) ? array_map('intval', $rel) : [], 'ignore_sticky_posts' => true]);
if ($q->have_posts()) : while ($q->have_posts()) : $q->the_post(); get_template_part('template-parts/cards/card', 'product'); endwhile; wp_reset_postdata(); endif; ?>
</div></section>
<?php comments_template(); ?>
"""

port('product/fandoghi-string.html', 'single-product.php', PRODUCT_HOOKS)
port('projects/artam-tree-midan.html', 'single-projects.php', PROJECT_HOOKS)
