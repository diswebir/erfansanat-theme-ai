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

        if (class_exists(Frontend\DeBloat::class)) {
            Frontend\DeBloat::init();
        }

        if (class_exists(Frontend\AssetManager::class)) {
            Frontend\AssetManager::init();
        }
    }
}
