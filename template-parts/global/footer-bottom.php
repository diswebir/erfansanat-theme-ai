<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
?>
<div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-6 text-[12px] text-slate-500"><p><?php echo esc_html((string) es_option('footer_copyright', '© ' . date_i18n('Y') . ' عرفان صنعت اصفهان — تمامی حقوق محفوظ است.')); ?></p><button type="button" data-scroll-top class="flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-800 transition-colors hover:bg-emerald-100 cursor-pointer">بازگشت به بالا ↑</button></div>
</div></footer>
