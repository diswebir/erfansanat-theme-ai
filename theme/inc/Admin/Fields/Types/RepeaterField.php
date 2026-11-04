<?php
/** PSR-4 adapter for the repeater control template. */

namespace ErfanSanat\Theme\Admin\Fields\Types;

defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__ ) . '/types/repeater.php';

/** Renders JetEngine-compatible repeater rows. */
final class RepeaterField {

	public static function render( array $field, $value, string $name, string $id ): string {
		return RepeaterControl::render( $field, $value, $name, $id );
	}
}
