<?php
/** Desktop header bar sliced from static-mirror/index.html > header. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esLogo = (string) es_option('site_logo', '');
$esLogo = $esLogo !== '' ? $esLogo : es_image_uri('ese-logo.svg');
$esCartCount = 0;
if (function_exists('WC')) { try { $esWoo = WC(); if (is_object($esWoo) && isset($esWoo->cart) && is_object($esWoo->cart) && method_exists($esWoo->cart, 'get_cart_contents_count')) $esCartCount = (int) $esWoo->cart->get_cart_contents_count(); } catch (Throwable) { $esCartCount = 0; } }
?>
<header data-navbar-header="true" class="fixed inset-x-0 top-0 z-50 transition-all duration-300 border-b border-slate-200/70 bg-white/95 backdrop-blur-md">
<?php if ((bool) es_option('announcement_enabled', true)) : ?>
<div class="hidden border-b border-emerald-100/70 bg-emerald-50/55 text-[11px] text-slate-600 lg:block"><div class="mx-auto flex max-w-[1300px] items-center justify-between px-5 py-1.5 lg:px-8"><div class="flex items-center gap-2.5 font-medium"><span class="font-extrabold text-emerald-800"><?php echo esc_html((string) es_option('brand_name', '')); ?> (ESE)</span><span class="text-slate-300">·</span><span><?php echo esc_html((string) es_option('announcement_text', 'گواهینامه ISO 9001:2015 و کدهای رسمی سامانه ستاد و GS1')); ?></span></div><div class="flex items-center gap-3.5"><a class="font-bold text-slate-600 transition-colors hover:text-emerald-700" href="<?php echo esc_url(home_url('/licenses-and-honors/')); ?>">مجوزها و افتخارات</a><span class="text-slate-300">·</span><a class="font-bold text-slate-600 transition-colors hover:text-emerald-700" href="<?php echo esc_url(home_url('/refund_returns/')); ?>">گارانتی ۲۴ ماهه</a><span class="text-slate-300">·</span><a class="font-bold text-slate-600 transition-colors hover:text-emerald-700" href="<?php echo esc_url(home_url('/recruitment/')); ?>">استخدام</a></div></div></div>
<?php endif; ?>
<div class="relative mx-auto flex max-w-[1300px] items-center justify-between gap-3 px-4 py-2.5 sm:px-5 lg:px-8">
<a class="group flex shrink-0 items-center gap-2.5 whitespace-nowrap" href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url($esLogo); ?>" alt="<?php echo esc_attr((string) es_option('brand_name', '')); ?>" class="h-9 w-9 sm:h-10 sm:w-10 object-contain" /><div class="leading-tight"><div class="text-[14px] font-black tracking-tight text-slate-900 sm:text-[15px]"><?php echo esc_html((string) es_option('brand_name', '')); ?></div><div class="text-[10px] font-semibold text-slate-500"><?php echo esc_html((string) es_option('brand_tagline', '')); ?></div></div></a>
<nav aria-label="منوی اصلی" class="hidden items-center gap-1 lg:flex">
<?php
if (has_nav_menu('main-mega-menu')) {
    wp_nav_menu(['theme_location' => 'main-mega-menu', 'container' => false, 'items_wrap' => '%3$s', 'fallback_cb' => false, 'walker' => class_exists(\ErfanSanat\Theme\Navigation\MegaMenuWalker::class) ? new \ErfanSanat\Theme\Navigation\MegaMenuWalker() : '']);
} else {
    if (locate_template('template-parts/global/mega-menu-products.php', false, false) !== '') get_template_part('template-parts/global/mega-menu-products');
    if (locate_template('template-parts/global/mega-menu-projects.php', false, false) !== '') get_template_part('template-parts/global/mega-menu-projects');
    if (locate_template('template-parts/global/mega-menu-corporate.php', false, false) !== '') get_template_part('template-parts/global/mega-menu-corporate');
}
?>
</nav>
<div class="flex items-center gap-2">
<?php if ((bool) es_option('header_show_search', true)) : ?><button type="button" data-search-modal-open class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-700 transition hover:bg-slate-50" aria-label="جستجو">⌕</button><?php endif; ?>
<?php if ((bool) es_option('header_show_cart', true)) : ?><button type="button" data-cart-modal-open class="relative grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-700 transition hover:bg-slate-50" aria-label="سبد خرید">🛒<span data-header-cart-badge="true" class="absolute -top-1 -left-1 grid h-5 min-w-5 place-items-center rounded-full bg-emerald-600 px-1 text-[10px] font-bold text-white"><?php echo esc_html((string) $esCartCount); ?></span></button><?php endif; ?>
<a href="tel:<?php echo esc_attr((string) es_option('header_phone', '')); ?>" class="hidden rounded-xl bg-emerald-700 px-4 py-2.5 text-[13px] font-extrabold text-white transition hover:bg-emerald-800 md:block"><?php echo esc_html((string) es_option('header_cta_text', 'مشاوره رایگان')); ?> <?php echo esc_html((string) es_option('header_phone', '')); ?></a>
<button type="button" data-mobile-toggle class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-800 lg:hidden" aria-label="منوی موبایل">☰</button>
</div>
</div>
<?php if (locate_template('template-parts/global/search-modal.php', false, false) !== '') get_template_part('template-parts/global/search-modal'); ?>
</header>
