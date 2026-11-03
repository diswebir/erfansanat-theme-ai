<?php
/**
 * Conditional enqueue with filemtime cache busting.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Assets;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues theme assets based on Manifest conditions.
 */
final class AssetManager implements ModuleInterface {

	/**
	 * Style handles that were enqueued by this class.
	 *
	 * @var string[]
	 */
	private array $enqueued_styles = array();

	/**
	 * Script handles that were enqueued by this class.
	 *
	 * @var string[]
	 */
	private array $enqueued_scripts = array();

	/**
	 * {@inheritDoc}
	 */
	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20 );
		add_filter( 'script_loader_tag', array( $this, 'add_defer_attribute' ), 10, 3 );
	}

	/**
	 * Enqueue everything whose condition passes.
	 */
	public function enqueue(): void {
		foreach ( Manifest::styles() as $handle => $asset ) {
			if ( ! $this->matches( $asset ) || ! $this->available( $asset ) ) {
				continue;
			}

			wp_enqueue_style(
				$handle,
				$this->url( $asset['file'] ),
				$asset['deps'] ?? array(),
				$this->version( $asset['file'] )
			);

			$this->enqueued_styles[] = $handle;
		}

		foreach ( Manifest::scripts() as $handle => $asset ) {
			if ( ! $this->matches( $asset ) || ! $this->available( $asset ) ) {
				continue;
			}

			wp_enqueue_script(
				$handle,
				$this->url( $asset['file'] ),
				$asset['deps'] ?? array(),
				$this->version( $asset['file'] ),
				(bool) ( $asset['in_footer'] ?? false )
			);

			$this->enqueued_scripts[] = $handle;
		}
	}

	/**
	 * Add `defer` to handles declared with defer = true.
	 *
	 * @param string $tag    Script tag.
	 * @param string $handle Script handle.
	 * @param string $src    Script source.
	 * @return string
	 */
	public function add_defer_attribute( $tag, $handle, $src = '' ): string {
		$scripts = Manifest::scripts();

		if ( empty( $scripts[ $handle ]['defer'] ) || false !== strpos( $tag, ' defer' ) ) {
			return $tag;
		}

		return (string) preg_replace( '/\s(src=)/', ' defer $1', $tag, 1 );
	}

	/**
	 * Absolute URL for a theme relative asset.
	 *
	 * @param string $relative Relative path inside the theme.
	 */
	public function url( string $relative ): string {
		return ES_THEME_URI . '/' . ltrim( $relative, '/' );
	}

	/**
	 * Absolute filesystem path for a theme relative asset.
	 *
	 * @param string $relative Relative path inside the theme.
	 */
	public function path( string $relative ): string {
		return ES_THEME_DIR . '/' . ltrim( $relative, '/' );
	}

	/**
	 * Version string: theme version + filemtime so edits bust caches instantly.
	 *
	 * @param string $relative Relative path inside the theme.
	 */
	public function version( string $relative ): string {
		$file = $this->path( $relative );

		if ( is_file( $file ) ) {
			return ES_THEME_VERSION . '.' . filemtime( $file );
		}

		return ES_THEME_VERSION;
	}

	/**
	 * Handles enqueued by this manager (test helper).
	 *
	 * @return array{styles:string[],scripts:string[]}
	 */
	public function enqueued(): array {
		return array(
			'styles'  => $this->enqueued_styles,
			'scripts' => $this->enqueued_scripts,
		);
	}

	/**
	 * Never emit a 404-ing tag: the file has to exist on disk.
	 *
	 * @param array<string,mixed> $asset Asset definition.
	 */
	private function available( array $asset ): bool {
		$file = $this->path( (string) ( $asset['file'] ?? '' ) );

		if ( is_readable( $file ) ) {
			return true;
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( sprintf( 'ErfanSanat: missing asset %s', $file ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}

		return false;
	}

	/**
	 * Evaluate a manifest condition.
	 *
	 * @param array<string,mixed> $asset Asset definition.
	 */
	private function matches( array $asset ): bool {
		$condition = $asset['condition'] ?? null;

		return is_callable( $condition ) ? (bool) $condition() : true;
	}
}
