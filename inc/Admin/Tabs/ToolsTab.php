<?php
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin\Tabs;

final class ToolsTab extends BaseTab
{
    public static function id(): string
    {
        return 'tools';
    }

    /** @return array<int,array<string,mixed>> */
    public static function fields(): array
    {
        return [];
    }

    public function render(): void
    {
        echo '<div class="es-tools" data-es-tools>';
        echo '<section class="es-tool-card">';
        echo '<div class="es-tool-card__heading"><span class="dashicons dashicons-download" aria-hidden="true"></span><div><h3>' . esc_html__('پشتیبان‌گیری از تنظیمات', 'erfansanat') . '</h3><p>' . esc_html__('یک نسخه JSON از تنظیمات فعلی دریافت کنید تا بتوانید آن را نگه‌داری یا به سایت دیگری منتقل کنید.', 'erfansanat') . '</p></div></div>';
        echo '<div class="es-tool-card__actions"><button type="button" class="button button-primary" data-es-export-options>' . esc_html__('ساخت و دانلود پشتیبان', 'erfansanat') . '</button><button type="button" class="button" data-es-copy-export disabled>' . esc_html__('کپی JSON', 'erfansanat') . '</button></div>';
        echo '<label class="es-tool-label" for="es-export-json">' . esc_html__('محتوای فایل پشتیبان', 'erfansanat') . '</label>';
        echo '<textarea id="es-export-json" class="es-tool-json" data-es-export-output rows="10" dir="ltr" spellcheck="false" readonly placeholder="' . esc_attr__('پس از ساخت پشتیبان، JSON اینجا نمایش داده می‌شود.', 'erfansanat') . '"></textarea>';
        echo '</section>';

        echo '<section class="es-tool-card">';
        echo '<div class="es-tool-card__heading"><span class="dashicons dashicons-upload" aria-hidden="true"></span><div><h3>' . esc_html__('بازیابی تنظیمات', 'erfansanat') . '</h3><p>' . esc_html__('فایل JSON پشتیبان را انتخاب کنید یا محتوای آن را در کادر وارد کنید. فقط کلیدهای شناخته‌شده پذیرفته می‌شوند.', 'erfansanat') . '</p></div></div>';
        echo '<label class="es-tool-label" for="es-import-file">' . esc_html__('انتخاب فایل JSON', 'erfansanat') . '</label>';
        echo '<input id="es-import-file" class="es-tool-file" type="file" accept=".json,application/json" data-es-import-file>';
        echo '<label class="es-tool-label" for="es-import-json">' . esc_html__('یا چسباندن محتوای JSON', 'erfansanat') . '</label>';
        echo '<textarea id="es-import-json" class="es-tool-json" data-es-import-json rows="10" dir="ltr" spellcheck="false" placeholder="' . esc_attr('{"brand_name":"نام برند"}') . '"></textarea>';
        echo '<div class="es-tool-card__actions"><button type="button" class="button button-primary" data-es-import-options>' . esc_html__('درون‌ریزی تنظیمات', 'erfansanat') . '</button></div>';
        echo '</section>';

        echo '<section class="es-tool-card es-tool-card--danger">';
        echo '<div class="es-tool-card__heading"><span class="dashicons dashicons-image-rotate" aria-hidden="true"></span><div><h3>' . esc_html__('بازنشانی تنظیمات', 'erfansanat') . '</h3><p>' . esc_html__('همه مقادیر به پیش‌فرض قالب برمی‌گردند. این کار پس از تأیید شما انجام می‌شود.', 'erfansanat') . '</p></div></div>';
        echo '<button type="button" class="button" data-es-reset-options>' . esc_html__('بازنشانی به مقادیر پیش‌فرض', 'erfansanat') . '</button>';
        echo '</section>';
        echo '<div class="es-admin-status" data-es-tools-status role="status" aria-live="polite" aria-atomic="true"></div>';
        echo '</div>';
    }
}
