<?php
/** Section schemas for the warranty page. */
defined( 'ABSPATH' ) || exit;

$sections = array();

$sections['warranty-intro'] = array(
	'key' => 'warranty-intro',
	'label' => 'شرایط گارانتی',
	'template' => 'template-parts/pages/warranty/section-intro.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['warranty-warranty'] = array(
	'key' => 'warranty-warranty',
	'label' => 'ضمانت‌نامه',
	'template' => 'template-parts/pages/warranty/section-warranty.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['warranty-returns'] = array(
	'key' => 'warranty-returns',
	'label' => 'شرایط بازگشت',
	'template' => 'template-parts/pages/warranty/section-returns.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

return $sections;
