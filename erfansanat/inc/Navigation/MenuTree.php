<?php
/**
 * Menu tree and mega-menu data.
 *
 * The static reference builds its mega menu from four blocks (products,
 * projects, services, corporate) whose content is real taxonomy data. This
 * class turns the assigned WordPress menu into a tree and produces that block
 * data from `product_cat`, `city`, `project_type` and the `services` post type,
 * so the menu never invents a link and never fatals on an empty menu.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Navigation;

use ErfanSanat\Theme\ContentTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Tree building and mega-block data providers.
 */
final class MenuTree {

	/**
	 * Blocks in the order the reference header shows them.
	 *
	 * @return array<int, string>
	 */
	public static function blocks(): array {
		return array( 'products', 'projects', 'services', 'corporate' );
	}

	/**
	 * Build a nested tree from a menu location.
	 *
	 * Returns an empty array when no menu is assigned, when the menu is empty
	 * or when the menu id is invalid, so callers never see a partial structure.
	 *
	 * @param string $location Registered menu location.
	 * @return array<int, array<string, mixed>>
	 */
	public static function from_location( string $location ): array {
		$locations = get_nav_menu_locations();
		$menu_id   = isset( $locations[ $location ] ) ? (int) $locations[ $location ] : 0;
		if ( $menu_id <= 0 ) {
			return array();
		}
		$items = wp_get_nav_menu_items( $menu_id );
		if ( ! is_array( $items ) || array() === $items ) {
			return array();
		}
		return self::build( $items );
	}

	/**
	 * Turn a flat list of menu items into a nested tree.
	 *
	 * Menu items arrive ordered by `menu_order`; children are attached to their
	 * parent while preserving that order. Orphans (a parent that is missing or
	 * not published) are kept at the top level instead of being dropped.
	 *
	 * @param array<int, mixed> $items Menu items.
	 * @return array<int, array<string, mixed>>
	 */
	public static function build( array $items ): array {
		$nodes   = array();
		$by_id   = array();
		$ordered = array();

		foreach ( $items as $item ) {
			if ( is_object( $item ) ) {
				$item = (array) $item;
			}
			if ( ! is_array( $item ) || ! isset( $item['ID'] ) ) {
				continue;
			}
			$id = (int) $item['ID'];
			if ( $id <= 0 ) {
				continue;
			}
			$node           = array(
				'id'       => $id,
				'title'    => isset( $item['title'] ) ? (string) $item['title'] : '',
				'url'      => isset( $item['url'] ) ? (string) $item['url'] : '',
				'parent'   => isset( $item['menu_item_parent'] ) ? (int) $item['menu_item_parent'] : 0,
				'children' => array(),
				'classes'  => isset( $item['classes'] ) && is_array( $item['classes'] ) ? $item['classes'] : array(),
				'target'   => isset( $item['target'] ) ? (string) $item['target'] : '',
			);
			$by_id[ $id ]   = $node;
			$ordered[ $id ] = $node;
			unset( $nodes[ $id ] );
		}

		foreach ( array_keys( $ordered ) as $id ) {
			$parent = $ordered[ $id ]['parent'];
			if ( $parent > 0 && isset( $ordered[ $parent ] ) && $parent !== $id ) {
				$ordered[ $parent ]['children'][] = $id;
			}
		}

		$tree = array();
		foreach ( $ordered as $id => $node ) {
			if ( 0 === $node['parent'] || ! isset( $ordered[ $node['parent'] ] ) ) {
				$tree[] = self::attach_children( $id, $ordered );
			}
		}

		unset( $nodes, $by_id );
		return $tree;
	}

	/**
	 * Recursively attach children to one node.
	 *
	 * @param int                              $id      Node id.
	 * @param array<int, array<string, mixed>> $ordered Flat ordered nodes.
	 * @param array<int, int>                  $seen    Ids already visited.
	 * @return array<string, mixed>
	 */
	private static function attach_children( int $id, array $ordered, array $seen = array() ): array {
		$node             = $ordered[ $id ];
		$node['children'] = array();
		$seen[ $id ]      = $id;
		foreach ( $ordered[ $id ]['children'] as $child_id ) {
			if ( isset( $seen[ $child_id ] ) || ! isset( $ordered[ $child_id ] ) ) {
				continue;
			}
			$node['children'][] = self::attach_children( (int) $child_id, $ordered, $seen );
		}
		return $node;
	}

	/**
	 * Product mega menu: four tabs built from real product categories.
	 *
	 * The reference groups the fifteen categories into four tabs. When the
	 * fixture or live site has top-level categories, they become the tabs; when
	 * it has none, a single "all products" tab is returned.
	 *
	 * @param int $limit Per-tab item cap.
	 * @return array<int, array{key: string, label: string, url: string, items: array<int, array{title: string, url: string}>}>
	 */
	public static function product_tabs( int $limit = 8 ): array {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return array();
		}
		$parents = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => true,
				'number'     => 4,
			)
		);
		if ( ! is_array( $parents ) || array() === $parents ) {
			return array();
		}

		$tabs = array();
		foreach ( $parents as $index => $parent ) {
			$children = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => (int) $parent->term_id,
					'hide_empty' => true,
					'number'     => $limit,
				)
			);
			$items    = array();
			foreach ( is_array( $children ) ? $children : array() as $child ) {
				$url = get_term_link( $child, 'product_cat' );
				if ( ! is_string( $url ) ) {
					continue;
				}
				$items[] = array(
					'title' => (string) $child->name,
					'url'   => $url,
				);
			}
			$parent_url = get_term_link( $parent, 'product_cat' );
			$tabs[]     = array(
				'key'   => 'product-tab-' . ( (int) $index + 1 ),
				'label' => (string) $parent->name,
				'url'   => is_string( $parent_url ) ? $parent_url : '',
				'items' => $items,
			);
		}
		return $tabs;
	}

	/**
	 * Project mega menu: cities and project types from real terms.
	 *
	 * @param int $limit Items per group.
	 * @return array<string, array<int, array{title: string, url: string}>>
	 */
	public static function project_groups( int $limit = 10 ): array {
		$groups = array(
			'type' => array(),
			'city' => array(),
		);
		foreach ( array(
			'type' => ContentTypes::PROJECT_TYPE,
			'city' => ContentTypes::CITY,
		) as $bucket => $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => true,
					'number'     => $limit,
					'orderby'    => 'count',
					'order'      => 'DESC',
				)
			);
			foreach ( is_array( $terms ) ? $terms : array() as $term ) {
				$url = get_term_link( $term, $taxonomy );
				if ( ! is_string( $url ) ) {
					continue;
				}
				$groups[ $bucket ][] = array(
					'title' => (string) $term->name,
					'url'   => $url,
				);
			}
		}
		return $groups;
	}

	/**
	 * Services mega menu: the published service posts.
	 *
	 * @param int $limit Maximum items.
	 * @return array<int, array{title: string, url: string}>
	 */
	public static function service_items( int $limit = 8 ): array {
		if ( ! post_type_exists( ContentTypes::SERVICES ) ) {
			return array();
		}
		$posts = get_posts(
			array(
				'post_type'        => ContentTypes::SERVICES,
				'post_status'      => 'publish',
				'numberposts'      => $limit,
				'orderby'          => 'menu_order date',
				'order'            => 'ASC',
				'suppress_filters' => false,
			)
		);
		$items = array();
		foreach ( $posts as $post ) {
			$url = get_permalink( $post );
			if ( ! is_string( $url ) ) {
				continue;
			}
			$items[] = array(
				'title' => (string) get_the_title( $post ),
				'url'   => $url,
			);
		}
		return $items;
	}

	/**
	 * Corporate mega menu: the four "about" destinations from the reference.
	 *
	 * These are links to pages that must exist; a page template that is
	 * missing is skipped rather than linked to a 404.
	 *
	 * @return array<int, array{title: string, description: string, url: string}>
	 */
	public static function corporate_items(): array {
		$pages = array(
			'about'               => __( 'درباره کارخانه عرفان صنعت', 'erfansanat' ),
			'licenses-and-honors' => __( 'مجوزها و افتخارات رسمی', 'erfansanat' ),
			'faq'                 => __( 'سوالات متداول (FAQ)', 'erfansanat' ),
			'recruitment'         => __( 'فرصت‌های شغلی و استخدام', 'erfansanat' ),
			'contact'             => __( 'تماس با کارخانه و دفتر مرکزی', 'erfansanat' ),
		);
		$items = array();
		foreach ( $pages as $slug => $title ) {
			$page = get_page_by_path( $slug );
			if ( ! $page instanceof \WP_Post ) {
				continue;
			}
			$url = get_permalink( $page );
			if ( ! is_string( $url ) ) {
				continue;
			}
			$items[] = array(
				'title'       => $title,
				'description' => (string) get_the_excerpt( $page ),
				'url'         => $url,
			);
		}
		return $items;
	}

	/**
	 * Whether a block would render anything at all.
	 *
	 * @param string $block Block name.
	 * @return bool
	 */
	public static function block_has_content( string $block ): bool {
		switch ( $block ) {
			case 'products':
				return array() !== self::product_tabs();
			case 'projects':
				$groups = self::project_groups();
				return array() !== $groups['type'] || array() !== $groups['city'];
			case 'services':
				return array() !== self::service_items();
			case 'corporate':
				return array() !== self::corporate_items();
			default:
				return false;
		}
	}

	/**
	 * Labels of the four blocks, matching the reference wording.
	 *
	 * @return array<string, string>
	 */
	public static function block_labels(): array {
		return array(
			'products'  => __( 'محصولات', 'erfansanat' ),
			'projects'  => __( 'پروژه‌ها', 'erfansanat' ),
			'services'  => __( 'خدمات', 'erfansanat' ),
			'corporate' => __( 'درباره شرکت', 'erfansanat' ),
		);
	}
}
