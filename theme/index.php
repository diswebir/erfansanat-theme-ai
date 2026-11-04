<?php
/** Generic posts, projects, and search fallback template. */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="bg-slate-50/50 pb-28 lg:pb-0">
	<div class="mx-auto max-w-[1300px] px-4 py-16 sm:px-6 lg:px-8">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					$post_object = get_post();
					$post_id = (int) get_the_ID();
					$post_type = (string) get_post_type( $post_id );
					$card_type = in_array( $post_type, array( 'product', 'projects' ), true ) ? ( 'projects' === $post_type ? 'project' : 'product' ) : 'post';
					$card_args = array( 'post' => $post_object, 'post_id' => $post_id );

					if ( 'project' === $card_type ) {
						$city_terms = get_the_terms( $post_id, 'city' );
					$type_terms = get_the_terms( $post_id, 'project_type' );
					$city = is_array( $city_terms ) && isset( $city_terms[0]->name ) ? (string) $city_terms[0]->name : '';
					$type = is_array( $type_terms ) && isset( $type_terms[0]->name ) ? (string) $type_terms[0]->name : 'پروژه اجرایی';
					$finish_date = get_post_meta( $post_id, 'finish-pro-time', true );
					$finish_date = is_scalar( $finish_date ) ? trim( (string) $finish_date ) : '';
					$year = '' !== $finish_date ? preg_replace( '/[^0-9۰-۹]/u', '', substr( $finish_date, 0, 4 ) ) : '';
					$card_args['city'] = $city;
					$card_args['meta'] = $type . ( '' !== $year ? ' · سال ' . $year : '' );
					$card_args['cta'] = 'مشاهده پروژه';
				} elseif ( 'product' === $card_type ) {
					$category_terms = get_the_terms( $post_id, 'product_cat' );
					$category = is_array( $category_terms ) && isset( $category_terms[0] ) ? $category_terms[0] : null;
					$product_object = function_exists( 'wc_get_product' ) ? wc_get_product( $post_id ) : null;
					$card_args['category'] = is_object( $category ) ? (string) ( $category->name ?? '' ) : '';
					$card_args['product_category'] = is_object( $category ) ? (string) ( $category->slug ?? '' ) : '';
					$card_args['price'] = is_object( $product_object ) && method_exists( $product_object, 'get_price' ) ? (string) $product_object->get_price() : '';
					$card_args['stock_status'] = is_object( $product_object ) && method_exists( $product_object, 'get_stock_status' ) ? (string) $product_object->get_stock_status() : '';
					$card_args['badge'] = '' !== $card_args['price'] ? $card_args['price'] : '';
				} else {
					$categories = function_exists( 'get_the_category' ) ? get_the_category( $post_id ) : array();
					$card_args['category'] = is_array( $categories ) && isset( $categories[0]->name ) ? (string) $categories[0]->name : '';
					$card_args['cta'] = 'ادامه مطلب';
				}

				get_template_part( 'template-parts/cards/card-' . $card_type, null, $card_args );
			endwhile;
			?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="text-center text-sm font-bold text-slate-500"><?php esc_html_e( 'محتوایی یافت نشد.', 'erfansanat' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
