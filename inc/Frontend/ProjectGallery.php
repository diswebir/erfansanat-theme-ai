<?php
/**
 * گالری و محصولات پروژه — خواندن مقاوم از منابع قدیمی و مدرن
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\Admin\ProjectMetaboxes;
use ErfanSanat\Theme\Data\RepeaterEngine;

final class ProjectGallery {

    /**
     * گالری ادغام‌شده: کلیدهای تاریخی (۲ تا ۶) + گالری مدرن — dedup با حفظ ترتیب
     *
     * @return list<int>
     */
    public static function get_gallery(int $project_id): array {
        $out = [];
        foreach (ProjectMetaboxes::LEGACY_IMAGES as $key) {
            $raw = (string) get_post_meta($project_id, $key, true);
            $id  = self::to_id($raw);
            if ($id > 0 && !in_array($id, $out, true)) {
                $out[] = $id;
            }
        }
        $modern = (string) get_post_meta($project_id, ProjectMetaboxes::GALLERY_KEY, true);
        foreach (explode(',', $modern) as $part) {
            $part = trim($part);
            if ($part !== '' && ctype_digit($part)) {
                $id = (int) $part;
                if (!in_array($id, $out, true)) {
                    $out[] = $id;
                }
            }
        }
        return $out;
    }

    /** تبدیل مقدار کلید (آیدی یا URL قدیمی) به آیدی رسانه */
    private static function to_id(string $raw): int {
        $raw = trim($raw);
        if ($raw === '') {
            return 0;
        }
        if (ctype_digit($raw)) {
            return (int) $raw;
        }
        // URL قدیمی → آیدی (فیلتر توسعه/تست؛ پیاده‌سازی واقعی: جستجوی attachment)
        $map = apply_filters('es_attachment_id_from_url', 0, $raw);
        return absint($map);
    }

    /**
     * محصولات استفاده‌شده در پروژه (mortabet_mahsole_2) — خواندن آرایه/JSON/کاما
     *
     * @return list<int>
     */
    public static function get_products(int $project_id): array {
        $raw = get_post_meta($project_id, 'mortabet_mahsole_2', true);

        if (is_array($raw)) {
            $ids = [];
            foreach ($raw as $row) {
                $id = is_array($row) ? absint($row['id'] ?? 0) : absint(is_scalar($row) ? $row : 0);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
            return array_values(array_unique($ids));
        }

        if (is_string($raw) && str_starts_with(ltrim($raw), '{')) {
            $ids = [];
            foreach (RepeaterEngine::parse($raw)['rows'] as $row) {
                $id = absint($row['id'] ?? 0);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
            return array_values(array_unique($ids));
        }

        if (is_string($raw) && $raw !== '') {
            $ids = [];
            foreach (explode(',', $raw) as $part) {
                $part = trim($part);
                if ($part !== '' && ctype_digit($part)) {
                    $ids[] = (int) $part;
                }
            }
            return array_values(array_unique($ids));
        }

        return [];
    }
}
