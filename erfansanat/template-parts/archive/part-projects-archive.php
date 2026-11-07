<?php
/**
 * Shared body of the projects archive and the city / project-type archives.
 *
 * The three references differ only in the hero wording and the breadcrumb tail,
 * so one body serves all three. Paging is real server-side `?page=N` over the
 * main query, which keeps the archive usable without JavaScript; the filter
 * script layers on top of the current page.
 *
 * @package ErfanSanat
 *
 * @var \WP_Term|null $erfansanat_term Current term on a city or type archive.
 * @var string        $erfansanat_taxonomy Taxonomy of that term.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProjectData;
use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_term        = isset( $erfansanat_term ) && $erfansanat_term instanceof \WP_Term ? $erfansanat_term : null;
$erfansanat_taxonomy    = isset( $erfansanat_taxonomy ) ? (string) $erfansanat_taxonomy : '';
$erfansanat_archive_url = (string) get_post_type_archive_link( 'projects' );
$erfansanat_per_page    = (int) apply_filters( 'erfansanat_projects_per_page', 6 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Theme's own filter.
$erfansanat_per_page    = max( 1, $erfansanat_per_page );
$erfansanat_page        = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$erfansanat_query = $GLOBALS['wp_query'] ?? null;
$erfansanat_total = $erfansanat_query instanceof \WP_Query ? (int) $erfansanat_query->found_posts : 0;

// The filter panel lists every term that has a project, so a term with nothing
// in it never becomes a dead link. Reading it here means the panel and the
// native form share one source.
$erfansanat_types  = get_terms(
	array(
		'taxonomy'   => 'project_type',
		'hide_empty' => true,
	)
);
$erfansanat_types  = is_array( $erfansanat_types ) ? $erfansanat_types : array();
$erfansanat_cities = get_terms(
	array(
		'taxonomy'   => 'city',
		'hide_empty' => true,
	)
);
$erfansanat_cities = is_array( $erfansanat_cities ) ? $erfansanat_cities : array();

$erfansanat_projects = array();
if ( $erfansanat_query instanceof \WP_Query ) {
	foreach ( (array) $erfansanat_query->posts as $erfansanat_post ) {
		if ( $erfansanat_post instanceof \WP_Post && 'projects' === $erfansanat_post->post_type ) {
			$erfansanat_projects[] = $erfansanat_post;
		}
	}
}

$erfansanat_total_projects = (int) wp_count_posts( 'projects' )->publish;

// Terms are read once and reused by the sidebar and the hero.
$erfansanat_type_terms = get_terms(
	array(
		'taxonomy'   => 'project_type',
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
$erfansanat_type_terms = is_array( $erfansanat_type_terms ) ? $erfansanat_type_terms : array();

$erfansanat_city_terms = get_terms(
	array(
		'taxonomy'   => 'city',
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 24,
	)
);
$erfansanat_city_terms = is_array( $erfansanat_city_terms ) ? $erfansanat_city_terms : array();

$erfansanat_current_type = null;
$erfansanat_current_city = null;
if ( $erfansanat_term instanceof \WP_Term ) {
	if ( 'project_type' === $erfansanat_taxonomy ) {
		$erfansanat_current_type = $erfansanat_term;
	}
	if ( 'city' === $erfansanat_taxonomy ) {
		$erfansanat_current_city = $erfansanat_term;
	}
}

// Breadcrumb: home -> projects -> (type) -> (city) -> current.
$erfansanat_crumb_trail = array(
	array(
		'label' => __( 'صفحه اصلی', 'erfansanat' ),
		'url'   => home_url( '/' ),
	),
);
if ( null === $erfansanat_term ) {
	$erfansanat_crumb_trail[] = array(
		'label' => __( 'پروژه‌ها', 'erfansanat' ),
		'url'   => '',
	);
} else {
	$erfansanat_crumb_trail[] = array(
		'label' => __( 'پروژه‌ها', 'erfansanat' ),
		'url'   => $erfansanat_archive_url,
	);
	$erfansanat_crumb_trail[] = array(
		'label' => $erfansanat_term->name,
		'url'   => '',
	);
}

$erfansanat_hero = null === $erfansanat_term
	? array(
		'eyebrow'   => __( 'کارنامهٔ اجرایی کارخانه عرفان صنعت', 'erfansanat' ),
		'title'     => __( 'پروژه‌های نورپردازی', 'erfansanat' ),
		'highlight' => __( 'شهری', 'erfansanat' ),
		'lead'      => __( 'روایتی از هنر، مهندسی و نور در سراسر ایران و عراق؛ از تونل‌های نوری و میادین اصلی تا نورپردازی پل‌ها و ساختمان‌های شاخص.', 'erfansanat' ),
		'image'     => SectionParts::asset( 'images/light-tunnel.jpg' ),
	)
	: array(
		'eyebrow'   => 'city' === $erfansanat_taxonomy
			? __( 'آرشیو پروژه‌ها بر اساس شهر', 'erfansanat' )
			: __( 'آرشیو پروژه‌ها بر اساس نوع', 'erfansanat' ),
		'title'     => $erfansanat_term->name,
		'highlight' => '',
		'lead'      => trim( wp_strip_all_tags( (string) $erfansanat_term->description ) ),
		'image'     => SectionParts::asset( 'images/light-element.jpg' ),
	);

$erfansanat_term_url = \ErfanSanat\Theme\term_url( $erfansanat_term );
$erfansanat_base_url = null === $erfansanat_term || '' === $erfansanat_term_url
	? trailingslashit( $erfansanat_archive_url )
	: trailingslashit( $erfansanat_term_url );
?>
<section data-projects-hero="true" class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(1000px 500px at 50% -10%, rgba(0,177,64,0.1), transparent 60%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-50"></div>
		<div class="absolute -left-40 top-1/4 h-80 w-80 rounded-full bg-emerald-200/45 blur-[110px]"></div>
		<div class="absolute -right-28 bottom-0 h-72 w-72 rounded-full bg-teal-100/60 blur-[110px]"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur">
			<?php foreach ( $erfansanat_crumb_trail as $erfansanat_index => $erfansanat_crumb ) : ?>
				<?php if ( $erfansanat_index > 0 ) : ?>
					<span class="flex min-w-0 items-center gap-1.5">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
				<?php endif; ?>
				<?php if ( isset( $erfansanat_crumb['url'] ) && '' !== $erfansanat_crumb['url'] ) : ?>
					<a href="<?php echo esc_url( (string) $erfansanat_crumb['url'] ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php echo esc_html( (string) $erfansanat_crumb['label'] ); ?></a>
				<?php else : ?>
					<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( (string) $erfansanat_crumb['label'] ); ?></span>
				<?php endif; ?>
				<?php if ( $erfansanat_index > 0 ) : ?>
					</span>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>

		<div class="grid items-center gap-10 pb-4 pt-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-12">
			<div>
				<div class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
					<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( (string) $erfansanat_hero['eyebrow'] ); ?></span>
				</div>
				<h1 class="text-[2.2rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[2.8rem] lg:text-[3.2rem] lg:leading-[1.22]">
					<?php echo esc_html( (string) $erfansanat_hero['title'] ); ?>
					<?php if ( '' !== (string) $erfansanat_hero['highlight'] ) : ?>
						<span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php echo esc_html( (string) $erfansanat_hero['highlight'] ); ?></span>
					<?php endif; ?>
				</h1>
				<?php if ( '' !== (string) $erfansanat_hero['lead'] ) : ?>
					<p class="mt-5 max-w-[36rem] text-[15px] font-medium leading-9 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( (string) $erfansanat_hero['lead'] ); ?></p>
				<?php endif; ?>

				<div class="mt-7 flex w-fit items-center gap-5 rounded-3xl border border-emerald-100 bg-white py-4 pl-7 pr-5 shadow-[0_20px_50px_-20px_rgba(0,80,40,0.2)]">
					<span class="bg-gradient-to-b from-emerald-600 to-emerald-800 bg-clip-text text-[3.2rem] font-black leading-none text-transparent lg:text-[3.8rem]"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total_projects ) ); ?></span>
					<span class="max-w-[8.5rem] text-[13px] font-bold leading-6 text-slate-500"><?php esc_html_e( 'پروژه ثبت‌شده در این آرشیو', 'erfansanat' ); ?></span>
				</div>
			</div>

			<?php if ( '' !== (string) $erfansanat_hero['image'] ) : ?>
				<div class="relative mx-auto hidden h-[26rem] w-full max-w-[26rem] sm:block">
					<div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[80%] w-[80%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[90px]"></div>
					<div class="absolute left-0 top-6 h-[72%] w-[60%] overflow-hidden rounded-3xl border-4 border-white shadow-[0_30px_60px_-20px_rgba(0,80,40,0.35)]">
						<img src="<?php echo esc_url( (string) $erfansanat_hero['image'] ); ?>" alt="" class="h-full w-full object-cover" />
						<div class="absolute inset-0 rounded-3xl ring-1 ring-inset ring-white/40"></div>
					</div>
					<div class="absolute bottom-0 right-[10%] h-[58%] w-[62%] overflow-hidden rounded-3xl border-4 border-white shadow-[0_30px_70px_-18px_rgba(0,80,40,0.4)]">
						<img src="<?php echo esc_url( SectionParts::asset( 'images/light-element.jpg' ) ); ?>" alt="" class="h-full w-full object-cover" />
						<span class="absolute bottom-3 right-4 rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold text-emerald-800 backdrop-blur"><?php esc_html_e( 'آثار اجراشده', 'erfansanat' ); ?></span>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div>
</section>

<section
	id="archive"
	data-projects-archive="true"
	data-initial-type="<?php echo esc_attr( $erfansanat_current_type instanceof \WP_Term ? $erfansanat_current_type->name : __( 'همه', 'erfansanat' ) ); ?>"
	data-initial-location="<?php echo esc_attr( $erfansanat_current_city instanceof \WP_Term ? $erfansanat_current_city->name : __( 'همه', 'erfansanat' ) ); ?>"
	data-items-per-page="<?php echo esc_attr( (string) $erfansanat_per_page ); ?>"
	class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8 lg:py-16"
>
	<div class="grid items-start gap-8 lg:grid-cols-12">
		<aside aria-label="<?php esc_attr_e( 'فیلتر و آرشیو پروژه‌ها', 'erfansanat' ); ?>" class="hidden lg:col-span-4 lg:block xl:col-span-3">
			<?php
			$erfansanat_drawer = false;
			require get_template_directory() . '/template-parts/archive/part-projects-filters.php';
			?>
		</aside>
		<div class="lg:col-span-8 xl:col-span-9">
			<?php
			// The no-JavaScript floor: a real form the server can answer.
			$erfansanat_filter_kind   = 'projects';
			$erfansanat_filter_terms  = $erfansanat_types;
			$erfansanat_filter_places = $erfansanat_cities;
			$erfansanat_filter_action = $erfansanat_archive_url;
			require get_template_directory() . '/template-parts/archive/part-native-filters.php';
			?>
			<?php require get_template_directory() . '/template-parts/archive/part-projects-grid.php'; ?>
		</div>
	</div>

	<div data-archive-drawer-backdrop="true" class="fixed inset-0 z-40 hidden bg-slate-950/55 backdrop-blur-sm lg:hidden"></div>
	<div data-archive-drawer="true" class="fixed inset-y-0 right-0 z-50 hidden w-80 flex-col overflow-y-auto bg-slate-50 p-4 lg:hidden">
		<div class="mb-3 flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
			<span class="text-[13px] font-black text-slate-900"><?php esc_html_e( 'فیلتر و آرشیو پروژه‌ها', 'erfansanat' ); ?></span>
			<button type="button" data-archive-drawer-close="true" aria-label="<?php esc_attr_e( 'بستن فیلترها', 'erfansanat' ); ?>" class="grid h-9 w-9 cursor-pointer place-items-center rounded-xl border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
			</button>
		</div>
		<?php
		$erfansanat_drawer = true;
		require get_template_directory() . '/template-parts/archive/part-projects-filters.php';
		?>
	</div>
</section>

<?php
// The closing CTA is the home page's own contact section, reused unchanged.
require get_template_directory() . '/template-parts/archive/part-projects-closing.php';
