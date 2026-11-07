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
}
