<?php
/**
 * Announcement top bar (knowledge-based / ISO + quick links).
 *
 * Data source: HeaderTab::announcement() (theme options). Disabling the bar
 * removes the whole DOM node (AC-02). lg:block visibility classes preserved.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\HeaderTab;

$es_announcement = HeaderTab::announcement();

if ( empty( $es_announcement['enabled'] ) ) {
	return;
}

$es_title    = (string) $es_announcement['title'];
$es_subtitle = (string) $es_announcement['subtitle'];
$es_links    = array();

foreach ( (array) $es_announcement['links'] as $es_link ) {
	if ( ! is_array( $es_link ) ) {
		continue;
	}
	$es_label = trim( (string) ( $es_link['label'] ?? '' ) );
	$es_url   = trim( (string) ( $es_link['url'] ?? '' ) );
	if ( '' === $es_label || '' === $es_url ) {
		continue;
	}
	// Full URLs / anchors are used as-is; bare slugs resolve to site pages.
	if ( preg_match( '#^(https?://|//|\#|/)#', $es_url ) ) {
		$es_href = $es_url;
	} else {
		$es_href = Menus::page_url( $es_url );
	}
	$es_links[] = array( 'label' => $es_label, 'url' => $es_href );
}
?>
<div class="hidden border-b border-emerald-100/70 bg-emerald-50/55 text-[11px] text-slate-600 lg:block">
	<div class="mx-auto flex max-w-[1300px] items-center justify-between px-5 py-1.5 lg:px-8">
		<div class="flex items-center gap-2.5 font-medium">
			<?php if ( '' !== $es_title ) : ?>
				<span class="font-extrabold text-emerald-800"><?php echo esc_html( $es_title ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $es_title && '' !== $es_subtitle ) : ?>
				<span class="text-slate-300">·</span>
			<?php endif; ?>
			<?php if ( '' !== $es_subtitle ) : ?>
				<span><?php echo esc_html( $es_subtitle ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $es_links ) ) : ?>
			<div class="flex items-center gap-3.5">
				<?php foreach ( $es_links as $es_i => $es_link ) : ?>
					<?php if ( $es_i > 0 ) : ?>
						<span class="text-slate-300">·</span>
					<?php endif; ?>
					<a href="<?php echo esc_url( $es_link['url'] ); ?>" class="font-bold text-slate-600 transition-colors hover:text-emerald-700"><?php echo esc_html( $es_link['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
