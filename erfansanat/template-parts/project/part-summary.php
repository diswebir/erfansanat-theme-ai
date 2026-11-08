<?php
/**
 * Sticky project summary: status, title, quick facts and the contact rail.
 *
 * The reference hard-codes a rating, an execution duration and a client name.
 * None of those exist in the fixture, so the card prints only what the project
 * actually has, and every call to action uses the site's own phone number from
 * Settings rather than a literal.
 *
 * @package ErfanSanat
 *
 * @var int                      $erfansanat_project_id Project post id.
 * @var string                   $erfansanat_title      Project title.
 * @var array<string, string>    $erfansanat_facts      Label => value pairs.
 * @var array{year: string, month: string, day: string, month_name: string, display: string} $erfansanat_date Completion date.
 * @var int                      $erfansanat_views      View counter.
 * @var string                   $erfansanat_contact_url Contact page permalink.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_project_id  = isset( $erfansanat_project_id ) ? (int) $erfansanat_project_id : 0;
$erfansanat_title       = isset( $erfansanat_title ) ? (string) $erfansanat_title : '';
$erfansanat_facts       = isset( $erfansanat_facts ) && is_array( $erfansanat_facts ) ? $erfansanat_facts : array();
$erfansanat_date        = isset( $erfansanat_date ) && is_array( $erfansanat_date ) ? $erfansanat_date : array();
$erfansanat_views       = isset( $erfansanat_views ) ? (int) $erfansanat_views : 0;
$erfansanat_contact_url = isset( $erfansanat_contact_url ) ? (string) $erfansanat_contact_url : '';
$erfansanat_phone       = SectionParts::phone_number();
?>
<aside class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-[0_20px_50px_-24px_rgba(5,150,105,0.16)]" aria-label="<?php esc_attr_e( 'خلاصه پروژه', 'erfansanat' ); ?>">
	<div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-emerald-50/60 px-5 py-3.5 sm:px-6">
		<div class="flex items-center gap-2">
			<span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></span>
			<span class="text-xs font-extrabold text-emerald-950"><?php esc_html_e( 'پروژه تحویل‌شده و در حال بهره‌برداری', 'erfansanat' ); ?></span>
		</div>
		<?php if ( $erfansanat_views > 0 ) : ?>
			<span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
				<span>
					<?php
					printf(
						/* translators: %s: view count in Persian digits. */
						esc_html__( '%s بازدید', 'erfansanat' ),
						esc_html( SectionParts::fa_digits( (string) $erfansanat_views ) )
					);
					?>
				</span>
			</span>
		<?php endif; ?>
	</div>

	<div class="px-5 py-5 sm:px-6">
		<p class="text-[11px] font-bold text-slate-400"><?php esc_html_e( 'پروژه اجراشده', 'erfansanat' ); ?></p>
		<h2 class="mt-1.5 text-lg font-black leading-8 text-slate-900"><?php echo esc_html( $erfansanat_title ); ?></h2>
		<p class="mt-1 text-xs leading-6 text-slate-500"><?php esc_html_e( 'طراحی، تولید و اجرای تخصصی توسط کارخانه دانش‌بنیان عرفان صنعت اصفهان', 'erfansanat' ); ?></p>

		<?php if ( array() !== array_filter( $erfansanat_facts ) ) : ?>
			<div class="border-t border-slate-100 pt-4 mt-4">
				<div class="mb-2.5 flex items-center justify-between">
					<span class="text-xs font-extrabold text-slate-800"><?php esc_html_e( 'چکیده اطلاعات پروژه', 'erfansanat' ); ?></span>
					<a href="#project-details" data-scroll-to-specs="true" class="text-[11px] font-bold text-emerald-700 hover:underline"><?php esc_html_e( 'شناسنامه کامل اجرایی ↓', 'erfansanat' ); ?></a>
				</div>
				<?php require __DIR__ . '/part-facts.php'; ?>
			</div>
		<?php endif; ?>

		<div class="mt-4 space-y-2.5 border-t border-slate-100 pt-4">
			<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="group relative inline-flex w-full items-center justify-center gap-2.5 whitespace-nowrap rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_12px_28px_-8px_rgba(0,154,56,0.48)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 hover:via-emerald-700 hover:to-emerald-800 active:translate-y-0">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
				<span>
					<?php
					printf(
						/* translators: %s: phone number. */
						esc_html__( 'مشاوره و استعلام پروژه مشابه (%s)', 'erfansanat' ),
						esc_html( $erfansanat_phone )
					);
					?>
				</span>
			</a>
			<div class="grid grid-cols-2 gap-2.5">
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $erfansanat_phone ) ); ?>" class="flex items-center justify-center gap-1.5 whitespace-nowrap rounded-xl border border-emerald-200 bg-emerald-50/80 px-3 py-2.5 text-xs font-extrabold text-emerald-900 transition-all hover:border-emerald-400 hover:bg-emerald-100/80">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg>
					<span><?php esc_html_e( 'ارتباط با مدیر پروژه', 'erfansanat' ); ?></span>
				</a>
				<?php if ( '' !== $erfansanat_contact_url ) : ?>
					<a href="<?php echo esc_url( $erfansanat_contact_url ); ?>" class="flex items-center justify-center gap-1.5 whitespace-nowrap rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-all hover:border-emerald-300 hover:bg-white hover:text-emerald-800">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
						<span><?php esc_html_e( 'درخواست بازدید و طرح', 'erfansanat' ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</aside>
