<?php
/**
 * Service details section (P07-T027). DOM parity with
 * static-mirror/services/square-lighting.html #service-details.
 *
 * Deliverables/note/steps come from ServiceMetabox meta (editable); steps fall
 * back to the reference execution path. Related projects reuse the standard
 * project card. Expects $args['service_id'].
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\ServiceMetabox;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_service_id = isset( $args['service_id'] ) ? (int) $args['service_id'] : (int) get_the_ID();
if ( ! $es_service_id || 'services' !== get_post_type( $es_service_id ) ) {
	return;
}

$es_bullets_raw = (string) get_post_meta( $es_service_id, '_es_service_bullets', true );
$es_bullets     = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $es_bullets_raw ) ), static function ( $l ) { return '' !== $l; } ) );
$es_note        = (string) get_post_meta( $es_service_id, '_es_service_note', true );
if ( '' === trim( $es_note ) ) {
	$es_note = 'تمامی اقلام بالا با متریال درجه‌یک، استاندارد IP67/IP68 و ۲۴ ماه گارانتی تعویض به‌همراه ۵ سال خدمات پس از فروش اجرا می‌شوند.';
}
$es_steps   = ServiceMetabox::steps( $es_service_id );
$es_contact = FooterTab::contact();
$es_fa      = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_phone   = (string) $es_contact['phone'];
$es_tel     = preg_replace( '/\s+/', '', $es_phone );

$es_step_icons = array(
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-ruler h-4 w-4" aria-hidden="true"><path d="M13 7 8.7 2.7a2.41 2.41 0 0 0-3.4 0L2.7 5.3a2.41 2.41 0 0 0 0 3.4L7 13"></path><path d="m8 6 2-2"></path><path d="m18 16 2-2"></path><path d="m17 11 4.3 4.3c.94.94.94 2.46 0 3.4l-2.6 2.6c-.94.94-2.46.94-3.4 0L11 17"></path><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path><path d="m15 5 4 4"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wrench h-4 w-4" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hard-hat h-4 w-4" aria-hidden="true"><path d="M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"></path><path d="M14 6a6 6 0 0 1 6 6v3"></path><path d="M4 15v-3a6 6 0 0 1 6-6"></path><rect x="2" y="15" width="20" height="4" rx="1"></rect></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lightbulb h-4 w-4" aria-hidden="true"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"></path><path d="M9 18h6"></path><path d="M10 22h4"></path></svg>',
);

// Related projects: same-name project_type term first, latest otherwise.
$es_service_title = get_the_title( $es_service_id );
$es_related_args  = array( 'post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => 3 );
$es_term          = get_term_by( 'name', $es_service_title, 'project_type' );
if ( $es_term instanceof \WP_Term ) {
	$es_related_args['tax_query'] = array( array( 'taxonomy' => 'project_type', 'field' => 'term_id', 'terms' => $es_term->term_id ) );
}
$es_related = get_posts( $es_related_args );
?>
<section id="service-details" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-14 lg:px-8 lg:py-18">
	<div class="grid gap-8 lg:grid-cols-[1fr_340px]">
		<div>
			<div class="rounded-3xl border border-emerald-100 bg-white p-7 shadow-sm lg:p-9">
				<h2 class="mb-6 flex items-center gap-3 text-xl font-extrabold text-slate-900 lg:text-2xl"><span class="h-7 w-1.5 rounded-full bg-gradient-to-b from-emerald-500 to-emerald-700"></span><?php esc_html_e( 'اقلام اجرایی این خدمت', 'erfansanat' ); ?></h2>
				<?php if ( $es_bullets ) : ?>
					<ul class="grid gap-4 sm:grid-cols-2">
						<?php foreach ( $es_bullets as $es_i => $es_bullet ) : ?>
							<li class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 text-[14px] font-bold leading-7 text-slate-700"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-emerald-700 text-[13px] font-black text-white"><?php echo esc_html( strtr( (string) ( $es_i + 1 ), $es_fa ) ); ?></span><?php echo esc_html( $es_bullet ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="mt-7 rounded-2xl bg-emerald-50/70 p-5 text-[13.5px] font-medium leading-8 text-slate-600"><span class="font-extrabold text-emerald-800"><?php esc_html_e( 'نکته مهم: ', 'erfansanat' ); ?></span><?php echo esc_html( $es_note ); ?></div>
				<?php
				$es_service_post = get_post( $es_service_id );
				if ( $es_service_post && '' !== trim( wp_strip_all_tags( $es_service_post->post_content ) ) ) :
					?>
					<div class="es-content mt-7 text-[14px] leading-8 text-slate-600"><?php echo wp_kses_post( apply_filters( 'the_content', $es_service_post->post_content ) ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="flex flex-col gap-6">
			<div>
				<div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
					<h3 class="mb-5 text-[16px] font-extrabold text-slate-900"><?php esc_html_e( 'مسیر اجرای خدمت', 'erfansanat' ); ?></h3>
					<ol class="relative space-y-1">
						<span aria-hidden="true" class="absolute bottom-5 right-[1.1rem] top-5 w-0.5 rounded-full bg-emerald-100"></span>
						<?php foreach ( $es_steps as $es_i => $es_step ) : ?>
							<li class="relative flex items-center gap-3.5 py-2"><span class="relative z-10 grid h-9 w-9 shrink-0 place-items-center rounded-full border-2 border-emerald-200 bg-white text-emerald-700"><?php echo $es_step_icons[ $es_i % count( $es_step_icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span><span class="flex flex-col"><span class="text-[13px] font-extrabold text-slate-800"><?php echo esc_html( $es_step['title'] ); ?></span><span class="text-[11.5px] text-slate-500"><?php echo esc_html( $es_step['description'] ); ?></span></span><?php if ( 0 === $es_i ) : ?><span class="mr-auto rounded-full bg-emerald-50 px-2.5 py-1 text-[10.5px] font-black text-emerald-700"><?php esc_html_e( 'شروع', 'erfansanat' ); ?></span><?php endif; ?></li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>
			<div>
				<div class="relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50/70 p-6 text-center shadow-sm">
					<div class="absolute -top-12 left-1/2 h-32 w-32 -translate-x-1/2 rounded-full bg-emerald-200/40 blur-[50px]"></div>
					<h4 class="relative text-[15px] font-extrabold text-slate-900"><?php esc_html_e( 'همین خدمت را برای شهر خود بخواهید', 'erfansanat' ); ?></h4>
					<?php if ( '' !== $es_phone ) : ?><a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="relative mt-4 flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-[13.5px] font-black text-white shadow-sm transition-colors hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><?php echo esc_html( strtr( str_replace( '-', ' - ', $es_phone ), $es_fa ) ); ?></a><?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php if ( $es_related ) : ?>
		<div class="mt-16">
			<div>
				<div class="mb-8 flex items-center justify-between">
					<h3 class="text-2xl font-extrabold text-slate-900"><?php esc_html_e( 'نمونه پروژه‌های مرتبط', 'erfansanat' ); ?></h3>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' ) ); ?>" class="group flex items-center gap-2 text-sm font-bold text-emerald-700"><?php esc_html_e( 'همه پروژه‌ها', 'erfansanat' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
				</div>
			</div>
			<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
				<?php foreach ( $es_related as $es_related_post ) : ?>
					<?php get_template_part( 'template-parts/cards/card-project', null, array( 'project_id' => $es_related_post->ID ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
