<?php
/**
 * Shared blog category/search-reset controls (P07-T011).
 * Home: client-side category buttons for main.js. Scoped archives: real category
 * links so choosing another category never searches an incomplete collection.
 *
 * @package ErfanSanat
 * @var array $args { @type array $categories @type string $context @type WP_Term|null $term @type string $blog_url @type string $variant }
 */

defined( 'ABSPATH' ) || exit;

$es_categories = (array) ( $args['categories'] ?? array() );
$es_context    = (string) ( $args['context'] ?? 'home' );
$es_term       = isset( $args['term'] ) && $args['term'] instanceof WP_Term ? $args['term'] : null;
$es_blog_url   = (string) ( $args['blog_url'] ?? home_url( '/blog/' ) );
$es_variant    = (string) ( $args['variant'] ?? 'hero' );
$es_toolbar    = 'toolbar' === $es_variant;
$es_base       = $es_toolbar ? 'rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors' : 'rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer';
$es_active     = $es_toolbar ? 'bg-emerald-700 text-white' : 'bg-emerald-700 text-white shadow-md shadow-emerald-700/25';
$es_inactive   = $es_toolbar ? 'border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300' : 'border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700';
$es_all_active = null === $es_term;
?>
<?php if ( $es_toolbar ) : ?>
	<span class="flex items-center gap-1.5 text-[12px] font-extrabold text-slate-600"><svg aria-hidden="true" class="lucide lucide-layers h-4 w-4 text-emerald-700" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg><span><?php esc_html_e( 'آرشیو دسته‌بندی‌ها:', 'erfansanat' ); ?></span></span>
<?php endif; ?>

<?php if ( 'home' === $es_context && ! $es_toolbar ) : ?>
	<button class="<?php echo esc_attr( $es_base . ' ' . $es_active ); ?>" data-blog-cat-btn="<?php esc_attr_e( 'همه', 'erfansanat' ); ?>" type="button"><?php esc_html_e( 'همه', 'erfansanat' ); ?></button>
<?php else : ?>
	<a class="<?php echo esc_attr( $es_base . ' ' . ( $es_all_active ? $es_active : $es_inactive ) ); ?>" href="<?php echo esc_url( $es_blog_url ); ?>"><?php echo esc_html( $es_toolbar ? __( 'همه مطالب', 'erfansanat' ) : __( 'همه', 'erfansanat' ) ); ?></a>
<?php endif; ?>

<?php foreach ( $es_categories as $es_category ) : ?>
	<?php $es_selected = $es_term && (int) $es_term->term_id === (int) $es_category->term_id; ?>
	<?php if ( 'home' === $es_context && ! $es_toolbar ) : ?>
		<button class="<?php echo esc_attr( $es_base . ' ' . ( $es_selected ? $es_active : $es_inactive ) ); ?>" data-blog-cat-btn="<?php echo esc_attr( $es_category->name ); ?>" type="button"><?php echo esc_html( $es_category->name ); ?></button>
	<?php else : ?>
		<a class="<?php echo esc_attr( $es_base . ' ' . ( $es_selected ? $es_active : $es_inactive ) ); ?>" href="<?php echo esc_url( get_category_link( $es_category ) ); ?>"><?php echo esc_html( $es_category->name ); ?></a>
	<?php endif; ?>
<?php endforeach; ?>

<?php if ( $es_toolbar ) : ?>
	<button class="<?php echo $es_all_active ? 'hidden' : 'inline-flex'; ?> rounded-xl border border-slate-200 px-4 py-2 text-[12px] font-bold text-slate-600 transition-colors hover:border-emerald-400 hover:text-emerald-700 cursor-pointer" data-blog-reset="true" type="button"><?php esc_html_e( 'حذف فیلترها', 'erfansanat' ); ?></button>
<?php endif; ?>
