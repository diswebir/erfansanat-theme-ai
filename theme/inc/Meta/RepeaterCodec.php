<?php
/** Safe codec for JSON, serialized, CSV and array legacy metadata. */

namespace ErfanSanat\Theme\Meta;

defined( 'ABSPATH' ) || exit;

/** Reads common JetEngine encodings and writes using the source representation. */
final class RepeaterCodec {

	/** Identify a value's persistent representation. */
	public static function format( $value ): string {
		if ( is_array( $value ) ) { return 'array'; }
		if ( ! is_string( $value ) ) { return 'scalar'; }
		$trimmed = trim( $value );
		if ( '' !== $trimmed && ( '{' === $trimmed[0] || '[' === $trimmed[0] ) ) {
			json_decode( $trimmed, true );
			if ( JSON_ERROR_NONE === json_last_error() ) { return 'json'; }
		}
		if ( 'N;' === $trimmed || preg_match( '/^(?:a|O|s|i|b|d|N):/', $trimmed ) ) { return 'serialized'; }
		if ( false !== strpos( $trimmed, ',' ) ) { return 'csv'; }
		return 'scalar';
	}

	/** Decode JSON/serialized arrays without changing item-N or numeric keys. */
	public static function decode( $value ) {
		if ( is_array( $value ) ) { return $value; }
		if ( ! is_string( $value ) ) { return $value; }
		$format = self::format( $value );
		if ( 'json' === $format ) {
			$decoded = json_decode( $value, true );
			return JSON_ERROR_NONE === json_last_error() ? $decoded : $value;
		}
		if ( 'serialized' === $format ) {
			$decoded = @unserialize( $value, array( 'allowed_classes' => false ) );
			return false !== $decoded || 'b:0;' === $value ? $decoded : $value;
		}
		return $value;
	}

	/** Decode comma-separated attachment IDs into unique positive integers. */
	public static function decode_csv( $value ): array {
		if ( is_array( $value ) ) { $items = $value; }
		elseif ( is_string( $value ) && '' !== trim( $value ) ) { $items = explode( ',', $value ); }
		else { return array(); }
		$ids = array();
		foreach ( $items as $item ) {
			$id = function_exists( 'absint' ) ? absint( $item ) : abs( (int) $item );
			if ( $id > 0 && ! in_array( $id, $ids, true ) ) { $ids[] = $id; }
		}
		return $ids;
	}

	/** Encode unique positive IDs as a stable comma-separated string. */
	public static function encode_csv( array $values ): string {
		return implode( ',', self::decode_csv( $values ) );
	}

	/** Encode values using a chosen source format. */
	public static function encode( $value, string $format = 'array' ) {
		if ( 'csv' === $format ) { return is_array( $value ) ? self::encode_csv( $value ) : (string) $value; }
		if ( 'json' === $format ) {
			$json = json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			return is_string( $json ) ? $json : '[]';
		}
		if ( 'serialized' === $format ) { return serialize( $value ); }
		return $value;
	}

	/** Preserve the source encoding when replacing a decoded value. */
	public static function preserve_format( $original, $value ) {
		$format = self::format( $original );
		if ( 'scalar' === $format ) { $format = is_array( $value ) ? 'array' : 'scalar'; }
		return self::encode( $value, $format );
	}
}
