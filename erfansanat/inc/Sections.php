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
	 * @return array<string, array{label: string, anchor: string, part: string, fields: array<string, array<string, mixed>>}>
	 */
	public static function registry(): array {
		return array(
			'hero'          => array(
				'label'  => __( 'هیرو صفحه اصلی', 'erfansanat' ),
				'anchor' => 'home',
				'part'   => 'template-parts/home/section-hero.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر اصلی', 'erfansanat' ), 'وقتی کیفیت و ماندگاری مهمه' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'عرفان صنعت انتخاب توئه...' ),
					'media_id' => self::media_field( __( 'تصویر یا پوستر ویدیو', 'erfansanat' ) ),
					'cta'      => self::link_field( __( 'دکمهٔ فراخوان', 'erfansanat' ), 'مشاهده محصولات', '/shop/' ),
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
				'label'  => __( 'دسته‌های پروژه', 'erfansanat' ),
				'anchor' => 'projects',
				'part'   => 'template-parts/home/section-project-types.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'پروژه‌های نورپردازی شهری' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'اجرای تخصصی پروژه‌های نورپردازی برای شهرداری‌ها و سازمان‌های زیباسازی.' ),
					'items'    => self::repeater_field(
						__( 'دسته‌ها', 'erfansanat' ),
						array(
							'title' => self::text_field( __( 'عنوان', 'erfansanat' ), '' ),
							'url'   => self::url_field( __( 'لینک', 'erfansanat' ) ),
							'icon'  => self::icon_field( __( 'آیکون', 'erfansanat' ) ),
						),
						array(
							array(
								'title' => 'کمانی',
								'url'   => '/projects/',
								'icon'  => 'lamp',
							),
							array(
								'title' => 'نیم‌آرک',
								'url'   => '/projects/',
								'icon'  => 'lamp',
							),
							array(
								'title' => '۹۰ درجه',
								'url'   => '/projects/',
								'icon'  => 'lamp',
							),
							array(
								'title' => 'مربعی',
								'url'   => '/projects/',
								'icon'  => 'lamp',
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
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'نمونه‌کارهای اجراشده در کلان‌شهرهای کشور' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'آرشیو تفکیکی پروژه‌های اجرایی' ),
					'link'     => self::link_field( __( 'لینک آرشیو', 'erfansanat' ), 'همه پروژه‌ها', '/projects/' ),
				),
			),
			'about'         => array(
				'label'  => __( 'دربارهٔ شرکت', 'erfansanat' ),
				'anchor' => 'about',
				'part'   => 'template-parts/home/section-about-intro.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'بیش از ۲۵ سال تجربه مهندسی در تولید سازه‌های نوری شهری' ),
					'subtitle' => self::text_field( __( 'متن کوتاه', 'erfansanat' ), 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE) با تمرکز بر طراحی و تولید صنعتی تجهیزات روشنایی و المان‌های نوری شهری.' ),
					'media_id' => self::media_field( __( 'تصویر بخش', 'erfansanat' ) ),
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
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'مرجع بومی‌سازی سازه‌های نوری و تجهیزات روشنایی شهری در ایران' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'اصالت و هویت صنعتی کارخانه' ),
					'link'     => self::link_field( __( 'لینک', 'erfansanat' ), 'دربارهٔ ما', '/about/' ),
				),
			),
			'history'       => array(
				'label'  => __( 'تاریخچه', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-history.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'تاریخچه و دستاوردهای عرفان صنعت اصفهان (۱۳۸۰ تا ۱۴۰۵)' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'مسیر رشد و تکامل صنعتی' ),
					'media_id' => self::media_field( __( 'تصویر تاریخچه', 'erfansanat' ) ),
				),
			),
			'products'      => array(
				'label'  => __( 'منتخب محصولات', 'erfansanat' ),
				'anchor' => 'products',
				'part'   => 'template-parts/home/section-products.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'منتخب محصولات عرفان صنعت اصفهان' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'تولید صنعتی تجهیزات نورپردازی شهری با عایق‌بندی تزریق مستقیم.' ),
					'link'     => self::link_field( __( 'لینک فروشگاه', 'erfansanat' ), 'مشاهدهٔ همه محصولات', '/shop/' ),
				),
			),
			'catalog'       => array(
				'label'  => __( 'کاتالوگ', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-catalog.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'کاتالوگ جامع محصولات عرفان صنعت اصفهان (ویرایش ۱۴۰۴)' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'مشخصات فنی کامل، ابعاد سازه، توان مصرفی و درجه حفاظت IP68.' ),
					'media_id' => self::media_field( __( 'جلد کاتالوگ', 'erfansanat' ) ),
					'link'     => self::link_field( __( 'دریافت کاتالوگ', 'erfansanat' ), 'دریافت کاتالوگ', '/contact/' ),
				),
			),
			'services'      => array(
				'label'  => __( 'خدمات', 'erfansanat' ),
				'anchor' => 'services',
				'part'   => 'template-parts/home/section-services.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'خدمات تخصصی نورپردازی شهری' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'از طراحی سه‌بعدی تا تولید صنعتی و اجرای کامل در محل.' ),
					'items'    => self::repeater_field(
						__( 'فهرست خدمات', 'erfansanat' ),
						array(
							'title' => self::text_field( __( 'عنوان', 'erfansanat' ), '' ),
							'icon'  => self::icon_field( __( 'آیکون', 'erfansanat' ) ),
						),
						array(
							array(
								'title' => 'نورپردازی میادین شهری',
								'icon'  => 'city',
							),
							array(
								'title' => 'طراحی و مهندسی سازه',
								'icon'  => 'factory',
							),
							array(
								'title' => 'اجرا و نصب در محل',
								'icon'  => 'truck',
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
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'استانداردهای کیفیت و ماندگاری' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'مزیت‌های رقابتی کارخانه' ),
					'icon'     => self::icon_field( __( 'آیکون اصلی', 'erfansanat' ), 'badge' ),
				),
			),
			'support'       => array(
				'label'  => __( 'پشتیبانی و گارانتی', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-support.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'پشتیبانی فنی و گارانتی تعویض در سراسر ایران' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'پوشش سراسری ۳۱ استان کشور با مرکزیت کارخانه اصفهان' ),
					'icon'     => self::icon_field( __( 'آیکون اصلی', 'erfansanat' ), 'support' ),
				),
			),
			'clients'       => array(
				'label'  => __( 'مشتریان', 'erfansanat' ),
				'anchor' => '',
				'part'   => 'template-parts/home/section-clients.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'مشتریان و سازمان‌های همکار عرفان صنعت' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'افتخار همراهی با بیش از ۱۸۰ شهرداری و سازمان زیباسازی.' ),
					'items'    => self::repeater_field(
						__( 'فهرست مشتریان', 'erfansanat' ),
						array( 'title' => self::text_field( __( 'نام', 'erfansanat' ), '' ) ),
						array(
							array( 'title' => 'شهرداری اصفهان' ),
							array( 'title' => 'سازمان زیباسازی' ),
						)
					),
				),
			),
			'blog'          => array(
				'label'  => __( 'آخرین مقالات', 'erfansanat' ),
				'anchor' => 'blog-preview',
				'part'   => 'template-parts/home/section-blog.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'جدیدترین مقالات و راهنماهای تخصصی نورپردازی' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'بررسی فنی تجهیزات LED و استانداردهای حفاظتی IP68.' ),
					'link'     => self::link_field( __( 'لینک آرشیو', 'erfansanat' ), 'مشاهده آرشیو مقالات', '/blog/' ),
				),
			),
			'contact-cta'   => array(
				'label'  => __( 'فراخوان تماس', 'erfansanat' ),
				'anchor' => 'contact',
				'part'   => 'template-parts/home/section-contact-cta.php',
				'fields' => array(
					'title'    => self::text_field( __( 'تیتر', 'erfansanat' ), 'روشنایی و هویت بصری متمایز را به شهر خود دعوت کنید' ),
					'subtitle' => self::text_field( __( 'زیرتیتر', 'erfansanat' ), 'مشاوره مهندسی و استعلام قیمت مستقیم از کارخانه' ),
					'link'     => self::link_field( __( 'دکمهٔ تماس', 'erfansanat' ), 'درخواست مشاوره', '/contact/' ),
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
			$anchor  = '' !== (string) $section['anchor'] ? ' id="' . esc_attr( (string) $section['anchor'] ) . '"' : '';
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
