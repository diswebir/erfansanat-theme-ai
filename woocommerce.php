<?php
/**
 * Wrapper ووکامرس — صفحات فروشگاه/محصول/سبد/پرداخت داخل شل قالب رندر می‌شوند.
 * ساختار مطابق میرور: هدر + منوی موبایل (get_header) → <main> بدون اتریبیوت → محتوا → get_footer.
 * قالب‌های اختصاصی (single-product.php، archive-product.php، …) در صورت حضور این فایل را نادیده می‌گیرند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
echo '<main>';
woocommerce_content();
echo '</main>';
get_footer();
