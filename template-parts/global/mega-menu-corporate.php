<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esLinks = [['درباره ما', '/about/'], ['خدمات', '/smartelement/'], ['مجوزها و افتخارات', '/licenses-and-honors/'], ['استخدام', '/recruitment/'], ['سوالات متداول', '/faq/'], ['شرایط گارانتی', '/refund_returns/']];
?>
<div class="group static" data-mega-menu-corporate="true"><button type="button" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700" data-desktop-mega-trigger="corporate"><span>شرکت</span></button>
<div class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100" data-desktop-mega-panel="corporate"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><ul class="grid grid-cols-2 gap-1 p-3.5">
<?php foreach ($esLinks as [$label, $path]) : ?><li><a class="block rounded-xl px-3 py-2.5 text-sm font-bold hover:bg-emerald-50" href="<?php echo esc_url(home_url($path)); ?>"><?php echo esc_html($label); ?></a></li><?php endforeach; ?>
</ul></div></div></div>
