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
}
