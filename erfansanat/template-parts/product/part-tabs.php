<?php
/**
 * Product details: the three-tab panel the reference calls `product-details`.
 *
 * Tabs, panels and every `data-prod-tab-*` hook match the reference so the
 * transferred `initProductDetailAndFaqs()` drives them. The contents are real:
 * the long description, WooCommerce attributes plus the six official
 * identifiers, and approved WooCommerce reviews.
 *
 * @package ErfanSanat
 *
 * @var \WC_Product $erfansanat_product Product being viewed.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProductData;
use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_product ) || ! $erfansanat_product instanceof \WC_Product ) {
	return;
}

$erfansanat_id         = (int) $erfansanat_product->get_id();
$erfansanat_name       = $erfansanat_product->get_name();
$erfansanat_english    = \ErfanSanat\Theme\Data\LegacyMeta::text( $erfansanat_id, 'english_titl' );
$erfansanat_desc       = (string) $erfansanat_product->get_description();
$erfansanat_features   = ProductData::features( $erfansanat_id );
$erfansanat_highlights = ProductData::highlights( $erfansanat_id );
$erfansanat_usages     = ProductData::usages( $erfansanat_id );
$erfansanat_usage_cat  = ProductData::usage_terms( $erfansanat_id );
$erfansanat_rows       = ProductData::spec_rows( $erfansanat_product );
$erfansanat_idents     = ProductData::identifiers( $erfansanat_id );
$erfansanat_rating     = ProductData::rating( $erfansanat_product );
$erfansanat_reviews    = ProductData::reviews( $erfansanat_id );
$erfansanat_tabs       = array( 'desc', 'specs', 'reviews' );
?>
<section id="product-details" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-14 lg:px-8">
	<div class="report-tabs flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200/80 bg-white p-2 shadow-2xs">
		<?php foreach ( $erfansanat_tabs as $erfansanat_tab ) : ?>
			<?php
			$erfansanat_active = 'desc' === $erfansanat_tab;
			$erfansanat_labels = array(
				'desc'    => __( 'معرفی و کاربردها', 'erfansanat' ),
				'specs'   => __( 'مشخصات فنی و شناسه‌ها', 'erfansanat' ),
				'reviews' => $erfansanat_rating['count'] > 0
					/* translators: %s: number of reviews. */
					? sprintf( __( 'نظرات کاربران (%s)', 'erfansanat' ), SectionParts::fa_digits( (string) $erfansanat_rating['count'] ) )
					: __( 'نظرات کاربران', 'erfansanat' ),
			);
			?>
			<button type="button"
				data-prod-tab-btn="<?php echo esc_attr( $erfansanat_tab ); ?>"
				role="tab"
				aria-selected="<?php echo $erfansanat_active ? 'true' : 'false'; ?>"
				class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap <?php echo $erfansanat_active ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900'; ?>">
				<span data-prod-tab-icon="true" class="<?php echo $erfansanat_active ? 'text-white' : 'text-emerald-700'; ?>">
					<?php
					$erfansanat_icons = array(
						'desc'    => '<path d="M4 5h16M4 12h16M4 19h10"/>',
						'specs'   => '<path d="M9 3h6a2 2 0 0 1 2 2v16l-5-3-5 3V5a2 2 0 0 1 2-2z"/>',
						'reviews' => '<path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
					);
					printf(
						'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">%s</svg>',
						$erfansanat_icons[ $erfansanat_tab ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed whitelist above.
					);
					?>
				</span>
				<span><?php echo esc_html( (string) $erfansanat_labels[ $erfansanat_tab ] ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>

	<div class="mt-6">
		<div data-prod-tab-panel="desc" role="tabpanel" class="block">
			<div class="grid gap-10 lg:grid-cols-12">
				<div class="space-y-6 lg:col-span-7">
					<div>
						<div class="flex flex-wrap items-center justify-between gap-2">
							<h2 class="text-xl font-extrabold text-slate-900">
								<?php
								printf(
									/* translators: %s: product name. */
									esc_html__( 'معرفی تخصصی %s', 'erfansanat' ),
									esc_html( $erfansanat_name )
								);
								?>
							</h2>
							<?php if ( '' !== $erfansanat_english ) : ?>
								<span dir="ltr" class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-500"><?php echo esc_html( $erfansanat_english ); ?></span>
							<?php endif; ?>
						</div>
						<?php if ( '' !== trim( $erfansanat_desc ) ) : ?>
							<div class="mt-4 space-y-4 text-[14.5px] leading-9 text-slate-600"><?php echo wp_kses_post( wpautop( $erfansanat_desc ) ); ?></div>
						<?php else : ?>
							<p class="mt-4 text-[14px] leading-8 text-slate-500"><?php esc_html_e( 'توضیح تخصصی برای این محصول ثبت نشده است؛ برای جزئیات فنی با واحد فروش تماس بگیرید.', 'erfansanat' ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( array() !== $erfansanat_features ) : ?>
						<div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-5 sm:p-6">
							<div class="mb-4 flex items-center gap-2 text-sm font-extrabold text-slate-900">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M12 2 15 8.5 22 9.3 17 14l1.2 7-6.2-3.4L5.8 21 7 14 2 9.3l7-.8z"></path></svg>
								<span><?php esc_html_e( 'مزایا و ویژگی‌های برجسته محصول', 'erfansanat' ); ?></span>
							</div>
							<div class="grid gap-3 sm:grid-cols-2">
								<?php foreach ( $erfansanat_features as $erfansanat_feature ) : ?>
									<div class="flex items-start gap-2.5 rounded-xl border border-slate-200/60 bg-white px-3.5 py-2.5 text-[13px] font-semibold leading-6 text-slate-700">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mt-1 h-3.5 w-3.5 shrink-0 text-emerald-600" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
										<span><?php echo esc_html( $erfansanat_feature ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( array() !== $erfansanat_highlights ) : ?>
						<div class="rounded-2xl border border-amber-200/90 bg-amber-50/70 p-5">
							<div class="flex items-center gap-2 text-xs font-black text-amber-950">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M12 9v4M12 17h.01"></path><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path></svg>
								<span><?php esc_html_e( 'نکات کلیدی پیش از ثبت سفارش', 'erfansanat' ); ?></span>
							</div>
							<dl class="mt-2.5 grid gap-2 text-[12.5px] leading-7 text-amber-900/90 sm:grid-cols-2">
								<?php foreach ( $erfansanat_highlights as $erfansanat_highlight ) : ?>
									<div class="flex flex-wrap gap-1.5">
										<?php if ( '' !== $erfansanat_highlight['title'] ) : ?>
											<dt class="font-black"><?php echo esc_html( $erfansanat_highlight['title'] ); ?>:</dt>
										<?php endif; ?>
										<?php if ( '' !== $erfansanat_highlight['value'] ) : ?>
											<dd class="font-medium"><?php echo esc_html( $erfansanat_highlight['value'] ); ?></dd>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</dl>
						</div>
					<?php endif; ?>
				</div>

				<div class="space-y-6 lg:col-span-5">
					<?php if ( array() !== $erfansanat_usages ) : ?>
						<div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-6">
							<h3 class="mb-4 flex items-center gap-2 text-base font-extrabold text-slate-900">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M4 21V9l8-5 8 5v12M9 21v-6h6v6"></path></svg>
								<span><?php esc_html_e( 'کاربردهای اصلی در پروژه‌ها', 'erfansanat' ); ?></span>
							</h3>
							<div class="flex flex-wrap gap-2">
								<?php foreach ( $erfansanat_usages as $erfansanat_usage ) : ?>
									<span class="rounded-xl border border-emerald-200/80 bg-white px-3.5 py-2 text-xs font-bold text-emerald-950 shadow-2xs"><?php echo esc_html( $erfansanat_usage ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( array() !== $erfansanat_usage_cat ) : ?>
						<div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-6">
							<div class="mb-3.5 flex items-center gap-2 text-[13.5px] font-black text-slate-900">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h12"></path></svg>
								<span><?php esc_html_e( 'آرشیو پروژه‌های مرتبط با این محصول:', 'erfansanat' ); ?></span>
							</div>
							<div class="flex flex-wrap gap-2">
								<?php foreach ( $erfansanat_usage_cat as $erfansanat_term ) : ?>
									<?php $erfansanat_term_link = get_term_link( $erfansanat_term ); ?>
									<?php if ( ! is_string( $erfansanat_term_link ) ) : ?>
										<?php continue; ?>
									<?php endif; ?>
									<a href="<?php echo esc_url( $erfansanat_term_link ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-xs font-extrabold text-emerald-900 shadow-2xs transition-all hover:border-emerald-500 hover:bg-emerald-700 hover:text-white">
										<span><?php echo esc_html( $erfansanat_term->name ); ?></span>
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3" aria-hidden="true"><path d="M7 17 17 7"></path><path d="M7 7h10v10"></path></svg>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div data-prod-tab-panel="specs" role="tabpanel" class="hidden">
			<div class="space-y-8">
				<?php if ( array() !== $erfansanat_rows ) : ?>
					<div>
						<div class="mb-5 flex flex-wrap items-center justify-between gap-2">
							<div>
								<h2 class="text-xl font-extrabold text-slate-900">
									<?php
									printf(
										/* translators: %s: product name. */
										esc_html__( 'جدول کامل مشخصات فنی و مهندسی %s', 'erfansanat' ),
										esc_html( $erfansanat_name )
									);
									?>
								</h2>
								<p class="mt-1 text-xs font-medium text-slate-500"><?php esc_html_e( 'مقادیر مستقیماً از ویژگی‌های ثبت‌شدهٔ این محصول در ووکامرس خوانده می‌شوند.', 'erfansanat' ); ?></p>
							</div>
						</div>
						<div class="overflow-hidden rounded-2xl border border-slate-200">
							<?php foreach ( $erfansanat_rows as $erfansanat_row_index => $erfansanat_row ) : ?>
								<div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 <?php echo 0 === $erfansanat_row_index % 2 ? 'bg-slate-50/70' : 'bg-white'; ?> border-b border-slate-100 last:border-b-0">
									<div class="text-xs font-bold text-slate-500"><?php echo esc_html( $erfansanat_row['label'] ); ?></div>
									<div class="text-sm font-extrabold text-slate-900 sm:col-span-2"><?php echo esc_html( $erfansanat_row['value'] ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( array() !== $erfansanat_idents ) : ?>
					<div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-6">
						<div class="mb-4">
							<h3 class="flex items-center gap-2 text-base font-black text-emerald-950">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"></path><rect x="7" y="7" width="10" height="10" rx="1"></rect></svg>
								<span><?php esc_html_e( 'شناسه‌های رسمی کالا (ویژه سامانه ستاد، تدارکات دولتی و ایران‌کد)', 'erfansanat' ); ?></span>
							</h3>
							<p class="mt-1 text-xs font-medium text-slate-600"><?php esc_html_e( 'کدهای ثبت‌شده محصول جهت درج در اسناد مناقصات، فاکتور رسمی و سامانه تدارکات الکترونیکی دولت', 'erfansanat' ); ?></p>
						</div>
						<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
							<?php foreach ( $erfansanat_idents as $erfansanat_ident ) : ?>
								<div class="rounded-xl border border-emerald-100 bg-white p-3.5">
									<span class="block text-[11px] font-bold text-slate-500"><?php echo esc_html( $erfansanat_ident['label'] ); ?></span>
									<span dir="ltr" class="mt-1 block font-mono text-sm font-black text-slate-900 tabular-nums"><?php echo esc_html( $erfansanat_ident['value'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( array() === $erfansanat_rows && array() === $erfansanat_idents ) : ?>
					<p class="rounded-2xl border border-slate-200/80 bg-white p-6 text-sm font-bold text-slate-500">
						<?php esc_html_e( 'مشخصات فنی این محصول هنوز در سیستم ثبت نشده است.', 'erfansanat' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<div data-prod-tab-panel="reviews" role="tabpanel" class="hidden">
			<div class="grid items-start gap-8 lg:grid-cols-12">
				<div class="space-y-5 lg:col-span-7">
					<div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 sm:px-5">
						<div class="flex items-center gap-3.5">
							<div class="grid h-13 w-13 place-items-center rounded-2xl bg-emerald-700 text-lg font-black text-white tabular-nums">
								<?php echo esc_html( $erfansanat_rating['count'] > 0 ? number_format_i18n( $erfansanat_rating['average'], 2 ) : '—' ); ?>
							</div>
							<div>
								<div class="flex items-center gap-1 text-amber-500">
									<?php
									$erfansanat_stars = (int) round( $erfansanat_rating['average'] );
									for ( $erfansanat_star = 1; $erfansanat_star <= 5; $erfansanat_star++ ) :
										?>
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="<?php echo $erfansanat_star <= $erfansanat_stars ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.5" class="h-3.5 w-3.5 <?php echo $erfansanat_star <= $erfansanat_stars ? '' : 'text-slate-300'; ?>" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
									<?php endfor; ?>
								</div>
								<div class="mt-1 text-xs font-bold text-slate-600">
									<?php
									printf(
										/* translators: %s: product name. */
										esc_html__( 'میانگین امتیاز خریداران برای %s', 'erfansanat' ),
										esc_html( $erfansanat_name )
									);
									?>
								</div>
							</div>
						</div>
						<span data-reviews-count-badge="true" class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs font-extrabold text-emerald-800 tabular-nums">
							<?php
							printf(
								/* translators: %s: number of reviews. */
								esc_html__( '%s دیدگاه ثبت‌شده', 'erfansanat' ),
								esc_html( SectionParts::fa_digits( (string) $erfansanat_rating['count'] ) )
							);
							?>
						</span>
					</div>

					<div data-reviews-list="true" class="space-y-3.5">
						<?php if ( array() === $erfansanat_reviews ) : ?>
							<p class="rounded-2xl border border-slate-200/80 bg-white p-5 text-sm font-bold text-slate-500">
								<?php esc_html_e( 'هنوز دیدگاهی برای این محصول ثبت نشده است. اولین نفری باشید که تجربه خود را می‌نویسد.', 'erfansanat' ); ?>
							</p>
						<?php else : ?>
							<?php foreach ( $erfansanat_reviews as $erfansanat_review ) : ?>
								<div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs">
									<div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
										<div>
											<div class="text-sm font-black text-slate-900"><?php echo esc_html( $erfansanat_review['author'] ); ?></div>
											<?php if ( $erfansanat_review['verified'] ) : ?>
												<div class="mt-0.5 text-[11px] font-bold text-emerald-700"><?php esc_html_e( 'خرید تأییدشده', 'erfansanat' ); ?></div>
											<?php endif; ?>
										</div>
										<div class="flex items-center gap-3">
											<div class="flex items-center gap-0.5" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: rating out of five. */ __( 'امتیاز %s از ۵', 'erfansanat' ), SectionParts::fa_digits( (string) $erfansanat_review['rating'] ) ) ); ?>">
												<?php for ( $erfansanat_star = 1; $erfansanat_star <= 5; $erfansanat_star++ ) : ?>
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="<?php echo $erfansanat_star <= $erfansanat_review['rating'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.5" class="h-3 w-3 <?php echo $erfansanat_star <= $erfansanat_review['rating'] ? 'text-amber-500' : 'text-slate-300'; ?>" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
												<?php endfor; ?>
											</div>
											<span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[10.5px] font-bold text-slate-500 tabular-nums"><?php echo esc_html( $erfansanat_review['date'] ); ?></span>
										</div>
									</div>
									<p class="mt-3 text-[13px] leading-7 font-medium text-slate-600"><?php echo esc_html( $erfansanat_review['content'] ); ?></p>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>

				<div class="lg:col-span-5">
					<?php
					/*
					 * Real comment form, not the reference's fake submit. WooCommerce
					 * posts `rating` and the theme keeps that name, so
					 * `WC_Comments::add_comment_rating()` records the score.
					 */
					$erfansanat_commenter = wp_get_current_commenter();
					$erfansanat_form_args = array(
						'title_reply'          => __( 'ثبت نظر و امتیاز شما', 'erfansanat' ),
						'title_reply_before'   => '<h3 class="flex items-center gap-2 text-base font-black text-slate-900">',
						'title_reply_after'    => '</h3>',
						'comment_notes_before' => '<p class="mt-1 text-xs font-medium text-slate-500">' . esc_html__( 'نشانی ایمیل شما منتشر نخواهد شد.', 'erfansanat' ) . '</p>',
						'comment_notes_after'  => '',
						'label_submit'         => __( 'ثبت نظر و امتیاز', 'erfansanat' ),
						'class_submit'         => 'group relative inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-3.5 text-xs sm:text-sm font-black text-white shadow-[0_10px_24px_-6px_rgba(0,154,56,0.45)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 cursor-pointer',
						'comment_field'        => '',
						'fields'               => array(
							'author' => '<div class="mb-3"><label class="mb-1 block text-xs font-extrabold text-slate-700" for="author">' . esc_html__( 'نام و نام خانوادگی', 'erfansanat' ) . ' <span class="required text-rose-600">*</span></label><input id="author" name="author" type="text" required value="' . esc_attr( $erfansanat_commenter['comment_author'] ) . '" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:outline-none" /></div>',
							'email'  => '<div class="mb-3"><label class="mb-1 block text-xs font-extrabold text-slate-700" for="email">' . esc_html__( 'ایمیل', 'erfansanat' ) . ' <span class="required text-rose-600">*</span></label><input id="email" name="email" type="email" required value="' . esc_attr( $erfansanat_commenter['comment_author_email'] ) . '" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:outline-none" /></div>',
						),
					);

					if ( wc_review_ratings_enabled() ) {
						$erfansanat_form_args['comment_field'] .= '<div class="mb-3"><label class="mb-1 block text-xs font-extrabold text-slate-700" for="rating">' . esc_html__( 'امتیاز شما', 'erfansanat' ) . '</label><select id="rating" name="rating" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-extrabold text-slate-800 focus:border-emerald-500 focus:outline-none">';
						$erfansanat_form_args['comment_field'] .= '<option value="5">' . esc_html__( '۵ از ۵ (عالی و درجه یک)', 'erfansanat' ) . '</option>';
						$erfansanat_form_args['comment_field'] .= '<option value="4">' . esc_html__( '۴ از ۵ (خیلی خوب)', 'erfansanat' ) . '</option>';
						$erfansanat_form_args['comment_field'] .= '<option value="3">' . esc_html__( '۳ از ۵ (خوب)', 'erfansanat' ) . '</option>';
						$erfansanat_form_args['comment_field'] .= '<option value="2">' . esc_html__( '۲ از ۵ (متوسط)', 'erfansanat' ) . '</option>';
						$erfansanat_form_args['comment_field'] .= '<option value="1">' . esc_html__( '۱ از ۵ (ضعیف)', 'erfansanat' ) . '</option>';
						$erfansanat_form_args['comment_field'] .= '</select></div>';
					}

					$erfansanat_form_args['comment_field'] .= '<div class="mb-3"><label class="mb-1 block text-xs font-extrabold text-slate-700" for="comment">' . esc_html__( 'متن دیدگاه شما', 'erfansanat' ) . ' <span class="required text-rose-600">*</span></label><textarea id="comment" name="comment" rows="3" required class="w-full rounded-xl border border-slate-200 bg-white p-3.5 text-xs leading-6 font-semibold text-slate-800 focus:border-emerald-500 focus:outline-none"></textarea></div>';

					comment_form( $erfansanat_form_args, $erfansanat_id );
					?>
				</div>
			</div>
		</div>
	</div>
</section>
