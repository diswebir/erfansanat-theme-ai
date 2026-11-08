<?php
/**
 * Home section S07: featured products from WooCommerce plus the custom-build card.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;
use ErfanSanat\Theme\Queries;

$erfansanat_fields   = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_link     = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
$erfansanat_products = Queries::products( 3 );
$erfansanat_custom   = ! empty( $erfansanat_link['url'] ) ? (string) $erfansanat_link['url'] : home_url( '/contact/' );

if ( array() === $erfansanat_products ) {
	return;
}
?>
<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
	<div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
		<?php
		SectionParts::heading(
			array(
				'eyebrow'  => (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ),
				'title'    => (string) SectionParts::field( $erfansanat_fields, 'title', '' ),
				'subtitle' => (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ),
			)
		);
		?>
		<div class="shrink-0">
			<div class="inline-flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-2xs">
				<span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M12 20h.01"></path><path d="M2 8.82a15 15 0 0 1 20 0"></path><path d="M5 12.859a10 10 0 0 1 14 0"></path><path d="M8.5 16.429a5 5 0 0 1 7 0"></path></svg>
				</span>
				<div class="leading-tight">
					<div class="text-xs font-extrabold text-slate-900"><?php esc_html_e( 'کنترل هوشمند یکپارچه', 'erfansanat' ); ?></div>
					<div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php esc_html_e( 'پشتیبانی از پروتکل بی‌سیم NRF و WiFi', 'erfansanat' ); ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="mt-8 sm:mt-10 grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
		<?php foreach ( $erfansanat_products as $erfansanat_index => $erfansanat_product ) : ?>
			<div class="<?php echo 0 === (int) $erfansanat_index ? 'sm:col-span-2' : ''; ?>">
				<?php
				$erfansanat_size = 0 === (int) $erfansanat_index ? 'feature' : 'card';
				include get_template_directory() . '/template-parts/cards/card-product.php';
				?>
			</div>
		<?php endforeach; ?>

		<div class="card-light flex h-full min-h-[260px] flex-col justify-between rounded-3xl p-6 sm:min-h-[295px]">
			<div class="flex items-center justify-between">
				<span class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-50 text-emerald-700">
					<?php SectionParts::icon( 'factory', 'h-5 w-5' ); ?>
				</span>
				<span class="text-xs font-extrabold text-emerald-700"><?php esc_html_e( 'ساخت سفارشی', 'erfansanat' ); ?></span>
			</div>
			<div class="my-4">
				<h3 class="text-lg font-black leading-snug text-slate-900 sm:text-xl">
					<?php esc_html_e( 'طراحی و ساخت المان', 'erfansanat' ); ?>
					<span class="block text-emerald-700"><?php esc_html_e( 'مطابق طرح اختصاصی شما', 'erfansanat' ); ?></span>
				</h3>
				<p class="mt-2 text-xs leading-6 font-medium text-slate-600"><?php esc_html_e( 'واحد مهندسی و قالب‌سازی کارخانه آماده طراحی سه‌بعدی و ساخت سازه‌های نوری سفارشی برای میادین و بوستان‌هاست.', 'erfansanat' ); ?></p>
			</div>
			<a href="<?php echo esc_url( $erfansanat_custom ); ?>" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-xs font-black text-white transition-colors hover:bg-emerald-800">
				<span><?php esc_html_e( 'ثبت سفارش اختصاصی', 'erfansanat' ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		</div>
	</div>

	<?php if ( ! empty( $erfansanat_link['url'] ) ) : ?>
		<div class="mt-6 flex justify-center">
			<a href="<?php echo esc_url( (string) $erfansanat_link['url'] ); ?>" class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800">
				<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? '' ) ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
