<?php
/** Tiny markup-structure comparator for the Phase 4 static mirror gate. */

namespace ErfanSanat\Harness;

/** Compares tag/class structure as a multiset, ignoring text and URL values. */
final class DomDiff {

	/** @return string[] */
	public static function signature( string $html ): array {
		if ( ! preg_match_all( '/<([a-z][a-z0-9:-]*)\b([^<>]*)>/i', $html, $matches, PREG_SET_ORDER ) ) { return array(); }
		$signature = array();
		foreach ( $matches as $match ) {
			$tag = strtolower( $match[1] );
			$attrs = array();
			if ( preg_match_all( '/\b([a-z_:][a-z0-9:._-]*)\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $match[2], $attributes, PREG_SET_ORDER ) ) {
				foreach ( $attributes as $attribute ) {
					$name = strtolower( $attribute[1] );
					if ( 'class' === $name ) {
						$classes = preg_split( '/\s+/', trim( $attribute[2] !== '' ? $attribute[2] : $attribute[3] ) );
						$classes = array_values( array_filter( $classes, 'strlen' ) );
						sort( $classes, SORT_STRING );
						$attrs[] = 'class:' . implode( ' ', $classes );
					} elseif ( in_array( $name, array( 'id', 'role', 'aria-label', 'aria-hidden', 'aria-expanded' ), true ) ) {
						$value = $attribute[2] !== '' ? $attribute[2] : $attribute[3];
						$attrs[] = $name . ':' . $value;
					}
				}
			}
			sort( $attrs, SORT_STRING );
			$signature[] = $tag . '|' . implode( ';', $attrs );
		}
		return $signature;
	}

	/** Percentage of reference nodes whose tag/structural attributes match. */
	public static function similarity( string $reference, string $candidate ): float {
		$expected = self::signature( $reference );
		$actual = self::signature( $candidate );
		if ( ! $expected ) { return ! $actual ? 100.0 : 0.0; }
		$counts = array_count_values( $actual );
		$matched = 0;
		foreach ( $expected as $node ) {
			if ( ! empty( $counts[ $node ] ) ) { ++$matched; --$counts[ $node ]; }
		}
		return 100 * $matched / max( count( $expected ), count( $actual ), 1 );
	}
}
