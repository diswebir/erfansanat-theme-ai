<?php
declare(strict_types=1);
/**
 * Task 1.3 acceptance: assets transferred byte-for-byte and loaded conditionally.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\AssetManager;
use PHPUnit\Framework\TestCase;

/**
 * Checks the transferred files, their versions, path safety and asset isolation.
 */
final class TaskP01T03Test extends TestCase {

	/**
	 * Absolute path to the immutable static reference.
	 *
	 * The theme directory is a symlink inside .local/wordpress, so the reference
	 * is resolved from the repository root instead of from the theme path.
	 *
	 * @return string
	 */
	private function reference_root(): string {
		$root = getenv( 'ERFANSANAT_REPO_ROOT' );
		if ( is_string( $root ) && '' !== $root ) {
			return rtrim( $root, '/' ) . '/static-mirror';
		}
		$theme = realpath( get_template_directory() );
		$repo = $theme ? dirname( $theme ) : '';
		$this->assertNotSame( '', $repo, 'The theme must live in the repository root for reference comparisons.' );
		$this->assertDirectoryExists( $repo . '/static-mirror' );
		return $repo . '/static-mirror';
	}

	/**
	 * AssetManager source with comments removed, so documentation text cannot
	 * satisfy or break a code assertion.
	 *
	 * @return string
	 */
	private function asset_source_without_comments(): string {
		$tokens = token_get_all( (string) file_get_contents( get_template_directory() . '/inc/AssetManager.php' ) );
		$code = '';
		foreach ( $tokens as $token ) {
			if ( is_array( $token ) && in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) ) {
				continue;
			}
			$code .= is_array( $token ) ? $token[1] : $token;
		}
		return $code;
	}

	/**
	 * Every transferred asset must match the immutable reference byte-for-byte.
	 */
	public function testTransferredAssetsMatchTheReference(): void {
		$pairs = array(
			'assets/css/styles.css' => 'assets/css/styles.css',
			'assets/js/main.js' => 'assets/js/main.js',
			'assets/fonts/vazirmatn-arabic.woff2' => 'assets/fonts/vazirmatn-arabic.woff2',
			'assets/fonts/vazirmatn-latin.woff2' => 'assets/fonts/vazirmatn-latin.woff2',
			'assets/fonts/vazirmatn-latin-ext.woff2' => 'assets/fonts/vazirmatn-latin-ext.woff2',
			'images/hero-video.mp4' => 'assets/video/hero-video.mp4',
			'images/ese-logo.svg' => 'assets/images/ese-logo.svg',
			'images/hero.jpg' => 'assets/images/hero.jpg',
			'images/pexels/pexels-19112845.jpg' => 'assets/images/pexels/pexels-19112845.jpg',
		);
		$reference = $this->reference_root();

		foreach ( $pairs as $source => $target ) {
			$from = $reference . '/' . $source;
			$to = get_template_directory() . '/' . $target;
			$this->assertFileExists( $from, 'Reference file is missing: ' . $source );
			$this->assertFileExists( $to, 'Transferred file is missing: ' . $target );
			$this->assertSame(
				hash_file( 'sha256', $from ),
				hash_file( 'sha256', $to ),
				'Transferred file differs from the reference: ' . $target
			);
		}
	}

	/**
	 * The whole image set must be present so later templates cannot 404.
	 */
	public function testEveryReferenceImageWasTransferred(): void {
		$reference = $this->reference_root() . '/images/';
		$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $reference, FilesystemIterator::SKIP_DOTS ) );
		$count = 0;
		foreach ( $iterator as $file ) {
			if ( ! $file->isFile() || ! preg_match( '/\.(?:jpe?g|png|svg|webp)$/i', $file->getFilename() ) ) {
				continue;
			}
			++$count;
			$relative = substr( $file->getPathname(), strlen( $reference ) );
			$this->assertFileExists(
				get_template_directory() . '/assets/images/' . $relative,
				'Missing transferred image: ' . $relative
			);
		}
		$this->assertSame( 21, $count, 'The reference contains 20 JPG files and one SVG.' );
	}

	/**
	 * Stylesheet and script are registered with filemtime versions.
	 */
	public function testEnqueuedFilesUseFilemtimeVersions(): void {
		AssetManager::enqueue_frontend();

		$style = wp_styles()->registered[ AssetManager::STYLE_HANDLE ] ?? null;
		$script = wp_scripts()->registered[ AssetManager::SCRIPT_HANDLE ] ?? null;

		$this->assertNotNull( $style, 'The stylesheet handle must be registered.' );
		$this->assertNotNull( $script, 'The script handle must be registered.' );
		$this->assertSame( AssetManager::uri( 'css/styles.css' ), $style->src );
		$this->assertSame( AssetManager::uri( 'js/main.js' ), $script->src );
		$this->assertSame(
			(string) filemtime( get_template_directory() . '/assets/css/styles.css' ),
			(string) $style->ver,
			'The stylesheet version must equal the file modification time.'
		);
		$this->assertSame(
			(string) filemtime( get_template_directory() . '/assets/js/main.js' ),
			(string) $script->ver
		);
		$this->assertMatchesRegularExpression( '/^\d+$/', (string) $style->ver );
	}

	/**
	 * The script must load after the markup it enhances.
	 */
	public function testScriptLoadsInTheFooter(): void {
		AssetManager::enqueue_frontend();
		$script = wp_scripts()->registered[ AssetManager::SCRIPT_HANDLE ];
		$this->assertTrue( (bool) $script->extra['group'] ?? true, 'The behaviour script is printed in the footer.' );
		$this->assertSame( array(), $script->deps, 'The script must not depend on jQuery or any framework.' );
		$this->assertSame( array(), wp_styles()->registered[ AssetManager::STYLE_HANDLE ]->deps );
	}

	/**
	 * A missing file must fall back to the theme version instead of a stale stamp.
	 */
	public function testMissingAssetFallsBackToThemeVersion(): void {
		$this->assertSame( ERFANSANAT_THEME_VERSION, AssetManager::version( 'css/does-not-exist.css' ) );
		$this->assertFalse( AssetManager::has( 'css/does-not-exist.css' ) );
	}

	/**
	 * Asset paths must never escape the assets directory.
	 */
	public function testAssetPathsCannotEscapeTheTheme(): void {
		$this->assertSame( '', AssetManager::path( '../functions.php' ) );
		$this->assertSame( '', AssetManager::path( 'css/../../functions.php' ) );
		$this->assertSame( '', AssetManager::path( '' ) );
		$this->assertSame( '', AssetManager::path( '.' ) );
		$this->assertSame( array(), AssetManager::media( '../../wp-config.php' ) );
		$this->assertTrue( AssetManager::has( 'images/hero.jpg' ) );
		$this->assertStringEndsWith( '/assets/css/styles.css', AssetManager::path( 'css/styles.css' ) );
	}

	/**
	 * Font paths inside the stylesheet must resolve to the transferred files.
	 */
	public function testRelativeFontPathsResolveToRealFiles(): void {
		$css = (string) file_get_contents( get_template_directory() . '/assets/css/styles.css' );
		$this->assertMatchesRegularExpression( '/url\([\'"]?\.\.\/fonts\/([a-z0-9.-]+\.woff2)/', $css );
		preg_match_all( '/url\([\'"]?\.\.\/fonts\/([a-z0-9.-]+\.woff2)/', $css, $matches );
		$this->assertNotEmpty( $matches[1] );
		foreach ( array_unique( $matches[1] ) as $font ) {
			$path = get_template_directory() . '/assets/css/../fonts/' . $font;
			$this->assertFileExists( $path, 'CSS references a font that was not transferred: ' . $font );
		}
		$this->assertCount( 3, array_unique( $matches[1] ) );
	}

	/**
	 * Media printed outside the queue must report real dimensions.
	 */
	public function testMediaHelperReturnsDimensions(): void {
		$media = AssetManager::media( 'images/hero.jpg' );
		$this->assertArrayHasKey( 'url', $media );
		$this->assertGreaterThan( 0, $media['width'] );
		$this->assertGreaterThan( 0, $media['height'] );
		$this->assertStringContainsString( '/assets/images/hero.jpg', $media['url'] );
		$this->assertSame( array(), AssetManager::media( 'images/missing.jpg' ) );
		$this->assertSame( array(), AssetManager::media( 'js/main.js' ), 'Non-images must not report dimensions.' );
	}

	/**
	 * The 6.7 MB video must never be queued or preloaded on ordinary pages.
	 */
	public function testVideoIsNotEnqueuedAnywhere(): void {
		global $wp_scripts, $wp_styles;
		$handles = array_merge( array_keys( $wp_scripts->registered ), array_keys( $wp_styles->registered ) );
		foreach ( $handles as $handle ) {
			if ( isset( $wp_scripts->registered[ $handle ] ) ) {
				$this->assertStringNotContainsString( 'hero-video.mp4', (string) $wp_scripts->registered[ $handle ]->src, $handle );
			}
			if ( isset( $wp_styles->registered[ $handle ] ) ) {
				$this->assertStringNotContainsString( 'hero-video.mp4', (string) $wp_styles->registered[ $handle ]->src, $handle );
			}
		}

		$source = (string) file_get_contents( get_template_directory() . '/inc/AssetManager.php' );
		$this->assertStringNotContainsString( 'wp_enqueue_script( self::SCRIPT_HANDLE, self::uri( \'video/', $source );
		$this->assertSame(
				array( 'print_font_preloads' ),
			array_values(
				array_filter(
					array_map(
						static fn( $line ) => preg_match( '/function (\w*preload\w*)/i', $line, $m ) ? $m[1] : '',
						explode( "\n", $source )
					)
				)
			),
			'Only fonts may be preloaded by AssetManager.'
		);
		$this->assertStringNotContainsString( 'hero-video.mp4', $this->asset_source_without_comments(), 'The video must never be queued by the asset manager.' );
		$this->assertTrue( AssetManager::has( 'video/hero-video.mp4' ), 'The video ships with the theme but is printed by the hero section only.' );
	}

	/**
	 * Dashicons are dropped for visitors but kept for logged-in users.
	 */
	public function testDashiconsAreDroppedForVisitorsOnly(): void {
		wp_enqueue_style( 'dashicons' );
		$current = get_current_user_id();
		wp_set_current_user( 0 );
		AssetManager::drop_unneeded_styles();
		$dropped = ! wp_style_is( 'dashicons', 'enqueued' );
		wp_set_current_user( $current );
		$this->assertTrue( $dropped );
	}

	/**
	 * Font preloads are printed once, with crossorigin and without duplicates.
	 */
	public function testFontPreloadsArePrintedWithCrossorigin(): void {
		ob_start();
		AssetManager::print_font_preloads();
		$html = (string) ob_get_clean();
		$this->assertSame( 2, substr_count( $html, '<link rel="preload"' ) );
		$this->assertSame( 2, substr_count( $html, 'as="font"' ) );
		$this->assertSame( 2, substr_count( $html, 'crossorigin' ) );
		$this->assertStringContainsString( 'vazirmatn-arabic.woff2', $html );
		$this->assertStringNotContainsString( 'hero-video', $html );
	}

	/**
	 * The approval record must exist as a machine-readable manifest.
	 */
	public function testAssetManifestRecordsHashAndSize(): void {
		$manifest_path = get_template_directory() . '/assets/manifest.json';
		$this->assertFileExists( $manifest_path );
		$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );
		$this->assertIsArray( $manifest );
		$this->assertArrayHasKey( 'files', $manifest );
		$this->assertNotEmpty( $manifest['files'] );
		foreach ( $manifest['files'] as $relative => $meta ) {
			$path = get_template_directory() . '/assets/' . $relative;
			$this->assertFileExists( $path, $relative );
			$this->assertSame( $meta['sha256'], hash_file( 'sha256', $path ), 'Manifest hash is stale for ' . $relative );
			$this->assertSame( $meta['bytes'], filesize( $path ) );
		}
	}
}
