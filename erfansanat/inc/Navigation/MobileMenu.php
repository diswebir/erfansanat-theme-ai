<?php
/**
 * Mobile drawer: accordion blocks, plain links and a close control.
 *
 * The markup follows the reference contract (`data-mobile-menu`,
 * `data-mobile-backdrop`, `data-mobile-sidebar`, `data-mobile-close`,
 * `data-mobile-acc-btn`, `data-mobile-acc-panel`) so the existing main.js keeps
 * handling open/close and the accordion. Every accordion button is a real
 * button with `aria-expanded` and `aria-controls`, which is what makes the
 * drawer usable with a keyboard alone.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Navigation;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the off-canvas menu.
 */
final class MobileMenu {

	/**
	 * Render the drawer.
	 *
	 * @param array<int, array<string, mixed>> $tree Primary menu tree.
	 * @return void
	 */
	public static function render( array $tree = array() ): void {
		$home = home_url( '/' );
		?>
		<div data-mobile-menu="true" class="fixed inset-0 z-[90] lg:hidden hidden" aria-hidden="true">
			<div data-mobile-backdrop="true" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>
			<aside data-mobile-sidebar="true"
				aria-label="<?php esc_attr_e( 'منوی موبایل', 'erfansanat' ); ?>"
				role="dialog"
				aria-modal="true"
				class="fixed inset-y-0 right-0 z-[95] flex h-dvh max-h-dvh w-[88vw] max-w-[380px] flex-col border-l border-emerald-100 bg-white shadow-2xl">
				<div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-3.5">
					<a href="<?php echo esc_url( $home ); ?>" class="flex items-center gap-2.5">
						<span class="erfansanat-brand__mark grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-black text-emerald-800">
							<?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_mark', 'ESE' ) ); ?>
						</span>
						<span>
							<span class="block text-sm font-black text-slate-900"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_name', '' ) ); ?></span>
							<span class="block text-[10px] font-semibold text-emerald-700"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_tagline', '' ) ); ?></span>
						</span>
					</a>
					<button type="button" data-mobile-close="true" aria-label="<?php esc_attr_e( 'بستن منو', 'erfansanat' ); ?>"
						class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 cursor-pointer">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
					</button>
				</div>

				<div class="flex-1 space-y-2.5 overflow-y-auto overscroll-contain px-3.5 py-3.5">
					<a href="<?php echo esc_url( $home ); ?>" class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-3 text-xs font-black text-emerald-900">
						<span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></span>
					</a>
					<?php
					$blocks = MenuTree::blocks();
					foreach ( $blocks as $block ) {
						if ( ! MenuTree::block_has_content( $block ) ) {
							continue;
						}
						self::render_accordion( $block );
					}
					self::render_plain_links( $tree );
					?>
				</div>

				<div class="shrink-0 space-y-2 border-t border-slate-100 px-3.5 py-3.5">
					<?php
					$phone = (string) \ErfanSanat\Theme\Settings::get( 'phone_primary', '' );
					$mail  = (string) \ErfanSanat\Theme\Settings::get( 'email', '' );
					?>
					<?php if ( '' !== $phone ) : ?>
						<a href="<?php echo esc_url( \ErfanSanat\Theme\Frontend\SiteParts::phone_link( $phone ) ); ?>"
							class="flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3 py-2.5 text-xs font-black text-white hover:bg-emerald-700" dir="ltr">
							<?php echo esc_html( $phone ); ?>
						</a>
					<?php endif; ?>
					<?php if ( '' !== $mail && is_email( $mail ) ) : ?>
						<a href="<?php echo esc_url( 'mailto:' . $mail ); ?>"
							class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50" dir="ltr">
							<?php echo esc_html( $mail ); ?>
						</a>
					<?php endif; ?>
				</div>
			</aside>
		</div>
		<?php
	}

	/**
	 * One accordion block.
	 *
	 * @param string $block Block name.
	 * @return void
	 */
	private static function render_accordion( string $block ): void {
		$labels = MenuTree::block_labels();
		$label  = $labels[ $block ] ?? $block;
		$panel  = 'erfansanat-mobile-panel-' . $block;
		$button = 'erfansanat-mobile-button-' . $block;
		?>
		<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white">
			<button type="button"
				id="<?php echo esc_attr( $button ); ?>"
				data-mobile-acc-btn="<?php echo esc_attr( $block ); ?>"
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $panel ); ?>"
				class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50">
				<span class="text-xs font-black text-slate-900"><?php echo esc_html( $label ); ?></span>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-slate-500 transition-transform duration-200" aria-hidden="true" data-mobile-acc-icon="true"><path d="m6 9 6 6 6-6"></path></svg>
			</button>
			<div id="<?php echo esc_attr( $panel ); ?>"
				data-mobile-acc-panel="<?php echo esc_attr( $block ); ?>"
				role="region"
				aria-labelledby="<?php echo esc_attr( $button ); ?>"
				class="hidden flex-col gap-2.5 border-t border-slate-100 bg-slate-50/50 px-3.5 py-3">
				<?php self::render_panel_items( $block ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Product tabs for the mobile drawer.
	 *
	 * @return void
	 */
	private static function render_product_tabs(): void {
		$tabs = MenuTree::product_tabs();
		if ( array() === $tabs ) {
			echo '<p class="text-[11px] text-slate-400">' . esc_html__( 'موردی برای نمایش وجود ندارد.', 'erfansanat' ) . '</p>';
			return;
		}
		?>
		<div class="grid grid-cols-2 gap-1">
			<?php foreach ( $tabs as $index => $tab ) : ?>
				<button type="button"
					data-mobile-prod-tab="<?php echo (int) $index; ?>"
					class="truncate rounded-lg border px-2.5 py-1.5 text-center text-[11px] font-extrabold transition-all cursor-pointer <?php echo 0 === $index ? 'border-emerald-600 bg-emerald-700 text-white' : 'border-slate-200 bg-white text-slate-600'; ?>">
					<?php echo esc_html( $tab['label'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<?php foreach ( $tabs as $index => $tab ) : ?>
			<div data-mobile-prod-panel="<?php echo (int) $index; ?>" class="<?php echo 0 === $index ? 'flex' : 'hidden'; ?> flex-col gap-1">
				<?php if ( array() === $tab['items'] ) : ?>
					<a href="<?php echo esc_url( $tab['url'] ); ?>" class="rounded-lg bg-white px-3 py-2 text-[11.5px] font-bold text-emerald-800"><?php esc_html_e( 'مشاهده همه محصولات این دسته', 'erfansanat' ); ?></a>
				<?php else : ?>
					<?php foreach ( $tab['items'] as $item ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" class="rounded-lg bg-white px-3 py-2 text-[11.5px] font-bold text-slate-600 hover:text-emerald-800">
							<?php echo esc_html( $item['title'] ); ?>
						</a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Project type/city tabs for the mobile drawer.
	 *
	 * @return void
	 */
	private static function render_project_tabs(): void {
		$groups = MenuTree::project_groups();
		$labels = array(
			'type' => __( 'بر اساس نوع سازه', 'erfansanat' ),
			'city' => __( 'بر اساس شهر اجرا', 'erfansanat' ),
		);
		?>
		<div class="grid grid-cols-2 gap-1 rounded-lg bg-slate-200/70 p-1">
			<?php foreach ( $labels as $mode => $label ) : ?>
				<button type="button"
					data-mobile-proj-tab="<?php echo esc_attr( $mode ); ?>"
					class="rounded-md py-1.5 text-[11px] font-black transition-all cursor-pointer <?php echo 'type' === $mode ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600'; ?>">
					<?php echo esc_html( $label ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<?php foreach ( $labels as $mode => $label ) : ?>
			<div data-mobile-proj-panel="<?php echo esc_attr( $mode ); ?>" class="<?php echo 'type' === $mode ? 'flex' : 'hidden'; ?> flex-col gap-1">
				<?php if ( array() === $groups[ $mode ] ) : ?>
					<p class="text-[11px] text-slate-400"><?php esc_html_e( 'موردی ثبت نشده است.', 'erfansanat' ); ?></p>
				<?php else : ?>
					<?php foreach ( $groups[ $mode ] as $item ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" class="rounded-lg bg-white px-3 py-2 text-[11.5px] font-bold text-slate-600 hover:text-emerald-800">
							<?php echo esc_html( $item['title'] ); ?>
						</a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Items inside one accordion panel.
	 *
	 * @param string $block Block name.
	 * @return void
	 */
	private static function render_panel_items( string $block ): void {
		// Products and projects mirror the desktop panels: a tab rail on top
		// and one panel per tab, using the same data-* hooks main.js binds.
		if ( 'products' === $block ) {
			self::render_product_tabs();
			return;
		}
		if ( 'projects' === $block ) {
			self::render_project_tabs();
			return;
		}

		$links = array();
		switch ( $block ) {
			case 'services':
				$links = MenuTree::service_items();
				break;
			case 'corporate':
				foreach ( MenuTree::corporate_items() as $item ) {
					$links[] = array(
						'title' => $item['title'],
						'url'   => $item['url'],
					);
				}
				break;
		}

		if ( array() === $links ) {
			echo '<p class="text-[11px] text-slate-400">' . esc_html__( 'موردی برای نمایش وجود ندارد.', 'erfansanat' ) . '</p>';
			return;
		}
		foreach ( $links as $link ) {
			if ( '' === (string) $link['url'] ) {
				continue;
			}
			?>
			<a href="<?php echo esc_url( (string) $link['url'] ); ?>" class="rounded-lg bg-white px-3 py-2 text-[11.5px] font-bold text-slate-600 hover:text-emerald-800">
				<?php echo esc_html( (string) $link['title'] ); ?>
			</a>
			<?php
		}
	}

	/**
	 * Plain links from the assigned primary menu.
	 *
	 * Nested levels are flattened with an indentation marker so the drawer stays
	 * one level deep, which is what the reference does.
	 *
	 * @param array<int, array<string, mixed>> $tree Menu tree.
	 * @param int                              $depth Current depth.
	 * @return void
	 */
	private static function render_plain_links( array $tree, int $depth = 0 ): void {
		foreach ( $tree as $node ) {
			if ( ! is_array( $node ) || ! isset( $node['title'] ) ) {
				continue;
			}
			$title = (string) $node['title'];
			$url   = isset( $node['url'] ) ? (string) $node['url'] : '';
			if ( '' === $url ) {
				continue;
			}
			$pad = 0 === $depth ? '' : ' pr-3';
			?>
			<a href="<?php echo esc_url( $url ); ?>"
				class="flex items-center justify-between rounded-xl border border-slate-200/80 px-3.5 py-2.5 text-xs font-black text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50<?php echo esc_attr( $pad ); ?>">
				<span><?php echo esc_html( $title ); ?></span>
			</a>
			<?php
			if ( ! empty( $node['children'] ) && is_array( $node['children'] ) && $depth < 2 ) {
				self::render_plain_links( $node['children'], $depth + 1 );
			}
		}
	}
}
