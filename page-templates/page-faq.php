<?php
/**
 * Template Name: سوالات متداول
 *
 * قالب برگه «سوالات متداول» — رندر سکشن‌های SectionManager (گروه faq)
 * ترتیب/فعال‌بودن و فیلدها از متای _es_sections برگه؛ حالت خالی = عین میرور
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

es_render_sections((int) get_queried_object_id());

get_footer();
