<?php
/**
 * Single post shell (P07-T012). Reference: blog/types-of-light-tunnel.html.
 * Hero + breadcrumb, full content, sidebar and related articles. TOC (T013),
 * sharing (T014), counters (T015) and comments (T017) attach in their own tasks.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Calendar;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_post = get_post();
if ( ! $es_post instanceof WP_Post ) {
	get_header();
	?>
	<main class="min-h-screen bg-[#f8faf9]">
		<section class="mx-auto max-w-[820px] px-5 py-24 text-center lg:px-8">
			<div class="rounded-[1.5rem] border border-slate-200 bg-white px-6 py-14">
				<h1 class="text-xl font-black text-slate-900"><?php esc_html_e( 'مقاله مورد نظر یافت نشد.', 'erfansanat' ); ?></h1>
				<p class="mt-2 text-[13px] font-bold text-slate-500"><?php esc_html_e( 'ممکن است این مقاله حذف شده باشد یا نشانی اشتباه باشد.', 'erfansanat' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="mt-6 inline-flex rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'بازگشت به وبلاگ', 'erfansanat' ); ?></a>
			</div>
		</section>
	</main>
	<?php
	get_footer();
	return;
}
if ( 'post' !== $es_post->post_type ) {
	require locate_template( 'index.php' );
	return;
}

get_header();

$es_pid       = (int) $es_post->ID;
$es_title     = get_the_title( $es_pid );
$es_protected = post_password_required( $es_post );
$es_excerpt   = $es_protected ? '' : get_the_excerpt( $es_pid );
$es_cats      = get_the_category( $es_pid );
$es_cat       = $es_cats ? $es_cats[0] : null;
$es_tags      = get_the_tags( $es_pid ) ?: array();
$es_author    = (string) get_the_author_meta( 'display_name', (int) $es_post->post_author );
$es_date      = Calendar::post_date( $es_pid );
$es_words     = str_word_count( wp_strip_all_tags( $es_post->post_content ) );
$es_minutes   = max( 1, (int) ceil( $es_words / 200 ) );
$es_minutes   = (int) apply_filters( 'es_post_card_read_minutes', $es_minutes, $es_pid );
$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_fa        = static function ( $value ) use ( $es_fa_digits ) { return strtr( (string) $value, $es_fa_digits ); };
$es_image     = get_the_post_thumbnail_url( $es_pid, 'large' ) ?: ES_ASSETS_URI . 'images/hero.jpg';
$es_blog_url  = home_url( '/blog/' );
$es_contact   = FooterTab::contact();
$es_phone     = (string) ( $es_contact['phone'] ?: '031-91091011' );
$es_mobile    = (string) ( $es_contact['mobile'] ?: '09137976915' );
$es_tel       = preg_replace( '/\s+/', '', $es_phone );
$es_mobile_tel = preg_replace( '/\s+/', '', $es_mobile );

?>

<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-50"></div>
			<div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div>
			<div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div>
			<div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div>
			<div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<div class="flex justify-center">
				<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
					<a class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg aria-hidden="true" class="lucide lucide-house h-3.5 w-3.5" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
					<span class="flex min-w-0 items-center gap-1.5"><svg aria-hidden="true" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m15 18-6-6 6-6"></path></svg><a class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700" href="<?php echo esc_url( $es_blog_url ); ?>"><?php esc_html_e( 'وبلاگ', 'erfansanat' ); ?></a></span>
					<?php if ( $es_cat ) : ?>
						<span class="flex min-w-0 items-center gap-1.5"><svg aria-hidden="true" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m15 18-6-6 6-6"></path></svg><a class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800 transition-colors hover:bg-emerald-100" href="<?php echo esc_url( get_category_link( $es_cat ) ); ?>"><?php echo esc_html( $es_cat->name ); ?></a></span>
					<?php endif; ?>
					<span class="flex min-w-0 items-center gap-1.5"><svg aria-hidden="true" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php echo esc_html( $es_title ); ?></span></span>
				</nav>
			</div>
			<div class="mt-8 flex items-center justify-center gap-3"><span class="h-px w-8 bg-emerald-400/60"></span><span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs"><?php echo esc_html( $es_cat ? $es_cat->name : __( 'مقاله', 'erfansanat' ) ); ?></span><span class="h-px w-8 bg-emerald-400/60"></span></div>
			<h1 class="mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4]"><?php echo esc_html( $es_title ); ?></h1>
			<?php if ( '' !== trim( $es_excerpt ) ) : ?>
				<p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600"><?php echo esc_html( $es_excerpt ); ?></p>
			<?php endif; ?>
			<div class="mx-auto mt-8 flex max-w-2xl flex-wrap items-center justify-center gap-x-7 gap-y-3 rounded-3xl border border-emerald-100 bg-white px-6 py-4 shadow-[0_20px_50px_-24px_rgba(0,80,40,0.18)]">
				<span class="flex items-center gap-2 text-[13px] font-bold text-slate-800"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-50 text-emerald-700"><svg aria-hidden="true" class="lucide lucide-user-round h-4 w-4" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg></span><?php echo esc_html( $es_author ); ?></span>
				<span class="hidden h-8 w-px bg-emerald-100 sm:block"></span>
				<span class="flex items-center gap-2 text-[13px] font-bold text-slate-700"><svg aria-hidden="true" class="lucide lucide-calendar-days h-4 w-4 text-emerald-600" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect height="18" rx="2" width="18" x="3" y="4"></rect><path d="M3 10h18"></path><path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg><?php echo esc_html( $es_date ); ?></span>
				<span class="hidden h-8 w-px bg-emerald-100 sm:block"></span>
				<span class="flex items-center gap-2 text-[13px] font-bold text-slate-700"><svg aria-hidden="true" class="lucide lucide-clock3 lucide-clock-3 h-4 w-4 text-emerald-600" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg><?php echo esc_html( $es_fa( $es_minutes ) ); ?> <?php esc_html_e( 'دقیقه مطالعه', 'erfansanat' ); ?></span>
			</div>
			<?php if ( $es_tags ) : ?>
				<div class="mt-5 flex flex-wrap items-center justify-center gap-2">
					<?php foreach ( $es_tags as $es_tag ) : ?>
						<a class="rounded-full border border-emerald-200 bg-white px-3.5 py-1.5 text-[12px] font-bold text-emerald-800 shadow-2xs transition-colors hover:bg-emerald-50" href="<?php echo esc_url( get_tag_link( $es_tag ) ); ?>">#<?php echo esc_html( $es_tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="relative z-10 mx-auto mt-10 max-w-[1300px] px-5 lg:px-8">
			<div class="overflow-hidden rounded-[2rem] border border-emerald-100 bg-white p-2 shadow-[0_36px_80px_-32px_rgba(0,80,40,0.28)]">
				<img alt="<?php echo esc_attr( $es_title ); ?>" class="h-64 w-full rounded-[1.5rem] object-cover sm:h-96" src="<?php echo esc_url( $es_image ); ?>"/>
			</div>
		</div>
		<div aria-hidden="true" class="relative z-10 mt-12 h-10 rounded-t-[2.5rem] bg-[#f8faf9] sm:h-12"></div>
	</section>

	<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
		<div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_300px]">
			<article class="min-w-0">
				<div>
					<?php get_template_part( 'template-parts/post/toc', null, array( 'prepare' => true ) ); ?>
					<?php
					if ( $es_protected ) {
						echo get_the_password_form( $es_post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core form is escaped.
					} else {
						the_content();
					}
					?>
				</div>
				<?php do_action( 'es_single_post_content_after', $es_pid ); ?>
				<?php if ( ! $es_protected ) : ?>
					<?php get_template_part( 'template-parts/post/share', null, array( 'post_id' => $es_pid ) ); ?>
				<?php endif; ?>
				<?php do_action( 'es_single_post_comments', $es_pid ); ?>
			</article>
			<aside class="flex flex-col gap-6">
				<?php do_action( 'es_single_post_sidebar_top', $es_pid ); ?>
				<div>
					<div class="relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50/70 p-6 text-center shadow-sm">
						<div class="absolute -top-12 left-1/2 h-32 w-32 -translate-x-1/2 rounded-full bg-emerald-200/40 blur-[50px]"></div>
						<h4 class="relative text-[15.5px] font-extrabold text-slate-900"><?php esc_html_e( 'مشاوره فنی رایگان پروژه', 'erfansanat' ); ?></h4>
						<p class="relative mt-2.5 text-[13px] leading-7 text-slate-600"><?php esc_html_e( 'برای انتخاب بهترین تجهیزات نورپردازی و دریافت استعلام قیمت روز با کارشناسان ما تماس بگیرید.', 'erfansanat' ); ?></p>
						<a class="relative mt-5 flex items-center justify-center gap-2.5 rounded-2xl bg-emerald-700 px-5 py-3.5 text-[13.5px] font-black text-white shadow-sm transition-colors hover:bg-emerald-800" href="<?php echo esc_url( 'tel:' . $es_tel ); ?>"><svg aria-hidden="true" class="lucide lucide-phone-call h-4.5 w-4.5" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><?php echo esc_html( $es_fa( str_replace( '-', ' - ', $es_phone ) ) ); ?></a>
						<a class="relative mt-3 flex items-center justify-center gap-2.5 rounded-2xl border border-emerald-200 bg-white px-5 py-3 text-[13px] font-bold text-emerald-800 transition-colors hover:border-emerald-400 hover:bg-emerald-50" href="<?php echo esc_url( 'tel:' . $es_mobile_tel ); ?>"><svg aria-hidden="true" class="lucide lucide-message-circle h-4.5 w-4.5" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"></path></svg><?php echo esc_html( sprintf( __( 'گفتگو با کارشناس فروش (%s)', 'erfansanat' ), $es_fa( $es_mobile ) ) ); ?></a>
					</div>
				</div>
			</aside>
		</div>
	</section>

	<?php get_template_part( 'template-parts/post/related', null, array( 'post_id' => $es_pid ) ); ?>
</main>

<?php
wp_reset_postdata();
get_footer();
