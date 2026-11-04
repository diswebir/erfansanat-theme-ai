<?php
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin\Tabs;

use ErfanSanat\Theme\Admin\SectionManager\SectionStorage;

final class SectionsTab extends BaseTab
{
    public static function id(): string
    {
        return 'sections';
    }

    /** @return array<int,array<string,mixed>> */
    public static function fields(): array
    {
        return [];
    }

    public function render(): void
    {
        $pages = get_pages([
            'post_status' => ['publish', 'future', 'draft', 'pending', 'private'],
            'sort_column' => 'post_title',
            'sort_order' => 'ASC',
            'number' => 50,
        ]);

        echo '<div class="es-sections-overview" data-es-sections-overview>';
        echo '<div class="es-sections-overview__intro"><div><h3>' . esc_html__('مدیریت سکشن‌های برگه‌ها', 'erfansanat') . '</h3><p>' . esc_html__('برای مرتب‌سازی، فعال‌سازی یا ویرایش محتوای سکشن‌ها، برگه موردنظر را باز کنید.', 'erfansanat') . '</p></div><a class="button button-primary" href="' . esc_url(admin_url('edit.php?post_type=page')) . '">' . esc_html__('رفتن به فهرست برگه‌ها', 'erfansanat') . '</a></div>';

        if (!is_array($pages) || $pages === []) {
            echo '<div class="es-admin-empty"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span><p>' . esc_html__('هنوز برگه‌ای برای مدیریت وجود ندارد.', 'erfansanat') . '</p></div></div>';
            return;
        }

        echo '<div class="es-admin-table-wrap"><table class="es-admin-table"><thead><tr><th>' . esc_html__('برگه', 'erfansanat') . '</th><th>' . esc_html__('قالب برگه', 'erfansanat') . '</th><th>' . esc_html__('سکشن‌های فعال / کل', 'erfansanat') . '</th><th>' . esc_html__('عملیات', 'erfansanat') . '</th></tr></thead><tbody>';
        foreach ($pages as $page) {
            if (!$page instanceof \WP_Post) {
                continue;
            }

            $sections = SectionStorage::get((int) $page->ID);
            $enabledCount = count(array_filter($sections, static fn (array $section): bool => !empty($section['enabled'])));
            $template = get_page_template_slug($page);
            $templateLabel = $template !== '' ? basename($template) : __('پیش‌فرض', 'erfansanat');
            $editUrl = get_edit_post_link($page->ID, 'raw');

            echo '<tr data-es-page-row><td><strong>' . esc_html(get_the_title($page)) . '</strong><span class="es-admin-table__subtext">' . esc_html($page->post_status) . '</span></td>';
            echo '<td><code dir="ltr">' . esc_html($templateLabel) . '</code></td>';
            echo '<td><span class="es-admin-count">' . esc_html((string) $enabledCount) . ' / ' . esc_html((string) count($sections)) . '</span></td><td>';
            if (is_string($editUrl) && $editUrl !== '') {
                echo '<a class="button" href="' . esc_url($editUrl) . '">' . esc_html__('ویرایش سکشن‌ها', 'erfansanat') . '</a>';
            } else {
                echo '<span class="es-admin-table__subtext">' . esc_html__('دسترسی ویرایش موجود نیست', 'erfansanat') . '</span>';
            }
            echo '</td></tr>';
        }
        echo '</tbody></table></div></div>';
    }
}
