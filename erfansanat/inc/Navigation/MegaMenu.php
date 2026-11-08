<?php
/**
 * Desktop mega menu markup.
 *
 * Every block is a trigger button plus a panel, matching the reference
 * `data-desktop-mega-trigger` / `data-desktop-mega-panel` contract so main.js
 * keeps working without changes. Buttons carry `aria-expanded` and the panels
 * are labelled, which is what the keyboard/escape handling in main.js toggles.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Navigation;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the four desktop panels.
 */
final class MegaMenu {

	/**
	 * Trigger button classes, taken from the reference.
	 */
	private const TRIGGER_CLASS = 'inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700';

	/**
	 * Render one trigger plus its panel.
	 *
	 * @param string $block Block name.
	 * @return void
	 */
	public static function render_block( string $block ): void {
		// Only the four reference blocks exist; anything else must print
		// nothing rather than an empty, focusable trigger.
		if ( ! in_array( $block, MenuTree::blocks(), true ) ) {
			return;
		}

		$labels   = MenuTree::block_labels();
		$label    = $labels[ $block ] ?? $block;
		$panel_id = 'erfansanat-mega-panel-' . $block;
		?>
		<div class="group static" data-mega-menu-<?php echo esc_attr( $block ); ?>="true">
			<button type="button"
				data-desktop-mega-trigger="<?php echo esc_attr( $block ); ?>"
				aria-expanded="false"
				aria-haspopup="true"
				aria-controls="<?php echo esc_attr( $panel_id ); ?>"
				class="<?php echo esc_attr( self::TRIGGER_CLASS ); ?>">
				<span><?php echo esc_html( $label ); ?></span>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
			</button>
			<div id="<?php echo esc_attr( $panel_id ); ?>"
				data-desktop-mega-panel="<?php echo esc_attr( $block ); ?>"
				role="region"
				aria-label="<?php echo esc_attr( $label ); ?>"
				class="erfansanat-mega-panel absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
				<div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-2xl">
					<?php
					switch ( $block ) {
						case 'products':
							self::render_products();
							break;
						case 'projects':
							self::render_projects();
							break;
						case 'services':
							self::render_services();
							break;
						case 'corporate':
							self::render_corporate();
							break;
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Products panel with four real category tabs.
	 *
	 * @return void
	 */
	private static function render_products(): void {
		$tabs = MenuTree::product_tabs();
		if ( array() === $tabs ) {
			echo '<p class="p-6 text-xs text-slate-500">' . esc_html__( 'دسته‌بندی محصولی برای نمایش وجود ندارد.', 'erfansanat' ) . '</p>';
			return;
		}
		$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		?>
		<div class="grid gap-0 md:grid-cols-12">
			<div class="border-b border-slate-100 bg-slate-50/60 p-4 md:col-span-4 md:border-b-0 md:border-l">
				<p class="mb-3 text-[11px] font-black uppercase tracking-wide text-emerald-700"><?php esc_html_e( 'دسته‌بندی تولیدات', 'erfansanat' ); ?></p>
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<button type="button"
						data-mega-prod-tab="<?php echo (int) $index; ?>"
						class="mb-1 flex w-full items-center justify-between rounded-lg px-3 py-2 text-right text-[12px] font-bold <?php echo 0 === $index ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:bg-white/70'; ?>">
						<span><?php echo esc_html( $tab['label'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
			<div class="p-4 md:col-span-8">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<div data-mega-prod-panel="<?php echo (int) $index; ?>" class="<?php echo 0 === $index ? '' : 'hidden'; ?>">
						<div class="mb-2 flex items-center justify-between">
							<span class="text-[12px] font-black text-slate-900"><?php echo esc_html( $tab['label'] ); ?></span>
							<?php if ( '' !== $tab['url'] ) : ?>
								<a href="<?php echo esc_url( $tab['url'] ); ?>" class="text-[11px] font-bold text-emerald-700 hover:underline"><?php esc_html_e( 'مشاهده همه', 'erfansanat' ); ?></a>
							<?php endif; ?>
						</div>
						<ul class="grid grid-cols-2 gap-1.5">
							<?php foreach ( $tab['items'] as $item ) : ?>
								<li>
									<a href="<?php echo esc_url( $item['url'] ); ?>" class="block rounded-lg px-2.5 py-1.5 text-[11.5px] font-semibold text-slate-600 transition-colors hover:bg-emerald-50 hover:text-emerald-800">
										<?php echo esc_html( $item['title'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
				<?php if ( is_string( $shop ) && '' !== $shop ) : ?>
					<div class="mt-3 flex flex-wrap gap-2 border-t border-slate-100 pt-3">
						<a href="<?php echo esc_url( $shop ); ?>" class="rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-800 hover:bg-emerald-100"><?php esc_html_e( 'فروشگاه آنلاین', 'erfansanat' ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Projects panel: type and city groups.
	 *
	 * @return void
	 */
	private static function render_projects(): void {
		$groups  = MenuTree::project_groups();
		$archive = get_post_type_archive_link( 'projects' );
		?>
		<div class="grid gap-0 md:grid-cols-12">
			<div class="border-b border-slate-100 bg-slate-50/60 p-4 md:col-span-3 md:border-b-0 md:border-l">
				<p class="mb-2 text-[11px] font-black uppercase tracking-wide text-emerald-700"><?php esc_html_e( 'تفکیک پروژه‌ها', 'erfansanat' ); ?></p>
				<div class="space-y-1">
					<?php
					// The reference rail switches the right-hand panel between the
					// type and city archives; main.js toggles `data-mega-proj-panel`.
					$erfansanat_rail = array(
						'type' => __( 'بر اساس نوع سازه', 'erfansanat' ),
						'city' => __( 'بر اساس شهر اجرا', 'erfansanat' ),
					);
					foreach ( $erfansanat_rail as $erfansanat_mode => $erfansanat_label ) :
						$erfansanat_active = 'type' === $erfansanat_mode;
						?>
						<button type="button"
							data-mega-proj-tab="<?php echo esc_attr( $erfansanat_mode ); ?>"
							aria-pressed="<?php echo $erfansanat_active ? 'true' : 'false'; ?>"
							class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right text-[12px] font-extrabold transition-all cursor-pointer <?php echo $erfansanat_active ? 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-white/70'; ?>">
							<?php echo esc_html( $erfansanat_label ); ?>
						</button>
					<?php endforeach; ?>
					<?php if ( is_string( $archive ) ) : ?>
						<a href="<?php echo esc_url( $archive ); ?>" class="block rounded-lg px-3 py-2 text-[12px] font-bold text-emerald-800 hover:bg-white"><?php esc_html_e( 'همه پروژه‌ها', 'erfansanat' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php
			$columns = array(
				'type' => __( 'بر اساس نوع سازه', 'erfansanat' ),
				'city' => __( 'بر اساس شهر اجرا', 'erfansanat' ),
			);
			foreach ( $columns as $bucket => $heading ) :
				?>
				<div class="p-4 md:col-span-4" data-mega-proj-panel="<?php echo esc_attr( $bucket ); ?>">
					<p class="mb-2 text-[11px] font-black uppercase tracking-wide text-slate-500"><?php echo esc_html( $heading ); ?></p>
					<?php if ( array() === $groups[ $bucket ] ) : ?>
						<p class="text-[11px] text-slate-400"><?php esc_html_e( 'موردی ثبت نشده است.', 'erfansanat' ); ?></p>
					<?php else : ?>
						<ul class="grid grid-cols-2 gap-1">
							<?php foreach ( $groups[ $bucket ] as $item ) : ?>
								<li>
									<a href="<?php echo esc_url( $item['url'] ); ?>" class="block rounded-lg px-2 py-1.5 text-[11.5px] font-semibold text-slate-600 hover:bg-emerald-50 hover:text-emerald-800">
										<?php echo esc_html( $item['title'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Services panel.
	 *
	 * @return void
	 */
	private static function render_services(): void {
		$items   = MenuTree::service_items();
		$archive = get_post_type_archive_link( 'services' );
		?>
		<div class="p-4">
			<div class="mb-2 flex items-center justify-between">
				<p class="text-[11px] font-black uppercase tracking-wide text-emerald-700"><?php esc_html_e( 'خدمات مهندسی و اجرایی', 'erfansanat' ); ?></p>
				<?php if ( is_string( $archive ) ) : ?>
					<a href="<?php echo esc_url( $archive ); ?>" class="text-[11px] font-bold text-emerald-700 hover:underline"><?php esc_html_e( 'همه خدمات', 'erfansanat' ); ?></a>
				<?php endif; ?>
			</div>
			<?php if ( array() === $items ) : ?>
				<p class="text-[11px] text-slate-400"><?php esc_html_e( 'خدمتی منتشر نشده است.', 'erfansanat' ); ?></p>
			<?php else : ?>
				<ul class="space-y-1">
					<?php foreach ( $items as $item ) : ?>
						<li>
							<a href="<?php echo esc_url( $item['url'] ); ?>" class="block rounded-lg px-2.5 py-1.5 text-[12px] font-semibold text-slate-600 hover:bg-emerald-50 hover:text-emerald-800">
								<?php echo esc_html( $item['title'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Corporate panel: links to pages that actually exist.
	 *
	 * @return void
	 */
	private static function render_corporate(): void {
		$items = MenuTree::corporate_items();
		if ( array() === $items ) {
			echo '<p class="p-6 text-xs text-slate-500">' . esc_html__( 'برگه‌های معرفی شرکت یافت نشد.', 'erfansanat' ) . '</p>';
			return;
		}
		?>
		<div class="grid gap-2 p-4 md:grid-cols-2">
			<?php foreach ( $items as $item ) : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="rounded-xl border border-slate-100 p-3 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60">
					<span class="block text-[12.5px] font-black text-slate-900"><?php echo esc_html( $item['title'] ); ?></span>
					<?php if ( '' !== $item['description'] ) : ?>
						<span class="mt-0.5 block text-[10.5px] text-slate-500"><?php echo esc_html( wp_trim_words( $item['description'], 12 ) ); ?></span>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
