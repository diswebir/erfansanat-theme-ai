<?php
/**
 * Idempotent synthetic fixture seeder for the isolated local WordPress site.
 * Run: tools/wp --path=.local/wordpress eval-file tools/local/seed.php
 * Never run against production. All records carry _es_fixture = 1.
 */
defined('ABSPATH') || exit;
if (!defined('WP_CLI') || !WP_CLI) {
    exit("CLI only\n");
}
if (!class_exists('WooCommerce')) {
    WP_CLI::error('WooCommerce is not active.');
}
$home = home_url();
if (!str_contains($home, '127.0.0.1') && !str_contains($home, 'localhost') && !str_contains($home, '.e2b.app')) {
    WP_CLI::error('Refusing to seed a non-local site: ' . $home);
}
$data = json_decode(file_get_contents(__DIR__ . '/fixture-data.json'), true, 512, JSON_THROW_ON_ERROR);
define('ES_FIXTURE_SOURCE_ROOT', dirname(__DIR__, 2) . '/static-mirror');
$fixture_meta = '_es_fixture';

/**
 * The block markup WooCommerce's own installer writes for a page.
 *
 * A bare `<!-- wp:woocommerce/cart /-->` is not enough: the cart block renders
 * by filling the inner block tree that the installer's template already
 * contains, so a self-closing tag produces an empty page. Reusing the exact
 * installer string keeps the fixture faithful to a real store, and the theme is
 * then tested against the block markup it has to support.
 *
 * @param string $block Either `cart` or `checkout`.
 * @return string
 */
function es_woocommerce_block_page(string $block): string {
    if (!class_exists('WC_Install')) {
        include_once WC_ABSPATH . 'includes/class-wc-install.php';
    }
    $method = new ReflectionMethod('WC_Install', $block === 'checkout' ? 'get_checkout_block_content' : 'get_cart_block_content');
    $method->setAccessible(true);
    $content = $method->invoke(null);
    return is_string($content) && $content !== '' ? $content : '';
}

function es_fixture_post(string $type, string $slug, string $title, string $content, array $extra = []): int {
    $existing = get_page_by_path($slug, OBJECT, $type);
    $args = array_merge([
        'post_type' => $type,
        'post_name' => $slug,
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => 1,
    ], $extra);
    if ($existing) {
        $args['ID'] = $existing->ID;
        $id = wp_update_post($args, true);
    } else {
        $id = wp_insert_post($args, true);
    }
    if (is_wp_error($id)) {
        WP_CLI::error($id->get_error_message());
    }
    update_post_meta((int) $id, '_es_fixture', 1);
    return (int) $id;
}

function es_fixture_term(string $tax, string $name, string $slug = '', array $args = []): int {
    // sanitize_title() percent-encodes non-ASCII characters, so a Persian term
    // would get the literal slug "%d8%a7..." and every generated term URL would
    // 404 after a second round of encoding. Decode it back to real characters.
    $slug = $slug ?: urldecode(sanitize_title($name));
    $term = term_exists($slug, $tax);
    if (!$term) {
        $term = wp_insert_term($name, $tax, array_merge(['slug' => $slug], $args));
    }
    if (is_wp_error($term)) {
        WP_CLI::error($term->get_error_message());
    }
    $id = is_array($term) ? (int) $term['term_id'] : (int) $term;
    update_term_meta($id, '_es_fixture', 1);
    return $id;
}

function es_fixture_media(string $relative, string $title): int {
    $found = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'any',
        'numberposts' => 1,
        'meta_key' => '_es_fixture_source',
        'meta_value' => $relative,
        'fields' => 'ids',
    ]);
    if ($found) {
        return (int) $found[0];
    }
    $file = ES_FIXTURE_SOURCE_ROOT . '/' . $relative;
    if (!is_file($file)) {
        WP_CLI::error('Missing fixture image: ' . $file);
    }
    $upload = wp_upload_bits('fixture-' . basename($relative), null, file_get_contents($file));
    if (!empty($upload['error'])) {
        WP_CLI::error($upload['error']);
    }
    $filetype = wp_check_filetype($upload['file']);
    $attach_id = wp_insert_attachment([
        'post_mime_type' => $filetype['type'],
        'post_title' => $title . ' (تصویر آزمایشی)',
        'post_content' => 'تصویر آزمایشی برگرفته از میرور استاتیک؛ فقط برای محیط محلی.',
        'post_status' => 'inherit',
    ], $upload['file']);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $upload['file']));
    update_post_meta($attach_id, '_es_fixture', 1);
    update_post_meta($attach_id, '_es_fixture_source', $relative);
    return (int) $attach_id;
}

function es_fixture_attribute(string $slug): string {
    $taxonomies = wc_get_attribute_taxonomies();
    $exists = false;
    foreach ($taxonomies as $tax) {
        if ($tax->attribute_name === $slug) {
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        $id = wc_create_attribute([
            'name' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'type' => $slug === 'color' ? 'color' : 'select',
            'order_by' => 'menu_order',
            'has_archives' => false,
        ]);
        if (is_wp_error($id)) {
            WP_CLI::error($id->get_error_message());
        }
        delete_transient('wc_attribute_taxonomies');
    }
    $name = wc_attribute_taxonomy_name($slug);
    if (!taxonomy_exists($name)) {
        register_taxonomy($name, 'product', ['hierarchical' => false, 'show_ui' => false, 'query_var' => true, 'rewrite' => false]);
    }
    return $name;
}

function es_repeater(array $rows): string {
    $out = [];
    foreach ($rows as $i => $row) {
        $out['item-' . $i] = $row;
    }
    return wp_json_encode($out, JSON_UNESCAPED_UNICODE);
}

WP_CLI::log('Seeding synthetic Persian fixtures for ' . $home);

// Core settings and WooCommerce pages.
update_option('blogname', 'عرفان صنعت | محیط آزمایشی');
update_option('blogdescription', 'داده‌های این سایت کاملاً ساختگی و مخصوص آزمون هستند.');
update_option('timezone_string', 'Asia/Tehran');
update_option('date_format', 'Y/m/d');
update_option('permalink_structure', '/%postname%/');
update_option('woocommerce_currency', 'IRT');
update_option('woocommerce_currency_pos', 'right_space');
update_option('woocommerce_price_thousand_sep', ',');
update_option('woocommerce_price_decimal_sep', '.');
update_option('woocommerce_price_num_decimals', '0');
update_option('woocommerce_default_country', 'IR:ISF');
update_option('woocommerce_store_address', 'نشانی ساختگی — فقط برای آزمون');
update_option('woocommerce_store_city', 'اصفهان');
update_option('woocommerce_store_postcode', '00000');
update_option('woocommerce_enable_guest_checkout', 'yes');
update_option('woocommerce_enable_reviews', 'yes');
update_option('woocommerce_review_rating_verification_required', 'no');
update_option('woocommerce_coming_soon', 'no');
update_option('woocommerce_permalinks', [
    'product_base' => 'product',
    'category_base' => 'product_cat',
    'tag_base' => 'product-tag',
    'attribute_base' => '',
    'use_verbose_page_rules' => false,
]);

$pages = [
    'home' => 'خانه آزمایشی',
    'blog' => 'وبلاگ آزمایشی',
    'about' => 'درباره ما (آزمایشی)',
    'contact' => 'تماس با ما (آزمایشی)',
    'licenses-and-honors' => 'مجوزها و افتخارات (آزمایشی)',
    'faq' => 'سوالات متداول (آزمایشی)',
    'recruitment' => 'فرصت‌های شغلی (آزمایشی)',
    'refund_returns' => 'شرایط گارانتی (آزمایشی)',
    'arya' => 'درب بازکن هوشمند آریا (آزمایشی)',
    'project' => 'پروژه منتخب (آزمایشی)',
    'shop' => 'فروشگاه آزمایشی',
    'cart' => 'سبد خرید آزمایشی',
    'checkout' => 'پرداخت آزمایشی',
    'my-account' => 'حساب کاربری آزمایشی',
];
$page_ids = [];
foreach ($pages as $slug => $title) {
    $content = "<!-- wp:paragraph --><p>این برگه برای آزمون محیط محلی ساخته شده است. هیچ داده واقعی، قیمت واقعی یا سفارش واقعی در آن وجود ندارد.</p><!-- /wp:paragraph -->";
    if ($slug === 'cart' || $slug === 'checkout') {
        $content = es_woocommerce_block_page($slug);
    } elseif ($slug === 'my-account') {
        $content = '<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->';
    }
    $page_ids[$slug] = es_fixture_post('page', $slug, $title, $content);
}
update_option('show_on_front', 'page');
update_option('page_on_front', $page_ids['home']);
update_option('page_for_posts', $page_ids['blog']);
update_option('woocommerce_shop_page_id', $page_ids['shop']);
update_option('woocommerce_cart_page_id', $page_ids['cart']);
update_option('woocommerce_checkout_page_id', $page_ids['checkout']);
update_option('woocommerce_myaccount_page_id', $page_ids['my-account']);
update_option('woocommerce_refund_returns_page_id', $page_ids['refund_returns']);

// Media.
$media = [];
foreach ($data['images'] as $i => $relative) {
    $media[$relative] = es_fixture_media($relative, 'رسانه آزمایشی ' . ($i + 1));
}

// Product categories, tags, brands and technical attributes.
$category_ids = [];
foreach ($data['product_categories'] as $i => $cat) {
    $id = es_fixture_term('product_cat', $cat['title'], $cat['slug'], ['description' => 'دسته آزمایشی: ' . $cat['title']]);
    $category_ids[$cat['slug']] = $id;
    update_term_meta($id, 'thumbnail', $media[$cat['image']]);
    update_term_meta($id, 'faq', es_repeater([
        ['name_question' => 'این دسته آزمایشی چه کاربردی دارد؟', 'answer_question' => 'این پاسخ صرفاً برای آزمون فیلد تکرارشونده دسته ساخته شده است.'],
    ]));
}
$tag_ids = [];
foreach (['نورپردازی شهری', 'ضدآب', 'هوشمند', 'نمای ساختمان'] as $tag) {
    $tag_ids[] = es_fixture_term('product_tag', $tag);
}
$brand_id = es_fixture_term('product_brand', 'عرفان صنعت آزمایشی');
$attribute_terms = [];
foreach ($data['attributes'] as $slug) {
    $tax = es_fixture_attribute($slug);
    $values = match ($slug) {
        'ip-rating' => ['IP65', 'IP67', 'IP68'],
        'input-voltage' => ['12V', '24V', '220V'],
        'color' => ['آفتابی', 'مهتابی', 'RGB'],
        'wattage' => ['10W', '24W', '48W'],
        'warranty' => ['۲۴ ماهه', '۳۶ ماهه'],
        default => ['مقدار آزمایشی ۱', 'مقدار آزمایشی ۲'],
    };
    foreach ($values as $value) {
        $attribute_terms[$tax][] = es_fixture_term($tax, $value);
    }
}
$city_ids = [];
foreach ($data['cities'] as $city) {
    $city_ids[$city] = es_fixture_term('city', $city);
}
$type_ids = [];
foreach ($data['project_types'] as $type) {
    $type_ids[$type] = es_fixture_term('project_type', $type);
}

// Products with native Woo data plus exact legacy meta keys and mixed formats.
$product_ids = [];
$project_ids = [];
foreach ($data['projects'] as $p) {
    $project_ids[$p['slug']] = es_fixture_post('projects', $p['slug'], $p['title'], "<p>این پروژه ساختگی است و فقط برای آزمون آرشیو، تکسونومی و گالری ساخته شده است.</p>");
}
foreach ($data['products'] as $i => $p) {
    $sku = 'ES-FIX-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);
    $id = wc_get_product_id_by_sku($sku);
    $product = $id ? wc_get_product($id) : ($p['mode'] === 'variable' ? new WC_Product_Variable() : new WC_Product_Simple());
    $product->set_name($p['title']);
    $product->set_slug($p['slug']);
    $product->set_status('publish');
    $product->set_sku($sku);
    $product->set_description('<p>این محصول و همه قیمت‌ها/مشخصات آن ساختگی هستند. برای آزمون قالب، متاهای قدیمی و چرخه خرید استفاده می‌شود.</p>');
    $product->set_short_description('<p>داده آزمایشی — قابل استفاده در سایت زنده نیست.</p>');
    $product->set_regular_price((string) $p['price']);
    if ($i % 4 === 1) {
        $product->set_sale_price((string) round($p['price'] * 0.9));
    }
    $product->set_manage_stock(true);
    $product->set_stock_quantity($p['mode'] === 'outofstock' ? 0 : 25 + $i);
    $product->set_stock_status($p['mode'] === 'outofstock' ? 'outofstock' : 'instock');
    $product->set_category_ids([$category_ids[$p['category']]]);
    $product->set_tag_ids([$tag_ids[$i % count($tag_ids)]]);
    $product->set_brand_ids([$brand_id]);
    $product->set_image_id($media[$p['image']]);
    $gallery = array_values(array_slice($media, ($i + 1) % count($media), 3, true));
    $product->set_gallery_image_ids($gallery);
    $attributes = [];
    foreach (['ip-rating', 'input-voltage', 'wattage', 'warranty'] as $slug) {
        $tax = wc_attribute_taxonomy_name($slug);
        $terms = $attribute_terms[$tax];
        $attr = new WC_Product_Attribute();
        $attr->set_id(wc_attribute_taxonomy_id_by_name($slug));
        $attr->set_name($tax);
        $attr->set_options([$terms[$i % count($terms)]]);
        $attr->set_visible(true);
        $attr->set_variation(false);
        $attributes[$tax] = $attr;
    }
    if ($p['mode'] === 'variable') {
        $tax = wc_attribute_taxonomy_name('color');
        $terms = array_slice($attribute_terms[$tax], 0, 3);
        $attr = new WC_Product_Attribute();
        $attr->set_id(wc_attribute_taxonomy_id_by_name('color'));
        $attr->set_name($tax);
        $attr->set_options($terms);
        $attr->set_visible(true);
        $attr->set_variation(true);
        $attributes[$tax] = $attr;
    }
    $product->set_attributes($attributes);
    $id = $product->save();
    if (!$id) {
        WP_CLI::error('Could not save product ' . $p['slug']);
    }
    $product_ids[$p['slug']] = (int) $id;
    update_post_meta($id, '_es_fixture', 1);
    update_post_meta($id, 'gs1_id', '000' . str_pad((string) ($i + 1), 10, '0', STR_PAD_LEFT));
    update_post_meta($id, 'national_code', 'FIX' . str_pad((string) ($i + 1), 8, '0', STR_PAD_LEFT));
    update_post_meta($id, 'hs_id', '940540' . ($i % 10));
    update_post_meta($id, 'isic_id', '2740');
    update_post_meta($id, 'cpc_id', '4653');
    update_post_meta($id, 'product_id', 'ES-' . ($i + 1));
    update_post_meta($id, 'zireonvan', 'زیرعنوان آزمایشی محصول');
    update_post_meta($id, 'english_titl', 'Synthetic fixture product ' . ($i + 1));
    update_post_meta($id, 'before-sale-c', 'این اطلاعیه آزمایشی است؛ خرید واقعی انجام نشود.');
    update_post_meta($id, 'true_sale', $p['mode'] === 'inquiry' ? 'false' : 'true');
    update_post_meta($id, 'product-catalog-file', wp_get_attachment_url($media[$p['image']]));
    update_post_meta($id, 'galerry_produ', implode(',', $gallery));
    update_post_meta($id, 'gallery-filed', $gallery);
    foreach ($gallery as $gi => $gid) {
        update_post_meta($id, 'image-' . ($gi + 2), $gid);
    }
    $related_projects = array_slice(array_values($project_ids), $i % 4, 2);
    update_post_meta($id, 'project-ids', wp_json_encode($related_projects));
    update_post_meta($id, 'usage_countr', serialize(array_slice(array_values($type_ids), $i % 2, 2)));
    update_post_meta($id, 'product-feature', es_repeater([
        ['add-new-feature' => 'ویژگی آزمایشی یک'],
        ['add-new-feature' => 'ویژگی آزمایشی دو'],
    ]));
    update_post_meta($id, 'product-feature2_copy', es_repeater([
        ['add-new-feature' => 'گارانتی', 'vaule_of_variable' => '۲۴ ماهه آزمایشی'],
    ]));
    update_post_meta($id, 'usage_product', es_repeater([
        ['add-new-usage' => 'کاربرد آزمایشی در نورپردازی شهری'],
    ]));
    update_post_meta($id, 'faq', es_repeater([
        ['name_question' => 'آیا این محصول واقعی است؟', 'answer_question' => 'خیر؛ این رکورد فقط داده آزمایشی است.'],
    ]));
    update_post_meta($id, 'hrefmeta_fa', home_url('/product/' . $p['slug'] . '/'));
    update_post_meta($id, 'hrefmeta_en', home_url('/en/product/' . $p['slug'] . '/'));
    update_post_meta($id, 'hrefmeta_ar', home_url('/ar/product/' . $p['slug'] . '/'));
    update_post_meta($id, 'same_key_hrefl', $p['slug']);
    update_post_meta($id, 'number2-viewc', (string) (100 + $i));
    if ($p['mode'] === 'variable') {
        $color_tax = wc_attribute_taxonomy_name('color');
        $existing_variations = [];
        foreach ($product->get_children() as $child_id) {
            $child = wc_get_product($child_id);
            $existing_variations[$child->get_attribute($color_tax)] = $child;
        }
        foreach (array_slice($attribute_terms[$color_tax], 0, 3) as $vi => $term_id) {
            $term_slug = get_term($term_id)->slug;
            $term_name = get_term($term_id)->name;
            $variation = $existing_variations[$term_name] ?? $existing_variations[$term_slug] ?? new WC_Product_Variation();
            $variation->set_parent_id($id);
            $variation->set_attributes([$color_tax => $term_slug]);
            $variation->set_regular_price((string) ($p['price'] + $vi * 100000));
            $variation->set_status('publish');
            $variation->set_stock_status('instock');
            $variation->save();
            update_post_meta($variation->get_id(), '_es_fixture', 1);
        }
        WC_Product_Variable::sync($id);
    }
}

// Projects, services and posts.
foreach ($data['projects'] as $i => $p) {
    $id = $project_ids[$p['slug']];
    set_post_thumbnail($id, $media[$p['image']]);
    wp_set_object_terms($id, [$city_ids[$p['city']]], 'city');
    wp_set_object_terms($id, [$type_ids[$p['type']]], 'project_type');
    foreach ($p['gallery'] as $gi => $relative) {
        $keys = ['second-image', 'three-image', 'foure-image', 'five-image', 'six-image'];
        update_post_meta($id, $keys[$gi], $media[$relative]);
    }
    update_post_meta($id, 'finish-pro-time', '1404/0' . (($i % 9) + 1) . '/15');
    update_post_meta($id, 'mortabet_mahsole_2', wp_json_encode(array_slice(array_values($product_ids), $i % 5, 3)));
    update_post_meta($id, 'hrefmeta_fa', home_url('/projects/' . $p['slug'] . '/'));
    update_post_meta($id, 'same_key_hrefl', $p['slug']);
    update_post_meta($id, 'number2-viewc', (string) (200 + $i));
}
foreach ($data['services'] as $i => $s) {
    $id = es_fixture_post('services', $s['slug'], $s['title'], '<p>این خدمت ساختگی است. اسمارت المنت به‌عنوان یکی از آیتم‌های CPT خدمات ثبت شده است.</p>', ['post_excerpt' => 'شرح آزمایشی خدمت']);
    set_post_thumbnail($id, $media[$s['image']]);
    update_post_meta($id, 'hrefmeta_fa', home_url('/services/' . $s['slug'] . '/'));
    update_post_meta($id, 'same_key_hrefl', $s['slug']);
}
$post_category = es_fixture_term('category', 'مقالات آزمایشی');
foreach ($data['posts'] as $i => $p) {
    $id = es_fixture_post('post', $p['slug'], $p['title'], "<h2>بخش آزمایشی یک</h2><p>این مقاله ساختگی است و برای آزمون فهرست مطالب، دیدگاه و شمارنده بازدید ساخته شده است.</p><h2>بخش آزمایشی دو</h2><p>هیچ ادعای واقعی درباره محصول یا پروژه در این متن وجود ندارد.</p>");
    set_post_thumbnail($id, $media[$p['image']]);
    wp_set_post_categories($id, [$post_category]);
    update_post_meta($id, 'number2-viewc', (string) (300 + $i));
    update_post_meta($id, 'hrefmeta_fa', home_url('/blog/' . $p['slug'] . '/'));
    $existing_comment = get_comments(['post_id' => $id, 'meta_key' => '_es_fixture', 'meta_value' => '1', 'number' => 1, 'fields' => 'ids']);
    if (!$existing_comment) {
        $comment_id = wp_insert_comment([
            'comment_post_ID' => $id,
            'comment_author' => 'کاربر آزمایشی',
            'comment_author_email' => 'fixture@example.invalid',
            'comment_content' => 'این دیدگاه ساختگی است و فقط برای آزمون نمایش دیدگاه‌ها ثبت شده است.',
            'comment_approved' => 1,
            'comment_date' => current_time('mysql'),
        ]);
        update_comment_meta((int) $comment_id, '_es_fixture', 1);
    }
}

/*
 * A local test payment method.
 *
 * The cart and checkout blocks refuse to render a gateway-less checkout, so
 * without one the fixture cannot place an order at all and task 6.3 could not
 * be tested. Cash on delivery is WooCommerce's own offline gateway, enabled
 * only in this local fixture and labelled as a test method; no real gateway,
 * credential or payment is involved, and nothing here can charge anyone.
 */
update_option('woocommerce_cod_settings', [
    'enabled' => 'yes',
    'title' => 'پرداخت در محل (روش آزمایشی محیط محلی)',
    'description' => 'این روش فقط برای آزمون محلی است و هیچ پرداختی انجام نمی‌دهد.',
    'instructions' => '',
    'enable_for_methods' => [],
    'enable_for_virtual' => 'yes',
]);

// Synthetic customer, orders and product reviews.
$creds = json_decode(file_get_contents(dirname(__DIR__, 2) . '/.local/credentials.json'), true);
$customer = get_user_by('login', $creds['customer_user']);
if (!$customer) {
    $customer_id = wp_insert_user([
        'user_login' => $creds['customer_user'],
        'user_pass' => $creds['customer_password'],
        'user_email' => 'customer@example.invalid',
        'display_name' => 'مشتری آزمایشی',
        'role' => 'customer',
    ]);
    if (is_wp_error($customer_id)) {
        WP_CLI::error($customer_id->get_error_message());
    }
} else {
    $customer_id = $customer->ID;
}
update_user_meta($customer_id, '_es_fixture', 1);
if (!get_option('erfansanat_fixture_orders_done')) {
    $simple_ids = array_values($product_ids);
    foreach (['processing', 'completed', 'on-hold'] as $oi => $status) {
        $order = wc_create_order(['customer_id' => $customer_id, 'status' => $status]);
        $order->add_product(wc_get_product($simple_ids[$oi]), 1 + $oi);
        $order->set_address([
            'first_name' => 'مشتری',
            'last_name' => 'آزمایشی',
            'address_1' => 'نشانی ساختگی',
            'city' => 'اصفهان',
            'postcode' => '00000',
            'country' => 'IR',
            'email' => 'customer@example.invalid',
            'phone' => '0000000000',
        ], 'billing');
        $order->set_payment_method('cod');
        $order->set_payment_method_title('پرداخت در محل — آزمایشی');
        $order->update_meta_data('_es_fixture', 1);
        $order->calculate_totals();
        $order->save();
    }
    foreach (array_slice($simple_ids, 0, 4) as $pi => $pid) {
        $review_id = wp_insert_comment([
            'comment_post_ID' => $pid,
            'comment_type' => 'review',
            'comment_author' => 'بازبین آزمایشی',
            'comment_author_email' => 'reviewer@example.invalid',
            'comment_content' => 'این نظر ساختگی است و اعتبار واقعی ندارد.',
            'comment_approved' => 1,
            'comment_date' => current_time('mysql'),
        ]);
        update_comment_meta((int) $review_id, 'rating', 4 + ($pi % 2));
        update_comment_meta((int) $review_id, '_es_fixture', 1);
    }
    update_option('erfansanat_fixture_orders_done', 1);
}

// Menu.
$menu = wp_get_nav_menu_object('منوی آزمایشی');
if (!$menu) {
    $menu_id = wp_create_nav_menu('منوی آزمایشی');
    foreach (['home' => 'خانه', 'shop' => 'فروشگاه', 'blog' => 'وبلاگ', 'about' => 'درباره ما', 'contact' => 'تماس'] as $slug => $label) {
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => $label,
            'menu-item-object-id' => $page_ids[$slug],
            'menu-item-object' => 'page',
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ]);
    }
    $locations = get_theme_mod('nav_menu_locations', []);
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}


// Remove only known local defaults and duplicate fixture artifacts; never touch production.
foreach (['hello-world' => 'post', 'sample-page' => 'page', 'privacy-policy' => 'page'] as $slug => $type) {
    $default = get_page_by_path($slug, OBJECT, $type);
    if ($default && !get_post_meta($default->ID, '_es_fixture', true)) {
        wp_delete_post($default->ID, true);
    }
}
$placeholder = get_page_by_path('woocommerce-placeholder', OBJECT, 'attachment');
if ($placeholder && !get_post_meta($placeholder->ID, '_es_fixture', true)) {
    wp_delete_attachment($placeholder->ID, true);
}
$uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
if ($uncategorized && (int) $uncategorized->count === 0) {
    wp_delete_term($uncategorized->term_id, 'product_cat');
}
foreach ($data['posts'] as $p) {
    $post = get_page_by_path($p['slug'], OBJECT, 'post');
    if (!$post) { continue; }
    $comments = get_comments(['post_id' => $post->ID, 'author_email' => 'fixture@example.invalid', 'orderby' => 'comment_ID', 'order' => 'ASC']);
    foreach ($comments as $ci => $comment) {
        if ($ci === 0) { update_comment_meta((int) $comment->comment_ID, '_es_fixture', 1); }
        else { wp_delete_comment($comment->comment_ID, true); }
    }
}
$variable = wc_get_product(wc_get_product_id_by_sku('ES-FIX-001'));
if ($variable) {
    $seen = [];
    foreach ($variable->get_children() as $child_id) {
        $child = wc_get_product($child_id);
        $key = $child->get_attribute(wc_attribute_taxonomy_name('color'));
        if (isset($seen[$key])) { wp_delete_post($child_id, true); }
        else { $seen[$key] = true; }
    }
    WC_Product_Variable::sync($variable->get_id());
}

update_option('erfansanat_fixture_version', '1.0.0');
update_option('erfansanat_fixture_notice', 'همه داده‌ها ساختگی هستند؛ به سایت زنده منتقل نشوند.');
flush_rewrite_rules(false);
WP_CLI::success(sprintf(
    'Fixtures ready: %d products, %d projects, %d services, %d posts, %d pages, %d media, %d product categories.',
    count($product_ids), count($project_ids), count($data['services']), count($data['posts']), count($page_ids), count($media), count($category_ids)
));
