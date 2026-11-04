<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
status_header(404);
nocache_headers();
get_header();
?>
<main id="primary" class="site-main"><section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8" data-error-page="404"><div class="rounded-3xl border border-slate-200 bg-white px-6 py-12 text-center md:px-12"><p class="text-6xl font-black tracking-tight text-emerald-700" aria-hidden="true">404</p><h1 class="mt-5 text-2xl font-black text-slate-900 md:text-4xl">صفحه مورد نظر پیدا نشد</h1><p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-slate-600">ممکن است نشانی صفحه تغییر کرده باشد یا صفحه حذف شده باشد. می‌توانید جستجو کنید یا از پیوندهای زیر استفاده کنید.</p>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" data-404-search="true" class="mx-auto mt-7 flex max-w-xl gap-3"><label class="sr-only" for="search-404">جستجو در سایت</label><input id="search-404" type="search" name="s" class="min-w-0 flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="محصول، پروژه یا مقاله"><button type="submit" class="rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">جستجو</button></form>
<nav aria-label="پیوندهای پیشنهادی" class="mt-8 flex flex-wrap justify-center gap-3"><a href="<?php echo esc_url(home_url('/')); ?>" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">صفحه اصلی</a><a href="<?php echo esc_url(home_url('/projects/')); ?>" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">پروژه‌ها</a><a href="<?php echo esc_url(home_url('/shop/')); ?>" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">فروشگاه</a><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">تماس با ما</a></nav></div></section></main>
<?php get_footer(); ?>
