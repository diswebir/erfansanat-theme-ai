<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1 || (string) get_post_meta($postId, 'true_sale', true) !== 'false') return;
$phoneDisplay = trim((string) es_option('contact_phone', '031-91091011')) ?: '031-91091011';
$phone = preg_replace('/[^0-9+]/', '', $phoneDisplay) ?: '03191091011';
$notice = trim((string) get_post_meta($postId, 'before-sale-c', true));
$contactUrl = add_query_arg(['product' => $postId], home_url('/contact/'));
?>
<section class="rounded-3xl border border-amber-200 bg-amber-50 p-5" aria-label="استعلام قیمت کارخانه" data-inquiry-panel>
    <div class="mb-4 flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-amber-100 text-xl text-amber-800" aria-hidden="true">؟</span><div><h2 class="text-lg font-black text-slate-900">استعلام قیمت کارخانه</h2><p class="text-xs font-medium text-slate-600">برای دریافت قیمت روز و پیش‌فاکتور رسمی با کارشناسان فروش تماس بگیرید.</p></div></div>
    <?php if ($notice !== '') : ?><p class="mb-4 rounded-xl border border-amber-200 bg-white/70 p-3 text-sm leading-6 text-amber-900"><?php echo wp_kses_post(nl2br(esc_html($notice))); ?></p><?php endif; ?>
    <div class="grid gap-2 sm:grid-cols-2"><a href="tel:<?php echo esc_attr($phone); ?>" class="rounded-xl bg-white px-4 py-3 text-center text-sm font-extrabold text-slate-900 shadow-sm">تماس با کارخانه: <?php echo esc_html($phoneDisplay); ?></a><a href="<?php echo esc_url($contactUrl); ?>" class="rounded-xl bg-emerald-700 px-4 py-3 text-center text-sm font-extrabold text-white">درخواست پیش‌فاکتور رسمی</a></div>
</section>
