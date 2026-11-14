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

			// ── Global contact CTA (P04-T016) ──────────────────────────────.
			'cta_enabled'           => array(
				'type'    => 'toggle',
				'default' => true,
				'label'   => 'نمایش بخش تماس (CTA)',
				'tab'     => 'footer',
			),
			'cta_eyebrow'           => array(
				'type'    => 'text',
				'default' => 'مشاوره مهندسی و استعلام قیمت مستقیم از کارخانه',
				'label'   => 'CTA: متن نشان بالا',
				'tab'     => 'footer',
			),
			'cta_heading'           => array(
				'type'    => 'text',
				'default' => 'روشنایی و هویت بصری متمایز را به شهر خود دعوت کنید',
				'label'   => 'CTA: تیتر',
				'tab'     => 'footer',
			),
			'cta_heading_highlight' => array(
				'type'    => 'text',
				'default' => 'شهر خود',
				'label'   => 'CTA: بخش برجسته تیتر',
				'tab'     => 'footer',
			),
			'cta_description'       => array(
				'type'    => 'textarea',
				'default' => 'واحد مهندسی فروش عرفان صنعت اصفهان آماده ارائه مشاوره تخصصی، طراحی سه‌بعدی المان‌های نوری، صدور پیش‌فاکتور رسمی سامانه تدارکات الکترونیکی دولت (ستاد) و ارسال مستقیم به سراسر کشور است.',
				'label'   => 'CTA: توضیح',
				'tab'     => 'footer',
			),
			'cta_phone_label'       => array(
				'type'    => 'text',
				'default' => 'تماس مستقیم با کارشناسان فروش',
				'label'   => 'CTA: متن دکمه تماس',
				'tab'     => 'footer',
			),
			'cta_quote_label'       => array(
				'type'    => 'text',
				'default' => 'درخواست پیش‌فاکتور رسمی ستاد',
				'label'   => 'CTA: متن دکمه پیش‌فاکتور',
				'tab'     => 'footer',
			),
			'cta_quote_url'         => array(
				'type'    => 'url',
				'default' => '',
				'label'   => 'CTA: لینک دکمه پیش‌فاکتور (خالی = برگه تماس)',
				'tab'     => 'footer',
			),
			'cta_hours'             => array(
				'type'    => 'text',
				'default' => 'شنبه تا پنجشنبه · ۷:۰۰ الی ۱۶:۳۰',
				'label'   => 'CTA: ساعات پاسخگویی',
				'tab'     => 'footer',
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

			// ── Home / hero (P05-T005) ───────────────────────────────────.
			// The main hero video is a global theme option, independent from
			// the per-page project slides (AC-01). Poster + preload are
			// configurable (AC-03); an empty video falls back to an image
			// (AC-02).
			'hero_video_id'           => array(
				'type'    => 'media',
				'default' => 0,
				'label'   => 'ویدیوی اصلی هیرو (رسانه)',
				'tab'     => 'home',
			),
			'hero_video_poster'       => array(
				'type'    => 'media',
				'default' => 0,
				'label'   => 'پوستر ویدیوی هیرو (تصویر)',
				'tab'     => 'home',
			),
			'hero_video_preload'      => array(
				'type'    => 'select',
				'default' => 'metadata',
				'label'   => 'پیش‌بارگذاری ویدیوی هیرو',
				'tab'     => 'home',
				'choices' => array(
					'auto'     => 'کامل (auto)',
					'metadata' => 'فقط فراداده (metadata)',
					'none'     => 'بدون پیش‌بارگذاری (none)',
				),
			),
			'hero_video_fallback'     => array(
				'type'    => 'media',
				'default' => 0,
				'label'   => 'تصویر جایگزین هیرو (وقتی ویدیو خالی است)',
				'tab'     => 'home',
			),
			'hero_slides'             => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'اسلایدهای هیرو (صحنه‌های پروژه‌ای)',
				'tab'       => 'home',
				'subfields' => array(
					'project_id' => array( 'type' => 'number', 'default' => 0 ),
					'image'      => array( 'type' => 'media', 'default' => 0 ),
				),
				'min'       => 0,
				'max'       => 8,
			),

			// ── Static pages: licenses / faq / warranty / recruitment (P07) ──.
			'licenses_heading'        => array(
				'type'    => 'text',
				'default' => 'مجوزها و افتخارات',
				'label'   => 'عنوان صفحه مجوزها',
				'tab'     => 'pages',
			),
			'licenses_intro'          => array(
				'type'    => 'textarea',
				'default' => 'پشتوانه بیش از دو دهه تولید صنعتی؛ از گواهی دانش‌بنیان معاونت علمی ریاست جمهوری و استاندارد مدیریت کیفیت ISO 9001:2015 تا گواهی‌های آزمایشگاهی IP67 / IP68 و کدهای بین‌المللی GS1',
				'label'   => 'توضیح صفحه مجوزها',
				'tab'     => 'pages',
			),
			'licenses_items'          => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'گواهی‌ها و افتخارات (به‌ترتیب نمایش)',
				'tab'       => 'pages',
				'subfields' => array(
					'category'    => array( 'type' => 'text', 'default' => '' ),
					'title'       => array( 'type' => 'text', 'default' => '' ),
					'code'        => array( 'type' => 'text', 'default' => '' ),
					'description' => array( 'type' => 'textarea', 'default' => '' ),
					'issuer'      => array( 'type' => 'text', 'default' => '' ),
					'year'        => array( 'type' => 'text', 'default' => '' ),
					'image'       => array( 'type' => 'media', 'default' => 0 ),
					'alt'         => array( 'type' => 'text', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 30,
			),
			'faq_heading'             => array(
				'type'    => 'text',
				'default' => 'سوالات متداول',
				'label'   => 'عنوان صفحه سوالات متداول',
				'tab'     => 'pages',
			),
			'faq_intro'               => array(
				'type'    => 'textarea',
				'default' => 'پاسخ کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان به پرسش‌های پرتکرار درباره استعلام قیمت، خرید از سامانه ستاد، استانداردهای IP67 / IP68 و گارانتی',
				'label'   => 'توضیح صفحه سوالات متداول',
				'tab'     => 'pages',
			),
			'faq_items'               => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'پرسش و پاسخ‌ها (گروه/سوال/جواب)',
				'tab'       => 'pages',
				'subfields' => array(
					'group'    => array( 'type' => 'text', 'default' => '' ),
					'question' => array( 'type' => 'text', 'default' => '' ),
					'answer'   => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 60,
			),
			'warranty_heading'        => array(
				'type'    => 'text',
				'default' => 'شرایط گارانتی و خدمات پس از فروش',
				'label'   => 'عنوان صفحه گارانتی',
				'tab'     => 'pages',
			),
			'warranty_intro'          => array(
				'type'    => 'textarea',
				'default' => 'پشتوانه ۲۴ ماه گارانتی تعویض بی‌قید و شرط کارخانه دانش‌بنیان عرفان صنعت اصفهان و ۵ سال تعهد تأمین قطعات در سراسر کشور',
				'label'   => 'توضیح صفحه گارانتی',
				'tab'     => 'pages',
			),
			'warranty_cards'          => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'کارت‌های اصلی گارانتی (آیکون/عنوان/متن)',
				'tab'       => 'pages',
				'subfields' => array(
					'icon'        => array( 'type' => 'icon', 'default' => 'shield' ),
					'title'       => array( 'type' => 'text', 'default' => '' ),
					'description' => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 12,
			),
			'warranty_covered'        => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'موارد تحت پوشش گارانتی',
				'tab'       => 'pages',
				'subfields' => array(
					'text' => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 30,
			),
			'warranty_excluded'       => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'موارد خارج از شمول گارانتی',
				'tab'       => 'pages',
				'subfields' => array(
					'text' => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 30,
			),
			'warranty_steps'          => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'مراحل ثبت درخواست گارانتی',
				'tab'       => 'pages',
				'subfields' => array(
					'title'       => array( 'type' => 'text', 'default' => '' ),
					'description' => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 12,
			),
			'recruit_heading'         => array(
				'type'    => 'text',
				'default' => 'فرصت‌های شغلی و استخدام',
				'label'   => 'عنوان صفحه استخدام',
				'tab'     => 'pages',
			),
			'recruit_intro'           => array(
				'type'    => 'textarea',
				'default' => 'ما در شرکت دانش‌بنیان عرفان صنعت اصفهان همواره پذیرای متخصصان خلاق و پرتلاش در حوزه‌های تحقیق و توسعه، اجرای پروژه‌های شهری، حسابداری صنعتی و فناوری اطلاعات هستیم',
				'label'   => 'توضیح صفحه استخدام',
				'tab'     => 'pages',
			),
			'recruit_tabs'            => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'تب‌های واحدهای سازمانی (کلید/برچسب/عنوان/شرح)',
				'tab'       => 'pages',
				'subfields' => array(
					'key'         => array( 'type' => 'text', 'default' => '' ),
					'label'       => array( 'type' => 'text', 'default' => '' ),
					'badge'       => array( 'type' => 'text', 'default' => '' ),
					'title'       => array( 'type' => 'text', 'default' => '' ),
					'description' => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 12,
			),
			'recruit_jobs'            => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'موقعیت‌های شغلی (وابسته به کلید تب)',
				'tab'       => 'pages',
				'subfields' => array(
					'tab'        => array( 'type' => 'text', 'default' => '' ),
					'title'      => array( 'type' => 'text', 'default' => '' ),
					'type'       => array( 'type' => 'text', 'default' => '' ),
					'experience' => array( 'type' => 'text', 'default' => '' ),
					'location'   => array( 'type' => 'text', 'default' => '' ),
					'skills'     => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 40,
			),
			'services_intro'          => array(
				'type'    => 'textarea',
				'default' => 'طیف کاملی از خدمات طراحی، تولید و اجرای پروژه‌های روشنایی برای شهرداری‌ها، سازمان‌های عمرانی و پیمانکاران پروژه‌های شهری',
				'label'   => 'توضیح هیرو برگه خدمات',
				'tab'     => 'pages',
			),
			'services_path'           => array(
				'type'      => 'repeater',
				'default'   => array(),
				'label'     => 'مسیر همکاری (مراحل ایده تا درخشش)',
				'tab'       => 'pages',
				'subfields' => array(
					'title'       => array( 'type' => 'text', 'default' => '' ),
					'description' => array( 'type' => 'textarea', 'default' => '' ),
				),
				'min'       => 0,
				'max'       => 8,
			),
			'services_cta_text'       => array(
				'type'    => 'text',
				'default' => 'درخواست مشاوره رایگان',
				'label'   => 'متن دکمه CTA خدمات',
				'tab'     => 'pages',
			),
			'services_cta_url'        => array(
				'type'    => 'text',
				'default' => '',
				'label'   => 'لینک دکمه CTA خدمات (خالی = برگه تماس)',
				'tab'     => 'pages',
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
			'overview' => array( 'title' => 'پیشخوان', 'icon' => 'dashicons-dashboard' ),
			'home'     => array( 'title' => 'صفحه اصلی و هیرو', 'icon' => 'dashicons-admin-home' ),
			'header'   => array( 'title' => 'هدر، اعلان و لوگو', 'icon' => 'dashicons-align-full-width' ),
			'megamenu' => array( 'title' => 'مگامنو', 'icon' => 'dashicons-menu-alt3' ),
			'footer'   => array( 'title' => 'فوتر، تماس و شبکه‌ها', 'icon' => 'dashicons-admin-site-alt3' ),
			'brand'    => array( 'title' => 'برند، رنگ و کد سفارشی', 'icon' => 'dashicons-art' ),
			'archive'  => array( 'title' => 'تنظیمات آرشیو', 'icon' => 'dashicons-screenoptions' ),
			'pages'    => array( 'title' => 'برگه‌های ثابت (مجوز/سوالات/گارانتی/استخدام)', 'icon' => 'dashicons-media-document' ),
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
