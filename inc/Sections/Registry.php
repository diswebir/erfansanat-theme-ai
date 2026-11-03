<?php
/**
 * رجیستری سکشن‌های صفحات — نگاشت کلید برگه → لیست سکشن‌های ثبت‌شده
 *
 * سکشن‌های اختصاصی هر صفحه در فاز ۵ (home) و فاز ۷ (about/contact/licensing/لندینگ‌ها)
 * از همین کلاس ثبت می‌شوند؛ این فایل API پایه را فراهم می‌کند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Sections;

use ErfanSanat\Theme\Admin\SectionManager;

final class Registry {

    /** آیا رجیستری مقداردهی شده است (گارد اجرای دوباره) */
    private static bool $booted = false;

    public static function init(): void {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        /**
         * فیلتر es_sections_registry — فازهای ۵ و ۷ سکشن‌های واقعی را اضافه می‌کنند
         * ساختار: [page_key => [ [id,label,schema], … ]]
         */
        $registry = apply_filters('es_sections_registry', []);
        if (!is_array($registry)) {
            return;
        }
        foreach ($registry as $page_key => $items) {
            if (!is_string($page_key) || !is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                if (!is_array($item) || !isset($item['id'], $item['label'])) {
                    continue;
                }
                SectionManager::register_section(
                    $page_key,
                    (string) $item['id'],
                    (string) $item['label'],
                    is_array($item['schema'] ?? null) ? $item['schema'] : []
                );
            }
        }
    }
}
