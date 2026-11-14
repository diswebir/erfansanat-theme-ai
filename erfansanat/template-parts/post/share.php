<?php
/**
 * Single-post share row (P07-T014). Reference: tags + data-share-btn row in
 * blog/types-of-light-tunnel.html. The copy button keeps the immutable
 * main.js contract (initShareButtons); encoded social links and a readonly
 * URL fallback are additive. Hidden for password-protected posts.
 *
 * @package ErfanSanat
 * @var array $args { @type int $post_id }
 */

defined( 'ABSPATH' ) || exit;

$es_post_id = (int) ( $args['post_id'] ?? get_the_ID() );
if ( $es_post_id <= 0 || post_password_required( $es_post_id ) ) {
	return;
}

$es_url       = (string) get_permalink( $es_post_id );
$es_title     = get_the_title( $es_post_id );
$es_tags      = get_the_tags( $es_post_id ) ?: array();
$es_enc_url   = rawurlencode( $es_url );
$es_enc_title = rawurlencode( $es_title );
$es_networks  = array(
	'telegram' => array(
		'label' => __( 'اشتراک در تلگرام', 'erfansanat' ),
		'href'  => 'https://t.me/share/url?url=' . $es_enc_url . '&text=' . $es_enc_title,
		'icon'  => '<path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path>',
	),
	'whatsapp' => array(
		'label' => __( 'اشتراک در واتس‌اپ', 'erfansanat' ),
		'href'  => 'https://wa.me/?text=' . $es_enc_title . '%20' . $es_enc_url,
		'icon'  => '<path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"></path>',
	),
	'twitter'  => array(
		'label' => __( 'اشتراک در ایکس', 'erfansanat' ),
		'href'  => 'https://twitter.com/intent/tweet?url=' . $es_enc_url . '&text=' . $es_enc_title,
		'icon'  => '<path d="M4 4l16 16"></path><path d="M20 4 4 20"></path>',
	),
	'linkedin' => array(
		'label' => __( 'اشتراک در لینکدین', 'erfansanat' ),
		'href'  => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $es_enc_url,
		'icon'  => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle>',
	),
);
$es_networks = apply_filters( 'es_post_share_networks', $es_networks, $es_post_id );
?>
<div class="mt-12 flex flex-col gap-5 rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs sm:flex-row sm:items-center sm:justify-between">
	<?php if ( $es_tags ) : ?>
		<div class="flex flex-wrap items-center gap-2">
			<span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'برچسب‌ها:', 'erfansanat' ); ?></span>
			<?php foreach ( $es_tags as $es_tag ) : ?>
				<a class="rounded-full bg-emerald-50 px-3 py-1.5 text-[12px] font-bold text-emerald-800 transition-colors hover:bg-emerald-100" href="<?php echo esc_url( get_tag_link( $es_tag ) ); ?>"><?php echo esc_html( $es_tag->name ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="flex flex-wrap items-center gap-2"><span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'این مقاله را به اشتراک بگذارید:', 'erfansanat' ); ?></span></div>
	<?php endif; ?>
	<div class="flex flex-wrap items-center gap-4">
		<div class="flex items-center gap-2.5">
			<?php foreach ( $es_networks as $es_key => $es_network ) : ?>
				<a href="<?php echo esc_url( $es_network['href'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $es_network['label'] ); ?>" data-share-network="<?php echo esc_attr( $es_key ); ?>" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-slate-500 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><?php echo $es_network['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed icon markup. ?></svg></a>
			<?php endforeach; ?>
		</div>
		<button type="button" data-share-btn="true" class="flex items-center gap-2.5 cursor-pointer text-slate-500 transition-colors hover:text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-share2 lucide-share-2 h-4.5 w-4.5 text-slate-400" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg><span data-share-label="true" class="text-[13px] font-bold"><?php esc_html_e( 'اشتراک‌گذاری مقاله', 'erfansanat' ); ?></span></button>
	</div>
	<label class="sr-only" for="es-share-url-<?php echo (int) $es_post_id; ?>"><?php esc_html_e( 'لینک مقاله برای کپی دستی', 'erfansanat' ); ?></label>
	<input id="es-share-url-<?php echo (int) $es_post_id; ?>" data-share-url="true" type="text" readonly value="<?php echo esc_url( $es_url ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-medium text-slate-500" onclick="this.select()"/>
</div>
