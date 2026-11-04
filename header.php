<?php
/**
 * هدر اصلی قالب عرفان صنعت — shell سند + فراخوانی بلوک هدر میرور
 * میرور: uploads/static-mirror — برش عین DOM در template-parts/header/
 *
 * @package ErfanSanat
 */
declare(strict_types=1);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
// بلوک هدر (نوار بالا + لوگو + ناو مگا + اکشن‌ها + مودال جستجو) — برش عین میرور
es_get_template('template-parts/header/header-top.php');

// منوی موبایل off-canvas + backdrop (بعد از </header> در میرور)
es_get_template('template-parts/header/mobile-sidebar.php');
