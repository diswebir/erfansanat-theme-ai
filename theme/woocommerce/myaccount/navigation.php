<?php
/** Account endpoint navigation built from WooCommerce's live menu registry. */
/** @version 9.3.0 */

defined( 'ABSPATH' ) || exit;
$account_menu_items = wc_get_account_menu_items();
?>
<nav class="woocommerce-MyAccount-navigation rounded-2xl border border-slate-200 bg-white p-3" aria-label="منوی حساب کاربری"><ul class="space-y-1"><?php foreach ( $account_menu_items as $endpoint => $label ) : $is_current = wc_is_current_account_menu_item( $endpoint ); ?><li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--<?php echo esc_attr( $endpoint ); ?>"><a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo $is_current ? 'aria-current="page"' : ''; ?> class="block rounded-xl px-4 py-3 text-sm font-bold transition-colors <?php echo $is_current ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-800'; ?>"><?php echo esc_html( $label ); ?></a></li><?php endforeach; ?></ul></nav>
