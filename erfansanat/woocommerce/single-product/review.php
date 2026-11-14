<?php
/**
 * Single product review card (P06-T014). WooCommerce review template override
 * matching the reference DOM. Reads the real comment + `rating` / `es_review_org`
 * comment meta. Used by template-parts/product/reviews.php.
 *
 * @package ErfanSanat
 * @var WP_Comment $comment
 */

defined( 'ABSPATH' ) || exit;

$es_comment = isset( $comment ) ? $comment : ( isset( $args['comment'] ) ? $args['comment'] : null );
if ( ! $es_comment instanceof WP_Comment ) {
	return;
}

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

$es_rating = (int) get_comment_meta( $es_comment->comment_ID, 'rating', true );
$es_rating = max( 0, min( 5, $es_rating ) );
$es_org    = (string) get_comment_meta( $es_comment->comment_ID, 'es_review_org', true );
$es_author = get_comment_author( $es_comment );
$es_date   = $es_fa( get_comment_date( 'Y/m/d', $es_comment ) );
?>
<div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs" data-review-item>
	<div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
		<div>
			<div class="text-sm font-black text-slate-900"><?php echo esc_html( $es_author ); ?></div>
			<?php if ( '' !== $es_org ) : ?>
				<div class="mt-0.5 text-[11px] font-bold text-emerald-700"><?php echo esc_html( $es_org ); ?></div>
			<?php endif; ?>
		</div>
		<div class="flex items-center gap-3">
			<div class="flex items-center gap-0.5" aria-label="<?php echo esc_attr( sprintf( 'امتیاز %s از ۵', $es_fa( $es_rating ) ) ); ?>">
				<?php for ( $es_s = 1; $es_s <= 5; $es_s++ ) : ?>
					<svg class="h-3.5 w-3.5 <?php echo $es_s <= $es_rating ? 'fill-amber-400 text-amber-400' : 'text-slate-200'; ?>" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
				<?php endfor; ?>
			</div>
			<span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[10.5px] font-bold text-slate-500 tabular-nums"><?php echo esc_html( $es_date ); ?></span>
		</div>
	</div>
	<p class="mt-3 text-[13px] leading-7 font-medium text-slate-600"><?php echo wp_kses_post( get_comment_text( $es_comment ) ); ?></p>
</div>
