<?php
/**
 * Theme option schema, sanitisation and read cache.
 *
 * Every theme setting lives in the single `erfansanat_theme_options` option. The
 * schema below is the only source of truth for what may be stored, so unknown
 * keys, wrong types and out-of-range values are dropped instead of saved.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Typed definition of the stored theme options.
 */
final class Settings {

	/**
	 * Single option name for every theme setting.
	 */
	public const OPTION = 'erfansanat_theme_options';

	/**
	 * Read-through cache of the stored option.
	 *
	 * @var array<string, mixed>|null
	 */
	private static ?array $cache = null;

	/**
	 * Human readable tab labels for the settings screen.
	 *
	 * @return array<string, string>
	 */
	public static function tabs(): array {
		return array(
			'brand'    => __( 'برند و لوگو', 'erfansanat' ),
			'header'   => __( 'هدر و نوار اعلان', 'erfansanat' ),
			'contact'  => __( 'اطلاعات تماس', 'erfansanat' ),
			'footer'   => __( 'فوتر', 'erfansanat' ),
			'archive'  => __( 'آرشیوها و فهرست‌ها', 'erfansanat' ),
			'advanced' => __( 'اسکریپت و پیشرفته', 'erfansanat' ),
		);
	}

	/**
	 * Schema of every stored field, grouped by tab.
	 *
	 * @return array<string, array<string, array<string, mixed>>>
	 */
	public static function schema(): array {
		return array(
			'brand'    => array(
				'brand_mark'    => array(
					'type'    => 'text',
					'label'   => __( 'نشان متنی (ESE)', 'erfansanat' ),
					'default' => 'ESE',
					'max'     => 8,
				),
				'brand_name'    => array(
					'type'    => 'text',
					'label'   => __( 'نام برند در هدر', 'erfansanat' ),
					'default' => 'عرفان صنعت اصفهان',
				),
				'brand_tagline' => array(
					'type'    => 'text',
					'label'   => __( 'شعار کوتاه برند', 'erfansanat' ),
					'default' => 'نورپردازی شهری و المان‌های نوری',
				),
				'logo_id'       => array(
					'type'        => 'media',
					'label'       => __( 'لوگو (تصویر)', 'erfansanat' ),
					'default'     => 0,
					'description' => __( 'اگر خالی بماند نشان متنی استفاده می‌شود. SVG مجاز است.', 'erfansanat' ),
				),
			),
			'header'   => array(
				'notice_enabled'  => array(
					'type'    => 'bool',
					'label'   => __( 'نمایش نوار اعلان بالای هدر', 'erfansanat' ),
					'default' => false,
				),
				'notice_text'     => array(
					'type'        => 'text',
					'label'       => __( 'متن نوار اعلان', 'erfansanat' ),
					'default'     => '',
					'description' => __( 'اگر «نمایش نوار» روشن باشد و این متن خالی نباشد، نوار بالای سایت چاپ می‌شود.', 'erfansanat' ),
				),
				'notice_link'     => array(
					'type'    => 'url',
					'label'   => __( 'لینک نوار اعلان', 'erfansanat' ),
					'default' => '',
				),
				'topbar_title'    => array(
					'type'    => 'text',
					'label'   => __( 'عنوان نوار بالای هدر', 'erfansanat' ),
					'default' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)',
				),
				'topbar_subtitle' => array(
					'type'    => 'text',
					'label'   => __( 'زیرنویس نوار بالای هدر', 'erfansanat' ),
					'default' => 'گواهینامه ISO 9001:2015 و کدهای رسمی سامانه ستاد و GS1',
				),
				'topbar_links'    => array(
					'type'    => 'links',
					'label'   => __( 'لینک‌های نوار بالا', 'erfansanat' ),
					'default' => array(
						array(
							'title' => 'مجوزها و افتخارات',
							'url'   => '/licenses-and-honors/',
						),
						array(
							'title' => 'گارانتی ۲۴ ماهه',
							'url'   => '/refund_returns/',
						),
						array(
							'title' => 'استخدام',
							'url'   => '/recruitment/',
						),
					),
				),
			),
			'contact'  => array(
				'phone_primary'       => array(
					'type'    => 'phone',
					'label'   => __( 'تلفن اصلی', 'erfansanat' ),
					'default' => '031-91091011',
				),
				'phone_primary_label' => array(
					'type'    => 'text',
					'label'   => __( 'متن نمایشی تلفن اصلی', 'erfansanat' ),
					'default' => '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)',
				),
				'phone_sales'         => array(
					'type'    => 'phone',
					'label'   => __( 'تلفن مدیر فروش', 'erfansanat' ),
					'default' => '09137976915',
				),
				'phone_sales_label'   => array(
					'type'    => 'text',
					'label'   => __( 'متن نمایشی تلفن فروش', 'erfansanat' ),
					'default' => 'مدیر فروش (آقای محمدعلی قربانی): ۰۹۱۳۷۹۷۶۹۱۵',
				),
				'email'               => array(
					'type'    => 'email',
					'label'   => __( 'ایمیل', 'erfansanat' ),
					'default' => 'info@erfansanat.com',
				),
				'address'             => array(
					'type'    => 'textarea',
					'label'   => __( 'نشانی', 'erfansanat' ),
					'default' => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
				),
				'hours'               => array(
					'type'    => 'text',
					'label'   => __( 'ساعات کاری', 'erfansanat' ),
					'default' => 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر',
				),
				'social'              => array(
					'type'        => 'links',
					'label'       => __( 'شبکه‌های اجتماعی', 'erfansanat' ),
					'default'     => array(),
					'description' => __( 'مرجع استاتیک هیچ شبکه اجتماعی فعالی ندارد؛ فقط نشانی‌های تأییدشده را اضافه کنید.', 'erfansanat' ),
				),
			),
			'footer'   => array(
				'footer_about'       => array(
					'type'    => 'textarea',
					'label'   => __( 'معرفی کوتاه در فوتر', 'erfansanat' ),
					'default' => 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته، المان‌های نوری تزئینی، درختان نوری هوشمند، خورشیدنماها، ریسه‌های LED (بلوطی، فندقی، سوزنی IP68) و پوینت لایت پیکسل با ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش.',
				),
				'footer_badges'      => array(
					'type'    => 'list',
					'label'   => __( 'نشان‌های اعتماد', 'erfansanat' ),
					'default' => array( 'ISO 9001:2015', 'IP67 / IP68', 'کد بین‌المللی GS1' ),
				),
				'footer_quick_links' => array(
					'type'    => 'links',
					'label'   => __( 'دسترسی سریع', 'erfansanat' ),
					'default' => array(
						array(
							'title' => 'فروشگاه محصولات',
							'url'   => '/shop/',
						),
						array(
							'title' => 'پروژه‌های اجراشده',
							'url'   => '/projects/',
						),
						array(
							'title' => 'خدمات نورپردازی',
							'url'   => '/services/',
						),
						array(
							'title' => 'مجوزها و افتخارات',
							'url'   => '/licenses-and-honors/',
						),
						array(
							'title' => 'سوالات متداول',
							'url'   => '/faq/',
						),
						array(
							'title' => 'شرایط گارانتی محصولات',
							'url'   => '/refund_returns/',
						),
						array(
							'title' => 'فرصت‌های شغلی و استخدام',
							'url'   => '/recruitment/',
						),
						array(
							'title' => 'اخبار و مقالات',
							'url'   => '/blog/',
						),
					),
				),
				'footer_categories'  => array(
					'type'        => 'terms',
					'label'       => __( 'دسته‌بندی محصولات در فوتر', 'erfansanat' ),
					'default'     => array(),
					'taxonomy'    => 'product_cat',
					'description' => __( 'خالی بماند تا از دسته‌های معرفی‌شدهٔ مرجع استفاده شود.', 'erfansanat' ),
				),
				'footer_copyright'   => array(
					'type'    => 'text',
					'label'   => __( 'متن کپی‌رایت', 'erfansanat' ),
					'default' => 'تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.',
				),
			),
			'archive'  => array(
				'products_per_page' => array(
					'type'    => 'int',
					'label'   => __( 'تعداد محصول در هر صفحه', 'erfansanat' ),
					'default' => 12,
					'min'     => 1,
					'max'     => 48,
				),
				'projects_per_page' => array(
					'type'    => 'int',
					'label'   => __( 'تعداد پروژه در هر صفحه', 'erfansanat' ),
					'default' => 9,
					'min'     => 1,
					'max'     => 48,
				),
				'posts_per_page'    => array(
					'type'    => 'int',
					'label'   => __( 'تعداد نوشته در هر صفحه', 'erfansanat' ),
					'default' => 9,
					'min'     => 1,
					'max'     => 48,
				),
				'archive_sidebar'   => array(
					'type'    => 'bool',
					'label'   => __( 'نمایش ستون کنار در آرشیوها', 'erfansanat' ),
					'default' => false,
				),
			),
			'advanced' => array(
				'header_scripts' => array(
					'type'        => 'code',
					'label'       => __( 'اسکریپت سفارشی هدر', 'erfansanat' ),
					'default'     => '',
					'capability'  => 'unfiltered_html',
					'description' => __( 'فقط برای مدیر کل با مجوز unfiltered_html؛ روی سایت چندنویسنده برای ادمین نمایش داده نمی‌شود.', 'erfansanat' ),
				),
				'footer_scripts' => array(
					'type'       => 'code',
					'label'      => __( 'اسکریپت سفارشی فوتر', 'erfansanat' ),
					'default'    => '',
					'capability' => 'unfiltered_html',
				),
			),
		);
	}

	/**
	 * Flat map of field name to definition.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function fields(): array {
		$fields = array();
		foreach ( self::schema() as $tab => $group ) {
			foreach ( $group as $name => $definition ) {
				$definition['tab'] = $tab;
				$fields[ $name ]   = $definition;
			}
		}
		return $fields;
	}

	/**
	 * Default value for every field.
	 *
	 * @return array<string, mixed>
	 */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $name => $definition ) {
			$defaults[ $name ] = $definition['default'];
		}
		return $defaults;
	}

	/**
	 * Stored options merged over the defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function all(): array {
		if ( null !== self::$cache ) {
			return self::$cache;
		}
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$values = self::defaults();
		foreach ( $stored as $key => $value ) {
			$definition = self::fields()[ $key ] ?? null;
			if ( null === $definition ) {
				continue;
			}
			if ( ! self::is_valid_stored_value( $definition, $value ) ) {
				continue;
			}
			$values[ $key ] = $value;
		}
		self::$cache = $values;
		return self::$cache;
	}

	/**
	 * Whether a value read from the database matches its declared type.
	 *
	 * A corrupted or hand-edited row must fall back to the default instead of
	 * reaching a template as an array where a string is expected.
	 *
	 * @param array<string, mixed> $definition Field definition.
	 * @param mixed                $value      Stored value.
	 * @return bool
	 */
	private static function is_valid_stored_value( array $definition, $value ): bool {
		switch ( $definition['type'] ) {
			case 'bool':
				return is_bool( $value ) || in_array( $value, array( 0, 1, '0', '1' ), true );
			case 'int':
				return is_numeric( $value );
			case 'media':
				return is_numeric( $value ) && (int) $value >= 0;
			case 'links':
				if ( ! is_array( $value ) ) {
					return false;
				}
				foreach ( $value as $row ) {
					if ( ! is_array( $row ) || ! isset( $row['title'], $row['url'] ) || ! is_scalar( $row['title'] ) || ! is_scalar( $row['url'] ) ) {
						return false;
					}
				}
				return true;
			case 'list':
			case 'terms':
				if ( ! is_array( $value ) ) {
					return false;
				}
				foreach ( $value as $item ) {
					if ( ! is_scalar( $item ) ) {
						return false;
					}
				}
				return true;
			case 'text':
			case 'textarea':
			case 'code':
			case 'email':
			case 'url':
			case 'phone':
			default:
				return is_string( $value );
		}
	}

	/**
	 * One setting, falling back to the schema default.
	 *
	 * @param string $key         Field name.
	 * @param mixed  $fallback    Optional override for missing keys.
	 * @return mixed
	 */
	public static function get( string $key, $fallback = null ) {
		$all = self::all();
		if ( array_key_exists( $key, $all ) ) {
			return $all[ $key ];
		}
		return $fallback;
	}

	/**
	 * Forget the read cache, for example after an update from another process.
	 *
	 * @return void
	 */
	public static function flush(): void {
		self::$cache = null;
	}

	/**
	 * Sanitise a submitted settings payload.
	 *
	 * Unknown keys are dropped, values are cast to the declared type and text is
	 * filtered through the WordPress KSES layer appropriate to the request.
	 *
	 * @param array<string, mixed> $input  Raw request payload.
	 * @param bool                 $partial Whether missing keys keep their stored value.
	 * @return array{values: array<string, mixed>, errors: array<string, string>}
	 */
	public static function sanitize( array $input, bool $partial = false ): array {
		$current = self::all();
		// A full save starts from the defaults so the returned set is always
		// complete; a partial save keeps whatever is already stored.
		$values = $partial ? $current : self::defaults();
		$errors = array();

		foreach ( self::fields() as $name => $definition ) {
			if ( ! array_key_exists( $name, $input ) ) {
				continue;
			}
			$raw = $input[ $name ];
			try {
				$values[ $name ] = self::sanitize_value( $name, $definition, $raw );
			} catch ( \InvalidArgumentException $exception ) {
				$errors[ $name ] = $exception->getMessage();
				$values[ $name ] = $current[ $name ] ?? $definition['default'];
			}
		}

		return array(
			'values' => array_intersect_key( $values, self::defaults() ),
			'errors' => $errors,
		);
	}

	/**
	 * Sanitise one value according to its definition.
	 *
	 * @param string               $name       Field name.
	 * @param array<string, mixed> $definition Field definition.
	 * @param mixed                $raw        Submitted value.
	 * @return mixed
	 * @throws \InvalidArgumentException When a value is structurally unacceptable.
	 */
	private static function sanitize_value( string $name, array $definition, $raw ) {
		unset( $name );
		switch ( $definition['type'] ) {
			case 'bool':
				return (bool) $raw;

			case 'int':
				$value = is_numeric( $raw ) ? (int) $raw : (int) $definition['default'];
				$min   = (int) ( $definition['min'] ?? 0 );
				$max   = (int) ( $definition['max'] ?? PHP_INT_MAX );
				return max( $min, min( $max, $value ) );

			case 'media':
				$id = absint( $raw );
				if ( $id > 0 && 'attachment' !== get_post_type( $id ) ) {
					throw new \InvalidArgumentException( esc_html__( 'شناسهٔ رسانه معتبر نیست.', 'erfansanat' ) );
				}
				return $id;

			case 'email':
				return sanitize_email( is_string( $raw ) ? $raw : '' );

			case 'url':
				return esc_url_raw( is_string( $raw ) ? trim( $raw ) : '', array( 'http', 'https', 'tel', 'mailto' ) );

			case 'phone':
				$value = is_string( $raw ) ? $raw : '';
				$value = preg_replace( '/[^0-9+\-()\s]/u', '', $value );
				return substr( (string) $value, 0, 32 );

			case 'textarea':
				return self::cap_text( is_string( $raw ) ? $raw : '', $definition );

			case 'code':
				return self::cap_code( is_string( $raw ) ? $raw : '', $definition );

			case 'links':
				return self::sanitize_links( $raw );

			case 'list':
				return self::sanitize_list( $raw );

			case 'terms':
				return self::sanitize_terms( $raw, $definition );

			case 'text':
			default:
				$value = self::cap_text( is_string( $raw ) ? $raw : '', $definition );
				if ( ! empty( $definition['max'] ) ) {
					$value = mb_substr( $value, 0, (int) $definition['max'] );
				}
				return $value;
		}
	}

	/**
	 * Plain text for a user that may or may not have unfiltered_html.
	 *
	 * @param string               $value      Raw text.
	 * @param array<string, mixed> $definition Field definition.
	 * @return string
	 */
	private static function cap_text( string $value, array $definition ): string {
		unset( $definition );
		$value = wp_check_invalid_utf8( $value );
		return sanitize_textarea_field( $value );
	}

	/**
	 * Script field, limited to an administrator with unfiltered_html.
	 *
	 * Administrators on a normal single-site install have unfiltered_html, while
	 * administrators on multisite do not; the capability is checked per request
	 * instead of assuming the role.
	 *
	 * @param string               $value      Raw code.
	 * @param array<string, mixed> $definition Field definition.
	 * @return string
	 */
	private static function cap_code( string $value, array $definition ): string {
		$capability = (string) ( $definition['capability'] ?? 'unfiltered_html' );
		if ( ! current_user_can( $capability ) ) {
			return '';
		}
		return trim( $value );
	}

	/**
	 * Repeater of title/url rows.
	 *
	 * @param mixed $raw Submitted value.
	 * @return array<int, array{title: string, url: string}>
	 */
	private static function sanitize_links( $raw ): array {
		if ( ! is_array( $raw ) ) {
			return array();
		}
		$rows = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = sanitize_text_field( (string) ( $row['title'] ?? '' ) );
			$url   = esc_url_raw( trim( (string) ( $row['url'] ?? '' ) ), array( 'http', 'https', 'tel', 'mailto' ) );
			if ( '' === $title || '' === $url ) {
				continue;
			}
			$rows[] = array(
				'title' => $title,
				'url'   => $url,
			);
			if ( count( $rows ) >= 30 ) {
				break;
			}
		}
		return $rows;
	}

	/**
	 * Plain list of short texts.
	 *
	 * @param mixed $raw Submitted value.
	 * @return array<int, string>
	 */
	private static function sanitize_list( $raw ): array {
		if ( is_string( $raw ) ) {
			$lines = preg_split( '/\r\n|\r|\n/', $raw );
			$raw   = is_array( $lines ) ? $lines : array();
		}
		if ( ! is_array( $raw ) ) {
			return array();
		}
		$items = array();
		foreach ( $raw as $item ) {
			if ( ! is_scalar( $item ) ) {
				continue;
			}
			$item = sanitize_text_field( (string) $item );
			if ( '' === $item ) {
				continue;
			}
			$items[] = $item;
			if ( count( $items ) >= 30 ) {
				break;
			}
		}
		return $items;
	}

	/**
	 * Term ids limited to the declared taxonomy.
	 *
	 * @param mixed                $raw        Submitted value.
	 * @param array<string, mixed> $definition Field definition.
	 * @return array<int, int>
	 */
	private static function sanitize_terms( $raw, array $definition ): array {
		$taxonomy = (string) ( $definition['taxonomy'] ?? '' );
		$ids      = array();
		foreach ( (array) $raw as $id ) {
			$id = absint( $id );
			if ( $id > 0 && '' !== $taxonomy && term_exists( $id, $taxonomy ) ) {
				$ids[] = $id;
			}
		}
		return array_values( array_unique( $ids ) );
	}
}
