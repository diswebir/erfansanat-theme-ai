<?php
/**
 * Home section S05: the dark "who we are" band with the factory credentials.
 *
 * The reference uses two photo cards; they are rendered from the section
 * repeater so an editor can swap the pair without touching markup.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_items  = (array) SectionParts::field( $erfansanat_fields, 'items', array() );
$erfansanat_link   = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
$erfansanat_media  = (int) SectionParts::field( $erfansanat_fields, 'media_id', 0 );
$erfansanat_bg     = $erfansanat_media > 0 ? (string) wp_get_attachment_image_url( $erfansanat_media, 'erfansanat-hero' ) : SectionParts::asset( 'images/hero.jpg' );
?>
<div class="mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
	<div class="relative overflow-hidden rounded-[2.25rem] border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_24px_60px_-24px_rgba(4,120,87,0.38)] sm:rounded-[2.5rem]">

		<?php if ( '' !== $erfansanat_bg ) : ?>
			<img src="<?php echo esc_url( $erfansanat_bg ); ?>" alt="<?php echo esc_attr( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) ); ?>" loading="lazy" decoding="async" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-15 mix-blend-luminosity" />
		<?php endif; ?>
		<div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(52,211,153,0.22),transparent_55%)]"></div>

		<div class="absolute -top-0.5 -right-0.5 z-20">
			<div class="relative rounded-bl-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-bl-[2rem] sm:px-8 sm:py-3">
				<div class="flex items-center gap-2 text-xs sm:text-sm font-black text-emerald-700">
					<span class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ) ); ?></span>
				</div>
				<svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute top-0.5 -left-[31px] h-8 w-8 text-[#f6f8f7]"><path d="M32 0H0C17.673 0 32 14.327 32 32V0Z" fill="currentColor"></path></svg>
				<svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute right-0.5 -bottom-[31px] h-8 w-8 text-[#f6f8f7]"><path d="M32 0V32C32 14.327 17.673 0 0 0H32Z" fill="currentColor"></path></svg>
			</div>
		</div>

		<div class="relative z-10 px-6 pt-16 pb-15 sm:px-10 sm:pt-16 sm:pb-16 lg:px-12">
			<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10">

				<div class="text-right lg:col-span-6">
					<div class="inline-flex flex-wrap items-center gap-2.5 rounded-2xl bg-white/10 px-3.5 py-1.5 backdrop-blur-xs">
						<span dir="ltr" class="font-mono text-lg font-black tracking-[0.14em] text-white sm:text-xl"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_mark', 'ESE' ) ); ?></span>
						<span class="h-3.5 w-px bg-white/25"></span>
						<span class="text-xs font-extrabold text-emerald-100 sm:text-sm"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ) ); ?></span>
					</div>

					<h3 class="mt-4 text-xl font-black leading-snug text-white sm:text-2xl [text-wrap:balance]">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by title_markup().
						echo SectionParts::title_markup( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) );
						?>
					</h3>

					<?php if ( array() !== $erfansanat_items ) : ?>
						<div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-3 sm:gap-2.5">
							<?php foreach ( $erfansanat_items as $erfansanat_item ) : ?>
								<?php $erfansanat_item = is_array( $erfansanat_item ) ? $erfansanat_item : array(); ?>
								<div class="rounded-xl bg-white/10 px-2.5 py-2 backdrop-blur-xs sm:px-3 sm:py-2.5">
									<div class="text-[11.5px] sm:text-xs font-black text-white tabular-nums"><?php echo esc_html( (string) ( $erfansanat_item['title'] ?? '' ) ); ?></div>
									<div class="mt-0.5 text-[10px] sm:text-[10.5px] font-semibold text-emerald-100/85"><?php echo esc_html( (string) ( $erfansanat_item['description'] ?? '' ) ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="grid grid-cols-12 gap-4 lg:col-span-6">
					<?php
					$erfansanat_photos = array(
						array(
							'image' => 'images/light-tunnel.jpg',
							'tag'   => __( 'سازه و المان شهری', 'erfansanat' ),
							'label' => __( 'تونل‌های نوری پیکسلی و کمانی', 'erfansanat' ),
							'class' => 'col-span-7',
						),
						array(
							'image' => 'images/light-tree.jpg',
							'tag'   => __( 'تولید انحصاری', 'erfansanat' ),
							'label' => __( 'درختان نوری هوشمند NRF', 'erfansanat' ),
							'class' => 'col-span-5',
						),
					);
					foreach ( $erfansanat_photos as $erfansanat_photo ) :
						$erfansanat_photo_src = SectionParts::asset( $erfansanat_photo['image'] );
						if ( '' === $erfansanat_photo_src ) {
							continue;
						}
						?>
						<div class="group relative <?php echo esc_attr( $erfansanat_photo['class'] ); ?> h-48 overflow-hidden rounded-2xl bg-emerald-950/40 shadow-lg sm:h-56">
							<img src="<?php echo esc_url( $erfansanat_photo_src ); ?>" alt="<?php echo esc_attr( $erfansanat_photo['label'] ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
							<div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-transparent"></div>
							<div class="absolute right-3.5 bottom-3 text-right">
								<div class="text-[10.5px] font-bold text-emerald-300"><?php echo esc_html( $erfansanat_photo['tag'] ); ?></div>
								<div class="text-xs font-extrabold text-white"><?php echo esc_html( $erfansanat_photo['label'] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php if ( ! empty( $erfansanat_link['url'] ) ) : ?>
			<div class="absolute -bottom-0.5 -left-0.5 z-20">
				<div class="relative rounded-tr-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-tr-[2rem] sm:px-8 sm:py-3">
					<a href="<?php echo esc_url( (string) $erfansanat_link['url'] ); ?>" class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 transition-colors hover:text-emerald-900 sm:text-sm">
						<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? '' ) ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
					</a>
					<svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute -top-[31px] left-0.5 h-8 w-8 text-[#f6f8f7]"><path d="M0 32V0C0 17.673 14.327 32 32 32H0Z" fill="currentColor"></path></svg>
					<svg viewBox="0 0 32 32" aria-hidden="true" class="pointer-events-none absolute -right-[31px] bottom-0.5 h-8 w-8 text-[#f6f8f7]"><path d="M0 32H32C14.327 32 0 17.673 0 0V32Z" fill="currentColor"></path></svg>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
