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
			),
			'footer' => array(
				'address'    => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
				'work_hours' => 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر',
				'copyright'  => '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.',
			),
			'social' => array(
				'instagram' => 'https://instagram.com/erfan_sanat_esfahan',
				'linkedin'  => 'https://linkedin.com/company/erfansanat',
				'telegram'  => 'https://t.me/erfansanat',
			),
			'brand' => array(
				'color'   => '',
				'heading' => 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)',
			),
		);
	}
}
