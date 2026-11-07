<?php
/** Secure downloadable product catalog file, omitted when the field is empty. */

defined( 'ABSPATH' ) || exit;
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $args['product'] ?? null );
if ( ! $product_id ) { return; }
$raw = \ErfanSanat\Theme\Woo\ProductTemplate::meta( $product_id, 'product-catalog-file', '' );
$url = \ErfanSanat\Theme\Woo\ProductTemplate::url( $raw );
if ( '' === $url ) { return; }
$file_size = '';
$attachment_id = function_exists( 'attachment_url_to_postid' ) ? absint( attachment_url_to_postid( $url ) ) : 0;
$file_path = $attachment_id && function_exists( 'get_attached_file' ) ? get_attached_file( $attachment_id ) : '';
if ( is_string( $file_path ) && is_file( $file_path ) && function_exists( 'size_format' ) ) { $file_size = size_format( (int) filesize( $file_path ) ); }
$label = function_exists( 'wp_basename' ) ? wp_basename( (string) $raw ) : basename( (string) $raw );
?>
<section class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5" aria-label="دانلود کاتالوگ محصول"><div class="flex flex-wrap items-center justify-between gap-4"><div><h3 class="text-sm font-extrabold text-slate-900">کاتالوگ محصول</h3><p class="mt-1 text-xs text-slate-600"><?php echo esc_html( $label ); ?><?php if ( '' !== $file_size ) : ?><span> — <?php echo esc_html( $file_size ); ?></span><?php endif; ?></p></div><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" download class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-4 py-2.5 text-xs font-extrabold text-white">دانلود فایل</a></div></section>
