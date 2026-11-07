<?php
/**
 * Search results (P07-T018). Combined post/product/projects results, typed
 * badges, safe escaped query, server-side pagination and a real empty state.
 * Reference layout language: blog.html hero + card grid.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_query    = get_search_query( false );
$es_query    = is_string( $es_query ) ? trim( $es_query ) : '';
$es_per_page = max( 1, (int) apply_filters( 'es_search_per_page', 9 ) );
$es_paged    = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$es_types    = array( 'post' );
foreach ( array( 'product', 'projects' ) as $es_type ) {
	if ( post_type_exists( $es_type ) ) {
		$es_types[] = $es_type;
	}
}
$es_types = (array) apply_filters( 'es_search_post_types', $es_types );
$es_labels = array(
	'post'     => __( 'مقاله', 'erfansanat' ),
	'product'  => __( 'محصول', 'erfansanat' ),
	'projects' => __( 'پروژه', 'erfansanat' ),
	'page'     => __( 'برگه', 'erfansanat' ),
	'services' => __( 'خدمت', 'erfansanat' ),
);
$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_fa        = static function ( $value ) use ( $es_fa_digits ) { return strtr( (string) $value, $es_fa_digits ); };

$es_results = null;
if ( '' !== $es_query ) {
	$es_results = new WP_Query(
		apply_filters(
			'es_search_query_args',
			array(
				's'                   => $es_query,
				'post_type'           => $es_types,
				'post_status'         => 'publish',
				'has_password'        => false,
				'posts_per_page'      => $es_per_page,
				'paged'               => $es_paged,
				'ignore_sticky_posts' => true,
			)
		)
	);
}
$es_total = $es_results ? (int) $es_results->found_posts : 0;
?>

<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 460px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-50"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 pb-12 lg:px-8">
			<div class="mx-auto max-w-2xl text-center">
				<span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs"><?php esc_html_e( 'جستجوی سایت', 'erfansanat' ); ?></span>
				<h1 class="mt-5 text-[2rem] font-black leading-[1.4] text-slate-950 sm:text-[2.6rem]">
					<?php
					if ( '' !== $es_query ) {
						printf(
							/* translators: %s: escaped search phrase. */
							esc_html__( 'نتایج جستجو برای «%s»', 'erfansanat' ),
							esc_html( $es_query )
						);
					} else {
						esc_html_e( 'جستجو در عرفان صنعت', 'erfansanat' );
					}
					?>
				</h1>
				<?php if ( $es_results ) : ?>
					<p class="mt-3 text-[13.5px] font-bold text-slate-500">
						<?php
						printf(
							/* translators: %s: result count in Persian digits. */
							esc_html__( '%s نتیجه یافت شد', 'erfansanat' ),
							esc_html( $es_fa( $es_total ) )
						);
						?>
					</p>
				<?php endif; ?>
				<form role="search" method="get" class="relative mx-auto mt-7 max-w-xl" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<svg aria-hidden="true" class="lucide lucide-search absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
					<input type="search" name="s" value="<?php echo esc_attr( $es_query ); ?>" placeholder="<?php esc_attr_e( 'جستجو در مقالات، محصولات و پروژه‌ها…', 'erfansanat' ); ?>" aria-label="<?php esc_attr_e( 'عبارت جستجو', 'erfansanat' ); ?>" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-28 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"/>
					<button type="submit" class="absolute left-2.5 top-1/2 -translate-y-1/2 cursor-pointer rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white transition-colors hover:bg-emerald-800"><?php esc_html_e( 'جستجو', 'erfansanat' ); ?></button>
				</form>
			</div>
		</div>
		<div aria-hidden="true" class="relative z-10 h-10 rounded-t-[2.5rem] bg-[#f8faf9]"></div>
	</section>

	<section class="mx-auto max-w-7xl px-5 pb-16 lg:px-8">
		<?php if ( '' === $es_query ) : ?>
			<div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 py-16 text-center">
				<p class="text-[14.5px] font-extrabold text-slate-600"><?php esc_html_e( 'عبارت مورد نظر خود را در کادر بالا جستجو کنید.', 'erfansanat' ); ?></p>
				<p class="mt-2 text-[12.5px] font-medium text-slate-400"><?php esc_html_e( 'نام محصول، پروژه یا موضوع مقاله را بنویسید.', 'erfansanat' ); ?></p>
			</div>
		<?php elseif ( ! $es_results->have_posts() ) : ?>
			<div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 py-16 text-center" data-search-empty="true">
				<p class="text-[14.5px] font-extrabold text-slate-600"><?php esc_html_e( 'هیچ نتیجه‌ای یافت نشد.', 'erfansanat' ); ?></p>
				<p class="mt-2 text-[12.5px] font-medium text-slate-400"><?php esc_html_e( 'عبارت دیگری را امتحان کنید یا از منوی محصولات و پروژه‌ها بازدید کنید.', 'erfansanat' ); ?></p>
				<div class="mt-6 flex flex-wrap items-center justify-center gap-3">
					<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="rounded-xl border border-emerald-200 bg-white px-5 py-2.5 text-[12.5px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-50"><?php esc_html_e( 'وبلاگ', 'erfansanat' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' ) ); ?>" class="rounded-xl border border-emerald-200 bg-white px-5 py-2.5 text-[12.5px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-50"><?php esc_html_e( 'پروژه‌ها', 'erfansanat' ); ?></a>
				</div>
			</div>
		<?php else : ?>
			<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-search-results="true">
				<?php
				while ( $es_results->have_posts() ) :
					$es_results->the_post();
					$es_rid   = get_the_ID();
					$es_rtype = get_post_type( $es_rid );
					$es_label = $es_labels[ $es_rtype ] ?? $es_rtype;
					?>
					<div class="relative flex flex-col" data-result-type="<?php echo esc_attr( $es_rtype ); ?>">
						<span class="absolute -top-2.5 right-4 z-10 rounded-full border border-emerald-200 bg-white px-3 py-1 text-[10.5px] font-black text-emerald-800 shadow-2xs"><?php echo esc_html( $es_label ); ?></span>
						<?php
						if ( 'post' === $es_rtype ) {
							get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => $es_rid, 'compact' => true, 'search_result' => true ) );
						} else {
							$es_rimg     = get_the_post_thumbnail_url( $es_rid, 'medium_large' ) ?: ES_ASSETS_URI . 'images/hero.jpg';
							$es_rexcerpt = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $es_rid ) ), 24, '…' );
							?>
							<a href="<?php echo esc_url( get_permalink( $es_rid ) ); ?>" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]">
								<div class="relative h-44 overflow-hidden">
									<img src="<?php echo esc_url( $es_rimg ); ?>" alt="<?php echo esc_attr( get_the_title( $es_rid ) ); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-108"/>
								</div>
								<div class="flex flex-1 flex-col gap-2.5 p-5">
									<h4 class="text-[15.5px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( get_the_title( $es_rid ) ); ?></h4>
									<?php if ( '' !== trim( $es_rexcerpt ) ) : ?><p class="line-clamp-2 text-[12.5px] leading-6 text-slate-500"><?php echo esc_html( $es_rexcerpt ); ?></p><?php endif; ?>
									<span class="mt-auto flex items-center gap-1.5 pt-2 text-[12.5px] font-bold text-emerald-700"><?php echo esc_html( sprintf( __( 'مشاهده %s', 'erfansanat' ), $es_label ) ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform duration-400 group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span>
								</div>
							</a>
						<?php } ?>
					</div>
				<?php endwhile; ?>
			</div>
			<?php
			$es_pages = (int) $es_results->max_num_pages;
			if ( $es_pages > 1 ) {
				echo '<nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="' . esc_attr__( 'صفحه‌بندی نتایج جستجو', 'erfansanat' ) . '">';
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $es_pages,
							'current'   => $es_paged,
							'mid_size'  => 1,
							'prev_text' => __( 'قبلی', 'erfansanat' ),
							'next_text' => __( 'بعدی', 'erfansanat' ),
							'type'      => 'plain',
							'add_args'  => array( 's' => rawurlencode( $es_query ) ),
						)
					)
				);
				echo '</nav>';
			}
			?>
		<?php endif; ?>
	</section>
</main>

<?php
wp_reset_postdata();
get_footer();
