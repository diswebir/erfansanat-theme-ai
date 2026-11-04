<?php
/** Section schemas for the faq page. */
defined( 'ABSPATH' ) || exit;

$sections = array();

$sections['faq-intro'] = array(
	'key' => 'faq-intro',
	'label' => 'سوالات متداول',
	'template' => 'template-parts/pages/faq/section-intro.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['faq-questions'] = array(
	'key' => 'faq-questions',
	'label' => 'پرسش و پاسخ',
	'template' => 'template-parts/pages/faq/section-questions.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['faq-contact'] = array(
	'key' => 'faq-contact',
	'label' => 'پشتیبانی بیشتر',
	'template' => 'template-parts/pages/faq/section-contact.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

return $sections;
