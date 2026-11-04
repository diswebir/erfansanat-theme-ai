<?php
/**
 * Single project shell (P07-T001). Reference layout: hero + breadcrumb, about,
 * gallery, executive details (id=project-details), process, used products
 * (id=used-products) and related projects. The specialised inner blocks are
 * attached by P07-T002..T005 through the es_project_* actions (AC-02). A
 * missing/invalid project renders a graceful notice instead of a fatal (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_post = get_post();
$es_pid  = $es_post ? (int) $es_post->ID : 0;

if ( ! $es_post || 'projects' !== $es_post->post_type ) :
	?>
	<main class="bg-slate-50/50">
		<section class="mx-auto max-w-[820px] px-5 py-24 text-center lg:px-8">
			<div class="rounded-[1.5rem] border border-slate-200 bg-white px-6 py-14">
				<h1 class="text-xl font-black text-slate-900"><?php esc_html_e( 'پروژه مورد نظر یافت نشد.', 'erfansanat' ); ?></h1>
				<p class="mt-2 text-[13px] font-bold text-slate-500"><?php esc_html_e( 'ممکن است این پروژه حذف شده باشد یا نشانی اشتباه باشد.', 'erfansanat' ); ?></p>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' ) ); ?>" class="mt-6 inline-flex rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'بازگشت به پروژه‌ها', 'erfansanat' ); ?></a>
			</div>
		</section>
	</main>
	<?php
	get_footer();
	return;
endif;

$es_title    = get_the_title( $es_pid );
$es_types    = get_the_terms( $es_pid, 'project_type' );
$es_cities   = get_the_terms( $es_pid, 'city' );
$es_type     = ( $es_types && ! is_wp_error( $es_types ) ) ? $es_types[0] : null;
$es_city     = ( $es_cities && ! is_wp_error( $es_cities ) ) ? $es_cities[0] : null;
$es_badge    = trim( ( $es_type ? $es_type->name : 'پروژه' ) . ( $es_city ? ' · ' . $es_city->name : '' ) );
$es_excerpt  = get_the_excerpt( $es_pid );
$es_archive  = get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' );
$es_thumb    = get_the_post_thumbnail_url( $es_pid, 'large' ) ?: ES_ASSETS_URI . 'images/hero.jpg';
?>

<main class="bg-slate-50/50" data-project-single="true">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
			<div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur-md">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
				<span class="flex min-w-0 items-center gap-1.5">
					<a href="<?php echo esc_url( $es_archive ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'پروژه‌ها', 'erfansanat' ); ?></a>
				</span>
				<?php if ( $es_type ) : ?>
					<span class="flex min-w-0 items-center gap-1.5">
						<a href="<?php echo esc_url( get_term_link( $es_type ) ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php echo esc_html( $es_type->name ); ?></a>
					</span>
				<?php endif; ?>
				<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( $es_title ); ?></span>
			</nav>
			<div class="pt-6">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800">
					<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $es_badge ); ?></span>
				</div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $es_title ); ?></h1>
				<?php if ( $es_excerpt ) : ?>
					<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $es_excerpt ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php
	// Gallery (P07-T002).
	do_action( 'es_project_gallery', $es_pid );
	?>

	<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8">
		<div class="rounded-[1.5rem] border border-slate-200/80 bg-white p-6 shadow-xs lg:p-8">
			<h2 class="text-xl font-extrabold text-slate-900"><?php echo esc_html( sprintf( 'درباره %s', $es_title ) ); ?></h2>
			<div class="es-project-content mt-4 text-[14px] font-medium leading-8 text-slate-600">
				<?php
				while ( have_posts() ) {
					the_post();
					the_content();
				}
				?>
			</div>
		</div>
	</section>

	<section id="project-details" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-4 pb-14 sm:px-6 lg:px-8">
		<div class="rounded-[1.5rem] border border-slate-200/80 bg-white p-6 shadow-xs lg:p-8">
			<h2 class="text-xl font-extrabold text-slate-900"><?php echo esc_html( sprintf( 'شناسنامه کامل اجرایی %s', $es_title ) ); ?></h2>
			<?php
			// Executive metadata tabs (P07-T003).
			do_action( 'es_project_details', $es_pid );
			?>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8">
		<div class="rounded-[1.5rem] border border-slate-200/80 bg-white p-6 shadow-xs lg:p-8">
			<h2 class="text-xl font-extrabold text-slate-900"><?php esc_html_e( 'فرآیند مهندسی، تولید و اجرای پروژه', 'erfansanat' ); ?></h2>
			<p class="mt-1 text-xs font-bold text-slate-500"><?php esc_html_e( 'از طراحی تا تحویل نهایی، تمام مراحل توسط تیم مهندسی عرفان صنعت اجرا می‌شود.', 'erfansanat' ); ?></p>
			<ol class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
				<?php
				$es_steps = array( 'طراحی و محاسبات روشنایی', 'تولید و کنترل کیفیت', 'نصب و اجرای میدانی', 'تست و تحویل نهایی' );
				foreach ( $es_steps as $es_i => $es_step ) :
					?>
					<li class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
						<span class="grid h-8 w-8 place-items-center rounded-xl bg-emerald-700 text-[13px] font-black text-white"><?php echo esc_html( number_format_i18n( $es_i + 1 ) ); ?></span>
						<span class="mt-3 block text-[12.5px] font-extrabold text-slate-800"><?php echo esc_html( $es_step ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section id="used-products" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-14 lg:px-8">
		<div class="mb-7 flex flex-wrap items-center justify-between gap-3">
			<h2 class="text-xl font-extrabold text-slate-900"><?php esc_html_e( 'محصولات و تجهیزات استفاده‌شده', 'erfansanat' ); ?></h2>
			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="text-[12px] font-extrabold text-emerald-700 hover:underline"><?php esc_html_e( 'مشاهده فروشگاه محصولات ←', 'erfansanat' ); ?></a>
		</div>
		<?php
		// Used products grid (P07-T004).
		do_action( 'es_project_used_products', $es_pid );
		?>
	</section>

	<section class="mx-auto max-w-6xl px-5 pb-16 lg:px-8">
		<div class="mb-6">
			<div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700">
				<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
				<span><?php esc_html_e( 'پروژه‌های شاخص', 'erfansanat' ); ?></span>
			</div>
			<h2 class="text-xl font-extrabold text-slate-900"><?php esc_html_e( 'سایر پروژه‌های شاخص عرفان صنعت', 'erfansanat' ); ?></h2>
		</div>
		<?php
		// Related projects grid (P07-T005).
		do_action( 'es_project_related', $es_pid );
		?>
	</section>

	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<section class="mx-auto max-w-6xl px-5 pb-16 lg:px-8">';
		comments_template();
		echo '</section>';
	}
	?>
</main>

<?php
get_footer();
