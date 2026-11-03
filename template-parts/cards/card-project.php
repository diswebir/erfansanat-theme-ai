<?php
/**
 * card-project.php — standard project card (static-mirror design).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

$pid       = get_the_ID();
$permalink = get_permalink($pid);
$title     = get_the_title($pid);
$thumb     = get_the_post_thumbnail_url($pid, 'large') ?: ERFANSANAT_THEME_URI . 'assets/images/light-tunnel.jpg';
$cities    = get_the_terms($pid, 'city');
$cityName  = ($cities && !is_wp_error($cities)) ? $cities[0]->name : '';
?>
<a href="<?php echo esc_url($permalink); ?>" class="card-glow group relative flex h-full min-h-[240px] flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6">
    <div class="relative z-10">
        <?php if ($cityName) : ?><div class="text-[11px] font-bold text-emerald-600"><?php echo esc_html($cityName); ?></div><?php endif; ?>
        <h3 class="mt-1 text-base font-black leading-snug text-slate-900 sm:text-lg"><?php echo esc_html($title); ?></h3>
    </div>
    <div class="relative z-10 mt-6 overflow-hidden rounded-2xl">
        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" class="h-32 w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
    </div>
</a>
