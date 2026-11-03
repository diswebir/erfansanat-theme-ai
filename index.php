<?php
/**
 * قالب پیش‌فرض (Fallback) — آخرین لایه نگاشت سلسله‌مراتب وردپرس
 *
 * هر شرطی که به این فایل برسد یعنی قالب اختصاصی آن نوع محتوا هنوز پیاده نشده؛
 * خروجی با مارک‌آپ و کلاس‌های میرور استاتیک رندر می‌شود تا ظاهر سایت هرگز نشکند.
 *
 * @package ErfanSanat
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="main" class="relative z-10 mx-auto max-w-[1300px] scroll-mt-24 px-4 pt-12 pb-8 sm:px-6 sm:pt-16 lg:px-8">
    <div class="rounded-2xl border border-slate-200/70 bg-white p-8 text-center sm:p-12">
        <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">
            <?php echo esc_html__('محتوای این صفحه در دست تعریف است', 'erfansanat'); ?>
        </h1>
        <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-slate-600 sm:text-base">
            <?php echo esc_html__('این بخش هنوز به قالب اختصاصی خود متصل نشده یا محتوایی برای نمایش ندارد. از منوی سایت استفاده کنید یا به صفحه اصلی بازگردید.', 'erfansanat'); ?>
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="<?php echo esc_url(home_url('/')); ?>"
               class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                <?php echo esc_html__('بازگشت به صفحه اصلی', 'erfansanat'); ?>
            </a>
            <?php if (function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0 && get_permalink(wc_get_page_id('shop'))) : ?>
                <a href="<?php echo esc_url((string) get_permalink(wc_get_page_id('shop'))); ?>"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                    <?php echo esc_html__('مشاهده محصولات', 'erfansanat'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php
get_footer();
