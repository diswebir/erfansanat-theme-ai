<?php
/** Global footer sliced from static-mirror/index.html. @package ErfanSanat\Theme */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_template_part('template-parts/global/footer-main');
get_template_part('template-parts/global/footer-bottom');
?>
</div>
</div>
</div>
<?php $esFooterCode = (string) es_option('custom_footer_code', ''); if ($esFooterCode !== '' && current_user_can('manage_options')) echo $esFooterCode . "\n"; ?>
<?php wp_footer(); ?>
</body>
</html>
