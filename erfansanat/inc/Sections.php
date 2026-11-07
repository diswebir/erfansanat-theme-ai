<?php
/**
 * Home page section registry, layout storage and renderer.
 *
 * The approved reference home page is a fixed sequence of fifteen sections.
 * This class is the single source of truth for that sequence, for the fields
 * each section exposes, and for turning a saved layout back into markup.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Section definitions, sanitisation and ordered rendering.
 */
final class Sections {

	/**
	 * Icon names accepted by the icon field type.
	 *
	 * A closed list keeps hostile markup out of inline SVG output.
	 */
	public const ICONS = array( 'lamp', 'city', 'shield', 'star', 'factory', 'truck', 'support', 'badge' );

	/**
	 * Maximum rows accepted in one repeater field.
	 */
	public const MAX_ROWS = 20;

	/**
	 * Maximum sections accepted in one layout.
	 */
	public const MAX_SECTIONS = 30;

	/**
	 * Registry of every known section, in the reference DOM order.
	 *
	 * `anchor` mirrors the ids used by the static reference; only seven of the
	 * fifteen reference sections carry one, so the rest render without an id.
	 *
	 * `anchor_in_part` marks a section whose template part owns the id itself
	 * (the hero writes `id="home"` on its own `<section>`), so the wrapper must
	 * not print it again. Without it the reference repeats the id and ships
	 * invalid HTML.
	 *
	 * @return array<string, array{label: string, anchor: string, part: string, fields: array<string, array<string, mixed>>}>
	 */
	public static function registry(): array {
		return array(
			'hero'          => array(
				'label'          => __( 'هیرو صفحه اصلی', 'erfansanat' ),
				'anchor'         => 'home',
				'anchor_in_part' => true,
				'part'           => 'template-parts/home/section-hero.php',
				'fields'         => array(
					'title'    => self::text_field( __( 'تیتر اصلی', 'erfansanat' ), 'وقتی کیفیت و ماندگاری مهمه' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'عرفان صنعت انتخاب توئه...' ),
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'المان‌های نوری شهری · استاندارد IP68' ),
					'media_id' => self::media_field( __( 'تصویر یا پوستر ویدیو', 'erfansanat' ) ),
					'cta'      => self::link_field( __( 'دکمهٔ فراخوان', 'erfansanat' ), 'مشاهده محصولات', '/shop/' ),
					'scenes'   => self::repeater_field(
						__( 'صحنه‌های هیرو', 'erfansanat' ),
						array(
							'title' => self::text_field( __( 'عنوان صحنه', 'erfansanat' ), '' ),
							'tab'   => self::text_field( __( 'برچسب دکمهٔ صحنه', 'erfansanat' ), '' ),
							'image' => self::text_field( __( 'نام فایل تصویر', 'erfansanat' ), '' ),
							'url'   => self::url_field( __( 'لینک صحنه', 'erfansanat' ) ),
							'cta'   => self::text_field( __( 'متن دکمهٔ صحنه', 'erfansanat' ), '' ),
						),
						array(
							array(
								'title' => 'تیزر رسمی خط تولید و پروژه‌های عرفان صنعت اصفهان',
								'tab'   => 'معرفی شرکت',
								'image' => 'images/hero.jpg',
								'url'   => '/about/',
								'cta'   => 'درباره کارخانه',
							),
							array(
								'title' => 'تونل نوری نیم‌آرک پارک جهان‌نما',
								'tab'   => 'استان البرز',
								'image' => 'images/light-tunnel.jpg',
								'url'   => '/projects/',
								'cta'   => 'مشاهده پروژه',
							),
							array(
								'title' => 'آذین‌بندی نوری میدان و بلوار شهدا اصفهان',
								'tab'   => 'استان اصفهان',
								'image' => 'images/sun-light.jpg',
								'url'   => '/projects/',
								'cta'   => 'مشاهده پروژه',
							),
							array(
								'title' => 'درختان نوری هوشمند آرتام مشهد',
								'tab'   => 'استان خراسان رضوی',
								'image' => 'images/light-tree.jpg',
								'url'   => '/projects/',
								'cta'   => 'مشاهده پروژه',
							),
							array(
								'title' => 'المان حجمی و نیم‌هرم نوری اهواز',
								'tab'   => 'استان خوزستان',
								'image' => 'images/light-sphere.jpg',
								'url'   => '/projects/',
								'cta'   => 'مشاهده پروژه',
							),
						)
					),
					'chips'    => self::repeater_field(
						__( 'نشان‌های کوتاه', 'erfansanat' ),
						array(
							'title' => self::text_field( __( 'متن', 'erfansanat' ), '' ),
							'icon'  => self::icon_field( __( 'آیکون', 'erfansanat' ) ),
						),
						array(
							array(
								'title' => 'استاندارد IP68',
								'icon'  => 'shield',
							),
							array(
								'title' => 'المان‌های نوری شهری',
								'icon'  => 'city',
							),
							array(
								'title' => '۲۵ سال تجربه',
								'icon'  => 'star',
							),
						)
					),
				),
			),
			'project-types' => array(
				'label'          => __( 'دسته‌های پروژه', 'erfansanat' ),
				'anchor'         => 'projects',
				'anchor_in_part' => true,
				'part'           => 'template-parts/home/section-project-types.php',
				'fields'         => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'نمونه‌کارهای شاخص' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'پروژه‌های [نورپردازی شهری]' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'اجرای تخصصی پروژه‌های نورپردازی برای شهرداری‌ها و سازمان‌های زیباسازی؛ تلفیق مهندسی سازه، استاندارد حفاظتی IP68 و زیبایی‌شناسی شهری.' ),
					'items'    => self::repeater_field(
						__( 'دسته‌ها', 'erfansanat' ),
						array(
							'title'       => self::text_field( __( 'عنوان', 'erfansanat' ), '' ),
							'tag'         => self::text_field( __( 'برچسب کوچک', 'erfansanat' ), '' ),
							'description' => self::text_field( __( 'توضیح کوتاه', 'erfansanat' ), '' ),
							'image'       => self::text_field( __( 'نام فایل تصویر', 'erfansanat' ), '' ),
							'url'         => self::url_field( __( 'لینک', 'erfansanat' ) ),
						),
						array(
							array(
								'title'       => 'تونل نوری شهری',
								'tag'         => 'کمانی · نیم‌آرک · ۹۰ درجه · مربعی',
								'description' => 'مسیرهای عبوری با جلوه‌ای چشم‌نواز؛ از ورودی بوستان‌ها تا جشنواره‌های شهری با سازه‌های فولادی و پلی‌کربنات ضدضربه',
								'image'       => 'images/light-tunnel.jpg',
								'url'         => '/projects/',
							),
							array(
								'title'       => 'گوی و المان حجمی نورانی',
								'tag'         => 'المان حجمی سه‌بعدی · فول‌کالر',
								'description' => 'کره‌ها و حجم‌های نوری درخشان در نقطه کانونی میادین و فضاهای باز شهری',
								'image'       => 'images/light-sphere.jpg',
								'url'         => '/projects/',
							),
							array(
								'title'       => 'درخت و المان نوری هوشمند',
								'tag'         => 'سازه‌های نوری اختصاصی · استاندارد IP68',
								'description' => 'طراحی و ساخت المان‌های نوری منحصربه‌فرد با هویت بصری ماندگار برای شهرداری‌ها',
								'image'       => 'images/light-element.jpg',
								'url'         => '/projects/',
							),
						)
					),
				),
			),
			'projects'      => array(
				'label'  => __( 'نمونه‌کارها', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-projects.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'آرشیو تفکیکی پروژه‌های اجرایی' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'نمونه‌کارهای اجراشده در کلان‌شهرهای کشور' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), '' ),
					'link'     => self::link_field( __( 'لینک آرشیو', 'erfansanat' ), 'مشاهده آرشیو کامل پروژه‌ها', '/projects/' ),
				),
			),
			'about'         => array(
				'label'  => __( 'دربارهٔ شرکت', 'erfansanat' ),
				'anchor' => 'about',
				'part'   => 'template-parts/home/section-about-intro.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'درباره شرکت دانش‌بنیان عرفان صنعت اصفهان' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'بیش از ۲۵ سال تجربه مهندسی در تولید سازه‌های نوری شهری' ),
					'subtitle' => self::text_field( __( 'متن کوتاه', 'erfansanat' ), 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE) با تمرکز بر طراحی و تولید صنعتی تجهیزات روشنایی و المان‌های نوری شهری.' ),
					'media_id' => self::media_field( __( 'تصویر بخش', 'erfansanat' ) ),
					'features' => self::repeater_field(
						__( 'فهرست مزیت‌ها', 'erfansanat' ),
						array( 'title' => self::text_field( __( 'متن', 'erfansanat' ), '' ) ),
						array(
							array( 'title' => 'طراحی، تولید و اجرای صفر تا صد پروژه‌های روشنایی شهری و میادین' ),
							array( 'title' => 'دارای گواهینامه بین‌المللی ISO 9001:2015 و ثبت رسمی در سامانه ستاد' ),
							array( 'title' => '۲۴ ماه گارانتی تعویض بی‌قیدوشرط کارخانه + ۵ سال خدمات پس از فروش' ),
						)
					),
					'stats'    => self::repeater_field(
						__( 'آمار کوتاه', 'erfansanat' ),
						array(
							'title' => self::text_field( __( 'برچسب', 'erfansanat' ), '' ),
							'value' => self::text_field( __( 'مقدار', 'erfansanat' ), '' ),
						),
						array(
							array(
								'title' => 'سال تجربه',
								'value' => '۲۵',
							),
							array(
								'title' => 'پروژهٔ شهری',
								'value' => '۸۵۰+',
							),
						)
					),
				),
			),
			'knowledge'     => array(
				'label'  => __( 'مرجع بومی‌سازی', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-knowledge.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'اصالت و هویت صنعتی کارخانه' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'مرجع بومی‌سازی سازه‌های نوری و تجهیزات روشنایی شهری در ایران' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'تاسیس کارخانه از سال ۱۳۸۰ خورشیدی' ),
					'media_id' => self::media_field( __( 'تصویر بخش', 'erfansanat' ) ),
					'link'     => self::link_field( __( 'لینک', 'erfansanat' ), 'ارتباط مستقیم با کارخانه', '/contact/' ),
					'items'    => self::repeater_field(
						__( 'ردیف‌های معرفی', 'erfansanat' ),
						array(
							'title'       => self::text_field( __( 'عنوان', 'erfansanat' ), '' ),
							'description' => self::text_field( __( 'توضیح', 'erfansanat' ), '' ),
							'icon'        => self::icon_field( __( 'آیکون', 'erfansanat' ) ),
						),
						array(
							array(
								'title'       => 'خط تولید صنعتی',
								'description' => 'قالب‌سازی، تزریق پلیمر و مونتاژ بردهای الکترونیکی در یک مجموعه',
								'icon'        => 'factory',
							),
							array(
								'title'       => 'مهندسی بومی',
								'description' => 'طراحی سه‌بعدی و بومی‌سازی سازه‌های نوری مطابق اقلیم ایران',
								'icon'        => 'lamp',
							),
							array(
								'title'       => 'پشتیبانی ملی',
								'description' => 'پوشش ۳۱ استان با تیم نصب و تامین قطعات یدکی',
								'icon'        => 'truck',
							),
						)
					),
				),
			),
			'history'       => array(
				'label'  => __( 'تاریخچه', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-history.php',
				'fields' => array(
					'eyebrow'    => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'مسیر رشد و تکامل صنعتی' ),
					'title'      => self::text_field( __( 'تیتر', 'erfansanat' ), 'تاریخچه و دستاوردهای عرفان صنعت اصفهان (۱۳۸۰ تا ۱۴۰۵)' ),
					'subtitle'   => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'بیش از دو دهه مهندسی و بومی‌سازی؛ از تاسیس کارخانه در سال ۱۳۸۰ تا عبور از ۸۵۰ پروژه شهری و ورود به صنعت نیروگاه‌های خورشیدی با برند «سانیرما».' ),
					'media_id'   => self::media_field( __( 'تصویر تاریخچه', 'erfansanat' ) ),
					'milestones' => self::repeater_field(
						__( 'دستاوردها', 'erfansanat' ),
						array(
							'year'        => self::text_field( __( 'سال', 'erfansanat' ), '' ),
							'label'       => self::text_field( __( 'برچسب', 'erfansanat' ), '' ),
							'title'       => self::text_field( __( 'عنوان دستاورد', 'erfansanat' ), '' ),
							'description' => self::text_field( __( 'توضیح', 'erfansanat' ), '' ),
						),
						array(
							array(
								'year'        => '۱۳۸۰',
								'label'       => 'فاز تاسیس صنعتی',
								'title'       => 'تاسیس مجموعه و آغاز تولید صنعتی',
								'description' => 'شرکت عرفان صنعت اصفهان فعالیت تخصصی خود را با هدف بومی‌سازی تجهیزات روشنایی و سازه‌های نوری شهری آغاز کرد.',
							),
							array(
								'year'        => '۱۳۹۲',
								'label'       => 'استاندارد IP68',
								'title'       => 'راه‌اندازی خط تزریق مستقیم ریسه‌های IP68',
								'description' => 'راه‌اندازی خط تزریق مستقیم پلیمر برای ریسه‌های LED با درجه حفاظت IP68 و مقاومت کامل در برابر رطوبت.',
							),
							array(
								'year'        => '۱۳۹۷',
								'label'       => 'هوشمندسازی NRF',
								'title'       => 'تولید درختان نوری هوشمند و پوینت‌لایت پیکسلی',
								'description' => 'ورود به نسل هوشمند المان‌های نوری با کنترل بی‌سیم NRF و پوینت‌لایت‌های پیکسلی RGB.',
							),
							array(
								'year'        => '۱۴۰۱',
								'label'       => 'گواهینامه‌های رسمی',
								'title'       => 'اخذ گواهینامه ISO 9001:2015 و کدهای GS1',
								'description' => 'دریافت گواهینامه ISO 9001:2015، کدهای GS1 و ثبت رسمی در سامانه تدارکات الکترونیکی دولت (ستاد).',
							),
							array(
								'year'        => '۱۴۰۴',
								'label'       => '+۸۵۰ پروژه شهری',
								'title'       => 'عبور از مرز ۸۵۰ پروژه موفق در ایران و عراق',
								'description' => 'اجرای موفق بیش از ۸۵۰ پروژه نورپردازی شهری در ایران و پروژه صادراتی عراق.',
							),
							array(
								'year'        => '۱۴۰۵',
								'label'       => 'برند سانیرما (Sunirma)',
								'title'       => 'ورود به حوزه تولید نیروگاه‌های خورشیدی با برند سانیرما',
								'description' => 'آغاز تولید نیروگاه‌های خورشیدی با برند سانیرما (Sunirma) و توسعه سبد انرژی پاک.',
							),
						)
					),
				),
			),
			'products'      => array(
				'label'  => __( 'منتخب محصولات', 'erfansanat' ),
				'anchor' => 'products',
				'part'   => 'template-parts/home/section-products.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'تولیدات تخصصی کارخانه' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'منتخب محصولات [عرفان صنعت اصفهان]' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'تولید صنعتی تجهیزات نورپردازی شهری با عایق‌بندی تزریق مستقیم، بدنه پلی‌کربنات نشکن و کنترلرهای مرکزی بی‌سیم.' ),
					'link'     => self::link_field( __( 'لینک فروشگاه', 'erfansanat' ), 'مشاهدهٔ همه محصولات', '/shop/' ),
				),
			),
			'catalog'       => array(
				'label'  => __( 'کاتالوگ', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-catalog.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'مرجع مشخصات فنی · کدهای سامانه ستاد · کد ملی کالا GS1' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'کاتالوگ جامع محصولات عرفان صنعت اصفهان (ویرایش ۱۴۰۴)' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'مشخصات فنی کامل، ابعاد سازه، توان مصرفی، درجه حفاظت IP68 و راهنمای نصب بیش از ۱۲۵ محصول تخصصی نورپردازی شهری در کاتالوگ رسمی کارخانه گردآوری شده است.' ),
					'note'     => self::text_field( __( 'یادداشت کنار دکمه', 'erfansanat' ), 'شامل +۱۲۵ محصول تخصصی · آماده ارائه به شهرداری‌ها' ),
					'badge'    => self::text_field( __( 'نسخهٔ کاتالوگ', 'erfansanat' ), 'نسخه ۱۴۰۴' ),
					'media_id' => self::media_field( __( 'جلد کاتالوگ', 'erfansanat' ) ),
					'link'     => self::link_field( __( 'دریافت کاتالوگ', 'erfansanat' ), 'مشاهده و دریافت کاتالوگ محصولات', '/shop/' ),
				),
			),
			'services'      => array(
				'label'          => __( 'خدمات', 'erfansanat' ),
				'anchor'         => 'services',
				'anchor_in_part' => true,
				'part'           => 'template-parts/home/section-services.php',
				'fields'         => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'خدمات مهندسی و اجرایی' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'خدمات تخصصی نورپردازی شهری' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'از طراحی سه‌بعدی تا تولید صنعتی و اجرای کامل در محل؛ راهکارهای جامع برای شهرداری‌ها و سازمان‌های عمرانی' ),
					'items'    => self::repeater_field(
						__( 'فهرست خدمات', 'erfansanat' ),
						array(
							'title'       => self::text_field( __( 'عنوان', 'erfansanat' ), '' ),
							'description' => self::text_field( __( 'توضیح', 'erfansanat' ), '' ),
							'icon'        => self::icon_field( __( 'آیکون', 'erfansanat' ) ),
							'image'       => self::text_field( __( 'نام فایل تصویر', 'erfansanat' ), '' ),
						),
						array(
							array(
								'title'       => 'نورپردازی میادین شهری',
								'description' => 'خلق نقطه کانونی بصری در قلب شهر با المان‌های حجمی و سازه‌های نوری شاخص',
								'icon'        => 'city',
								'image'       => 'images/light-sphere.jpg',
							),
							array(
								'title'       => 'نورپردازی خیابان و بلوار',
								'description' => 'آذین‌بندی و روشنایی یکنواخت مسیرهای شهری با لوسترهای ستون و ریسه‌های IP68',
								'icon'        => 'lamp',
								'image'       => 'images/string-lights.jpg',
							),
							array(
								'title'       => 'نورپردازی پل و تقاطع‌ها',
								'description' => 'نمای نورانی پل‌های سواره و پیاده با پوینت‌لایت‌های پیکسلی و والواشرهای خطی',
								'icon'        => 'badge',
								'image'       => 'images/light-tunnel.jpg',
							),
							array(
								'title'       => 'طراحی و ساخت المان‌های شهری',
								'description' => 'ساخت تونل‌های نوری و درختان هوشمند با هویت بصری اختصاصی برای هر شهر',
								'icon'        => 'factory',
								'image'       => 'images/light-tree.jpg',
							),
							array(
								'title'       => 'نورپردازی نمای ابنیه و ساختمان',
								'description' => 'روشنایی معماری نمای ساختمان‌های اداری، تاریخی و مراکز عمومی شهر',
								'icon'        => 'star',
								'image'       => 'images/light-element.jpg',
							),
						)
					),
				),
			),
			'quality'       => array(
				'label'  => __( 'استانداردهای کیفیت', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-quality.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'مزیت‌های رقابتی کارخانه' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'استانداردهای [کیفیت و ماندگاری]' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'تجهیزات نورپردازی شهری سرمایه‌گذاری بلندمدت هستند؛ هر محصول عرفان صنعت برای سال‌ها کارکرد مداوم در فضای باز مهندسی شده است.' ),
					'icon'     => self::icon_field( __( 'آیکون اصلی', 'erfansanat' ), 'badge' ),
					'items'    => self::repeater_field(
						__( 'کارت‌های کیفیت', 'erfansanat' ),
						array(
							'title'       => self::text_field( __( 'عنوان', 'erfansanat' ), '' ),
							'description' => self::text_field( __( 'توضیح', 'erfansanat' ), '' ),
							'code'        => self::text_field( __( 'کد یا استاندارد', 'erfansanat' ), '' ),
							'icon'        => self::icon_field( __( 'آیکون', 'erfansanat' ) ),
						),
						array(
							array(
								'title'       => 'مدیریت کیفیت بین‌المللی',
								'description' => 'کنترل کیفیت دقیق در تمامی مراحل قالب‌سازی، تزریق پلیمر و مونتاژ بردهای الکترونیکی',
								'code'        => 'ISO 9001:2015',
								'icon'        => 'badge',
							),
							array(
								'title'       => 'عایق‌بندی تزریق مستقیم',
								'description' => 'مقاومت کامل در برابر نفوذ آب، رطوبت شدید، گرد و غبار و تابش مستقیم UV خورشید',
								'code'        => 'IP67 / IP68',
								'icon'        => 'shield',
							),
							array(
								'title'       => 'کنترل و همگام‌سازی هوشمند',
								'description' => 'مدیریت بی‌سیم افکت‌های نوری و طیف ۱۶ میلیون رنگ بدون نیاز به سیم‌کشی دیتا بین المان‌ها',
								'code'        => 'NRF + WiFi',
								'icon'        => 'star',
							),
							array(
								'title'       => 'گارانتی تعویض + ۵ سال خدمات',
								'description' => 'ضمانت رسمی تعویض کارخانه و تامین پایدار قطعات یدکی برای شهرداری‌ها و پیمانکاران',
								'code'        => '۲۴ ماه گارانتی',
								'icon'        => 'support',
							),
						)
					),
				),
			),
			'support'       => array(
				'label'  => __( 'پشتیبانی و گارانتی', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-support.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'شبکه خدمات پس از فروش ESE' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'پشتیبانی فنی و گارانتی تعویض در سراسر ایران' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'همراهی عرفان صنعت با شهرداری‌ها و پیمانکاران پس از تحویل کالا آغاز می‌شود. ارائه نقشه‌های اجرایی، اعزام تیم نصب، تامین قطعات اصلی و ۲۴ ماه گارانتی تعویض سریع در تمامی ۳۱ استان کشور تضمین شده است.' ),
					'icon'     => self::icon_field( __( 'آیکون اصلی', 'erfansanat' ), 'support' ),
					'items'    => self::repeater_field(
						__( 'مزیت‌های پشتیبانی', 'erfansanat' ),
						array(
							'title' => self::text_field( __( 'عنوان', 'erfansanat' ), '' ),
							'value' => self::text_field( __( 'مقدار', 'erfansanat' ), '' ),
							'icon'  => self::icon_field( __( 'آیکون', 'erfansanat' ) ),
						),
						array(
							array(
								'title' => 'ارسال مستقیم از کارخانه مرکزی اصفهان',
								'value' => '۱',
								'icon'  => 'truck',
							),
							array(
								'title' => 'ضمانت تعویض بی‌قیدوشرط',
								'value' => '۲۴ ماه گارانتی',
								'icon'  => 'shield',
							),
							array(
								'title' => 'تامین قطعات در ۳۱ استان',
								'value' => '۵ سال پشتیبانی',
								'icon'  => 'support',
							),
						)
					),
				),
			),
			'clients'       => array(
				'label'  => __( 'مشتریان', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-clients.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'شبکه گسترده کارفرمایان' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'مشتریان و سازمان‌های همکار عرفان صنعت' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'افتخار همراهی با بیش از ۱۸۰ شهرداری، سازمان زیباسازی و پیمانکار عمرانی در سراسر کشور' ),
					'items'    => self::repeater_field(
						__( 'فهرست مشتریان', 'erfansanat' ),
						array(
							'title'       => self::text_field( __( 'نام', 'erfansanat' ), '' ),
							'description' => self::text_field( __( 'زمینهٔ همکاری', 'erfansanat' ), '' ),
						),
						array(
							array(
								'title'       => 'شهرداری اصفهان',
								'description' => 'پروژه‌های تونل و المان نوری',
							),
							array(
								'title'       => 'شهرداری شیراز',
								'description' => 'نورپردازی بلوار و میادین',
							),
							array(
								'title'       => 'شهرداری مشهد',
								'description' => 'درختان نوری و ریسه IP68',
							),
							array(
								'title'       => 'شهرداری کرج',
								'description' => 'تامین تجهیزات روشنایی',
							),
							array(
								'title'       => 'شهرداری اهواز',
								'description' => 'پروژه‌های ساحلی و پل‌ها',
							),
							array(
								'title'       => 'شهرداری یزد',
								'description' => 'المان‌های نوری بافت تاریخی',
							),
							array(
								'title'       => 'شهرداری تبریز',
								'description' => 'سازه‌های نوری مقاوم به سرما',
							),
							array(
								'title'       => 'سامانه ستاد دولت',
								'description' => 'تامین‌کننده رسمی ثبت‌شده',
							),
							array(
								'title'       => 'مرکز ملی ایران‌کد',
								'description' => 'دارای کد ملی کالا GS1',
							),
							array(
								'title'       => 'شهرداری ورامین',
								'description' => 'لوسترهای ستون و بلوار',
							),
							array(
								'title'       => 'شهرداری ارومیه',
								'description' => 'نورپردازی پارک‌ها و معابر',
							),
							array(
								'title'       => 'سازمان زیباسازی',
								'description' => 'طراحی و ساخت المان حجمی',
							),
						)
					),
				),
			),
			'blog'          => array(
				'label'  => __( 'آخرین مقالات', 'erfansanat' ),
				'anchor' => 'blog-preview',
				'part'   => 'template-parts/home/section-blog.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'دانشنامه و مجله فنی' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'جدیدترین مقالات و [راهنماهای تخصصی نورپردازی]' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'بررسی فنی تجهیزات LED، استانداردهای حفاظتی IP68 و راهنمای خرید المان‌های نوری شهری' ),
					'link'     => self::link_field( __( 'لینک آرشیو', 'erfansanat' ), 'مشاهده آرشیو مقالات', '/blog/' ),
				),
			),
			'contact-cta'   => array(
				'label'  => __( 'فراخوان تماس', 'erfansanat' ),
				'anchor' => 'contact',
				'part'   => 'template-parts/home/section-contact-cta.php',
				'fields' => array(
					'eyebrow'  => self::text_field( __( 'برچسب بالای تیتر', 'erfansanat' ), 'مشاوره مهندسی و استعلام قیمت مستقیم از کارخانه' ),
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'روشنایی و هویت بصری متمایز را به [شهر خود] دعوت کنید' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'واحد مهندسی فروش عرفان صنعت اصفهان آماده ارائه مشاوره تخصصی، طراحی سه‌بعدی المان‌های نوری، صدور پیش‌فاکتور رسمی سامانه تدارکات الکترونیکی دولت (ستاد) و ارسال مستقیم به سراسر کشور است.' ),
					'link'     => self::link_field( __( 'دکمهٔ تماس', 'erfansanat' ), 'تماس مستقیم با کارشناسان فروش', '' ),
					'link2'    => self::link_field( __( 'دکمهٔ دوم', 'erfansanat' ), 'درخواست پیش‌فاکتور رسمی ستاد', '/contact/' ),
				),
			),
			'location'      => array(
				'label'  => __( 'نشانی کارخانه', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-location.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'نشانی کارخانه و دفتر مرکزی اصفهان' ),
					'subtitle' => self::text_field( __( 'نشانی', 'erfansanat' ), 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹' ),
					'media_id' => self::media_field( __( 'نقشه یا تصویر', 'erfansanat' ) ),
				),
			),
		);
	}

	/**
	 * One plain text field.
	 *
	 * @param string $label        Field label.
	 * @param string $default_text Default text.
	 * @return array<string, mixed>
	 */
	private static function text_field( string $label, string $default_text ): array {
		return array(
			'type'    => 'text',
			'label'   => $label,
			'default' => $default_text,
		);
	}

	/**
	 * One URL field.
	 *
	 * @param string $label Field label.
	 * @return array<string, mixed>
	 */
	private static function url_field( string $label ): array {
		return array(
			'type'    => 'url',
			'label'   => $label,
			'default' => '',
		);
	}

	/**
	 * One media field.
	 *
	 * @param string $label Field label.
	 * @return array<string, mixed>
	 */
	private static function media_field( string $label ): array {
		return array(
			'type'    => 'media',
			'label'   => $label,
			'default' => 0,
		);
	}

	/**
	 * One link field (title plus URL).
	 *
	 * @param string $label      Field label.
	 * @param string $title      Default title.
	 * @param string $url        Default URL.
	 * @return array<string, mixed>
	 */
	private static function link_field( string $label, string $title, string $url ): array {
		return array(
			'type'    => 'link',
			'label'   => $label,
			'default' => array(
				'title' => $title,
				'url'   => $url,
			),
		);
	}

	/**
	 * One icon field limited to the whitelist.
	 *
	 * @param string $label        Field label.
	 * @param string $default_icon Default icon name.
	 * @return array<string, mixed>
	 */
	private static function icon_field( string $label, string $default_icon = 'lamp' ): array {
		return array(
			'type'    => 'icon',
			'label'   => $label,
			'default' => $default_icon,
		);
	}

	/**
	 * One repeater field.
	 *
	 * @param string                              $label      Field label.
	 * @param array<string, array<string, mixed>> $fields    Sub-field definitions.
	 * @param array<int, array<string, mixed>>    $default_rows Default rows.
	 * @return array<string, mixed>
	 */
	private static function repeater_field( string $label, array $fields, array $default_rows = array() ): array {
		return array(
			'type'    => 'repeater',
			'label'   => $label,
			'fields'  => $fields,
			'default' => $default_rows,
		);
	}

	/**
	 * Default home layout: all fifteen reference sections, in DOM order.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function default_home(): array {
		$rows = array();
		foreach ( self::registry() as $key => $section ) {
			$rows[] = array(
				'key'     => $key,
				'enabled' => true,
				'fields'  => self::default_fields( $section['fields'] ),
			);
		}
		return $rows;
	}

	/**
	 * Default value for every field in a definition list.
	 *
	 * @param array<string, array<string, mixed>> $fields Field definitions.
	 * @return array<string, mixed>
	 */
	public static function default_fields( array $fields ): array {
		$values = array();
		foreach ( $fields as $name => $field ) {
			$values[ $name ] = $field['default'] ?? '';
		}
		return $values;
	}

	/**
	 * Sanitise a submitted layout.
	 *
	 * Unknown keys, duplicate keys and hostile field data are dropped. Sections
	 * missing from the payload are appended after the saved ones so a new
	 * section reaches an existing site without destroying the saved order.
	 *
	 * @param mixed $raw Submitted layout.
	 * @return array<int, array<string, mixed>>
	 */
	public static function sanitize_layout( $raw ): array {
		$registry = self::registry();
		$rows     = array();
		$seen     = array();

		foreach ( is_array( $raw ) ? $raw : array() as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$key = isset( $row['key'] ) && is_string( $row['key'] ) ? sanitize_key( $row['key'] ) : '';
			if ( '' === $key || ! isset( $registry[ $key ] ) || isset( $seen[ $key ] ) ) {
				continue;
			}
			$seen[ $key ] = true;
			$rows[]       = array(
				'key'     => $key,
				'enabled' => ! empty( $row['enabled'] ),
				'fields'  => self::sanitize_fields( $registry[ $key ]['fields'], $row['fields'] ?? array() ),
			);
			if ( count( $rows ) >= self::MAX_SECTIONS ) {
				break;
			}
		}

		foreach ( $registry as $key => $section ) {
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$rows[] = array(
				'key'     => $key,
				'enabled' => true,
				'fields'  => self::default_fields( $section['fields'] ),
			);
		}

		return $rows;
	}

	/**
	 * Sanitise the field bag of one section.
	 *
	 * @param array<string, array<string, mixed>> $definitions Field definitions.
	 * @param mixed                               $raw         Submitted fields.
	 * @return array<string, mixed>
	 */
	public static function sanitize_fields( array $definitions, $raw ): array {
		$input  = is_array( $raw ) ? $raw : array();
		$values = array();
		foreach ( $definitions as $name => $field ) {
			$values[ $name ] = self::sanitize_value( $field, $input[ $name ] ?? null );
		}
		return $values;
	}

	/**
	 * Sanitise one field value by type.
	 *
	 * @param array<string, mixed> $field Field definition.
	 * @param mixed                $raw   Submitted value.
	 * @return mixed
	 */
	private static function sanitize_value( array $field, $raw ) {
		$fallback = $field['default'] ?? '';
		switch ( (string) $field['type'] ) {
			case 'media':
				$id = absint( is_scalar( $raw ) ? $raw : 0 );
				return ( $id > 0 && 'attachment' === get_post_type( $id ) ) ? $id : 0;

			case 'url':
				return esc_url_raw( is_string( $raw ) ? trim( $raw ) : '', array( 'http', 'https', 'tel', 'mailto' ) );

			case 'icon':
				$icon = is_string( $raw ) ? sanitize_key( $raw ) : '';
				return in_array( $icon, self::ICONS, true ) ? $icon : (string) $fallback;

			case 'link':
				$row       = is_array( $raw ) ? $raw : array();
				$raw_title = $row['title'] ?? '';
				$raw_url   = $row['url'] ?? '';
				$title     = sanitize_text_field( is_scalar( $raw_title ) ? (string) $raw_title : '' );
				$url       = esc_url_raw( is_scalar( $raw_url ) ? (string) $raw_url : '', array( 'http', 'https', 'tel', 'mailto' ) );
				if ( '' === $title && '' === $url ) {
					// Nothing submitted at all: keep the reference default.
					$link_fallback = is_array( $fallback ) ? $fallback : array();
					return array(
						'title' => (string) ( $link_fallback['title'] ?? '' ),
						'url'   => (string) ( $link_fallback['url'] ?? '' ),
					);
				}
				// Something was submitted, so never resurrect the default: an
				// invalid URL becomes empty and the button simply stops rendering.
				return array(
					'title' => $title,
					'url'   => $url,
				);

			case 'repeater':
				return self::sanitize_rows( (array) ( $field['fields'] ?? array() ), $raw, (array) $fallback );

			case 'text':
			default:
				return sanitize_text_field( is_scalar( $raw ) ? (string) $raw : (string) $fallback );
		}
	}

	/**
	 * Sanitise repeater rows, dropping rows that end up completely empty.
	 *
	 * @param array<string, array<string, mixed>> $definitions Sub-field definitions.
	 * @param mixed                               $raw         Submitted rows.
	 * @param array<int, array<string, mixed>>    $fallback    Default rows.
	 * @return array<int, array<string, mixed>>
	 */
	private static function sanitize_rows( array $definitions, $raw, array $fallback ): array {
		if ( ! is_array( $raw ) ) {
			return $fallback;
		}
		$rows = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean = array();
			$empty = true;
			foreach ( $definitions as $name => $field ) {
				$clean[ $name ] = self::sanitize_value( $field, $row[ $name ] ?? null );
				$value          = $clean[ $name ];
				if ( is_array( $value ) ? array() !== array_filter( $value, static fn( $item ): bool => '' !== (string) $item ) : '' !== (string) $value ) {
					$empty = false;
				}
			}
			if ( ! $empty ) {
				$rows[] = $clean;
			}
			if ( count( $rows ) >= self::MAX_ROWS ) {
				break;
			}
		}
		return $rows;
	}

	/**
	 * Whether a stored value is a structurally valid layout.
	 *
	 * @param mixed $value Stored value.
	 * @return bool
	 */
	public static function is_valid_layout( $value ): bool {
		if ( ! is_array( $value ) || array() === $value ) {
			return false;
		}
		foreach ( $value as $row ) {
			if ( ! is_array( $row ) || ! isset( $row['key'], $row['fields'] ) || ! is_string( $row['key'] ) || ! is_array( $row['fields'] ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Saved home layout, or the default when nothing valid is stored.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function home_layout(): array {
		$stored = Settings::get( 'home_sections', array() );
		if ( ! self::is_valid_layout( $stored ) ) {
			return self::default_home();
		}
		return self::sanitize_layout( $stored );
	}

	/**
	 * Layout for a page that carries its own override.
	 *
	 * A page stores the ordered list of section keys it wants; anything missing
	 * falls back to the home layout so an old page cannot lose new sections.
	 *
	 * @param int $post_id Page id.
	 * @return array<int, array<string, mixed>>
	 */
	public static function layout_for_page( int $post_id ): array {
		$home  = self::home_layout();
		$saved = get_post_meta( $post_id, '_erfansanat_sections', true );
		if ( ! is_array( $saved ) || array() === $saved ) {
			return $home;
		}
		$keys = array();
		foreach ( $saved as $key ) {
			$key = is_string( $key ) ? sanitize_key( $key ) : '';
			if ( '' !== $key && ! in_array( $key, $keys, true ) ) {
				$keys[] = $key;
			}
		}
		$ordered = array();
		foreach ( $keys as $key ) {
			foreach ( $home as $row ) {
				if ( $row['key'] === $key ) {
					$ordered[] = $row;
					break;
				}
			}
		}
		return array() === $ordered ? $home : $ordered;
	}

	/**
	 * Keys that will actually render, in order.
	 *
	 * @param array<int, array<string, mixed>> $layout Layout rows.
	 * @return array<int, string>
	 */
	public static function enabled_keys( array $layout ): array {
		$keys = array();
		foreach ( $layout as $row ) {
			if ( ! empty( $row['enabled'] ) && isset( $row['key'] ) ) {
				$keys[] = (string) $row['key'];
			}
		}
		return $keys;
	}

	/**
	 * Print the enabled sections in the saved order.
	 *
	 * The wrapper carries the reference anchor id, so a future template part
	 * fills the same slot the static mirror uses. Phase 5 supplies those parts.
	 *
	 * @param array<int, array<string, mixed>> $layout Layout rows.
	 * @return void
	 */
	public static function render( array $layout ): void {
		$registry = self::registry();
		foreach ( $layout as $row ) {
			if ( empty( $row['enabled'] ) || ! isset( $registry[ $row['key'] ] ) ) {
				continue;
			}
			$key     = (string) $row['key'];
			$section = $registry[ $key ];
			$owns    = ! empty( $section['anchor_in_part'] );
			$anchor  = ( ! $owns && '' !== (string) $section['anchor'] ) ? ' id="' . esc_attr( (string) $section['anchor'] ) . '"' : '';
			$part    = (string) apply_filters( 'erfansanat_section_part', $section['part'], $key, $row['fields'] );
			$exists  = '' !== $part && file_exists( get_template_directory() . '/' . ltrim( $part, '/' ) );

			printf(
				'<section%s class="erfansanat-section erfansanat-section--%s" data-section="%s">',
				$anchor, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
				esc_attr( $key ),
				esc_attr( $key )
			);

			if ( $exists ) {
				$erfansanat_section_fields = $row['fields'];
				include get_template_directory() . '/' . ltrim( $part, '/' );
				unset( $erfansanat_section_fields );
			}

			echo '</section>';
		}
	}

	/**
	 * Inline SVG for one whitelisted icon.
	 *
	 * @param string $name Icon name.
	 * @param string $css  Extra class names.
	 * @return string
	 */
	public static function icon_svg( string $name, string $css = 'h-5 w-5' ): string {
		$name  = in_array( $name, self::ICONS, true ) ? $name : 'lamp';
		$paths = array(
			'lamp'    => '<path d="M12 3v3M5 6l2 2M19 6l-2 2M4 14h16l-2 6H6z"/>',
			'city'    => '<path d="M4 21V9l8-5 8 5v12M9 21v-6h6v6"/><path d="M4 21h16"/>',
			'shield'  => '<path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/>',
			'star'    => '<path d="M12 3l2.8 6 6.2.8-4.5 4.3 1.1 6.1L12 17.8 6.4 20.2l1.1-6.1L3 9.8 9.2 9z"/>',
			'factory' => '<path d="M3 21V10l6 4V10l6 4V7l6 3v11z"/><path d="M7 21v-3M12 21v-3M17 21v-3"/>',
			'truck'   => '<path d="M3 7h11v9H3z"/><path d="M14 11h4l3 3v2h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
			'support' => '<path d="M4 14v-2a8 8 0 0116 0v2"/><path d="M4 14h3v5H4zM17 14h3v5h-3z"/>',
			'badge'   => '<circle cx="12" cy="9" r="5"/><path d="M8.5 13.5L7 21l5-2 5 2-1.5-7.5"/>',
		);
		return sprintf(
			'<svg class="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
			esc_attr( $css ),
			$paths[ $name ]
		);
	}
}
