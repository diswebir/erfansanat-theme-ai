<?php
/**
 * Projects archive (P07-T006). Reference layout: hero + filter sidebar + project
 * card grid + client-side pagination. The query fetches only published projects
 * (AC-02); id=archive and the data-project-* contract are preserved (AC-01) and
 * driven by the immutable main.js. Pagination uses the shared archive part
 * (AC-03). An empty archive renders the reference empty state without fatal.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_per_page = (int) apply_filters( 'es_projects_per_page', 6 );

$es_archive_args = apply_filters(
	'es_projects_archive_query_args',
	array(
		'post_type'      => 'projects',
		'post_status'    => 'publish',
		'posts_per_page' => -1, // Client-side pagination needs the full set.
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);
$es_projects = new WP_Query( $es_archive_args );
$es_total    = count( $es_projects->posts );

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

$es_archive_url = get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' );
$es_types       = get_terms( array( 'taxonomy' => 'project_type', 'hide_empty' => true ) );
$es_cities      = get_terms( array( 'taxonomy' => 'city', 'hide_empty' => true ) );
$es_types       = is_wp_error( $es_types ) ? array() : $es_types;
$es_cities      = is_wp_error( $es_cities ) ? array() : $es_cities;

$es_active_type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'همه';
$es_active_loc  = isset( $_GET['location'] ) ? sanitize_text_field( wp_unslash( $_GET['location'] ) ) : 'همه';

$es_search_icon = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>';
?>

<main class="bg-slate-50/50">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<h1 class="text-[2rem] font-black leading-[1.3] text-slate-950 sm:text-[2.6rem]"><?php esc_html_e( 'پروژه‌های نورپردازی شهری', 'erfansanat' ); ?></h1>
			<p class="mt-3 max-w-[42rem] text-[14px] font-medium leading-8 text-slate-600"><?php esc_html_e( 'کارنامه طراحی، ساخت و اجرای پروژه‌های نورپردازی میدان، تونل نوری، المان و نمای شهری عرفان صنعت در سراسر کشور.', 'erfansanat' ); ?></p>
		</div>
	</section>

	<section id="archive" data-projects-archive="true" data-initial-type="<?php echo esc_attr( $es_active_type ); ?>" data-initial-location="<?php echo esc_attr( $es_active_loc ); ?>" class="mx-auto max-w-[1300px] scroll-mt-28 px-5 py-12 lg:px-8 lg:py-16">
		<div class="grid items-start gap-8 lg:grid-cols-12">
			<aside class="hidden lg:col-span-3 lg:block">
				<div class="sticky top-28 space-y-5 rounded-[1.5rem] border border-slate-200/80 bg-white p-5 shadow-2xs">
					<div class="flex items-center justify-between">
						<h2 class="text-sm font-black text-slate-900"><?php esc_html_e( 'فیلتر و آرشیو پروژه‌ها', 'erfansanat' ); ?></h2>
						<span class="rounded-lg bg-emerald-50 px-2 py-0.5 text-[11px] font-extrabold text-emerald-800"><span data-project-count="true"><?php echo esc_html( $es_fa( $es_total ) ); ?></span> <?php esc_html_e( 'پروژه', 'erfansanat' ); ?></span>
					</div>
					<label class="relative block">
						<span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"><?php echo $es_search_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<input id="project-sidebar-search" type="search" data-project-search="true" placeholder="<?php esc_attr_e( 'جستجو در پروژه‌ها…', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pr-9 pl-3 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
					</label>
					<div>
						<h3 class="mb-2 text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نوع پروژه', 'erfansanat' ); ?></h3>
						<div class="flex flex-wrap gap-1.5">
							<a href="<?php echo esc_url( $es_archive_url ); ?>" class="rounded-lg px-2.5 py-1 text-[11px] font-bold transition <?php echo 'همه' === $es_active_type ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-emerald-50'; ?>"><?php esc_html_e( 'همه', 'erfansanat' ); ?></a>
							<?php foreach ( $es_types as $es_term ) : ?>
								<a href="<?php echo esc_url( add_query_arg( 'type', $es_term->name, $es_archive_url ) ); ?>" class="rounded-lg px-2.5 py-1 text-[11px] font-bold transition <?php echo $es_active_type === $es_term->name ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-emerald-50'; ?>"><?php echo esc_html( $es_term->name ); ?></a>
							<?php endforeach; ?>
						</div>
					</div>
					<div>
						<h3 class="mb-2 text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'شهر', 'erfansanat' ); ?></h3>
						<div class="flex flex-wrap gap-1.5">
							<a href="<?php echo esc_url( $es_archive_url ); ?>" class="rounded-lg px-2.5 py-1 text-[11px] font-bold transition <?php echo 'همه' === $es_active_loc ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-emerald-50'; ?>"><?php esc_html_e( 'همه', 'erfansanat' ); ?></a>
							<?php foreach ( $es_cities as $es_term ) : ?>
								<a href="<?php echo esc_url( add_query_arg( 'location', $es_term->name, $es_archive_url ) ); ?>" class="rounded-lg px-2.5 py-1 text-[11px] font-bold transition <?php echo $es_active_loc === $es_term->name ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-emerald-50'; ?>"><?php echo esc_html( $es_term->name ); ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</aside>

			<div class="lg:col-span-9">
				<div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-5 py-4 shadow-2xs">
					<div class="flex items-center gap-2 text-[13px] font-black text-slate-900">
						<span><?php esc_html_e( 'پروژه‌ها', 'erfansanat' ); ?></span>
						<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800"><span data-project-count="true"><?php echo esc_html( $es_fa( $es_total ) ); ?></span> <?php esc_html_e( 'پروژه', 'erfansanat' ); ?></span>
					</div>
					<div class="flex items-center gap-2">
						<button type="button" data-archive-drawer-open="true" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[11.5px] font-extrabold text-slate-600 lg:hidden"><?php esc_html_e( 'فیلترها', 'erfansanat' ); ?></button>
						<label for="project-sort-select" class="text-[11.5px] font-bold text-slate-500"><?php esc_html_e( 'مرتب‌سازی:', 'erfansanat' ); ?></label>
						<select id="project-sort-select" data-project-sort="true" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-800 outline-none focus:border-emerald-500">
							<option value="newest"><?php esc_html_e( 'جدیدترین', 'erfansanat' ); ?></option>
							<option value="popular"><?php esc_html_e( 'پربازدیدترین', 'erfansanat' ); ?></option>
							<option value="rating"><?php esc_html_e( 'بالاترین امتیاز', 'erfansanat' ); ?></option>
						</select>
					</div>
				</div>

				<div data-project-grid="true" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 <?php echo $es_total ? '' : 'hidden'; ?>">
					<?php
					foreach ( $es_projects->posts as $es_project_post ) {
						get_template_part( 'template-parts/cards/card', 'project', array( 'project_id' => (int) $es_project_post->ID ) );
					}
					?>
				</div>

				<div data-project-empty="true" class="<?php echo $es_total ? 'hidden' : 'block'; ?> rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center">
					<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'پروژه‌ای مطابق با فیلترهای شما یافت نشد.', 'erfansanat' ); ?></p>
					<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو یا فیلترها را تغییر دهید.', 'erfansanat' ); ?></p>
				</div>

				<?php
				get_template_part( 'template-parts/archive/pagination', null, array(
					'total'    => $es_total,
					'per_page' => $es_per_page,
					'label'    => __( 'صفحه‌بندی پروژه‌ها', 'erfansanat' ),
				) );
				?>
			</div>
		</div>

		<div data-archive-drawer="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'فیلتر پروژه‌ها', 'erfansanat' ); ?>" class="hidden fixed inset-0 z-50 items-end justify-center bg-slate-950/60 p-0 backdrop-blur-xs sm:items-center sm:p-4 lg:hidden">
			<div data-archive-drawer-backdrop="true" class="absolute inset-0"></div>
			<div class="relative z-10 max-h-[85dvh] w-full overflow-y-auto rounded-t-[1.5rem] bg-white p-5 sm:max-w-[420px] sm:rounded-[1.5rem]">
				<div class="mb-4 flex items-center justify-between">
					<h2 class="text-sm font-black text-slate-900"><?php esc_html_e( 'فیلتر پروژه‌ها', 'erfansanat' ); ?></h2>
					<button type="button" data-archive-drawer-close="true" aria-label="<?php esc_attr_e( 'بستن فیلترها', 'erfansanat' ); ?>" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
					</button>
				</div>
				<label class="relative mb-4 block">
					<span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"><?php echo $es_search_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<input type="search" data-project-search="true" placeholder="<?php esc_attr_e( 'جستجو در پروژه‌ها…', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pr-9 pl-3 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
				</label>
				<div class="mb-4">
					<h3 class="mb-2 text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نوع پروژه', 'erfansanat' ); ?></h3>
					<div class="flex flex-wrap gap-1.5">
						<a href="<?php echo esc_url( $es_archive_url ); ?>" class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600"><?php esc_html_e( 'همه', 'erfansanat' ); ?></a>
						<?php foreach ( $es_types as $es_term ) : ?>
							<a href="<?php echo esc_url( add_query_arg( 'type', $es_term->name, $es_archive_url ) ); ?>" class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600"><?php echo esc_html( $es_term->name ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<button type="button" data-archive-drawer-close="true" class="w-full rounded-xl bg-emerald-700 px-4 py-3 text-[13px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'اعمال فیلترها', 'erfansanat' ); ?></button>
			</div>
		</div>
	</section>
</main>

<?php
wp_reset_postdata();
get_footer();
