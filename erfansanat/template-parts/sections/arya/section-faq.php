<?php
/**
 * Arya landing FAQ (P07-T028). Accordion driven by the immutable main.js
 * hooks (data-faq-list/toggle/body/icon) — same contract as the FAQ page.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'faq' );

$es_heading = (string) ( $es_fields['heading'] ?? 'سوالات متداول درباره آریا' );
$es_items   = ! empty( $es_fields['items'] ) && is_array( $es_fields['items'] ) ? $es_fields['items'] : array(
	array( 'question' => 'آریا روی چه درب‌هایی نصب می‌شود؟', 'answer' => 'آریا روی انواع درب‌های پارکینگی بازویی، ریلی و کرکره‌ای نصب می‌شود. کارشناسان ما پیش از نصب، محل را بازدید و مناسب‌ترین مدل موتور را پیشنهاد می‌دهند.' ),
	array( 'question' => 'هنگام قطعی برق چه اتفاقی می‌افتد؟', 'answer' => 'با کلید خلاص‌کن دستی، درب بدون نیاز به برق به‌راحتی باز و بسته می‌شود. همچنین امکان اتصال باتری پشتیبان برای تردد خودکار وجود دارد.' ),
	array( 'question' => 'نصب و گارانتی چگونه است؟', 'answer' => 'نصب توسط تیم مجرب عرفان صنعت در سراسر کشور انجام می‌شود و دستگاه دارای ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش کارخانه است.' ),
	array( 'question' => 'امکان کنترل با موبایل وجود دارد؟', 'answer' => 'بله؛ با ماژول هوشمند، درب از طریق اپلیکیشن موبایل از هر نقطه کنترل می‌شود و گزارش تردد برای مدیر ساختمان ارسال می‌گردد.' ),
);
?>
<section id="<?php echo esc_attr( $es_id ); ?>" class="mx-auto max-w-4xl scroll-mt-28 px-5 py-14 lg:px-8 lg:py-18" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="faq">
	<h2 class="mb-8 flex items-center gap-3 text-xl font-extrabold text-slate-900 lg:text-2xl"><span class="h-7 w-1.5 rounded-full bg-gradient-to-b from-emerald-500 to-emerald-700"></span><?php echo esc_html( $es_heading ); ?></h2>
	<div data-faq-list="true">
		<div class="space-y-4">
			<?php foreach ( $es_items as $es_idx => $es_item ) : ?>
				<?php
				$es_question = (string) ( $es_item['question'] ?? '' );
				$es_answer   = (string) ( $es_item['answer'] ?? '' );
				if ( '' === trim( $es_question ) ) { continue; }
				$es_open = ( 0 === $es_idx );
				?>
				<div data-faq-item="true">
					<div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white transition-all hover:border-emerald-300">
						<button type="button" data-faq-toggle="<?php echo esc_attr( (string) $es_idx ); ?>" class="flex w-full items-center justify-between gap-4 px-6 py-5 text-right cursor-pointer" aria-expanded="<?php echo $es_open ? 'true' : 'false'; ?>">
							<h3 class="text-[15px] font-black leading-7 text-slate-900 sm:text-[16px]"><?php echo esc_html( $es_question ); ?></h3>
							<span data-faq-icon="<?php echo esc_attr( (string) $es_idx ); ?>" class="grid h-9 w-9 shrink-0 place-items-center rounded-xl transition-transform duration-300 <?php echo $es_open ? 'rotate-45 bg-emerald-800 text-white' : 'bg-emerald-100 text-emerald-800'; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4.5 w-4.5" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></span>
						</button>
						<div data-faq-body="<?php echo esc_attr( (string) $es_idx ); ?>" class="<?php echo $es_open ? 'block' : 'hidden'; ?> border-t border-slate-100 bg-slate-50/50 px-6 py-5 text-[14px] leading-8 text-slate-600"><?php echo esc_html( $es_answer ); ?></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
