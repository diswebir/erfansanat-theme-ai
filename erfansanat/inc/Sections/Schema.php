<?php
/**
 * Section schema: the editable section types per page context.
 *
 * Section types map 1:1 to the static-mirror homepage blocks (see
 * temp/STATIC_TEMPLATE_MAP.md §4) and to the about/contact/arya landing pages.
 * Each type declares its template part and its editable fields. The actual
 * template parts are sliced in Phases 5/7; the Renderer falls back safely until
 * then.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

final class Schema {

	public const VERSION = 1;

	/**
	 * Versioned post-meta key holding a page's section configuration.
	 */
	public static function meta_key(): string {
		return '_es_sections_v' . self::VERSION;
	}

	/**
	 * Supported page contexts.
	 *
	 * @return array<int,string>
	 */
	public static function contexts(): array {
		return array( 'home', 'about', 'contact', 'arya' );
	}

	/**
	 * Section type definitions for a context: type => [title, template, fields].
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function sections( string $context ): array {
		switch ( $context ) {
			case 'home':
				return self::home_sections();
			case 'about':
				return self::about_sections();
			case 'contact':
				return self::contact_sections();
			case 'arya':
				return self::arya_sections();
			default:
				return array();
		}
	}

	/**
	 * Default ordered section list for a context (all enabled).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function defaults( string $context ): array {
		$out   = array();
		$order = 0;
		foreach ( self::sections( $context ) as $type => $def ) {
			$fields = array();
			foreach ( $def['fields'] as $key => $field ) {
				$fields[ $key ] = $field['default'];
			}
			$out[] = array(
				'id'      => $type,
				'type'    => $type,
				'enabled' => true,
				'order'   => $order++,
				'fields'  => $fields,
			);
		}
		return $out;
	}

	/**
	 * @return array<string,mixed>|null
	 */
	public static function get_section( string $context, string $type ): ?array {
		$sections = self::sections( $context );
		return $sections[ $type ] ?? null;
	}

	public static function is_known( string $context, string $type ): bool {
		return null !== self::get_section( $context, $type );
	}

	/**
	 * Field definitions of a section type.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function section_fields( string $context, string $type ): array {
		$def = self::get_section( $context, $type );
		return $def['fields'] ?? array();
	}

	// ── Context definitions ─────────────────────────────────────────────────.

	private static function home_sections(): array {
		return array(
			'hero'             => array(
				'title'    => 'هیرو (ویدیو / اسلایدر)',
				'template' => 'home/section-hero',
				'fields'   => array(
					'heading'    => self::f( 'text', 'عنوان هیرو', 'نورپردازی شهری و المان‌های نوری' ),
					'subheading' => self::f( 'textarea', 'زیرعنوان هیرو', 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته' ),
					'video_id'   => self::f( 'media', 'ویدیوی اصلی هیرو', 0 ),
					'slides'     => self::repeater( 'اسلایدهای انتخابی از پروژه‌ها', array(
						'project_id' => self::f( 'number', 'شناسه پروژه', 0 ),
						'image'      => self::f( 'media', 'تصویر اسلاید', 0 ),
					) ),
				),
			),
			'project-types'    => array(
				'title'    => 'انواع پروژه',
				'template' => 'home/section-project-types',
				'fields'   => array(
					'heading'    => self::f( 'text', 'عنوان', 'انواع پروژه‌های نورپردازی' ),
					'subheading' => self::f( 'textarea', 'زیرعنوان', '' ),
					'per_page'   => self::f( 'number', 'تعداد آیتم', 8, array( 'min' => 1, 'max' => 24 ) ),
				),
			),
			'featured-projects' => array(
				'title'    => 'نمونه‌کارهای اجراشده',
				'template' => 'home/section-featured-projects',
				'fields'   => array(
					'heading'    => self::f( 'text', 'عنوان', 'نمونه‌کارهای اجراشده در کلان‌شهرها' ),
					'subheading' => self::f( 'textarea', 'زیرعنوان', '' ),
					'count'      => self::f( 'number', 'تعداد پروژه', 6, array( 'min' => 1, 'max' => 24 ) ),
				),
			),
			'about'            => array(
				'title'    => 'درباره ما + آمار',
				'template' => 'home/section-about',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'درباره عرفان صنعت' ),
					'content' => self::f( 'rich', 'متن معرفی', '' ),
					'image'   => self::f( 'media', 'تصویر', 0 ),
					'stats'   => self::repeater( 'آمار و شمارنده‌ها', array(
						'number' => self::f( 'text', 'عدد', '۲۵' ),
						'label'  => self::f( 'text', 'برچسب', 'سال تجربه' ),
					) ),
				),
			),
			'localization'     => array(
				'title'    => 'بومی‌سازی سازه‌های نوری',
				'template' => 'home/section-localization',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'مرجع بومی‌سازی سازه‌های نوری' ),
					'content' => self::f( 'rich', 'متن', '' ),
					'image'   => self::f( 'media', 'تصویر', 0 ),
				),
			),
			'history'          => array(
				'title'    => 'تاریخچه و دستاوردها',
				'template' => 'home/section-history',
				'fields'   => array(
					'heading'  => self::f( 'text', 'عنوان', 'تاریخچه و دستاوردها' ),
					'timeline' => self::repeater( 'تایم‌لاین', array(
						'year'        => self::f( 'text', 'سال', '۱۳۰' ),
						'title'       => self::f( 'text', 'عنوان', '' ),
						'description' => self::f( 'textarea', 'توضیح', '' ),
					) ),
				),
			),
			'products'         => array(
				'title'    => 'منتخب محصولات',
				'template' => 'home/section-products',
				'fields'   => array(
					'heading'    => self::f( 'text', 'عنوان', 'منتخب محصولات' ),
					'subheading' => self::f( 'textarea', 'زیرعنوان', '' ),
					'count'      => self::f( 'number', 'تعداد محصول', 8, array( 'min' => 1, 'max' => 24 ) ),
				),
			),
			'catalog'          => array(
				'title'    => 'CTA کاتالوگ جامع',
				'template' => 'home/section-catalog',
				'fields'   => array(
					'heading'      => self::f( 'text', 'عنوان', 'کاتالوگ جامع ۱۴۰۴' ),
					'description'  => self::f( 'textarea', 'توضیح', '' ),
					'file_id'      => self::f( 'media', 'فایل کاتالوگ', 0 ),
					'button_label' => self::f( 'text', 'متن دکمه', 'دانلود کاتالوگ' ),
				),
			),
			'services'         => array(
				'title'    => 'خدمات تخصصی',
				'template' => 'home/section-services',
				'fields'   => array(
					'heading'    => self::f( 'text', 'عنوان', 'خدمات تخصصی نورپردازی' ),
					'subheading' => self::f( 'textarea', 'زیرعنوان', '' ),
				),
			),
			'standards'        => array(
				'title'    => 'استانداردهای کیفیت',
				'template' => 'home/section-standards',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'استانداردهای کیفیت' ),
					'items'   => self::repeater( 'موارد استاندارد', array(
						'title'       => self::f( 'text', 'عنوان', '' ),
						'description' => self::f( 'textarea', 'توضیح', '' ),
						'icon'        => self::f( 'icon', 'آیکون', 'check' ),
					) ),
				),
			),
			'support'          => array(
				'title'    => 'پشتیبانی و گارانتی',
				'template' => 'home/section-support',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'پشتیبانی فنی و گارانتی تعویض' ),
					'content' => self::f( 'rich', 'متن', '' ),
					'phone'   => self::f( 'text', 'تلفن پشتیبانی', '031-91091011' ),
				),
			),
			'clients'          => array(
				'title'    => 'مشتریان و همکاران',
				'template' => 'home/section-clients',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'مشتریان و سازمان‌های همکار' ),
					'logos'   => self::repeater( 'لوگوی مشتریان', array(
						'image' => self::f( 'media', 'لوگو', 0 ),
						'name'  => self::f( 'text', 'نام سازمان', '' ),
					) ),
				),
			),
			'blog-preview'     => array(
				'title'    => 'جدیدترین مقالات',
				'template' => 'home/section-blog',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'جدیدترین مقالات' ),
					'count'   => self::f( 'number', 'تعداد نوشته', 3, array( 'min' => 1, 'max' => 12 ) ),
				),
			),
		);
	}

	private static function about_sections(): array {
		return array(
			'hero'       => array(
				'title'    => 'هیرو درباره ما',
				'template' => 'about/section-hero',
				'fields'   => array(
					'heading'    => self::f( 'text', 'عنوان', 'درباره شرکت' ),
					'highlight'  => self::f( 'text', 'بخش برجسته عنوان', 'عرفان صنعت اصفهان' ),
					'content'    => self::f( 'rich', 'متن', '' ),
					'image'      => self::f( 'media', 'تصویر کارخانه', 0 ),
					'badge'      => self::f( 'text', 'نشان', 'شرکت دانش‌بنیان — تأسیس ۱۳۷۸' ),
					'stats'      => self::repeater( 'آمار هیرو', array(
						'number' => self::f( 'text', 'عدد', '' ),
						'label'  => self::f( 'text', 'برچسب', '' ),
					) ),
				),
			),
			'intro'      => array(
				'title'    => 'معرفی شرکت',
				'template' => 'about/section-intro',
				'fields'   => array(
					'heading'   => self::f( 'text', 'عنوان', 'بیش از دو دهه پیشگامی در صنعت روشنایی ایران' ),
					'content'   => self::f( 'rich', 'متن معرفی', '' ),
					'image'     => self::f( 'media', 'تصویر', 0 ),
					'establish' => self::f( 'text', 'سال تأسیس', 'تأسیس ۱۳۷۸' ),
					'stats'     => self::repeater( 'آمار', array(
						'number' => self::f( 'text', 'عدد', '' ),
						'label'  => self::f( 'text', 'برچسب', '' ),
					) ),
				),
			),
			'iso'        => array(
				'title'    => 'گواهینامه ISO',
				'template' => 'about/section-iso',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'گواهینامه ISO 9001:2015؛ نشانه تعهد ما به کیفیت' ),
					'content' => self::f( 'rich', 'متن', '' ),
					'badge'   => self::f( 'text', 'نشان', 'ISO 9001:2015' ),
					'items'   => self::repeater( 'موارد', array(
						'text' => self::f( 'text', 'مورد', '' ),
					) ),
				),
			),
			'principles' => array(
				'title'    => 'اصول بنیادین',
				'template' => 'about/section-principles',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'اصول بنیادین عرفان صنعت' ),
					'items'   => self::repeater( 'اصول', array(
						'title'       => self::f( 'text', 'عنوان', '' ),
						'description' => self::f( 'textarea', 'توضیح', '' ),
						'icon'        => self::f( 'icon', 'آیکون', 'check' ),
					) ),
				),
			),
			'history'    => array(
				'title'    => 'تاریخچه',
				'template' => 'about/section-history',
				'fields'   => array(
					'heading'  => self::f( 'text', 'عنوان', 'تاریخچه و دستاوردها' ),
					'timeline' => self::repeater( 'تایم‌لاین', array(
						'year'        => self::f( 'text', 'سال', '' ),
						'title'       => self::f( 'text', 'عنوان', '' ),
						'description' => self::f( 'textarea', 'توضیح', '' ),
					) ),
				),
			),
		);
	}

	private static function contact_sections(): array {
		return array(
			'hero' => array(
				'title'    => 'هیرو تماس',
				'template' => 'contact/section-hero',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'تماس با ما' ),
					'content' => self::f( 'rich', 'متن', '' ),
				),
			),
			'form' => array(
				'title'    => 'فرم تماس',
				'template' => 'contact/section-form',
				'fields'   => array(
					'heading'     => self::f( 'text', 'عنوان', 'ارسال پیام' ),
					'description' => self::f( 'textarea', 'توضیح', '' ),
				),
			),
			'info' => array(
				'title'    => 'اطلاعات تماس',
				'template' => 'contact/section-info',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'اطلاعات تماس' ),
					'address' => self::f( 'textarea', 'نشانی', '' ),
					'phone'   => self::f( 'text', 'تلفن', '' ),
					'email'   => self::f( 'email', 'ایمیل', '' ),
				),
			),
		);
	}

	private static function arya_sections(): array {
		return array(
			'hero'     => array(
				'title'    => 'هیرو آریا',
				'template' => 'arya/section-hero',
				'fields'   => array(
					'heading'    => self::f( 'text', 'عنوان', 'درب بازکن هوشمند آریا' ),
					'subheading' => self::f( 'textarea', 'زیرعنوان', '' ),
					'video_id'   => self::f( 'media', 'ویدیو', 0 ),
					'image'      => self::f( 'media', 'تصویر', 0 ),
				),
			),
			'features' => array(
				'title'    => 'ویژگی‌ها',
				'template' => 'arya/section-features',
				'fields'   => array(
					'heading' => self::f( 'text', 'عنوان', 'ویژگی‌های آریا' ),
					'items'   => self::repeater( 'ویژگی‌ها', array(
						'title'       => self::f( 'text', 'عنوان', '' ),
						'description' => self::f( 'textarea', 'توضیح', '' ),
						'icon'        => self::f( 'icon', 'آیکون', 'check' ),
					) ),
				),
			),
			'cta'      => array(
				'title'    => 'درخواست خرید',
				'template' => 'arya/section-cta',
				'fields'   => array(
					'heading'      => self::f( 'text', 'عنوان', 'درخواست خرید آریا' ),
					'button_label' => self::f( 'text', 'متن دکمه', 'ثبت درخواست' ),
					'button_url'   => self::f( 'url', 'لینک دکمه', '' ),
				),
			),
		);
	}

	// ── Field definition helpers ────────────────────────────────────────────.

	/**
	 * @param mixed             $default Default value.
	 * @param array<string,mixed> $extra Extra definition (min/max/choices...).
	 * @return array<string,mixed>
	 */
	private static function f( string $type, string $label, $default, array $extra = array() ): array {
		return array_merge( array( 'type' => $type, 'label' => $label, 'default' => $default ), $extra );
	}

	/**
	 * @param array<string,array<string,mixed>> $subfields Subfield definitions.
	 * @return array<string,mixed>
	 */
	private static function repeater( string $label, array $subfields ): array {
		return array(
			'type'      => 'repeater',
			'label'     => $label,
			'default'   => array(),
			'subfields' => $subfields,
			'min'       => 0,
			'max'       => 30,
		);
	}
}
