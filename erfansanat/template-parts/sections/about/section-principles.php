<?php
/**
 * About fundamental principles (P07-T020). DOM parity with about.html
 * section 4 (principle cards + national presence banner).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'principles' );

$es_heading = (string) ( $es_fields['heading'] ?? 'اصول بنیادین عرفان صنعت' );
$es_items   = ! empty( $es_fields['items'] ) && is_array( $es_fields['items'] ) ? $es_fields['items'] : array(
	array( 'title' => 'تعهد به کیفیت', 'description' => 'رعایت سخت‌گیرانه استانداردهای فنی در تمام مراحل تولید' ),
	array( 'title' => 'نوآوری در طراحی', 'description' => 'خلق المان‌های نوری منحصربه‌فرد با فناوری‌های نوین' ),
	array( 'title' => 'استانداردهای زیست‌محیطی', 'description' => 'چیپ‌های LED پربازده و مصرف انرژی بهینه' ),
	array( 'title' => 'مشتری‌مداری', 'description' => 'از مشاوره رایگان تا ۵ سال خدمات پس از فروش' ),
);
$es_projects_url = get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' );
?>
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-20" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="principles">
	<div><h2 class="mb-12 text-center text-2xl font-extrabold text-slate-900 lg:text-3xl"><?php echo esc_html( $es_heading ); ?></h2></div>
	<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
		<?php foreach ( $es_items as $es_item ) : ?>
			<div><div class="card-light flex h-full flex-col gap-4 rounded-3xl p-7"><span class="grid h-12 w-12 place-items-center rounded-2xl border border-emerald-200 bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-6 w-6 text-emerald-700" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg></span><h3 class="text-lg font-extrabold text-slate-900"><?php echo esc_html( (string) ( $es_item['title'] ?? '' ) ); ?></h3><p class="text-[13.5px] leading-7 text-slate-600"><?php echo esc_html( (string) ( $es_item['description'] ?? '' ) ); ?></p></div></div>
		<?php endforeach; ?>
	</div>
	<div>
		<div class="mt-16 grid items-center gap-8 rounded-[2rem] border border-emerald-100 bg-emerald-50/60 p-8 lg:grid-cols-[auto_1fr_auto] lg:p-12">
			<span class="mx-auto grid h-20 w-20 place-items-center rounded-3xl bg-white shadow-md"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-earth h-10 w-10 text-emerald-600" aria-hidden="true"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"></path><path d="M7 3.34V5a3 3 0 0 0 3 3a2 2 0 0 1 2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h3.17"></path><path d="M11 21.95V18a2 2 0 0 0-2-2a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2H2.05"></path><circle cx="12" cy="12" r="10"></circle></svg></span>
			<div><h3 class="text-xl font-extrabold text-slate-900"><?php esc_html_e( 'درخشش در جای‌جای ایران و فراتر از مرزها', 'erfansanat' ); ?></h3><p class="mt-3 max-w-3xl text-[14.5px] leading-8 text-slate-600"><?php esc_html_e( 'درخشش محصولات عرفان صنعت در سراسر ایران، گواهی بر تعهد بی‌چون و چرای ما به کیفیت، نوآوری و زیبایی است. از کلان‌شهرهای پویا تا شهرهای کوچک و صمیمی، نور عرفان صنعت نمادی از پیشرفت، توسعه پایدار و تعهد به آینده‌ای روشن‌تر است. اعتماد شما بزرگ‌ترین سرمایه ماست و تمامی محصولات ما با ۲۴ ماه گارانتی و ۵ سال خدمات پس از فروش ارائه می‌شوند.', 'erfansanat' ); ?></p></div>
			<a href="<?php echo esc_url( $es_projects_url ); ?>" class="group flex shrink-0 items-center gap-2.5 self-center rounded-full bg-emerald-600 px-7 py-3.5 text-[14px] font-extrabold text-white transition-all hover:bg-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round h-4.5 w-4.5" aria-hidden="true"><path d="M18 21a8 8 0 0 0-16 0"></path><circle cx="10" cy="8" r="5"></circle><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"></path></svg><?php esc_html_e( 'مشاهده پروژه‌ها', 'erfansanat' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
		</div>
	</div>
</section>
