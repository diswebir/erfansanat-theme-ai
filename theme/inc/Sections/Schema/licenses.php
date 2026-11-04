<?php
/** Section schemas for the licenses page. */
defined( 'ABSPATH' ) || exit;

$sections = array();

$sections['licenses-intro'] = array(
	'key' => 'licenses-intro',
	'label' => 'مجوزها و افتخارات',
	'template' => 'template-parts/pages/licenses/section-intro.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['licenses-licenses'] = array(
	'key' => 'licenses-licenses',
	'label' => 'مجوزها',
	'template' => 'template-parts/pages/licenses/section-licenses.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['licenses-honors'] = array(
	'key' => 'licenses-honors',
	'label' => 'افتخارات',
	'template' => 'template-parts/pages/licenses/section-honors.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

return $sections;
