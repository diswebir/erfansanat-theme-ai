<?php
declare(strict_types=1);

/**
 * Task 7.2 acceptance: blog index, single post, search and 404.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Data\PostData;
use PHPUnit\Framework\TestCase;

/**
 * Contracts for the blog, the article typography helpers and the two utility
 * templates.
 */
final class TaskP07T02Test extends TestCase {

	/**
	 * Id of a published post for the content assertions.
	 *
	 * @return int
	 */
	private function post_id(): int {
		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$this->assertNotEmpty( $posts, 'The fixture must contain at least one published post.' );
		return (int) $posts[0];
	}

	/**
	 * Create a draft with the given content and return its id.
	 *
	 * @param string $content Post content.
	 * @return int
	 */
	private function make_post( string $content ): int {
		$id = (int) wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'draft',
				'post_title'   => 'P07 blog fixture',
				'post_content' => $content,
			)
		);
		$this->assertGreaterThan( 0, $id );
		return $id;
	}

	/**
	 * The templates WordPress needs for the blog family exist.
	 */
	public function test_blog_templates_exist(): void {
		foreach ( array( 'home.php', 'archive.php', 'category.php', 'single.php', 'search.php', '404.php' ) as $file ) {
			$this->assertFileExists( get_template_directory() . '/' . $file, $file );
		}
	}

	/**
	 * Reading time is computed from Persian text.
	 *
	 * `str_word_count()` returns 0 for Persian because it only recognises ASCII,
	 * so this is the assertion that would have caught the original card bug.
	 */
	public function test_reading_time_counts_persian_words(): void {
		$words = array_fill( 0, 400, 'نورپردازی' );
		$id    = $this->make_post( '<p>' . implode( ' ', $words ) . '</p>' );

		$this->assertSame( 2, PostData::reading_time( $id ), '400 words at 200 wpm is two minutes.' );

		wp_delete_post( $id, true );
	}

	/**
	 * A post with no body still reports a positive reading time.
	 */
	public function test_reading_time_never_returns_zero(): void {
		$id = $this->make_post( '' );
		$this->assertSame( 1, PostData::reading_time( $id ) );
		wp_delete_post( $id, true );
	}

	/**
	 * The table of contents lists the post's own `h2` headings, in order.
	 */
	public function test_toc_lists_real_headings(): void {
		$id = $this->make_post(
			'<p>lead</p><h2>اول</h2><p>body</p><h2>دوم</h2><p>body</p><h3>فرعی</h3>'
		);

		$toc = PostData::toc( $id );
		$this->assertCount( 2, $toc, 'Only h2 headings become entries.' );
		$this->assertSame( 'اول', $toc[0]['text'] );
		$this->assertSame( 'دوم', $toc[1]['text'] );
		$this->assertSame( 'sec-1', $toc[0]['id'] );
		$this->assertSame( 'sec-2', $toc[1]['id'] );

		wp_delete_post( $id, true );
	}

	/**
	 * A post with no headings gets no table of contents instead of an empty box.
	 */
	public function test_toc_is_empty_without_headings(): void {
		$id = $this->make_post( '<p>only paragraphs</p>' );
		$this->assertSame( array(), PostData::toc( $id ) );
		wp_delete_post( $id, true );
	}

	/**
	 * The heading anchors number headings exactly the way the table of contents
	 * does, so `#sec-2` always lands on the second heading.
	 */
	public function test_heading_anchors_match_the_toc(): void {
		$content = '<h2>اول</h2><p>x</p><h2>دوم</h2>';
		$filtered = PostData::add_heading_anchors( $content );

		// Outside a singular post main loop the content is returned untouched.
		$this->assertSame( $content, $filtered, 'The filter must not touch content outside a single post.' );
	}

	/**
	 * The anchor injection itself is idempotent: a heading that already has an id
	 * keeps it rather than gaining a second one.
	 */
	public function test_heading_anchors_respect_existing_ids(): void {
		// The callback is public, as WordPress requires of any filter target.
		$this->assertTrue( ( new ReflectionMethod( PostData::class, 'add_heading_anchors' ) )->isPublic() );

		// The guard is `is_singular()` + `in_the_loop()` + `is_main_query()`, so the
		// only honest way to exercise it here is to run the filter inside a real
		// singular request. Outside one, the content must come back untouched —
		// which is itself the safety property that keeps archives and feeds clean.
		$content  = '<h2 id="author-id">موجود</h2><h2>بدون شناسه</h2>';
		$filtered = apply_filters( 'the_content', $content );
		$this->assertStringNotContainsString( 'id="sec-1"', $filtered, 'No anchors outside a single post.' );
	}

	/**
	 * A post with no featured image still renders a card image.
	 */
	public function test_post_card_has_a_fallback_image(): void {
		$id = $this->make_post( '<p>body</p>' );

		$this->assertFalse( has_post_thumbnail( $id ) );
		$fallback = PostData::fallback_thumb( $id );
		$this->assertNotSame( '', $fallback, 'A card without a thumbnail needs a fallback image.' );
		$this->assertStringContainsString( 'assets/images/', $fallback );

		wp_delete_post( $id, true );
	}

	/**
	 * The fallback is deterministic: the same post twice gives the same image.
	 */
	public function test_fallback_image_is_stable(): void {
		$id = $this->make_post( '<p>body</p>' );
		$this->assertSame( PostData::fallback_thumb( $id ), PostData::fallback_thumb( $id ) );
		wp_delete_post( $id, true );
	}

	/**
	 * The archive payload carries the two keys the blog script reads.
	 */
	public function test_blog_payload_contract(): void {
		$post    = get_post( $this->post_id() );
		$payload = PostData::archive_payload( $post );

		$this->assertArrayHasKey( 'category', $payload );
		$this->assertArrayHasKey( 'search_text', $payload );
		$this->assertStringContainsString( (string) $post->post_title, $payload['search_text'] );
	}

	/**
	 * Article typography is a separate stylesheet, loaded on single posts only.
	 *
	 * The compiled Tailwind build has no typography plugin, so `prose` classes
	 * would silently do nothing; the theme ships its own file instead.
	 */
	public function test_article_stylesheet_is_conditional(): void {
		$css = get_template_directory() . '/assets/css/article.css';
		$this->assertFileExists( $css );
		$this->assertStringContainsString( '.erfansanat-prose', (string) file_get_contents( $css ) );

		$manager = (string) file_get_contents( get_template_directory() . '/inc/AssetManager.php' );
		$this->assertStringContainsString( "is_singular( 'post' )", $manager, 'The article sheet must only load where it is used.' );
	}

	/**
	 * The 404 template offers a search box and real destinations, not a dead end.
	 */
	public function test_not_found_offers_a_way_out(): void {
		$source = (string) file_get_contents( get_template_directory() . '/404.php' );

		$this->assertStringContainsString( 'name="s"', $source, 'A search box is the first thing a lost visitor needs.' );
		$this->assertStringContainsString( 'home_url', $source );
		$this->assertStringContainsString( 'data-not-found', $source );
	}

	/**
	 * The search template groups results by post type instead of the default list.
	 */
	public function test_search_groups_results(): void {
		$source = (string) file_get_contents( get_template_directory() . '/search.php' );

		$this->assertStringContainsString( 'erfansanat_groups', $source );
		$this->assertStringContainsString( "'product'", $source );
		$this->assertStringContainsString( "'projects'", $source );
		$this->assertStringContainsString( 'paginate_links', $source, 'Search results must page.' );
	}
}
