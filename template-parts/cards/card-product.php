<?php
/**
 * card-product.php — standard product card matching the static mirror's
 * card-glow design. Expects a global $post product in the loop.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

$pid       = get_the_ID();
$permalink = get_permalink($pid);
$title     = get_the_title($pid);
$thumb     = get_the_post_thumbnail_url($pid, 'large') ?: ERFANSANAT_THEME_URI . 'assets/images/light-tree.jpg';
$true_sale = get_post_meta($pid, 'true_sale', true);
$price     = function_exists('wc_get_product') ? wc_get_product($pid) : null;
?>
<a href="<?php echo esc_url($permalink); ?>" class="card-glow group relative flex h-full min-h-[260px] sm:min-h-[295px] flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 p-5 sm:p-6">
    <div class="relative z-10 flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="text-[11px] font-bold text-emerald-400"><?php echo $true_sale === 'true' ? esc_html__('قابل خرید آنلاین', 'erfansanat') : esc_html__('استعلام قیمت', 'erfansanat'); ?></div>
            <h3 class="mt-1 text-base font-black leading-snug text-white sm:text-lg"><?php echo esc_html($title); ?></h3>
        </div>
        <?php if ($price) : ?>
            <span class="shrink-0 rounded-full bg-white/10 px-3 py-1 text-[11px] font-bold text-white"><?php echo wp_kses_post($price->get_price_html()); ?></span>
        <?php endif; ?>
    </div>
    <div class="relative z-10 mt-6 overflow-hidden rounded-2xl">
        <?php es_picture($thumb, $title, 'h-36 w-full object-cover transition-transform duration-500 group-hover:scale-105'); ?>
    </div>
</a>
