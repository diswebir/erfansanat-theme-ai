<?php
/**
 * Static-pages settings tab (P07-T022+): licenses, FAQ, warranty, recruitment.
 *
 * Read helpers normalize the stored repeaters and fall back to schema
 * defaults. Order of repeater items is preserved exactly as saved.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;

final class PagesTab {

	public const ID = 'pages';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
	}

	public static function id(): string {
		return self::ID;
	}

	/** @return array<int,string> */
	public static function fields(): array {
		return array_keys(
			array_filter(
				Schema::fields(),
				static function ( $field ) {
					return ( $field['tab'] ?? '' ) === self::ID;
				}
			)
		);
	}

	/** @return array<string,mixed> */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $key ) {
			$defaults[ $key ] = Schema::default( $key );
		}
		return $defaults;
	}

	/**
	 * Certificate/honor items in saved order. When nothing is stored yet the
	 * reference cards are returned so the page matches the static mirror
	 * out-of-the-box; once the editor saves items, those replace the defaults
	 * and their order is preserved exactly (AC-03).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function licenses(): array {
		$items = es_option( 'licenses_items' );
		if ( ! is_array( $items ) || array() === $items ) {
			return self::licenses_defaults();
		}
		$out = array();
		foreach ( array_values( $items ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$out[] = self::normalize_license( $item );
		}
		return $out;
	}

	/**
	 * @param array<string,mixed> $item Raw item.
	 * @return array<string,mixed>
	 */
	private static function normalize_license( array $item ): array {
		return array(
			'category'    => (string) ( $item['category'] ?? '' ),
			'title'       => (string) ( $item['title'] ?? '' ),
			'code'        => (string) ( $item['code'] ?? '' ),
			'description' => (string) ( $item['description'] ?? '' ),
			'issuer'      => (string) ( $item['issuer'] ?? '' ),
			'year'        => (string) ( $item['year'] ?? '' ),
			'image'       => (int) ( $item['image'] ?? 0 ),
			'alt'         => (string) ( $item['alt'] ?? '' ),
		);
	}

	/**
	 * Reference certificates (static-mirror/licenses-and-honors.html), in order.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function licenses_defaults(): array {
		$raw = array(
			array( 'دانش‌بنیان و فناوری', 'KB-9904182', 'گواهی شرکت دانش‌بنیان (معاونت علمی و فناوری ریاست جمهوری)', 'احراز صلاحیت دانش‌بنیان در زمینه طراحی و تولید سیستم‌های کنترل هوشمند نورپردازی شهری، پوینت لایت‌های پیکسلی قابل آدرس‌دهی و المان‌های نوری مالتی‌مدیا.', 'معاونت علمی، فناوری و اقتصاد دانش‌بنیان ریاست جمهوری', '۱۴۰۰ — تمدید تاکنون' ),
			array( 'استاندارد بین‌المللی', 'QMS-2015-88412', 'گواهینامه بین‌المللی سیستم مدیریت کیفیت ISO 9001:2015', 'استقرار کامل استانداردهای کنترل کیفیت در خطوط تولید ریسه‌های LED، تزریق رزین، جوش التراسونیک و مونتاژ تابلوهای فرمان صنعتی.', 'مؤسسه استاندارد و صدور گواهینامه‌های بین‌المللی', '۱۳۹۸ — معتبر' ),
			array( 'آزمون فنی آزمایشگاهی', 'LAB-IP68-4019', 'گواهی آزمون درجه حفاظت نفوذپذیری IP67 و IP68', 'تأییدیه مقاومت کامل پوینت لایت‌های پیکسلی و ریسه‌های سوزنی و بلوطی عرفان صنعت در برابر غوطه‌وری در آب، نفوذ ریزگردها و شوک حرارتی.', 'آزمایشگاه همکار سازمان ملی استاندارد ایران', '۱۴۰۱' ),
			array( 'شناسه رسمی و صادرات', 'GS1-62604918', 'گواهی عضویت و کدگذاری بین‌المللی کالا (GS1 / ایران‌کد)', 'ثبت رسمی تمامی محصولات تولیدی با کدهای بین‌المللی GTIN، کد ملی کالا، HS و ISIC جهت عرضه در سامانه تدارکات الکترونیکی دولت (ستاد) و صادرات.', 'مرکز ملی شماره‌گذاری کالا و خدمات ایران', '۱۳۹۹' ),
			array( 'افتخارات اجرایی', 'HON-1403-ESF', 'لوح تقدیر مجری برتر پروژه‌های زیباسازی و نورپردازی شهری', 'تقدیر به‌پاس طراحی، ساخت و اجرای به موقع بیش از ۸۵۰ پروژه شاخص میدانگاهی، تونل نوری و نورپردازی پل در استان‌های اصفهان، البرز، خوزستان، خراسان و قم.', 'سازمان‌های زیباسازی شهرداری‌های کشور', '۱۴۰۲ و ۱۴۰۳' ),
			array( 'ثبت اختراع و طرح صنعتی', 'PAT-1400-39104', 'گواهی ثبت طرح صنعتی قاب نگهدارنده ضدسرقت پیکسل (PLT)', 'طراحی انحصاری ریل و قاب نگهدارنده PLT جهت نصب سریع، منظم و ضدسرقت پوینت لایت‌های پیکسلی در تونل‌های نوری و نماهای شهری.', 'مرکز مالکیت معنوی قوه قضائیه', '۱۴۰۰' ),
		);
		$out = array();
		foreach ( $raw as $r ) {
			$out[] = array(
				'category'    => $r[0],
				'code'        => $r[1],
				'title'       => $r[2],
				'description' => $r[3],
				'issuer'      => $r[4],
				'year'        => $r[5],
				'image'       => 0,
				'alt'         => '',
			);
		}
		return $out;
	}

	/**
	 * FAQ items (group/question/answer) in saved order. Empty stored value
	 * falls back to the reference questions; an explicitly empty FAQ renders
	 * no section (AC-03) — see the template guard.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function faq(): array {
		// Never configured → reference questions for out-of-the-box parity.
		// Explicitly saved (even empty) → honor the editor: an empty FAQ
		// returns [] so the template builds no section (AC-03).
		if ( ! array_key_exists( 'faq_items', Store::raw() ) ) {
			return self::faq_defaults();
		}
		$items = es_option( 'faq_items' );
		if ( ! is_array( $items ) ) {
			return array();
		}
		$out = array();
		foreach ( array_values( $items ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$question = trim( (string) ( $item['question'] ?? '' ) );
			if ( '' === $question ) {
				continue;
			}
			$out[] = array(
				'group'    => (string) ( $item['group'] ?? '' ),
				'question' => $question,
				'answer'   => (string) ( $item['answer'] ?? '' ),
			);
		}
		return $out;
	}

	/**
	 * Reference FAQ (static-mirror/faq.html), in order.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function faq_defaults(): array {
		return array(
			array(
				'group'    => 'خرید، استعلام قیمت و سامانه ستاد',
				'question' => 'نحوه استعلام قیمت و دریافت پیش‌فاکتور رسمی برای شهرداری‌ها چگونه است؟',
				'answer'   => 'شهرداری‌ها، دهیاری‌ها، سازمان‌های زیباسازی و پیمانکاران محترم می‌توانند از طریق تماس با شماره ۰۳۱-۹۱۰۹۱۰۱۱ (۱۰ خط ویژه) یا ارسال مشخصات پروژه، در کمتر از ۲ ساعت کاری پیش‌فاکتور رسمی ممهور به همراه کدهای کالا و کاتالوگ فنی دریافت نمایند. همچنین امکان ثبت معامله در سامانه تدارکات الکترونیکی دولت (ستاد) فراهم است.',
			),
			array(
				'group'    => 'خرید، استعلام قیمت و سامانه ستاد',
				'question' => 'تفاوت محصولات «قابل خرید آنلاین» با محصولات «نیازمند استعلام» در فروشگاه چیست؟',
				'answer'   => 'تجهیزات تیپ و سری‌تولید کارخانه (مانند ریسه‌های بلوطی، فندقی، سوزنی، پوینت لایت، والواشر، باکس کنترل و لامپ LED) دارای قیمت مصوب بوده و مستقیماً قابل سفارش آنلاین هستند. در مقابل، سازه‌های بزرگ شهری (مانند درختان نوری، خورشیدنماها و لوسترها) به دلیل سفارشی‌سازی ارتفاع، رنگ سازه و تراکم پیکسل، پس از مشاوره فنی قیمت‌گذاری می‌شوند.',
			),
			array(
				'group'    => 'مشخصات فنی و استانداردهای IP67 / IP68',
				'question' => 'چرا ریسه‌ها و پوینت لایت‌های عرفان صنعت در بارندگی دچار اتصالی نمی‌شوند؟',
				'answer'   => 'تمامی ریسه‌های بلوطی و فندقی عرفان صنعت با حباب پلی‌کربنات Anti-UV تولید شده و محل اتصال حباب به سرپیچ توسط «جوش التراسونیک» یکپارچه می‌شود؛ سپس داخل سرپیچ با «رزین صنعتی پلی‌اورتان» پر می‌گردد. این فرآیند دوجداره، گواهی واقعی IP67 و IP68 را تضمین می‌کند.',
			),
			array(
				'group'    => 'مشخصات فنی و استانداردهای IP67 / IP68',
				'question' => 'برای پارک‌ها و فضاهایی که مردم به المان دسترسی دارند چه ولتاژی پیشنهاد می‌شود؟',
				'answer'   => 'مطابق استانداردهای ایمنی برق شهری، برای تمامی درختان نوری پارکی (مانند مدل دلسا)، ریسه‌بندی تنه درختان و تونل‌های نوری پیاده‌راه، از ولتاژ ایمن ۱۲ یا ۲۴ ولت DC استفاده می‌شود تا خطر برق‌گرفتگی به صفر برسد.',
			),
			array(
				'group'    => 'گارانتی، نصب و خدمات پس از فروش',
				'question' => 'شرایط گارانتی ۲۴ ماهه و خدمات پس از فروش ۵ ساله عرفان صنعت شامل چه مواردی است؟',
				'answer'   => 'تمامی محصولات تولیدی شرکت دانش‌بنیان عرفان صنعت اصفهان دارای ۲۴ ماه گارانتی تعویض بی‌قید و شرط در برابر نفوذ آب، سوختن چیپ LED و تغییر رنگ حباب در شرایط نصب استاندارد بوده و تا ۵ سال تأمین قطعات یدکی و پشتیبانی فنی آن‌ها تضمین می‌گردد.',
			),
			array(
				'group'    => 'گارانتی، نصب و خدمات پس از فروش',
				'question' => 'آیا عرفان صنعت تیم اجرایی برای نصب پروژه‌ها در سایر شهرهای ایران اعزام می‌کند؟',
				'answer'   => 'بله؛ تیم‌های فنی و اجرایی کارخانه عرفان صنعت در تمامی ۳۱ استان کشور و همچنین پروژه‌های صادراتی (کشور عراق) آماده اجرای صفر تا صد فونداسیون، نصب سازه، سیم‌کشی و راه‌اندازی سناریوهای نوری هستند.',
			),
		);
	}

	/**
	 * Warranty page data: cards (icon/title/description), covered/excluded
	 * lists and process steps. Each repeater falls back to the reference when
	 * empty/unset; saved items replace the defaults in their saved order.
	 *
	 * @return array{cards:array<int,array<string,string>>,covered:array<int,string>,excluded:array<int,string>,steps:array<int,array<string,string>>}
	 */
	public static function warranty(): array {
		$defaults = self::warranty_defaults();

		$cards = array();
		foreach ( self::repeater_items( 'warranty_cards' ) as $item ) {
			$title = trim( (string) ( $item['title'] ?? '' ) );
			if ( '' === $title ) {
				continue;
			}
			$cards[] = array(
				'icon'        => (string) ( $item['icon'] ?? 'shield' ),
				'title'       => $title,
				'description' => (string) ( $item['description'] ?? '' ),
			);
		}
		if ( array() === $cards ) {
			$cards = $defaults['cards'];
		}

		$covered = self::repeater_texts( 'warranty_covered' );
		if ( array() === $covered ) {
			$covered = $defaults['covered'];
		}
		$excluded = self::repeater_texts( 'warranty_excluded' );
		if ( array() === $excluded ) {
			$excluded = $defaults['excluded'];
		}

		$steps = array();
		foreach ( self::repeater_items( 'warranty_steps' ) as $item ) {
			$title = trim( (string) ( $item['title'] ?? '' ) );
			if ( '' === $title ) {
				continue;
			}
			$steps[] = array(
				'title'       => $title,
				'description' => (string) ( $item['description'] ?? '' ),
			);
		}
		if ( array() === $steps ) {
			$steps = $defaults['steps'];
		}

		return array(
			'cards'    => $cards,
			'covered'  => $covered,
			'excluded' => $excluded,
			'steps'    => $steps,
		);
	}

	/**
	 * Raw repeater items in saved order (empty array when none/invalid).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function repeater_items( string $key ): array {
		$items = es_option( $key );
		if ( ! is_array( $items ) ) {
			return array();
		}
		$out = array();
		foreach ( array_values( $items ) as $item ) {
			if ( is_array( $item ) ) {
				$out[] = $item;
			}
		}
		return $out;
	}

	/**
	 * Non-empty `text` subfield values of a repeater, in saved order.
	 *
	 * @return array<int,string>
	 */
	private static function repeater_texts( string $key ): array {
		$out = array();
		foreach ( self::repeater_items( $key ) as $item ) {
			$text = trim( (string) ( $item['text'] ?? '' ) );
			if ( '' !== $text ) {
				$out[] = $text;
			}
		}
		return $out;
	}

	/**
	 * Reference warranty content (static-mirror/refund_returns.html).
	 *
	 * @return array{cards:array<int,array<string,string>>,covered:array<int,string>,excluded:array<int,string>,steps:array<int,array<string,string>>}
	 */
	public static function warranty_defaults(): array {
		return array(
			'cards'    => array(
				array( 'icon' => 'shield', 'title' => '۲۴ ماه گارانتی تعویض بی‌قید و شرط', 'description' => 'کلیه ریسه‌های LED، پوینت لایت‌های پیکسلی، والواشرها و بردهای الکترونیکی تولیدی در صورت بروز هرگونه نقص فنی ناشی از فرآیند تولید یا نفوذ رطوبت، بدون دریافت هزینه تعویض می‌گردند.' ),
				array( 'icon' => 'shield', 'title' => '۵ سال خدمات پس از فروش و تأمین قطعات', 'description' => 'از آنجا که صفر تا صد قالب‌سازی، تولید برد، تزریق رزین و ساخت سازه در کارخانه عرفان صنعت انجام می‌شود، تأمین قطعات یدکی هر پروژه تا ۵ سال تضمین شده است.' ),
				array( 'icon' => 'shield', 'title' => 'کنترل کیفیت ۴ مرحله‌ای (QC) پیش از ارسال', 'description' => 'تمامی محصولات پیش از خروج از کارخانه، تحت تست غوطه‌وری در آب، تست بار الکتریکی ۲۴ ساعته (Aging Test) و بازرسی نهایی واحد کنترل کیفیت قرار می‌گیرند.' ),
			),
			'covered'  => array(
				'خاموشی یا افت شدید شدت نور LEDهای اپیستار در طول دوره ۲۴ ماهه گارانتی',
				'نفوذ آب یا رطوبت به داخل حباب ریسه‌ها، پوینت لایت‌ها و والواشرهای دارای گواهی IP67 / IP68',
				'تغییر رنگ یا کدر شدن حباب‌های پلی‌کربنات Anti-UV در اثر تابش مستقیم نور خورشید',
				'اختلال در عملکرد باکس کنترل مرکزی، ماژول‌های NRF و بردهای درایور در شرایط ولتاژ استاندارد',
				'پوسته شدن یا زنگ‌زدگی رنگ الکترواستاتیک کوره‌ای سازه‌های فلزی در شرایط اقلیمی متعارف',
			),
			'excluded' => array(
				'اتصال مستقیم محصولات ۱۲ یا ۲۴ ولت DC (مانند پوینت لایت یا ریسه سوزنی ۲۴ ولت) به برق شهری ۲۲۰ ولت بدون ترانس کاهنده',
				'آسیب‌های فیزیکی شدید ناشی از تصادف خودرو با سازه، وندالیسم شهری، سرقت کابل یا بریدگی سیم‌ها در حین عملیات عمرانی',
				'باز کردن پلمپ تابلو فرمان یا تعمیر غیرمجاز بردها توسط افراد متفرقه خارج از شبکه فنی عرفان صنعت',
				'نوسانات شدید شبکه برق یا صاعقه مستقیم در پروژه‌هایی که کارفرما از نصب سیستم ارت و محافظ سرج خودداری نموده است',
			),
			'steps'    => array(
				array( 'title' => 'ثبت درخواست پشتیبانی', 'description' => 'تماس با واحد خدمات پس از فروش کارخانه (۰۳۱-۹۱۰۹۱۰۱۱) و اعلام شماره فاکتور یا کد پروژه به همراه تصویر یا ویدیوی کوتاه از قطعه.' ),
				array( 'title' => 'بررسی فنی فوری (کمتر از ۲۴ ساعت)', 'description' => 'کارشناسان فنی در کمتر از ۲۴ ساعت کاری مشکل را بررسی کرده و راهنمایی رفع خطا در محل یا مجوز ارسال قطعه جایگزین را صادر می‌کنند.' ),
				array( 'title' => 'تعویض قطعه یا اعزام تکنسین', 'description' => 'برای محصولات فروشگاهی، قطعه نو فوراً ارسال می‌گردد و در پروژه‌های پیمانکاری شهری، تیم فنی جهت سرویس به محل پروژه اعزام می‌شود.' ),
			),
		);
	}
}
