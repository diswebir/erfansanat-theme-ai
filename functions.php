<?php
/**
 * بوت‌استرپ اصلی قالب عرفان صنعت اصفهان
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('ERFANSANAT_VERSION')) {
    define('ERFANSANAT_VERSION', '1.0.0');
}

if (!defined('ERFANSANAT_DIR')) {
    define('ERFANSANAT_DIR', trailingslashit(get_template_directory()));
}

if (!defined('ERFANSANAT_URI')) {
    define('ERFANSANAT_URI', trailingslashit(get_template_directory_uri()));
}

/*
 * ماژول‌های بوت: اوتولودر و هلپرها باید پیش از هر ماژول دیگری در دسترس باشند.
 * چک is_file برای مقاومت در برابر آپدیت‌های ناقص و سازگاری چایلدتم است.
 */
$es_boot_files = [
    'inc/Autoloader.php',
    'inc/Helpers.php',
];
foreach ($es_boot_files as $es_boot_file) {
    if (is_file(ERFANSANAT_DIR . $es_boot_file)) {
        require_once ERFANSANAT_DIR . $es_boot_file;
    }
}
unset($es_boot_files, $es_boot_file);

// ثبت اوتولودر پیش از هر resolve کلاسی
if (class_exists(\ErfanSanat\Theme\Autoloader::class)) {
    \ErfanSanat\Theme\Autoloader::register();
}

/**
 * بوت هسته قالب — تمام ماژول‌ها هوک‌های خود را از همین نقطه ثبت می‌کنند.
 * (کلاس Theme از اوتولودر resolve می‌شود؛ در نبود فایل‌های هسته خطایی رخ نمی‌دهد.)
 */
if (class_exists(\ErfanSanat\Theme\Theme::class)) {
    \ErfanSanat\Theme\Theme::boot();
}
