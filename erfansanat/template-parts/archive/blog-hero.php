<?php
/**
 * Blog hero sliced from static-mirror/blog.html (P07-T009).
 * Editor's pick is a published sticky post, falling back to the newest post.
 *
 * @package ErfanSanat
 * @var array $args { @type WP_Post|null $post @type int $total @type array $categories @type callable $fa }
 */

defined( 'ABSPATH' ) || exit;

$es_post        = isset( $args['post'] ) && $args['post'] instanceof WP_Post ? $args['post'] : null;
$es_total       = (int) ( $args['total'] ?? 0 );
$es_categories  = (array) ( $args['categories'] ?? array() );
$es_fa          = $args['fa'];
$es_hero_id     = $es_post ? (int) $es_post->ID : 0;
$es_hero_cats   = $es_hero_id ? get_the_category( $es_hero_id ) : array();
$es_hero_cat    = $es_hero_cats ? $es_hero_cats[0]->name : '';
$es_hero_image  = $es_hero_id ? get_the_post_thumbnail_url( $es_hero_id, 'large' ) : '';
$es_hero_image  = $es_hero_image ?: ES_ASSETS_URI . 'images/light-tunnel.jpg';
$es_hero_words  = $es_hero_id ? (int) preg_match_all( '/\S+/u', wp_strip_all_tags( (string) get_post_field( 'post_content', $es_hero_id ) ) ) : 0;
$es_hero_minutes = max( 1, (int) ceil( $es_hero_words / 200 ) );
?>
<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-50"></div>
		<div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div>
		<div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div>
		<div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div>
		<div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
			<a class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg aria-hidden="true" class="lucide lucide-house h-3.5 w-3.5" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>
				<?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?>
			</a>
			<span class="flex min-w-0 items-center gap-1.5"><svg aria-hidden="true" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php esc_html_e( 'وبلاگ', 'erfansanat' ); ?></span></span>
		</nav>
		<div class="grid items-center gap-12 pb-16 pt-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-14 lg:pb-20 lg:pt-12">
			<div>
				<div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm">
					<span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-700 text-white shadow-[0_0_20px_rgba(0,125,45,0.35)]"><svg aria-hidden="true" class="lucide lucide-newspaper h-4 w-4" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M15 18h-5"></path><path d="M18 14h-8"></path><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path><rect height="4" rx="1" width="8" x="10" y="6"></rect></svg></span>
					<span class="text-[13px] font-extrabold text-emerald-800"><?php echo esc_html( sprintf( __( 'مجله فنی و آموزشی عرفان صنعت · %s مقاله', 'erfansanat' ), $es_fa( $es_total ) ) ); ?></span>
				</div>
				<h1 class="text-[2.5rem] font-black leading-[1.25] tracking-[-0.015em] text-slate-950 sm:text-[3.3rem] lg:text-[3.8rem] lg:leading-[1.18]"><?php echo esc_html( apply_filters( 'es_blog_archive_title', __( 'اخبار و مقالات', 'erfansanat' ) ) ); ?><br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php echo esc_html( apply_filters( 'es_blog_archive_subtitle', __( 'نورپردازی شهری', 'erfansanat' ) ) ); ?></span></h1>
				<p class="mt-6 max-w-[34rem] text-[15.5px] font-medium leading-9 text-slate-600"><?php echo esc_html( apply_filters( 'es_blog_archive_description', __( 'مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان', 'erfansanat' ) ) ); ?></p>
				<div class="relative mt-8 max-w-[30rem]">
					<svg aria-hidden="true" class="lucide lucide-search absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
					<input id="blog-search" aria-label="<?php esc_attr_e( 'جستجو در مقالات', 'erfansanat' ); ?>" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-5 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15" data-blog-search="true" placeholder="<?php esc_attr_e( 'جستجو در مقالات… (مثلاً تونل نوری، والواشر)', 'erfansanat' ); ?>" type="text" value=""/>
				</div>
				<div class="mt-5 flex flex-wrap gap-2">
					<button class="rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer bg-emerald-700 text-white shadow-md shadow-emerald-700/25" data-blog-cat-btn="<?php esc_attr_e( 'همه', 'erfansanat' ); ?>" type="button"><?php esc_html_e( 'همه', 'erfansanat' ); ?></button>
					<?php foreach ( $es_categories as $es_category ) : ?>
						<button class="rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700" data-blog-cat-btn="<?php echo esc_attr( $es_category->name ); ?>" type="button"><?php echo esc_html( $es_category->name ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
			<?php if ( $es_post ) : ?>
				<div class="relative mx-auto w-full max-w-[30rem]">
					<div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div>
					<a class="group relative block overflow-hidden rounded-[2.5rem] border border-emerald-100 bg-white p-2 shadow-[0_36px_80px_-30px_rgba(0,80,40,0.28)]" href="<?php echo esc_url( get_permalink( $es_hero_id ) ); ?>">
						<div class="relative overflow-hidden rounded-[2rem]">
							<img alt="<?php echo esc_attr( get_the_title( $es_hero_id ) ); ?>" class="h-[24rem] w-full object-cover transition-transform duration-[1.4s] group-hover:scale-105 sm:h-[27rem]" src="<?php echo esc_url( $es_hero_image ); ?>"/>
							<div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
							<span class="absolute right-5 top-5 rounded-full bg-emerald-700 px-4 py-1.5 text-[11.5px] font-black text-white shadow-lg"><?php esc_html_e( '★ منتخب سردبیر', 'erfansanat' ); ?></span>
							<div class="absolute inset-x-0 bottom-0 p-6">
								<div class="mb-2.5 flex items-center gap-3 text-[11.5px] font-bold text-emerald-200"><span><?php echo esc_html( $es_hero_cat ); ?></span><span class="h-1 w-1 rounded-full bg-white/50"></span><span class="flex items-center gap-1"><svg aria-hidden="true" class="lucide lucide-clock3 lucide-clock-3 h-3 w-3" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg><?php echo esc_html( $es_fa( $es_hero_minutes ) ); ?> <?php esc_html_e( 'دقیقه مطالعه', 'erfansanat' ); ?></span></div>
								<h3 class="line-clamp-2 text-[19px] font-black leading-8 text-white [text-shadow:0_2px_14px_rgba(0,0,0,0.6)]"><?php echo esc_html( get_the_title( $es_hero_id ) ); ?></h3>
								<span class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/95 px-4 py-2 text-[12.5px] font-extrabold text-emerald-900 shadow-sm transition-colors group-hover:bg-emerald-600 group-hover:text-white"><?php esc_html_e( 'مطالعه مقاله', 'erfansanat' ); ?><svg aria-hidden="true" class="lucide lucide-arrow-up-left h-4 w-4" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span>
							</div>
						</div>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div>
</section>
