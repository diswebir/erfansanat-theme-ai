<?php
/**
 * Template Name: فرصت‌های شغلی و استخدام
 *
 * قالب برگه «فرصت‌های شغلی و استخدام» — رندر سکشن‌های SectionManager (گروه recruitment)
 * ترتیب/فعال‌بودن و فیلدها از متای _es_sections برگه؛ حالت خالی = عین میرور
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

es_render_sections((int) get_queried_object_id());

get_footer();
