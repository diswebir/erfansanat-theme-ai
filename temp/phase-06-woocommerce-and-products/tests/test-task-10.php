<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$tabs = es_p6_source( 'template-parts/product/tabs.php' );
$reviews = es_p6_source( 'woocommerce/single-product-reviews.php' );
$part = es_p6_source( 'template-parts/product/reviews.php' );
$js = es_p6_source( 'assets/js/product-enhancements.js' );
check( 'تب‌های محصول دارای data hooks و stateهای ARIA هستند', fn() => es_p6_has_all( $tabs, array( 'data-prod-tabs-container', 'data-prod-tab-btn', 'data-prod-tab-panel', 'aria-selected="true"', 'aria-selected="false"' ) ) && str_contains( $js, 'ArrowRight' ) );
check( 'فرم واقعی از comments_template و comment_form استاندارد استفاده می‌کند', fn() => str_contains( $part, 'comments_template(' ) && str_contains( $reviews, 'comment_form(' ) && str_contains( $reviews, 'woocommerce_product_review_comment_form_args' ) );
check( 'امتیازدهی پنج‌ستاره و ثبت امتیاز به فیلد rating فرم وصل است', fn() => es_p6_has_all( $reviews . $js, array( 'data-review-stars-group', 'data-review-star=', 'data-review-rating-input', 'name="rating"', 'selectRating(' ) ) );
summary();
