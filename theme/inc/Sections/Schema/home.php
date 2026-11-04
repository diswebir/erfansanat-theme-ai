<?php
/**
 * Homepage section schemas in the exact order found in the static reference.
 *
 * @package ErfanSanat\Theme
 */

defined( 'ABSPATH' ) || exit;

$section = static function ( string $key, string $label, string $template, array $default_data = array() ): array {
	$title = isset( $default_data['title'] ) ? $default_data['title'] : '';
	$description = isset( $default_data['description'] ) ? $default_data['description'] : '';
	return array(
		'key'          => $key,
		'label'        => $label,
		'template'     => $template,
		'fields'       => array(
			array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => $title ),
			array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => $description ),
			array( 'id' => 'image', 'type' => 'media', 'label' => 'تصویر', 'default' => 0, 'media_kind' => 'image' ),
		),
		'default_data' => array(
			'title'       => $title,
			'description' => $description,
			'image'       => 0,
		),
	);
};

$hero = $section( 'hero', 'بخش آغازین', 'template-parts/home/section-hero.php' );
$hero['fields'][] = array( 'id' => 'button_label', 'type' => 'text', 'label' => 'متن دکمه', 'default' => '' );
$hero['fields'][] = array( 'id' => 'button_url', 'type' => 'url', 'label' => 'پیوند دکمه', 'default' => '' );
$hero['default_data']['button_label'] = '';
$hero['default_data']['button_url'] = '';

$gallery = $section( 'gallery', 'گالری نمونه‌کارها', 'template-parts/home/section-gallery.php' );
$gallery['fields'][] = array(
	'id' => 'items', 'type' => 'repeater', 'label' => 'موارد گالری', 'default' => array(),
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'url', 'type' => 'url', 'label' => 'پیوند', 'default' => '' ),
	),
);
$gallery['default_data']['items'] = array();

$timeline = $section( 'timeline', 'تاریخچه شرکت', 'template-parts/home/section-timeline.php' );
$timeline_content_path = __DIR__ . '/home-content/timeline.json';
$timeline_content = is_file( $timeline_content_path ) ? json_decode( (string) file_get_contents( $timeline_content_path ), true ) : array();
$timeline_token_map = array(
	array( 'T008', 'T010', 'T026', 'T027', 'T028', 'T029', 'T030', 'A005', 'A006' ),
	array( 'T011', 'T013', 'T040', 'T041', 'T042', 'T043', 'T044', 'A013', 'A014' ),
	array( 'T014', 'T016', 'T054', 'T055', 'T056', 'T057', 'T058', 'A021', 'A022' ),
	array( 'T017', 'T019', 'T068', 'T069', 'T070', 'T071', 'T072', 'A029', 'A030' ),
	array( 'T020', 'T022', 'T082', 'T083', 'T084', 'T085', 'T086', 'A037', 'A038' ),
	array( 'T023', 'T025', 'T096', 'T097', 'T098', 'T099', 'T100', 'A045', 'A046' ),
);
$timeline_rows = array();
foreach ( $timeline_token_map as $tokens ) {
	$timeline_rows[] = array(
		'year' => (string) ( $timeline_content[ $tokens[0] ]['value'] ?? '' ),
		'milestone' => (string) ( $timeline_content[ $tokens[1] ]['value'] ?? '' ),
		'title' => (string) ( $timeline_content[ $tokens[4] ]['value'] ?? '' ),
		'summary' => (string) ( $timeline_content[ $tokens[5] ]['value'] ?? '' ),
		'description' => (string) ( $timeline_content[ $tokens[6] ]['value'] ?? '' ),
		'image' => (string) ( $timeline_content[ $tokens[7] ]['value'] ?? '' ),
		'image_alt' => (string) ( $timeline_content[ $tokens[8] ]['value'] ?? '' ),
	);
}
$timeline['fields'][] = array(
	'id' => 'history', 'type' => 'repeater', 'label' => 'مراحل تاریخچه', 'default' => $timeline_rows,
	'fields' => array(
		array( 'id' => 'year', 'type' => 'text', 'label' => 'سال', 'default' => '' ),
		array( 'id' => 'milestone', 'type' => 'text', 'label' => 'دسته دستاورد', 'default' => '' ),
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'summary', 'type' => 'text', 'label' => 'خلاصه', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیحات', 'default' => '' ),
		array( 'id' => 'image', 'type' => 'media', 'label' => 'تصویر مرحله', 'default' => '', 'return' => 'url', 'media_kind' => 'image' ),
		array( 'id' => 'image_alt', 'type' => 'text', 'label' => 'توضیح تصویر', 'default' => '' ),
	),
);
$timeline['default_data']['history'] = $timeline_rows;

$services = $section( 'services', 'خدمات تخصصی', 'template-parts/home/section-services.php' );
$services_content_path = __DIR__ . '/home-content/services.json';
$services_content = is_file( $services_content_path ) ? json_decode( (string) file_get_contents( $services_content_path ), true ) : array();
$service_token_map = array(
	array( 'A000', 'A001', 'T006' ), array( 'A003', 'A004', 'T009' ), array( 'A006', 'A007', 'T012' ), array( 'A009', 'A010', 'T015' ), array( 'A012', 'A013', 'T018' ),
);
$service_urls = array( '/services/square-lighting/', '/services/street-lighting/', '/services/bridge-lighting/', '/services/element-lighting/', '/services/building-lighting/' );
$service_rows = array();
foreach ( $service_token_map as $index => $tokens ) {
	$service_rows[] = array(
		'title' => (string) ( $services_content[ $tokens[0] ]['value'] ?? '' ),
		'image' => (string) ( $services_content[ $tokens[1] ]['value'] ?? '' ),
		'description' => (string) ( $services_content[ $tokens[2] ]['value'] ?? '' ),
		'url' => $service_urls[ $index ] ?? '',
	);
}
$services['fields'][] = array(
	'id' => 'services', 'type' => 'repeater', 'label' => 'ردیف‌های خدمات', 'default' => $service_rows,
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان خدمت', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیح خدمت', 'default' => '' ),
		array( 'id' => 'image', 'type' => 'media', 'label' => 'تصویر پیش‌نمایش', 'default' => '', 'return' => 'url', 'media_kind' => 'image' ),
		array( 'id' => 'url', 'type' => 'url', 'label' => 'پیوند صفحه خدمت', 'default' => '' ),
	),
);
$services['default_data']['services'] = $service_rows;

$catalog = $section( 'catalog', 'دانلود کاتالوگ', 'template-parts/home/section-catalog.php' );
$catalog['fields'][] = array( 'id' => 'catalog_file', 'type' => 'media', 'label' => 'فایل PDF کاتالوگ', 'default' => 0, 'return' => 'url', 'media_kind' => 'file' );
$catalog['default_data']['catalog_file'] = '';

$about = $section( 'about', 'درباره عرفان صنعت', 'template-parts/home/section-about.php' );
$about_content_path = __DIR__ . '/home-content/about.json';
$about_content = is_file( $about_content_path ) ? json_decode( (string) file_get_contents( $about_content_path ), true ) : array();
$stat_token_map = array(
	array( 'A003', 'A004', 'T014', 'T015', 'T016' ),
	array( 'A005', 'A006', 'T017', 'T018', 'T019' ),
	array( 'A007', 'A008', 'T020', 'T021', 'T022' ),
	array( 'A009', 'A010', 'T023', 'T024', 'T025' ),
);
$about_stats = array();
foreach ( $stat_token_map as $tokens ) {
	$about_stats[] = array(
		'value' => (string) ( $about_content[ $tokens[0] ]['value'] ?? '' ),
		'suffix' => (string) ( $about_content[ $tokens[1] ]['value'] ?? '' ),
		'label' => (string) ( $about_content[ $tokens[3] ]['value'] ?? '' ),
		'description' => (string) ( $about_content[ $tokens[4] ]['value'] ?? '' ),
	);
}
$about['fields'][] = array(
	'id' => 'stats', 'type' => 'repeater', 'label' => 'آمار صفحه درباره', 'default' => $about_stats,
	'fields' => array(
		array( 'id' => 'value', 'type' => 'number', 'label' => 'مقدار', 'default' => '' ),
		array( 'id' => 'suffix', 'type' => 'text', 'label' => 'پسوند', 'default' => '' ),
		array( 'id' => 'label', 'type' => 'text', 'label' => 'عنوان', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'text', 'label' => 'توضیح', 'default' => '' ),
	),
);
$about['fields'][] = array( 'id' => 'use_project_count', 'type' => 'toggle', 'label' => 'استفاده از تعداد پروژه‌های منتشرشده برای شمارنده اول', 'default' => false );
$about['default_data']['stats'] = $about_stats;
$about['default_data']['use_project_count'] = false;

$why_ese = $section( 'why-ese', 'چرا عرفان صنعت؟', 'template-parts/home/section-why-ese.php' );
$why_features = array(
	array( 'title' => 'بیش از ۲۵ سال تجربه مهندسی', 'description' => 'تجربه تخصصی در طراحی و تولید سازه‌های نوری شهری و راهکارهای متناسب با نیاز هر پروژه.', 'icon' => 'award' ),
	array( 'title' => 'طراحی، تولید و اجرای یکپارچه', 'description' => 'همراهی از طراحی اولیه تا ساخت کارخانه‌ای و اجرای نهایی در محل پروژه.', 'icon' => 'factory' ),
	array( 'title' => 'کیفیت و استانداردهای مستند', 'description' => 'گواهینامه ISO 9001:2015، کدهای GS1 و حضور در سامانه تدارکات الکترونیکی دولت (ستاد).', 'icon' => 'badge-check' ),
	array( 'title' => 'پشتیبانی پس از تحویل', 'description' => '۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش برای همراهی بلندمدت پروژه.', 'icon' => 'shield-check' ),
);
$why_ese['fields'][] = array(
	'id' => 'features', 'type' => 'repeater', 'label' => 'مزیت‌های عرفان صنعت', 'default' => $why_features,
	'fields' => array(
		array( 'id' => 'title', 'type' => 'text', 'label' => 'عنوان مزیت', 'default' => '' ),
		array( 'id' => 'description', 'type' => 'textarea', 'label' => 'توضیح مزیت', 'default' => '' ),
		array( 'id' => 'icon', 'type' => 'select', 'label' => 'نماد', 'default' => 'award', 'options' => array( 'award' => 'تجربه', 'factory' => 'تولید', 'badge-check' => 'استاندارد', 'shield-check' => 'پشتیبانی' ) ),
	),
);
$why_ese['default_data']['features'] = $why_features;

$sections = array(
	'hero'          => $hero,
	'projects'      => $section( 'projects', 'پروژه‌های منتخب', 'template-parts/home/section-projects-intro.php' ),
	'gallery'       => $gallery,
	'about'         => $about,
	'establishment' => $section( 'establishment', 'درباره تأسیس شرکت', 'template-parts/home/section-about-establishment.php' ),
	'timeline'      => $timeline,
	'products'      => $section( 'products', 'محصولات منتخب', 'template-parts/home/section-products.php' ),
	'catalog'       => $catalog,
	'services'      => $services,
	'quality'       => $section( 'quality', 'استانداردهای کیفیت', 'template-parts/home/section-quality.php' ),
	'support'       => $section( 'support', 'پشتیبانی و گارانتی', 'template-parts/home/section-support.php' ),
	'clients'       => $section( 'clients', 'مشتریان و سازمان‌های همکار', 'template-parts/home/section-clients.php' ),
	'blog-preview'  => $section( 'blog-preview', 'ویترین مقالات', 'template-parts/home/section-blog-preview.php' ),
	'contact-cta'   => $section( 'contact-cta', 'دعوت به تماس', 'template-parts/home/section-contact-cta.php' ),
	'map-contact'   => $section( 'map-contact', 'موقعیت کارخانه و اطلاعات تماس', 'template-parts/home/section-map-contact.php' ),
	'why-ese'       => $why_ese,
);

foreach ( $sections as $key => &$definition ) {
	$path = __DIR__ . '/home-content/' . $key . '.json';
	if ( ! is_file( $path ) ) { continue; }
	$json = (string) file_get_contents( $path );
	$definition['default_data']['content_json'] = $json;
	$definition['fields'][] = array(
		'id' => 'content_json',
		'type' => 'textarea',
		'label' => 'متن‌ها و پیوندهای سکشن (JSON)',
		'default' => $json,
	);
}
unset( $definition );
return $sections;
