<?php
/** Accessible, useful not-found page with search and key site routes. */
defined('ABSPATH') || exit;
get_header();
?>
<main id="primary" class="min-h-[68vh] bg-slate-50/60 px-5 pb-16 pt-28 lg:px-8">
    <section class="mx-auto grid max-w-6xl items-center gap-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10 lg:grid-cols-12 lg:p-14">
        <div class="text-center lg:col-span-7 lg:text-right">
            <p class="text-sm font-black tracking-widest text-emerald-700">۴۰۴</p>
            <h1 class="mt-2 text-3xl font-black leading-relaxed text-slate-950 sm:text-4xl"><?php echo esc_html__('صفحه‌ای که دنبالش هستید پیدا نشد.', 'erfansanat-theme'); ?></h1>
            <p class="mt-3 text-sm leading-7 text-slate-600"><?php echo esc_html__('نشانی را بررسی کنید یا برای پیدا کردن محصول، پروژه و مقاله از جستجو استفاده کنید.', 'erfansanat-theme'); ?></p>
            <form role="search" method="get" class="mt-6 flex flex-col gap-2 sm:flex-row" action="<?php echo esc_url(home_url('/')); ?>"><label class="sr-only" for="not-found-search"><?php echo esc_html__('جستجو در وب‌سایت', 'erfansanat-theme'); ?></label><input id="not-found-search" type="search" name="s" placeholder="<?php echo esc_attr__('چه چیزی را جستجو می‌کنید؟', 'erfansanat-theme'); ?>" class="min-h-12 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /><button type="submit" class="min-h-12 rounded-xl bg-emerald-700 px-6 text-sm font-extrabold text-white hover:bg-emerald-800"><?php echo esc_html__('جستجو', 'erfansanat-theme'); ?></button></form>
            <div class="mt-5 flex flex-wrap justify-center gap-2 lg:justify-start"><a href="<?php echo esc_url(home_url('/')); ?>" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-extrabold text-slate-700 hover:border-emerald-300 hover:text-emerald-800"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a><a href="<?php echo esc_url(get_post_type_archive_link('projects') ?: home_url('/projects/')); ?>" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-extrabold text-slate-700 hover:border-emerald-300 hover:text-emerald-800"><?php echo esc_html__('پروژه‌ها', 'erfansanat-theme'); ?></a><a href="<?php echo esc_url(home_url('/shop/')); ?>" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-extrabold text-slate-700 hover:border-emerald-300 hover:text-emerald-800"><?php echo esc_html__('فروشگاه', 'erfansanat-theme'); ?></a></div>
        </div>
        <div aria-hidden="true" class="mx-auto grid h-48 w-48 place-items-center rounded-full bg-emerald-50 text-7xl font-black text-emerald-700 lg:col-span-5 lg:h-64 lg:w-64">?</div>
    </section>
</main>
<?php get_footer(); ?>
