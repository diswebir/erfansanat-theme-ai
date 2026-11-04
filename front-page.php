<?php
/**
 * قالب صفحه اصلی — رندر پویای سکشن‌های SectionManager
 * برگه پیش‌خوان (page_on_front) → متای _es_sections → رندر به ترتیب ادمین
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

$es_front_id = (int) get_option('page_on_front');
es_render_sections($es_front_id);

get_footer();
