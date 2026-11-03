<?php
/** Section schemas for the contact page. */
defined( 'ABSPATH' ) || exit;

$sections = array();

$sections['contact-intro'] = array(
	'key' => 'contact-intro',
	'label' => 'راه‌های ارتباطی',
	'template' => 'template-parts/pages/contact/section-intro.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['contact-form'] = array(
	'key' => 'contact-form',
	'label' => 'فرم تماس',
	'template' => 'template-parts/pages/contact/section-form.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

return $sections;
