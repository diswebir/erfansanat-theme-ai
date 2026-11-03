<?php
/** PSR-4 adapter for the media control template. */

namespace ErfanSanat\Theme\Admin\Fields\Types;

defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__ ) . '/types/media.php';

/** Renders the media field's attachment ID control. */
final class MediaField {

	public static function render( array $field, $value, string $name, string $id ): string {
		return MediaControl::render( $field, $value, $name, $id );
	}
}
