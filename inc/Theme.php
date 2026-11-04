<?php
/**
 * کلاس بوت هسته قالب — نقطه ثبت هوک‌های همه ماژول‌ها
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme;

final class Theme {

    /**
     * ثبت هوک‌های ماژول‌های هسته.
     * هر ماژول با ورود به قالب در همین متد فراخوانی می‌شود؛
     * چک class_exists فقط تا تکمیل فاز جاری به‌عنوان لایه ایمنی باقی است.
     */
    public static function boot(): void {
        ThemeSetup::init();

        Frontend\DeBloat::init();

        Frontend\AssetManager::init();

        if (class_exists(Admin\ThemeOptions::class)) {
            Admin\ThemeOptions::init();
            Admin\OptionsOutput::init();
        }

        if (class_exists(Admin\SectionManager::class)) {
            Admin\SectionManager::init();
            if (class_exists(Sections\HomeSections::class)) {
                Sections\HomeSections::init();
            }
            if (class_exists(Sections\Registry::class)) {
                Sections\Registry::init();
            }
        }

        if (class_exists(Data\PostTypes::class)) {
            Data\PostTypes::init();
        }

        if (class_exists(Data\AdminListFilters::class)) {
            Data\AdminListFilters::init();
        }

        if (class_exists(Data\WooRewrite::class)) {
            Data\WooRewrite::init();
        }

        if (class_exists(Woo\Setup::class)) {
            Woo\Setup::init();
        }

        if (class_exists(Admin\ProductMetaboxes::class)) {
            Admin\ProductMetaboxes::init();
        }

        if (class_exists(Admin\ProjectMetaboxes::class)) {
            Admin\ProjectMetaboxes::init();
        }

        if (class_exists(Admin\CategoryTermMeta::class)) {
            Admin\CategoryTermMeta::init();
        }

        if (class_exists(Admin\HreflangMeta::class)) {
            Admin\HreflangMeta::init();
        }

        if (class_exists(Admin\QuickEdit::class)) {
            Admin\QuickEdit::init();
        }

        if (class_exists(Admin\RelatedPicker::class)) {
            Admin\RelatedPicker::init();
        }

        if (class_exists(Frontend\ViewCounter::class)) {
            Frontend\ViewCounter::init();
        }
    }
}
