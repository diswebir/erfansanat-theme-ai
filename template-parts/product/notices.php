<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$notice = trim((string) get_post_meta($postId, 'before-sale-c', true));
$catalog = trim((string) get_post_meta($postId, 'product-catalog-file', true));
if ($notice === '' && $catalog === '') return;
$catalogUrl = ctype_digit($catalog) ? wp_get_attachment_url((int) $catalog) : $catalog;
?>
<div class="space-y-3" data-product-notices>
    <?php if ($notice !== '') : ?><aside class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-7 text-sky-950" role="note"><strong class="mb-1 block">اطلاعیه پیش از خرید</strong><?php echo wp_kses_post(wpautop($notice)); ?></aside><?php endif; ?>
    <?php if (is_string($catalogUrl) && $catalogUrl !== '' && wp_http_validate_url($catalogUrl)) : ?><a href="<?php echo esc_url($catalogUrl); ?>" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm font-extrabold text-emerald-800" target="_blank" rel="noopener">دانلود کاتالوگ فنی محصول <span aria-hidden="true">↓</span></a><?php endif; ?>
</div>
