<?php
/**
 * card-post.php — standard blog card (static-mirror design).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

$pid       = get_the_ID();
$permalink = get_permalink($pid);
$title     = get_the_title($pid);
$thumb     = get_the_post_thumbnail_url($pid, 'large') ?: ERFANSANAT_THEME_URI . 'assets/images/string-lights.jpg';
$excerpt   = wp_trim_words(get_the_excerpt($pid), 18);
$date      = get_the_date('Y/m/d', $pid);
?>
<a href="<?php echo esc_url($permalink); ?>" class="card-glow group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white">
    <div class="relative overflow-hidden">
        <?php es_picture($thumb, $title, 'h-40 w-full object-cover transition-transform duration-500 group-hover:scale-105'); ?>
    </div>
    <div class="flex flex-1 flex-col p-5">
        <div class="text-[11px] font-bold text-emerald-600"><?php echo esc_html($date); ?></div>
        <h3 class="mt-1 text-base font-black leading-snug text-slate-900"><?php echo esc_html($title); ?></h3>
        <p class="mt-2 text-xs leading-relaxed text-slate-500"><?php echo esc_html($excerpt); ?></p>
    </div>
</a>
