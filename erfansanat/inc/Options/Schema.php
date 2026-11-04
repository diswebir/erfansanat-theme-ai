<?php
/**
 * Versioned theme-options schema (single source of truth).
 *
 * Every option lives in ONE WordPress option: erfansanat_theme_options.
 * Defaults are extracted verbatim from static-mirror/index.html so the
 * front-end renders identically before any admin customization.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options;

defined( 'ABSPATH' ) || exit;

final class Schema {

	public const VERSION = 1;

	/**
	 * Field registry: key => definition.
	 *
	 * Types: text, textarea, number, select, toggle, checkbox, color, email,
	 * url, media, link, icon, repeater, rich, code.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function fields(): array {
		return array(
			// ── Header / announcement / logo ────────────────────────────────.
			'announcement_enabled'  => array(
				'type'    => 'toggle',
				'default' => true,
				'label'   => 'نمایش نوار اعلان',
				'tab'     => 'header',
			),
			'announcement_title'    => array(
				'type'    => 'text',
				'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)',
				'label'   => 'متن اصلی نوار اعلان',
				'tab'     => 'header',
			),
			'announcement_subtitle' => array(
				'type'    => 'text',
				'default' => 'گواهینامه ISO 9001:2015 و کدهای رسمی سامانه ستاد و GS1',
				'label'   => 'متن فرعی نوار اعلان',
				'tab'     => 'header',
			),
			'announcement_links'    => array(
				'type'     => 'repeater',
				'default'  => array(
					array( 'label' => 'مجوزها و افتخارات', 'url' => 'licenses-and-honors' ),
					array( 'label' => 'گارانتی ۲۴ ماهه', 'url' => 'refund_returns' ),
					array( 'label' => 'استخدام', 'url' => 'recruitment' ),
				),
				'label'    => 'لینک‌های سریع نوار اعلان',
				'tab'      => 'header',
				'subfields' => array(
					'label' => array( 'type' => 'text', 'default' => '' ),
					'url'   => array( 'type' => 'text', 'default' => '' ),
				),
				'min'      => 0,
				'max'      => 6,
			),
			'logo_id'               => array(
				'type'    => 'media',
				'default' => 0,
				'label'   => 'لوگوی سفارشی (رسانه)',
				'tab'     => 'header',
			),
			'brand_name'            => array(
				'type'    => 'text',
				'default' => 'عرفان صنعت اصفهان',
				'label'   => 'نام برند',
				'tab'     => 'header',
			),
			'brand_tagline'         => array(
				'type'    => 'text',
				'default' => 'نورپردازی شهری و المان‌های نوری',
				'label'   => 'شعار زیر لوگو',
				'tab'     => 'header',
			),

			// ── Mega menu ───────────────────────────────────────────────────.
			'mega_products_enabled'    => array(
				'type'    => 'toggle',
				'default' => true,
				'label'   => 'مگامنوی محصولات',
				'tab'     => 'megamenu',
			),
			'mega_products_per_tab'    => array(
				'type'    => 'number',
				'default' => 6,
				'label'   => 'تعداد آیتم در هر تب محصولات',
				'tab'     => 'megamenu',
				'min'     => 1,
				'max'     => 24,
			),
			'mega_projects_enabled'    => array(
				'type'    => 'toggle',
				'default' => true,
				'label'   => 'مگامنوی پروژه‌ها',
				'tab'     => 'megamenu',
			),
			'mega_projects_group'      => array(
				'type'    => 'select',
				'default' => 'project_type',
				'label'   => 'گروه‌بندی پروژه‌ها',
				'tab'     => 'megamenu',
				'choices' => array(
					'project_type' => 'بر اساس نوع پروژه',
					'city'         => 'بر اساس شهر',
				),
			),
			'mega_projects_per_tab'    => array(
				'type'    => 'number',
				'default' => 6,
				'label'   => 'تعداد آیتم در هر تب پروژه‌ها',
				'tab'     => 'megamenu',
				'min'     => 1,
				'max'     => 24,
			),
			'mega_corporate_enabled'   => array(
				'type'    => 'toggle',
				'default' => true,
				'label'   => 'مگامنوی شرکتی',
				'tab'     => 'megamenu',
			),
			// Corporate panel editable image/text — added by P04-T008 (AC-03).
			'mega_corporate_title'     => array(
				'type'    => 'text',
				'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان',
				'label'   => 'عنوان پنل شرکتی',
				'tab'     => 'megamenu',
			),
			'mega_corporate_text'      => array(
				'type'    => 'textarea',
				'default' => 'طراح و تولیدکننده تخصصی المان‌های نوری شهری، درختان هوشمند، ریسه‌های ضدآب IP68 و سازه‌های خورشیدی در ایران.',
				'label'   => 'متن پنل شرکتی',
				'tab'     => 'megamenu',
			),
			'mega_corporate_image_id'  => array(
				'type'    => 'media',
				'default' => 0,
				'label'   => 'تصویر پنل شرکتی (رسانه)',
				'tab'     => 'megamenu',
			),

			// ── Footer / contact / social ───────────────────────────────────.
			'footer_description'  => array(
				'type'    => 'textarea',
				'default' => 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته، المان‌های نوری تزئینی، درختان نوری هوشمند، خورشیدنماها، ریسه‌های LED (بلوطی، فندقی، سوزنی IP68) و پوینت لایت پیکسل با ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش.',
				'label'   => 'توضیح فوتر',
				'tab'     => 'footer',
			),
			'footer_badges'       => array(
				'type'     => 'repeater',
				'default'  => array(
					array( 'text' => 'ISO 9001:2015' ),
					array( 'text' => 'IP67 / IP68' ),
					array( 'text' => 'کد بین‌المللی GS1' ),
					array( 'text' => 'عرضه در سامانه ستاد' ),
				),
				'label'    => 'نشان‌های فوتر',
				'tab'      => 'footer',
				'subfields' => array( 'text' => array( 'type' => 'text', 'default' => '' ) ),
				'min'      => 0,
				'max'      => 8,
			),
			'contact_phone'       => array(
				'type'    => 'text',
				'default' => '031-91091011',
				'label'   => 'تلفن کارخانه',
				'tab'     => 'footer',
			),
			'contact_mobile'      => array(
				'type'    => 'text',
				'default' => '09137976915',
				'label'   => 'تلفن همراه',
				'tab'     => 'footer',
			),
			'contact_email'       => array(
				'type'    => 'email',
				'default' => 'info@erfansanat.com',
				'label'   => 'ایمیل تماس',
				'tab'     => 'footer',
			),
			'contact_address'     => array(
				'type'    => 'textarea',
				'default' => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
				'label'   => 'نشانی کارخانه',
				'tab'     => 'footer',
			),
			'contact_postal_code' => array(
				'type'    => 'text',
				'default' => '۸۴۱۸۱۴۸۶۷۸',
				'label'   => 'کد پستی',
				'tab'     => 'footer',
			),
			'copyright_text'      => array(
				'type'    => 'text',
				'default' => '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.',
				'label'   => 'متن کپی‌رایت',
				'tab'     => 'footer',
			),
			'social_links'        => array(
				'type'     => 'repeater',
				'default'  => array(
					array( 'network' => 'instagram', 'url' => 'https://instagram.com/erfan_sanat_esfahan' ),
					array( 'network' => 'telegram', 'url' => 'https://t.me/erfansanat' ),
					array( 'network' => 'linkedin', 'url' => 'https://linkedin.com/company/erfansanat' ),
				),
				'label'    => 'شبکه‌های اجتماعی',
				'tab'      => 'footer',
				'subfields' => array(
					'network' => array(
						'type'    => 'select',
						'default' => 'instagram',
						'choices' => array(
							'instagram' => 'اینستاگرام',
							'telegram'  => 'تلگرام',
							'linkedin'  => 'لینکدین',
							'whatsapp'  => 'واتس‌اپ',
							'aparat'    => 'آپارات',
						),
					),
					'url'     => array( 'type' => 'url', 'default' => '' ),
				),
				'min'      => 0,
				'max'      => 8,
			),

			// ── Brand / colors / custom code ────────────────────────────────.
			'brand_primary'         => array(
				'type'    => 'color',
				'default' => '#047857',
				'label'   => 'رنگ اصلی برند',
				'tab'     => 'brand',
			),
			'brand_primary_dark'    => array(
				'type'    => 'color',
				'default' => '#065f46',
				'label'   => 'رنگ تیره برند',
				'tab'     => 'brand',
			),
			'brand_accent'          => array(
				'type'    => 'color',
				'default' => '#059669',
				'label'   => 'رنگ تأکیدی',
				'tab'     => 'brand',
			),
			'brand_surface'         => array(
				'type'    => 'color',
				'default' => '#ecfdf5',
				'label'   => 'رنگ پس‌زمینه روشن',
				'tab'     => 'brand',
			),
			'custom_css'            => array(
				'type'    => 'code',
				'default' => '',
				'label'   => 'CSS سفارشی',
				'tab'     => 'brand',
			),
			'custom_header_script'  => array(
				'type'    => 'code',
				'default' => '',
				'label'   => 'اسکریپت سفارشی هدر',
				'tab'     => 'brand',
			),
			'custom_footer_script'  => array(
				'type'    => 'code',
				'default' => '',
				'label'   => 'اسکریپت سفارشی فوتر',
				'tab'     => 'brand',
			),

			// ── Archive settings ────────────────────────────────────────────.
			'shop_posts_per_page'     => array(
				'type'    => 'number',
				'default' => 12,
				'label'   => 'تعداد کارت فروشگاه در هر صفحه',
				'tab'     => 'archive',
				'min'     => 1,
				'max'     => 48,
			),
			'projects_posts_per_page' => array(
				'type'    => 'number',
				'default' => 12,
				'label'   => 'تعداد کارت پروژه‌ها در هر صفحه',
				'tab'     => 'archive',
				'min'     => 1,
				'max'     => 48,
			),
			'blog_posts_per_page'     => array(
				'type'    => 'number',
				'default' => 9,
				'label'   => 'تعداد نوشته وبلاگ در هر صفحه',
				'tab'     => 'archive',
				'min'     => 1,
				'max'     => 48,
			),
			'pagination_type'         => array(
				'type'    => 'select',
				'default' => 'numeric',
				'label'   => 'نوع صفحه‌بندی',
				'tab'     => 'archive',
				'choices' => array(
					'numeric'  => 'شماره‌ای',
					'loadmore' => 'بارگذاری بیشتر',
				),
			),
			'shop_default_sort'       => array(
				'type'    => 'select',
				'default' => 'latest',
				'label'   => 'مرتب‌سازی پیش‌فرض فروشگاه',
				'tab'     => 'archive',
				'choices' => array(
					'latest'     => 'جدیدترین',
					'price_asc'  => 'ارزان‌ترین',
					'price_desc' => 'گران‌ترین',
					'popular'    => 'محبوب‌ترین',
				),
			),
			'archive_layout'          => array(
				'type'    => 'select',
				'default' => 'grid',
				'label'   => 'چیدمان آرشیو',
				'tab'     => 'archive',
				'choices' => array(
					'grid' => 'شبکه‌ای',
					'list' => 'لیستی',
				),
			),
		);
	}

	/**
	 * Tab metadata: id => [title, icon, field keys in display order].
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function tabs(): array {
		$tabs = array(
			'header'   => array( 'title' => 'هدر، اعلان و لوگو', 'icon' => 'dashicons-align-full-width' ),
			'megamenu' => array( 'title' => 'مگامنو', 'icon' => 'dashicons-menu-alt3' ),
			'footer'   => array( 'title' => 'فوتر، تماس و شبکه‌ها', 'icon' => 'dashicons-admin-site-alt3' ),
			'brand'    => array( 'title' => 'برند، رنگ و کد سفارشی', 'icon' => 'dashicons-art' ),
			'archive'  => array( 'title' => 'تنظیمات آرشیو', 'icon' => 'dashicons-screenoptions' ),
		);

		foreach ( $tabs as $id => $tab ) {
			$tabs[ $id ]['fields'] = array_keys(
				array_filter(
					self::fields(),
					static function ( $field ) use ( $id ) {
						return isset( $field['tab'] ) && $field['tab'] === $id;
					}
				)
			);
		}

		return $tabs;
	}

	/** @return array<string,mixed> */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $key => $field ) {
			$defaults[ $key ] = $field['default'];
		}
		return $defaults;
	}

	/** @return array<string,mixed>|null */
	public static function get_field( string $key ): ?array {
		$fields = self::fields();
		return $fields[ $key ] ?? null;
	}

	public static function is_known( string $key ): bool {
		return null !== self::get_field( $key );
	}

	public static function default( string $key ) {
		$field = self::get_field( $key );
		return $field['default'] ?? null;
	}

	public static function version(): int {
		return self::VERSION;
	}
}
