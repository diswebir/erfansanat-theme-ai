<?php
/**
 * Arya landing benefits (P07-T028). Editable repeater cards using the shared
 * emerald card vocabulary; icons come from the allowed icon set.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'features' );

$es_heading = (string) ( $es_fields['heading'] ?? 'ویژگی‌های آریا' );
$es_items   = ! empty( $es_fields['items'] ) && is_array( $es_fields['items'] ) ? $es_fields['items'] : array(
	array( 'title' => 'کنترل از هر جای دنیا', 'description' => 'باز و بسته کردن درب با اپلیکیشن موبایل، ریموت اختصاصی و دستیار صوتی؛ همراه با گزارش لحظه‌ای وضعیت درب.', 'icon' => 'mobile' ),
	array( 'title' => 'ایمنی هوشمند تردد', 'description' => 'سنسور تشخیص مانع، توقف خودکار و قفل ضدسرقت؛ امنیت کامل خودرو و اعضای خانواده در هر بار تردد.', 'icon' => 'shield' ),
	array( 'title' => 'نصب و راه‌اندازی سریع', 'description' => 'نصب توسط تیم مجرب عرفان صنعت در سراسر کشور، بدون تخریب و تغییر در سازه درب موجود.', 'icon' => 'truck' ),
	array( 'title' => 'گارانتی و پشتیبانی', 'description' => '۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش؛ پشتیبانی فنی مستقیم کارخانه.', 'icon' => 'check' ),
);

$es_icons = array(
	'check'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5.5 w-5.5" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
	'star'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-5.5 w-5.5" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.12 2.12 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.12 2.12 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.12 2.12 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.12 2.12 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.12 2.12 0 0 0 1.597-1.16z"></path></svg>',
	'shield' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-5.5 w-5.5" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>',
	'truck'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-5.5 w-5.5" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg>',
	'mobile' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-smartphone h-5.5 w-5.5" aria-hidden="true"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>',
	'phone'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-5.5 w-5.5" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>',
	'clock'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-5.5 w-5.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>',
);
?>
<section id="<?php echo esc_attr( $es_id ); ?>" class="mx-auto max-w-7xl scroll-mt-28 px-5 py-14 lg:px-8 lg:py-18" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="features">
	<h2 class="mb-10 flex items-center gap-3 text-xl font-extrabold text-slate-900 lg:text-2xl"><span class="h-7 w-1.5 rounded-full bg-gradient-to-b from-emerald-500 to-emerald-700"></span><?php echo esc_html( $es_heading ); ?></h2>
	<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
		<?php foreach ( $es_items as $es_item ) : ?>
			<?php
			$es_title = (string) ( $es_item['title'] ?? '' );
			$es_desc  = (string) ( $es_item['description'] ?? '' );
			$es_icon  = (string) ( $es_item['icon'] ?? 'check' );
			if ( '' === trim( $es_title ) ) { continue; }
			?>
			<div class="card-light rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
				<span class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-emerald-700"><?php echo $es_icons[ $es_icon ] ?? $es_icons['check']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG map. ?></span>
				<h3 class="text-[15.5px] font-extrabold text-slate-900"><?php echo esc_html( $es_title ); ?></h3>
				<p class="mt-2 text-[13px] leading-7 text-slate-600"><?php echo esc_html( $es_desc ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
