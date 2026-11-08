<?php
/**
 * Template Name: درباره ما
 *
 * قالب برگه «درباره ما» — رندر سکشن‌های SectionManager (گروه about)
 * ترتیب/فعال‌بودن و فیلدها از متای _es_sections برگه؛ حالت خالی = عین میرور about.html
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

es_render_sections((int) get_queried_object_id());

get_footer();
