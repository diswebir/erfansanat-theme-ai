<?php
/**
 * فهرست مطالب مقاله — تزریق id ترتیبی (sec-N) دور h2ها و جمع سرفصل‌ها
 * مطابق میرور blog/*.html: هر h2 داخل <div id="sec-N" class="scroll-mt-24"> می‌نشیند.
 * شناسه فایل: inc/Frontend/TOC.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class TOC {

    /** @var list<array{id: string, title: string}> */
    private static array $items = [];

    public static function init(): void {
        add_filter('the_content', [self::class, 'process'], 20);
    }

    /** پردازش محتوا: هر h2 → پوشش div ترتیبی + ثبت عنوان برای سایدبار */
    public static function process(string $content): string {
        self::$items = [];
        $n = 0;
        return (string) preg_replace_callback(
            '~<h2[^>]*>.*?</h2>~s',
            static function (array $m) use (&$n): string {
                $n++;
                $id = 'sec-' . $n;
                self::$items[] = ['id' => $id, 'title' => wp_strip_all_tags($m[0])];
                return '<div id="' . $id . '" class="scroll-mt-24">' . $m[0] . '</div>';
            },
            $content
        );
    }

    /** @return list<array{id: string, title: string}> */
    public static function items(): array {
        return self::$items;
    }

    public static function reset(): void {
        self::$items = [];
    }
}
