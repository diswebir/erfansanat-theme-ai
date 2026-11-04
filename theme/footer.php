<?php
/** Shared static-compatible footer and document close. */

defined( 'ABSPATH' ) || exit;

ob_start();
require __DIR__ . '/template-parts/footer/static-footer.html';
$es_footer_markup = (string) ob_get_clean();
echo \ErfanSanat\Theme\Nav\FooterMarkup::render( $es_footer_markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup is trusted; dashboard text and href values are escaped.
?>
</div><!-- /#es-page -->
<?php wp_footer(); ?>
</body>
</html>
