<?php
/**
 * Projects archive sidebar: search, type list, city list and a reset control.
 *
 * Every control is a hook `main.js` already binds (`data-project-search`,
 * `data-project-sort`, `data-project-type`, `data-project-location`), and every
 * option is read from the term tables, so a term with no projects never appears.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Term> $erfansanat_types   Published project types.
 * @var array<int, \WP_Term> $erfansanat_cities  Published cities.
 * @var int                  $erfansanat_total   Published project count.
 * @var string               $erfansanat_archive_url Archive permalink.
 * @var \WP_Term|null        $erfansanat_current_type Current type term, if any.
 * @var \WP_Term|null        $erfansanat_current_city Current city term, if any.
 * @var bool                 $erfansanat_drawer True on the mobile copy.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_types        = isset( $erfansanat_types ) && is_array( $erfansanat_types ) ? $erfansanat_types : array();
$erfansanat_cities       = isset( $erfansanat_cities ) && is_array( $erfansanat_cities ) ? $erfansanat_cities : array();
$erfansanat_total        = isset( $erfansanat_total ) ? (int) $erfansanat_total : 0;
$erfansanat_archive_url  = isset( $erfansanat_archive_url ) ? (string) $erfansanat_archive_url : '';
$erfansanat_current_type = isset( $erfansanat_current_type ) && $erfansanat_current_type instanceof \WP_Term ? $erfansanat_current_type : null;
$erfansanat_current_city = isset( $erfansanat_current_city ) && $erfansanat_current_city instanceof \WP_Term ? $erfansanat_current_city : null;
$erfansanat_drawer       = isset( $erfansanat_drawer ) && (bool) $erfansanat_drawer;

$erfansanat_active = static function ( ?\WP_Term $term ): string {
	if ( $term instanceof \WP_Term ) {
		return 'bg-emerald-700 text-white shadow-sm shadow-emerald-700/20';
	}
	return 'text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800';
};
?>
<div class="sticky top-28 space-y-5 rounded-[1.75rem] border border-slate-200/90 bg-white p-5 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]">
	<div class="flex items-center justify-between border-b border-slate-100 pb-4">
		<div class="flex items-center gap-2.5">
			<span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path></svg>
			</span>
			<div>
				<h2 class="text-[14.5px] font-black text-slate-900"><?php esc_html_e( 'فیلتر و آرشیو پروژه‌ها', 'erfansanat' ); ?></h2>
				<p class="text-[11.5px] font-semibold text-slate-500">
					<span data-project-count="true"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) ); ?></span>
					<?php esc_html_e( 'پروژه در این آرشیو', 'erfansanat' ); ?>
				</p>
			</div>
		</div>
		<?php if ( ! $erfansanat_drawer ) : ?>
			<button type="button" data-archive-drawer-open="true" class="cursor-pointer rounded-lg px-2 py-1 text-[11px] font-bold text-emerald-700 hover:bg-emerald-50 lg:hidden"><?php esc_html_e( 'فیلتر', 'erfansanat' ); ?></button>
		<?php endif; ?>
	</div>

	<div>
		<label for="project-sidebar-search<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" class="mb-2 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-700" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.34-4.34"></path></svg>
			<span><?php esc_html_e( 'جستجو در پروژه‌ها', 'erfansanat' ); ?></span>
		</label>
		<div class="relative">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
			<input id="project-sidebar-search<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" type="search" data-project-search="true" placeholder="<?php esc_attr_e( 'نام پروژه، شهر یا نوع...', 'erfansanat' ); ?>" value="" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2.5 pl-3.5 pr-10 text-[13px] font-medium text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none" />
		</div>
	</div>

	<div class="border-t border-slate-100 pt-4">
		<div class="mb-3 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path></svg>
			<span><?php esc_html_e( 'نوع پروژه', 'erfansanat' ); ?></span>
		</div>
		<nav aria-label="<?php esc_attr_e( 'آرشیو نوع پروژه', 'erfansanat' ); ?>" class="space-y-1.5">
			<a href="<?php echo esc_url( $erfansanat_archive_url ); ?>" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all <?php echo null === $erfansanat_current_type ? 'bg-emerald-700 text-white shadow-sm shadow-emerald-700/20' : 'text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800'; ?>">
				<span class="flex items-center gap-2">
					<span class="h-2 w-2 rounded-full <?php echo null === $erfansanat_current_type ? 'bg-white' : 'bg-emerald-500/60'; ?>"></span>
					<span><?php esc_html_e( 'همه انواع پروژه‌ها', 'erfansanat' ); ?></span>
				</span>
				<span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold <?php echo null === $erfansanat_current_type ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'; ?>"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) ); ?></span>
			</a>
			<?php foreach ( $erfansanat_types as $erfansanat_type ) : ?>
				<a href="<?php echo esc_url( \ErfanSanat\Theme\term_url( $erfansanat_type ) ); ?>" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all <?php echo esc_attr( $erfansanat_active( $erfansanat_current_type ) ); ?>">
					<span class="flex items-center gap-2">
						<span class="h-2 w-2 rounded-full <?php echo $erfansanat_current_type instanceof \WP_Term && (int) $erfansanat_current_type->term_id === (int) $erfansanat_type->term_id ? 'bg-white' : 'bg-emerald-500/60'; ?>"></span>
						<span><?php echo esc_html( $erfansanat_type->name ); ?></span>
					</span>
					<span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold <?php echo $erfansanat_current_type instanceof \WP_Term && (int) $erfansanat_current_type->term_id === (int) $erfansanat_type->term_id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'; ?>"><?php echo esc_html( SectionParts::fa_digits( (string) (int) $erfansanat_type->count ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>

	<?php if ( array() !== $erfansanat_cities ) : ?>
		<div class="border-t border-slate-100 pt-4">
			<div class="mb-3 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
				<span><?php esc_html_e( 'شهر اجرا', 'erfansanat' ); ?></span>
			</div>
			<nav aria-label="<?php esc_attr_e( 'آرشیو شهر', 'erfansanat' ); ?>" class="flex flex-wrap gap-1.5">
				<?php foreach ( $erfansanat_cities as $erfansanat_city ) : ?>
					<a href="<?php echo esc_url( \ErfanSanat\Theme\term_url( $erfansanat_city ) ); ?>" class="rounded-full border px-3 py-1.5 text-[11.5px] font-bold transition-colors <?php echo $erfansanat_current_city instanceof \WP_Term && (int) $erfansanat_current_city->term_id === (int) $erfansanat_city->term_id ? 'border-emerald-700 bg-emerald-700 text-white' : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-emerald-300 hover:text-emerald-800'; ?>">
						<?php echo esc_html( $erfansanat_city->name ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
	<?php endif; ?>

	<div class="border-t border-slate-100 pt-4">
		<button type="button" data-project-reset="true" class="w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12px] font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-white hover:text-emerald-800">
			<?php esc_html_e( 'حذف همهٔ فیلترها', 'erfansanat' ); ?>
		</button>
	</div>
</div>
