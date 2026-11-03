<?php
/**
 * Quick Edit شناسه‌های قانونی در لیست محصولات
 *
 * فیلدهای gs1_id / national_code / hs_id / isic_id / cpc_id / product_id
 * در ردیف ویرایش سریع + پر شدن خودکار از data-attributeهای ستون شناسه‌ها.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class QuickEdit {

    private const FIELDS = [
        'gs1_id'        => 'GS1',
        'national_code' => 'کد ملی',
        'hs_id'         => 'HS',
        'isic_id'       => 'ISIC',
        'cpc_id'        => 'CPC',
        'product_id'    => 'شناسه محصول',
    ];

    public static function init(): void {
        if (is_admin()) {
            add_action('quick_edit_custom_box', [self::class, 'render_fields'], 10, 2);
            add_action('admin_footer-edit.php', [self::class, 'print_inline_script']);
        }
    }

    /** فیلدهای inline در ردیف Quick Edit */
    public static function render_fields(string $column_name, string $post_type): void {
        if ($post_type !== 'product') {
            return;
        }
        if ($column_name !== 'es-identifiers') {
            return;
        }
        echo '<fieldset class="es-qe-identifiers inline-edit-col"><legend class="inline-edit-legend">' . esc_html__('شناسه‌های قانونی (عرفان صنعت)', 'erfansanat') . '<div class="es-qe-grid">';
        foreach (self::FIELDS as $key => $label) {
            printf(
                '<label><span>%1$s</span><input type="text" name="%2$s[%3$s]" data-es-qe="%3$s"></label>',
                esc_html((string) $label),
                esc_attr(ProductMetaboxes::META_PREFIX),
                esc_attr((string) $key)
            );
        }
        echo '</div></fieldset>';
    }

    /**
     * اسکریپت پر کردن فیلدهای Quick Edit از ستون شناسه‌ها + ارسال همراه فرم
     */
    public static function print_inline_script(): void {
        $screen_ok = function_exists('get_current_screen');
        ?>
        <script>
        (function () {
            'use strict';
            document.addEventListener('click', function (e) {
                var btn = e.target instanceof Element ? e.target.closest('.editinline') : null;
                if (!btn) { return; }
                var row = btn.closest('tr');
                var data = row && row.querySelector('.es-quickedit-data');
                if (!data) { return; }
                var apply = function () {
                    var inlineRow = document.querySelector('#edit-' + (data.getAttribute('data-post') || row.id.replace(/[^0-9]/g, '')));
                    var target = document.querySelector('tr.inline-editor');
                    if (!target) { return; }
                    ['gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id'].forEach(function (key) {
                        var input = target.querySelector('[data-es-qe="' + key + '"]');
                        if (input) { input.value = data.getAttribute('data-' + key.replace('_', '-')) || data.getAttribute('data-' + key) || ''; }
                    });
                    document.removeEventListener('DOMContentLoaded', apply);
                };
                setTimeout(apply, 50);
            });
        })();
        </script>
        <?php
    }
}
