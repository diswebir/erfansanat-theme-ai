<?php
/** HPOS-safe customer orders table with native endpoint actions and paging. */
/** @version 9.5.0 */

defined( 'ABSPATH' ) || exit;
$button_class = isset( $wp_button_class ) ? (string) $wp_button_class : '';
do_action( 'woocommerce_before_account_orders', $has_orders );
?>
<?php if ( $has_orders ) : ?>
	<div class="overflow-x-auto"><table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table w-full"><thead><tr><?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?><th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th><?php endforeach; ?></tr></thead><tbody>
	<?php foreach ( $customer_orders->orders as $customer_order ) : $order = wc_get_order( $customer_order ); if ( ! $order ) { continue; } $item_count = max( 0, $order->get_item_count() - $order->get_item_count_refunded() ); ?><tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order"><?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : $is_order_number = 'order-number' === $column_id; ?><<?php echo $is_order_number ? 'th' : 'td'; ?> class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>" <?php echo $is_order_number ? 'scope="row"' : ''; ?>>
		<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>
		<?php elseif ( $is_order_number ) : ?><a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View order number %s', 'woocommerce' ), $order->get_order_number() ) ); ?>"><?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?></a>
		<?php elseif ( 'order-date' === $column_id ) : ?><?php $date = $order->get_date_created(); if ( $date ) : ?><time datetime="<?php echo esc_attr( $date->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $date ) ); ?></time><?php endif; ?>
		<?php elseif ( 'order-status' === $column_id ) : ?><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
		<?php elseif ( 'order-total' === $column_id ) : ?><?php echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) ); ?>
		<?php elseif ( 'order-actions' === $column_id ) : ?><?php foreach ( wc_get_account_orders_actions( $order ) as $key => $action ) : $aria_label = $action['aria-label'] ?? sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() ); ?><a href="<?php echo esc_url( $action['url'] ); ?>" class="woocommerce-button<?php echo esc_attr( $button_class ); ?> button <?php echo esc_attr( sanitize_html_class( $key ) ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php echo esc_html( $action['name'] ); ?></a><?php endforeach; ?>
		<?php endif; ?>
		</<?php echo $is_order_number ? 'th' : 'td'; ?>><?php endforeach; ?></tr><?php endforeach; ?>
	</tbody></table></div>
	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>
	<?php if ( 1 < $customer_orders->max_num_pages ) : ?><nav class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination mt-5 flex justify-between" aria-label="صفحه‌بندی سفارش‌ها"><?php if ( 1 !== $current_page ) : ?><a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a><?php endif; ?><?php if ( (int) $customer_orders->max_num_pages !== (int) $current_page ) : ?><a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a><?php endif; ?></nav><?php endif; ?>
<?php else : ?>
	<?php wc_print_notice( esc_html__( 'هنوز سفارشی ثبت نکرده‌اید.', 'erfansanat-theme' ) . ' <a class="woocommerce-Button wc-forward button' . esc_attr( $button_class ) . '" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">' . esc_html__( 'مشاهده محصولات', 'erfansanat-theme' ) . '</a>', 'notice' ); ?>
<?php endif; ?>
<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
