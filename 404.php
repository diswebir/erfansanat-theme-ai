<?php
/**
 * 404.php — not found page in the theme shell.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
get_header();
?>
<main id="main" class="site-main">
    <section class="relative bg-[#f6f8f7] py-28 text-center lg:py-36">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <div class="text-7xl font-black text-emerald-700">404</div>
            <h1 class="mt-4 text-2xl font-black text-slate-900"><?php esc_html_e('صفحه پیدا نشد', 'erfansanat'); ?></h1>
            <p class="mt-2 text-sm font-bold text-slate-500"><?php esc_html_e('نشانی مورد نظر یافت نشد.', 'erfansanat'); ?></p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="mt-6 inline-block rounded-2xl bg-emerald-700 px-6 py-3 text-sm font-black text-white"><?php esc_html_e('بازگشت به صفحه اصلی', 'erfansanat'); ?></a>
        </div>
    </section>
</main>
<?php get_footer();
