<?php
/**
 * SectionManager: per-page section configuration stored in versioned post meta.
 *
 * Stores an ordered list of section instances (id/type/enabled/order/fields).
 * Unknown section types are rejected, duplicate instance ids are made unique,
 * and order + enabled flags are preserved. Field content is sanitized in
 * save_section_fields() (P02-T017); the Renderer consumes get_sections()
 * (P02-T018).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Sanitizer;

final class SectionManager {

	/**
	 * Determine the section context for a page.
	 */
	public static function context_for( int $post_id ): string {
		if ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === $post_id ) {
			return 'home';
		}

		$context = apply_filters( 'erfansanat_sections_context', '', $post_id );

		return in_array( $context, Schema::contexts(), true ) ? $context : '';
	}

	/**
	 * Read a page's sections (falls back to the context defaults).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function get_sections( int $post_id, string $context = '' ): array {
		if ( '' === $context ) {
			$context = self::context_for( $post_id );
		}
		if ( '' === $context ) {
			return array();
		}

		$stored = get_post_meta( $post_id, Schema::meta_key(), true );
		if ( ! is_array( $stored ) || empty( $stored ) ) {
			return Schema::defaults( $context );
		}

		return self::normalize( $stored, $context );
	}

	/**
	 * Persist a page's sections atomically and fire cache invalidation.
	 *
	 * @param array<int,array<string,mixed>> $sections Candidate sections.
	 */
	public static function save_sections( int $post_id, array $sections, string $context = '' ): bool {
		if ( '' === $context ) {
			$context = self::context_for( $post_id );
		}
		if ( '' === $context ) {
			return false;
		}

		$clean  = self::normalize( $sections, $context );
		$result = update_post_meta( $post_id, Schema::meta_key(), $clean );

		do_action( 'erfansanat_sections_updated', $post_id, $clean );

		return false !== $result;
	}

	/**
	 * Normalize a section list: reject unknown types, uniquify ids, preserve
	 * order + enabled, keep fields.
	 *
	 * @param array<int,mixed> $sections Raw sections.
	 * @return array<int,array<string,mixed>>
	 */
	public static function normalize( array $sections, string $context ): array {
		$out      = array();
		$used_ids = array();
		$order    = 0;

		foreach ( array_values( $sections ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$type = (string) ( $item['type'] ?? ( $item['id'] ?? '' ) );
			if ( ! Schema::is_known( $context, $type ) ) {
				continue; // AC-02: unknown section types are rejected.
			}

			$id = isset( $item['id'] ) && is_string( $item['id'] ) && '' !== $item['id'] ? $item['id'] : $type;
			if ( in_array( $id, $used_ids, true ) ) {
				$id = self::unique_id( $type, $used_ids ); // SC-02: duplicates get unique ids.
			}
			$used_ids[] = $id;

			$out[] = array(
				'id'      => $id,
				'type'    => $type,
				'enabled' => isset( $item['enabled'] ) ? (bool) $item['enabled'] : true,
				'order'   => $order++,
				'fields'  => isset( $item['fields'] ) && is_array( $item['fields'] ) ? $item['fields'] : array(),
			);
		}

		return $out;
	}

	/**
	 * Generate a unique instance id for a section type.
	 *
	 * @param array<int,string> $used Already-used ids.
	 */
	private static function unique_id( string $type, array $used ): string {
		$i         = 1;
		$candidate = $type;
		while ( in_array( $candidate, $used, true ) ) {
			$i++;
			$candidate = $type . '-' . $i;
		}
		return $candidate;
	}

	/**
	 * Sanitize + save the editable fields of one section instance (P02-T017).
	 *
	 * Only fields declared by the section schema are kept; empty values do not
	 * delete a field unless the schema default is empty. Media/link values are
	 * sanitized by the shared Sanitizer.
	 *
	 * @param array<string,mixed> $fields Raw field values.
	 */
	public static function save_section_fields( int $post_id, string $section_id, array $fields, string $context = '' ): bool {
		$sections = self::get_sections( $post_id, $context );
		$found    = false;

		foreach ( $sections as $index => $section ) {
			if ( $section['id'] !== $section_id ) {
				continue;
			}
			$found                     = true;
			$schema_fields             = Schema::section_fields( $context, $section['type'] );
			$clean                     = $section['fields'];

			foreach ( $schema_fields as $key => $def ) {
				if ( ! array_key_exists( $key, $fields ) ) {
					continue; // Not submitted: keep existing value.
				}
				$result = Sanitizer::sanitize_field( $def, $fields[ $key ] );
				if ( ! is_wp_error( $result ) ) {
					$clean[ $key ] = $result;
				}
			}

			$sections[ $index ]['fields'] = $clean;
			break;
		}

		if ( ! $found ) {
			return false;
		}

		return self::save_sections( $post_id, $sections, $context );
	}
}
