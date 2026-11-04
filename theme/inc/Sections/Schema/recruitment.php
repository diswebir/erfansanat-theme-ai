<?php
/** Section schemas for the recruitment page. */
defined( 'ABSPATH' ) || exit;

$sections = array();

$sections['recruitment-intro'] = array(
	'key' => 'recruitment-intro',
	'label' => 'فرصت‌های شغلی',
	'template' => 'template-parts/pages/recruitment/section-intro.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['recruitment-positions'] = array(
	'key' => 'recruitment-positions',
	'label' => 'موقعیت‌های شغلی',
	'template' => 'template-parts/pages/recruitment/section-positions.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

return $sections;
