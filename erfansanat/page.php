<?php
/**
 * Page template.
 *
 * WooCommerce's cart, checkout and account pages are ordinary WordPress pages
 * holding blocks or shortcodes, so they land here. The theme supplies the
 * approved header, breadcrumb and (on the three commerce steps) the checkout
 * indicator; the page body itself is printed by `the_content()`, which is what
 * keeps WooCommerce's blocks and shortcodes working and lets a plugin add its
 * own step without a template edit.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

get_header();

/*
 * The three commerce pages share one progress strip in the approved reference,
 * but only the step the visitor is actually on gets the emphasis. Reading the
 * real page ids means a moved or renamed page still highlights correctly.
 */
$erfansanat_cart_id     = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'cart' ) : 0;
$erfansanat_checkout_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'checkout' ) : 0;
$erfansanat_account_id  = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'myaccount' ) : 0;
$erfansanat_current_id  = (int) get_queried_object_id();

$erfansanat_steps = array();
if ( $erfansanat_cart_id > 0 ) {
	$erfansanat_steps[] = array(
		'id'    => $erfansanat_cart_id,
		'label' => __( 'بررسی سبد خرید', 'erfansanat' ),
		'hint'  => __( 'انتخاب تعداد، متراژ و ویژگی‌های فنی', 'erfansanat' ),
	);
}
if ( $erfansanat_checkout_id > 0 ) {
	$erfansanat_steps[] = array(
		'id'    => $erfansanat_checkout_id,
		'label' => __( 'اطلاعات ارسال و نوع فاکتور', 'erfansanat' ),
		'hint'  => __( 'خرید آنلاین یا درخواست پیش‌فاکتور رسمی', 'erfansanat' ),
	);
}
if ( $erfansanat_account_id > 0 ) {
	$erfansanat_steps[] = array(
		'id'    => $erfansanat_account_id,
		'label' => __( 'تایید نهایی و کد پیگیری', 'erfansanat' ),
		'hint'  => __( 'ارسال از انبار کارخانه با باربری بیمه', 'erfansanat' ),
	);
}
$erfansanat_show_steps = in_array( $erfansanat_current_id, array( $erfansanat_cart_id, $erfansanat_checkout_id ), true )
	&& count( $erfansanat_steps ) > 0;
?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>
	<?php
	$erfansanat_title     = get_the_title();
	$erfansanat_permalink = (string) get_permalink();
	?>

	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,176,64,0.09), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
			<div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">
					<span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
					</span>
					<?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?>
				</a>
				<span class="flex min-w-0 items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
					<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( $erfansanat_title ); ?></span>
				</span>
			</nav>
			<div class="pt-6">
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $erfansanat_title ); ?></h1>
			</div>
		</div>
	</section>

	<?php if ( $erfansanat_show_steps ) : ?>
		<div class="mx-auto max-w-7xl px-5 pt-10 lg:px-8">
			<ol class="grid grid-cols-3 gap-3 rounded-2xl border border-emerald-100 bg-white p-4 shadow-2xs sm:gap-6 sm:p-5">
				<?php
				$erfansanat_current_index = 0;
				foreach ( $erfansanat_steps as $erfansanat_key => $erfansanat_candidate ) {
					if ( $erfansanat_current_id === (int) $erfansanat_candidate['id'] ) {
						$erfansanat_current_index = $erfansanat_key;
					}
				}
				?>
				<?php foreach ( $erfansanat_steps as $erfansanat_index => $erfansanat_step ) : ?>
					<?php
					$erfansanat_is_current = $erfansanat_current_id === (int) $erfansanat_step['id'];
					$erfansanat_is_done    = ! $erfansanat_is_current && $erfansanat_current_index > $erfansanat_index;
					?>
					<li class="flex items-center gap-3 <?php echo $erfansanat_is_current || $erfansanat_is_done ? '' : 'opacity-60'; ?>">
						<span class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-xs font-black <?php echo $erfansanat_is_current ? 'bg-emerald-700 text-white' : 'border border-slate-200 bg-slate-50 text-slate-600'; ?>">
							<?php echo esc_html( SectionParts::fa_digits( (string) ( $erfansanat_index + 1 ) ) ); ?>
						</span>
						<div>
							<div class="text-xs font-black sm:text-sm <?php echo $erfansanat_is_current ? 'text-emerald-950' : 'text-slate-700'; ?>">
								<?php
								echo esc_html( (string) $erfansanat_step['label'] );
								?>
							</div>
							<div class="hidden text-[11px] font-medium text-slate-500 sm:block"><?php echo esc_html( (string) $erfansanat_step['hint'] ); ?></div>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	<?php endif; ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'erfansanat-page' ); ?>>
		<div class="entry-content mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<nav class="page-links">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		?>
		<div class="mx-auto max-w-7xl px-5 pb-12 lg:px-8">
			<?php comments_template(); ?>
		</div>
		<?php
	}
	?>
<?php endwhile; ?>

<?php
get_footer();
