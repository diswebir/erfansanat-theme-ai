<?php
/**
 * Theme kernel.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

use ErfanSanat\Theme\Admin\AdminMenu;
use ErfanSanat\Theme\Admin\AjaxSave;
use ErfanSanat\Theme\Admin\SectionsPage;
use ErfanSanat\Theme\Admin\ImportExport;
use ErfanSanat\Theme\Admin\ListTables;
use ErfanSanat\Theme\Admin\Tabs\ArchiveTab;
use ErfanSanat\Theme\Admin\Tabs\BrandTab;
use ErfanSanat\Theme\Admin\Tabs\FooterTab;
use ErfanSanat\Theme\Admin\Tabs\HeaderTab;
use ErfanSanat\Theme\Admin\Tabs\ScriptsTab;
use ErfanSanat\Theme\Admin\UrlAudit;
use ErfanSanat\Theme\Assets\AssetManager;
use ErfanSanat\Theme\Assets\Preload;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Content\PostTypes;
use ErfanSanat\Theme\Content\Rewrites;
use ErfanSanat\Theme\Content\Taxonomies;
use ErfanSanat\Theme\Frontend\ArchiveConfig;
use ErfanSanat\Theme\Frontend\CustomCode;
use ErfanSanat\Theme\Frontend\InlineCss;
use ErfanSanat\Theme\Performance\DeBloat;
use ErfanSanat\Theme\Setup\Menus;
use ErfanSanat\Theme\Setup\ThemeSupport;

defined( 'ABSPATH' ) || exit;

/**
 * Boots the theme: registers hooks and instantiates modules.
 */
final class Theme {

	/**
	 * Singleton instance.
	 */
	private static ?Theme $instance = null;

	/**
	 * Registered modules.
	 *
	 * @var array<string,ModuleInterface>
	 */
	private array $modules = array();

	/**
	 * Whether boot() already ran.
	 */
	private bool $booted = false;

	/**
	 * Private constructor — use instance().
	 */
	private function __construct() {}

	/**
	 * Retrieve the singleton.
	 */
	public static function instance(): Theme {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot the theme. Safe to call multiple times.
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		$this->register_module( 'theme-support', new ThemeSupport() );
		$this->register_module( 'post-types', new PostTypes() );
		$this->register_module( 'taxonomies', new Taxonomies() );
		$this->register_module( 'rewrites', new Rewrites() );
		$this->register_module( 'url-audit', new UrlAudit() );
		$this->register_module( 'admin-menu', new AdminMenu() );
		$this->register_module( 'list-tables', new ListTables() );
		$this->register_module( 'archive-tab', new ArchiveTab() );
		$this->register_module( 'archive-config', new ArchiveConfig() );
		$this->register_module( 'scripts-tab', new ScriptsTab() );
		$this->register_module( 'custom-code', new CustomCode() );
		$this->register_module( 'brand-tab', new BrandTab() );
		$this->register_module( 'inline-css', new InlineCss() );
		$this->register_module( 'header-tab', new HeaderTab() );
		$this->register_module( 'footer-tab', new FooterTab() );
		$this->register_module( 'ajax-save', new AjaxSave() );
		$this->register_module( 'sections-page', new SectionsPage() );
		$this->register_module( 'import-export', new ImportExport() );
		$this->register_module( 'menus', new Menus() );
		$this->register_module( 'debloat', new DeBloat() );
		$this->register_module( 'assets', new AssetManager() );
		$this->register_module( 'preload', new Preload() );

		/**
		 * Fires after the theme modules are registered.
		 *
		 * @param Theme $theme Kernel instance.
		 */
		do_action( 'es_theme_booted', $this );
	}

	/**
	 * Register (and boot) a module.
	 *
	 * @param string          $key    Unique module key.
	 * @param ModuleInterface $module Module instance.
	 */
	public function register_module( string $key, ModuleInterface $module ): void {
		if ( isset( $this->modules[ $key ] ) ) {
			return;
		}

		$this->modules[ $key ] = $module;

		$module->register();
	}

	/**
	 * Access a registered module.
	 *
	 * @param string $key Module key.
	 */
	public function module( string $key ): ?ModuleInterface {
		return $this->modules[ $key ] ?? null;
	}

	/**
	 * All registered module keys (test helper).
	 *
	 * @return string[]
	 */
	public function module_keys(): array {
		return array_keys( $this->modules );
	}
}
