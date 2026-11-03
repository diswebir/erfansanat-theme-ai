<?php
/** Section schemas for the about page. */
defined( 'ABSPATH' ) || exit;

$sections = array();

$sections['about-intro'] = array(
	'key' => 'about-intro',
	'label' => 'معرفی شرکت',
	'template' => 'template-parts/pages/about/section-intro.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['about-company'] = array(
	'key' => 'about-company',
	'label' => 'داستان شرکت',
	'template' => 'template-parts/pages/about/section-company.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['about-values'] = array(
	'key' => 'about-values',
	'label' => 'ارزش‌ها و توانمندی‌ها',
	'template' => 'template-parts/pages/about/section-values.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['about-projects'] = array(
	'key' => 'about-projects',
	'label' => 'پروژه‌ها و دستاوردها',
	'template' => 'template-parts/pages/about/section-projects.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

$sections['about-contact'] = array(
	'key' => 'about-contact',
	'label' => 'ارتباط با ما',
	'template' => 'template-parts/pages/about/section-contact.php',
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
	),
	'default_data' => array( 'title' => '', 'description' => '' ),
);

return $sections;
