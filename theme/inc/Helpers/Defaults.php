<?php
/**
 * Static-site values used only as fallbacks until an administrator overrides them.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Canonical fallback options collected from the static mirror.
 */
final class Defaults {

	public const BRAND_COLOR = '#047857';

	/**
	 * Return the default option tree.
	 *
	 * @return array<string,mixed>
	 */
	public static function all(): array {
		return array(
			'header' => array(
				'announcement' => array(
					'enabled' => true,
					'text'    => 'فروشگاه آنلاین (ارسال سریع)',
					'url'     => '',
				),
				'phone'        => '031-91091011',
				'sales_phone'  => '09137976915',
				'email'        => 'info@erfansanat.com',
				'logo_id'      => 0,
				'tagline'      => 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)',
			),
			'footer' => array(
				'address'    => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
				'work_hours' => 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر',
				'copyright'  => '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.',
				'show_quick_links' => true,
				'show_categories'  => true,
				'back_to_top'      => true,
			),
			'social' => array(
				'instagram' => 'https://instagram.com/erfan_sanat_esfahan',
				'linkedin'  => 'https://linkedin.com/company/erfansanat',
				'telegram'  => 'https://t.me/erfansanat',
				'links'     => array(
					'item-0' => array( 'name' => 'اینستاگرام', 'icon' => 'instagram', 'url' => 'https://instagram.com/erfan_sanat_esfahan' ),
					'item-1' => array( 'name' => 'لینکدین', 'icon' => 'linkedin', 'url' => 'https://linkedin.com/company/erfansanat' ),
					'item-2' => array( 'name' => 'تلگرام', 'icon' => 'telegram', 'url' => 'https://t.me/erfansanat' ),
				),
			),
			'brand' => array(
				'color'   => self::BRAND_COLOR,
				'heading' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)',
			),
			'scripts' => array(
				'head'   => '',
				'footer' => '',
			),
			'archive' => array(
				'columns'        => '3',
				'posts_per_page' => '12',
				'order'          => 'date_desc',
				'filters'        => array(
					'category' => true,
					'ip'       => true,
					'voltage'  => true,
					'price'    => true,
					'rating'   => true,
					'views'    => true,
				),
				'show_faq' => true,
			),
		);
	}
}
