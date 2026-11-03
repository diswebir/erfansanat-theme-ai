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

return array(
	'hero'          => $section( 'hero', 'بخش آغازین', 'template-parts/home/section-hero.php' ),
	'projects'      => $section( 'projects', 'پروژه‌های منتخب', 'template-parts/home/section-projects-intro.php' ),
	'gallery'       => $section( 'gallery', 'گالری نمونه‌کارها', 'template-parts/home/section-gallery.php' ),
	'about'         => $section( 'about', 'درباره عرفان صنعت', 'template-parts/home/section-about.php' ),
	'establishment' => $section( 'establishment', 'درباره تأسیس شرکت', 'template-parts/home/section-about-establishment.php' ),
	'timeline'      => $section( 'timeline', 'تاریخچه شرکت', 'template-parts/home/section-timeline.php' ),
	'products'      => $section( 'products', 'محصولات منتخب', 'template-parts/home/section-products.php' ),
	'catalog'       => $section( 'catalog', 'دانلود کاتالوگ', 'template-parts/home/section-catalog.php' ),
	'services'      => $section( 'services', 'خدمات تخصصی', 'template-parts/home/section-services.php' ),
	'quality'       => $section( 'quality', 'استانداردهای کیفیت', 'template-parts/home/section-quality.php' ),
	'support'       => $section( 'support', 'پشتیبانی و گارانتی', 'template-parts/home/section-support.php' ),
	'clients'       => $section( 'clients', 'مشتریان و سازمان‌های همکار', 'template-parts/home/section-clients.php' ),
	'blog-preview'  => $section( 'blog-preview', 'ویترین مقالات', 'template-parts/home/section-blog-preview.php' ),
	'contact-cta'   => $section( 'contact-cta', 'دعوت به تماس', 'template-parts/home/section-contact-cta.php' ),
	'map-contact'   => $section( 'map-contact', 'موقعیت کارخانه و اطلاعات تماس', 'template-parts/home/section-map-contact.php' ),
);
