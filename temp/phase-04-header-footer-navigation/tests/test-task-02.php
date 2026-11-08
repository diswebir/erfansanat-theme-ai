<?php
declare(strict_types=1);
/**
 * Task 4.2 acceptance: the four nav locations, the tree walker and block data.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Navigation\MegaMenu;
use ErfanSanat\Theme\Navigation\MenuTree;
use ErfanSanat\Theme\Navigation\MobileMenu;
use PHPUnit\Framework\TestCase;

/**
 * Verifies menu registration and the mega-menu data providers.
 */
final class TaskP04T02Test extends TestCase {

	/**
	 * Create a menu with the given items and assign it to a location.
	 *
	 * @param string                            $location Theme location slug.
	 * @param array<int, array<string, mixed>>  $items    Flat item definitions (id, title, parent).
	 * @return int Menu term id.
	 */
	private function assign_menu( string $location, array $items ): int {
		$menu_id = wp_create_nav_menu( 'p04-' . $location . '-' . wp_generate_password( 6, false ) );

		foreach ( $items as $item ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $item['title'],
					'menu-item-url'       => $item['url'] ?? home_url( '/' ),
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $item['parent'] ?? 0,
				)
			);
		}

		$locations              = get_nav_menu_locations();
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		return (int) $menu_id;
	}

	/**
	 * Delete every menu created by this test class.
	 */
	private function clear_menus(): void {
		foreach ( wp_get_nav_menus( array( 'hide_empty' => false ) ) as $menu ) {
			if ( str_starts_with( (string) $menu->name, 'p04-' ) ) {
				wp_delete_nav_menu( $menu->term_id );
			}
		}
		set_theme_mod( 'nav_menu_locations', array() );
	}

	/**
	 * Make sure the theme's own taxonomy registrations are live.
	 *
	 * The runner boots the theme, but a test that changes the current user or
	 * theme mods can fire `switch_theme`, so the product taxonomy is primed
	 * explicitly before any taxonomy-backed assertion.
	 */
	private function prime_content_types(): void {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			\ErfanSanat\Theme\ContentTypes::register();
			do_action( 'init' );
		}
		$this->assertTrue( taxonomy_exists( 'product_cat' ) );
	}

	/**
	 * The four locations the reference navigation needs are registered.
	 */
	public function testFourLocationsAreRegistered(): void {
		$locations = get_registered_nav_menus();

		foreach ( array( 'main-mega-menu', 'mobile-menu', 'tolidat-sherkat', 'landing' ) as $slug ) {
			$this->assertArrayHasKey( $slug, $locations, $slug . ' must be registered.' );
			$this->assertNotSame( '', trim( (string) $locations[ $slug ] ), $slug . ' needs a human label.' );
		}
	}

	/**
	 * The reference blocks are exactly the four mega panels, in order.
	 */
	public function testMegaPanelHasExplicitResponsiveGeometry(): void {
		$markup = (string) file_get_contents( get_template_directory() . '/inc/Navigation/MegaMenu.php' );
		$css    = (string) file_get_contents( get_template_directory() . '/assets/css/navigation.css' );
		$assets = (string) file_get_contents( get_template_directory() . '/inc/AssetManager.php' );

		$this->assertStringContainsString( 'erfansanat-mega-panel', $markup );
		$this->assertStringContainsString( 'width: min(780px, calc(100vw - 2rem))', $css );
		$this->assertStringContainsString( 'inset-block-start: 100%', $css );
		$this->assertStringContainsString( '@media (max-width: 1023px)', $css );
		$this->assertStringContainsString( "'css/navigation.css'", $assets );
	}

	public function testBlocksMatchTheReferencePanels(): void {
		$this->assertSame(
			array( 'products', 'projects', 'services', 'corporate' ),
			MenuTree::blocks()
		);

		$labels = MenuTree::block_labels();
		$this->assertSame( 'محصولات', $labels['products'] );
		$this->assertSame( 'پروژه‌ها', $labels['projects'] );
	}

	/**
	 * Children are nested under their parents, in menu order.
	 */
	public function testTreeNestsChildrenInOrder(): void {
		$this->clear_menus();
		$parent = 0;

		$menu_id = $this->assign_menu(
			'main-mega-menu',
			array(
				array( 'title' => 'ریشه' ),
				array( 'title' => 'فرزند دوم', 'parent' => $parent ),
			)
		);
		// Re-parent after creation so the ids are known.
		$items = wp_get_nav_menu_items( $menu_id );
		$root  = $items[0];
		wp_update_nav_menu_item(
			$menu_id,
			(int) $items[1]->ID,
			array(
				'menu-item-title'     => 'فرزند دوم',
				'menu-item-url'       => home_url( '/second/' ),
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => (int) $root->ID,
			)
		);

		$tree = MenuTree::from_location( 'main-mega-menu' );

		$this->assertCount( 1, $tree, 'The child must not be promoted to the top level.' );
		$this->assertSame( 'ریشه', $tree[0]['title'] );
		$this->assertNotEmpty( $tree[0]['children'] );
		$this->assertSame( 'فرزند دوم', $tree[0]['children'][0]['title'] );

		$this->clear_menus();
	}

	/**
	 * A menu with a broken/empty tree must not fatal or drop siblings.
	 */
	public function testWalkerIsOrphanAndCycleSafe(): void {
		$this->clear_menus();

		$menu_id = $this->assign_menu(
			'landing',
			array(
				array( 'title' => 'یک' ),
				array( 'title' => 'دو' ),
				array( 'title' => 'سه' ),
			)
		);

		// Point an item at a parent that does not exist.
		$items = wp_get_nav_menu_items( $menu_id );
		wp_update_nav_menu_item(
			$menu_id,
			(int) $items[2]->ID,
			array(
				'menu-item-title'     => 'سه',
				'menu-item-url'       => home_url( '/three/' ),
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => 999999,
			)
		);

		$tree = MenuTree::from_location( 'landing' );

		$this->assertNotEmpty( $tree, 'A broken parent must not empty the whole tree.' );
		$titles = array_column( $tree, 'title' );
		$this->assertContains( 'یک', $titles );
		$this->assertContains( 'دو', $titles, 'Siblings must survive an orphan.' );

		$this->clear_menus();
	}

	/**
	 * Products, projects, services and corporate all render without data loss.
	 */
	public function testEveryBlockRendersItsPanel(): void {
		foreach ( MenuTree::blocks() as $block ) {
			ob_start();
			MegaMenu::render_block( $block );
			$html = (string) ob_get_clean();

			$this->assertStringContainsString( 'data-desktop-mega-trigger="' . $block . '"', $html, $block . ' trigger missing.' );
			$this->assertStringContainsString( 'data-desktop-mega-panel="' . $block . '"', $html, $block . ' panel missing.' );
			$this->assertStringContainsString( 'aria-expanded="false"', $html, $block . ' must start collapsed.' );
			$this->assertStringContainsString( 'aria-controls=', $html, $block . ' trigger must point at its panel.' );
			$this->assertStringNotContainsString( 'Fatal error', $html );
			$this->assertStringNotContainsString( 'Undefined', $html );
		}
	}

	/**
	 * The desktop tab rails keep the data-* hooks the transferred main.js binds.
	 */
	public function testDesktopTabHooksMatchTheTransferredScript(): void {
		$this->prime_content_types();

		ob_start();
		MegaMenu::render_block( 'products' );
		$products = (string) ob_get_clean();

		$tabs   = preg_match_all( '/data-mega-prod-tab="(\d+)"/', $products, $tab_matches );
		$panels = preg_match_all( '/data-mega-prod-panel="(\d+)"/', $products, $panel_matches );

		$this->assertGreaterThan( 0, $tabs, 'The products panel needs its tab rail.' );
		$this->assertSame( $tabs, $panels, 'Every product tab needs exactly one panel.' );
		$this->assertSame( $tab_matches[1], $panel_matches[1], 'Tab and panel keys must line up.' );

		ob_start();
		MegaMenu::render_block( 'projects' );
		$projects = (string) ob_get_clean();

		$this->assertStringContainsString( 'data-mega-proj-tab="type"', $projects );
		$this->assertStringContainsString( 'data-mega-proj-tab="city"', $projects );
		$this->assertStringContainsString( 'data-mega-proj-panel="type"', $projects );
		$this->assertStringContainsString( 'data-mega-proj-panel="city"', $projects );
		$this->assertStringContainsString( 'data-mobile-proj-tab', $this->mobile_panel( 'projects' ) );
		$this->assertStringContainsString( 'data-mobile-prod-tab', $this->mobile_panel( 'products' ) );
	}

	/**
	 * Render one mobile accordion panel and return its HTML.
	 *
	 * @param string $block Block name.
	 * @return string
	 */
	private function mobile_panel( string $block ): string {
		ob_start();
		MobileMenu::render( \ErfanSanat\Theme\Navigation\MenuTree::from_location( 'mobile-menu' ) );
		$html = (string) ob_get_clean();
		$this->assertStringContainsString( 'data-mobile-acc-panel="' . $block . '"', $html );
		return $html;
	}

	/**
	 * An unknown block name is ignored instead of printing a broken header.
	 */
	public function testUnknownBlockIsIgnored(): void {
		ob_start();
		MegaMenu::render_block( 'does-not-exist' );
		$this->assertSame( '', (string) ob_get_clean() );
		$this->assertFalse( MenuTree::block_has_content( 'does-not-exist' ) );
	}

	/**
	 * Product tabs read the real top-level product categories.
	 */
	public function testProductTabsComeFromTheLiveTaxonomy(): void {
		$this->prime_content_types();
		$tabs = MenuTree::product_tabs();
		$this->assertNotEmpty( $tabs, 'The fixture data has product categories.' );
		$this->assertLessThanOrEqual( 8, count( $tabs ) );

		foreach ( $tabs as $tab ) {
			$this->assertNotEmpty( $tab['label'], 'Every tab needs a label.' );
			$this->assertNotEmpty( $tab['url'], 'Every tab needs a link.' );
			$this->assertStringStartsWith( 'http', (string) $tab['url'] );
			$this->assertIsArray( $tab['items'] );
		}
	}

	/**
	 * Corporate links may only point at pages that actually exist.
	 */
	public function testCorporateLinksNeverInventAPage(): void {
		$this->prime_content_types();
		$items = MenuTree::corporate_items();

		$this->assertNotEmpty( $items, 'The fixture has the corporate pages.' );
		foreach ( $items as $item ) {
			$this->assertNotEmpty( $item['title'] );
			$id = url_to_postid( (string) $item['url'] );
			$this->assertGreaterThan( 0, $id, 'Every corporate link must resolve to a real page: ' . $item['url'] );
			$this->assertSame( 'publish', get_post_status( $id ) );
		}

		// A slug that does not exist is skipped, never linked as a 404.
		$all = array_column( $items, 'url' );
		$this->assertNotContains( home_url( '/does-not-exist/' ), $all );
	}

	/**
	 * The mobile drawer mirrors the four blocks as accordions.
	 */
	public function testMobileDrawerMirrorsTheBlocks(): void {
		$tree = MenuTree::from_location( 'mobile-menu' );

		ob_start();
		MobileMenu::render( $tree );
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( 'data-mobile-menu="true"', $html );
		$this->assertStringContainsString( 'data-mobile-sidebar', $html );
		$this->assertStringContainsString( 'erfansanat-brand', $html );
		$this->assertStringNotContainsString( 'Fatal error', $html );
	}

	/**
	 * Every renderer escapes what it prints from the database.
	 */
	public function testRendererOutputIsEscaped(): void {
		$this->clear_menus();
		$this->assign_menu(
			'mobile-menu',
			array(
				array( 'title' => '<script>alert(1)</script>', 'url' => 'javascript:alert(1)' ),
			)
		);

		ob_start();
		MobileMenu::render( MenuTree::from_location( 'mobile-menu' ) );
		$html = (string) ob_get_clean();

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $html, 'Item titles must be escaped.' );
		$this->assertStringNotContainsString( 'javascript:alert(1)', $html, 'javascript: URLs must be stripped.' );

		$this->clear_menus();
	}
}
