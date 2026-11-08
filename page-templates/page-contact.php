<?php
/**
 * Template Name: تماس با ما
 *
 * قالب برگه «تماس با ما» — رندر سکشن‌های SectionManager (گروه contact)
 * ترتیب/فعال‌بودن و فیلدها از متای _es_sections برگه؛ حالت خالی = عین میرور contact.html
 * فرم: data-contact-form (ارسال واقعی و nonce در فاز ۸.۵ — تا آن زمان فقط نمایشی)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

es_render_sections((int) get_queried_object_id());

get_footer();
