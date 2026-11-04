<?php
/** Template Name: ورود کاربران */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
?>
<main id="primary" class="site-main mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8" data-auth-page="true"><section class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-10"><header class="mb-7 text-center"><p class="text-xs font-bold text-emerald-700">ورود و پنل مشتریان</p><h1 class="mt-2 text-2xl font-black text-slate-900">ورود به حساب کاربری</h1></header><?php get_template_part('template-parts/global/auth-panels'); ?></section></main>
<?php get_footer(); ?>
