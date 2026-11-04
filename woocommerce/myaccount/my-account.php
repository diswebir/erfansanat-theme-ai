<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
?>
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" data-my-account-page="true">
    <header class="mb-6"><p class="text-xs font-bold text-emerald-700">حساب کاربری عرفان صنعت</p><h1 class="mt-1 text-2xl font-black text-slate-900">پنل کاربری</h1></header>
    <?php do_action('woocommerce_before_my_account'); ?>
    <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]"><nav class="h-fit rounded-3xl border border-slate-200 bg-white p-4" aria-label="ناوبری حساب کاربری"><?php do_action('woocommerce_account_navigation'); ?></nav><div class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 md:p-8"><?php do_action('woocommerce_account_content'); ?></div></div>
    <?php do_action('woocommerce_after_my_account'); ?>
</section>
