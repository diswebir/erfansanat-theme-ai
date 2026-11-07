<?php
/**
 * Native (no-JavaScript) archive filter form.
 *
 * The reference filters with JavaScript. That is fine until a script fails to
 * load, a crawler visits, or a shopper on a locked-down browser arrives; then a
 * purely client-side filter leaves them on a page that cannot be narrowed at
 * all. This form is the floor: a plain GET form whose arguments
 * `ArchiveFilters` reads on the server, so filtering works with JavaScript off.
 *
 * It is hidden when scripting is available (`hidden` is removed by the script
 * itself only when it takes over), so a JavaScript visitor never sees two filter
 * controls doing the same job.
 *
 * @package ErfanSanat
 *
 * @var string $erfansanat_filter_kind       `shop` or `projects`.
 * @var array<int, \WP_Term> $erfansanat_filter_terms  Category / type terms.
 * @var array<int, \WP_Term> $erfansanat_filter_places City terms, projects only.
 * @var string $erfansanat_filter_action     Form target.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\ArchiveFilters;

$erfansanat_filter_kind   = isset( $erfansanat_filter_kind ) ? (string) $erfansanat_filter_kind : 'projects';
$erfansanat_filter_terms  = isset( $erfansanat_filter_terms ) && is_array( $erfansanat_filter_terms ) ? $erfansanat_filter_terms : array();
$erfansanat_filter_places = isset( $erfansanat_filter_places ) && is_array( $erfansanat_filter_places ) ? $erfansanat_filter_places : array();
$erfansanat_filter_action = isset( $erfansanat_filter_action ) ? (string) $erfansanat_filter_action : home_url( '/' );

$erfansanat_is_shop  = 'shop' === $erfansanat_filter_kind;
$erfansanat_term_key = $erfansanat_is_shop ? 'product_cat' : 'project_type';
?>
<form method="get"
	action="<?php echo esc_url( $erfansanat_filter_action ); ?>"
	data-native-filters="true"
	class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs"
	aria-label="<?php esc_attr_e( 'فیلتر نتایج', 'erfansanat' ); ?>">
	<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
		<label class="block">
			<span class="mb-1.5 block text-[11.5px] font-extrabold text-slate-600"><?php echo esc_html( $erfansanat_is_shop ? __( 'دسته‌بندی محصول', 'erfansanat' ) : __( 'نوع پروژه', 'erfansanat' ) ); ?></span>
			<select name="<?php echo esc_attr( $erfansanat_term_key ); ?>" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-[12.5px] font-semibold text-slate-800">
				<option value=""><?php esc_html_e( 'همه', 'erfansanat' ); ?></option>
				<?php foreach ( $erfansanat_filter_terms as $erfansanat_filter_term ) : ?>
					<?php if ( ! $erfansanat_filter_term instanceof \WP_Term ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<option value="<?php echo esc_attr( (string) $erfansanat_filter_term->slug ); ?>" <?php selected( ArchiveFilters::param( $erfansanat_term_key ), (string) $erfansanat_filter_term->slug ); ?>>
						<?php echo esc_html( (string) $erfansanat_filter_term->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>

		<?php if ( ! $erfansanat_is_shop && array() !== $erfansanat_filter_places ) : ?>
			<label class="block">
				<span class="mb-1.5 block text-[11.5px] font-extrabold text-slate-600"><?php esc_html_e( 'شهر', 'erfansanat' ); ?></span>
				<select name="city" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-[12.5px] font-semibold text-slate-800">
					<option value=""><?php esc_html_e( 'همه شهرها', 'erfansanat' ); ?></option>
					<?php foreach ( $erfansanat_filter_places as $erfansanat_filter_place ) : ?>
						<?php if ( ! $erfansanat_filter_place instanceof \WP_Term ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<option value="<?php echo esc_attr( (string) $erfansanat_filter_place->slug ); ?>" <?php selected( ArchiveFilters::param( 'city' ), (string) $erfansanat_filter_place->slug ); ?>>
							<?php echo esc_html( (string) $erfansanat_filter_place->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		<?php endif; ?>

		<?php if ( $erfansanat_is_shop ) : ?>
			<label class="block">
				<span class="mb-1.5 block text-[11.5px] font-extrabold text-slate-600"><?php esc_html_e( 'بازهٔ قیمت', 'erfansanat' ); ?></span>
				<select name="es_price" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-[12.5px] font-semibold text-slate-800">
					<option value=""><?php esc_html_e( 'همهٔ قیمت‌ها', 'erfansanat' ); ?></option>
					<?php foreach ( ArchiveFilters::price_bands() as $erfansanat_band_key => $erfansanat_band ) : ?>
						<option value="<?php echo esc_attr( (string) $erfansanat_band_key ); ?>" <?php selected( ArchiveFilters::param( 'es_price' ), (string) $erfansanat_band_key ); ?>>
							<?php echo esc_html( (string) $erfansanat_band[2] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		<?php endif; ?>

		<label class="block">
			<span class="mb-1.5 block text-[11.5px] font-extrabold text-slate-600"><?php esc_html_e( 'ترتیب', 'erfansanat' ); ?></span>
			<select name="es_sort" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-[12.5px] font-semibold text-slate-800">
				<?php foreach ( ArchiveFilters::sort_options() as $erfansanat_sort_key => $erfansanat_sort_label ) : ?>
					<option value="<?php echo esc_attr( (string) $erfansanat_sort_key ); ?>" <?php selected( ArchiveFilters::param( 'es_sort' ), (string) $erfansanat_sort_key ); ?>>
						<?php echo esc_html( (string) $erfansanat_sort_label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>

	<div class="mt-3 flex flex-wrap items-center gap-2">
		<button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2 text-[12.5px] font-extrabold text-white transition-colors hover:bg-emerald-800"><?php esc_html_e( 'اعمال فیلتر', 'erfansanat' ); ?></button>
		<a href="<?php echo esc_url( $erfansanat_filter_action ); ?>" class="rounded-xl border border-slate-200 px-4 py-2 text-[12.5px] font-extrabold text-slate-600 transition-colors hover:border-emerald-300 hover:text-emerald-800"><?php esc_html_e( 'حذف فیلترها', 'erfansanat' ); ?></a>
	</div>

	<?php $erfansanat_active_filters = ArchiveFilters::active(); ?>
	<?php if ( array() !== $erfansanat_active_filters ) : ?>
		<div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-200/80 pt-3">
			<?php foreach ( $erfansanat_active_filters as $erfansanat_active_filter ) : ?>
				<a href="<?php echo esc_url( (string) $erfansanat_active_filter['url'] ); ?>" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11.5px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-100">
					<span><?php echo esc_html( (string) $erfansanat_active_filter['label'] ); ?></span>
					<span aria-hidden="true">×</span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</form>
