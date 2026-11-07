<?php
declare(strict_types=1);
/**
 * Task 3.3 acceptance: project, term, translation and view meta.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Admin\ContentMetaboxes;
use ErfanSanat\Theme\Admin\ProjectMetaboxes;
use ErfanSanat\Theme\Admin\TermMetaboxes;
use ErfanSanat\Theme\Data\LegacyMeta;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the remaining historical keys and their safety rules.
 */
final class TaskP03T03Test extends TestCase {

	/**
	 * Objects created by the test.
	 *
	 * @var array<int, int>
	 */
	private array $posts = array();

	/**
	 * Terms created by the test.
	 *
	 * @var array<int, int>
	 */
	private array $terms = array();

	/**
	 * Create a project fixture.
	 *
	 * @return int
	 */
	private function make_project(): int {
		$id = wp_insert_post(
			array(
				'post_type'   => 'projects',
				'post_status' => 'draft',
				'post_title'  => 'es-test-project',
			)
		);
		$this->assertIsInt( $id );
		$this->posts[] = (int) $id;
		return (int) $id;
	}

	/**
	 * Create an attachment fixture.
	 *
	 * @param string $title Title.
	 * @return int
	 */
	private function make_attachment( string $title ): int {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$file = get_template_directory() . '/assets/images/ese-logo.svg';
		$id   = wp_insert_attachment(
			array(
				'post_title'     => $title,
				'post_mime_type' => 'image/svg+xml',
				'post_status'    => 'inherit',
			),
			$file
		);
		$this->assertIsInt( $id );
		wp_update_attachment_metadata( (int) $id, wp_generate_attachment_metadata( (int) $id, $file ) );
		$this->posts[] = (int) $id;
		return (int) $id;
	}

	/**
	 * Clean up.
	 */
	protected function tearDown(): void {
		foreach ( $this->posts as $id ) {
			wp_delete_post( $id, true );
		}
		foreach ( $this->terms as $term_id ) {
			wp_delete_term( $term_id, 'product_cat' );
		}
		$this->posts = array();
		$this->terms = array();
		parent::tearDown();
	}

	/**
	 * The five gallery slots keep their exact historical names.
	 */
	public function testProjectImageKeysKeepTheirHistoricalSpelling(): void {
		$this->assertSame(
			array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image' ),
			LegacyMeta::project_image_slots(),
			'The misspelled `foure-image` must never be "fixed".'
		);

		$project = $this->make_project();
		$images  = array();
		for ( $i = 1; $i <= 6; $i++ ) {
			$images[] = $this->make_attachment( 'es-project-img-' . $i );
		}

		ProjectMetaboxes::persist( $project, array( 'gallery' => implode( ',', $images ) ) );

		foreach ( array_slice( $images, 0, 5 ) as $index => $id ) {
			$this->assertSame(
				(string) $id,
				get_post_meta( $project, LegacyMeta::project_image_slots()[ $index ], true ),
				'Slot ' . $index . ' must hold image ' . $id
			);
		}
		$this->assertSame( array_slice( $images, 0, 5 ), LegacyMeta::project_gallery( $project ), 'The sixth image is not invented into a new key.' );

		// Shrinking the gallery deletes the trailing slots instead of leaving stale ids.
		ProjectMetaboxes::persist( $project, array( 'gallery' => (string) $images[0] ) );
		$this->assertSame( array( $images[0] ), LegacyMeta::project_gallery( $project ) );
		$this->assertFalse( metadata_exists( 'post', $project, 'foure-image' ) );
	}

	/**
	 * Related products keep the exact key and a JSON array of ids.
	 */
	public function testRelatedProductsRoundTrip(): void {
		$project = $this->make_project();
		ProjectMetaboxes::persist(
			$project,
			array(
				'mortabet_mahsole_2' => '51,52,52,-9',
				'finish-pro-time'    => '1404/01/01',
			)
		);

		$this->assertSame( array( 51, 52 ), LegacyMeta::read_ids( $project, 'mortabet_mahsole_2' ) );
		$raw = (string) get_post_meta( $project, 'mortabet_mahsole_2', true );
		$this->assertSame( '[51,52]', $raw, 'The stored shape is a JSON id array.' );
		$this->assertSame( '1404/01/01', get_post_meta( $project, 'finish-pro-time', true ) );

		ProjectMetaboxes::persist( $project, array( 'mortabet_mahsole_2' => '' ) );
		$this->assertFalse( metadata_exists( 'post', $project, 'mortabet_mahsole_2' ) );
	}

	/**
	 * Jalali dates are normalised but never invented.
	 */
	public function testJalaliDatesAreNormalisedOrDropped(): void {
		$project = $this->make_project();

		ProjectMetaboxes::persist( $project, array( 'finish-pro-time' => '۱۴۰۴/۱/۵' ) );
		$this->assertSame( '1404/01/05', get_post_meta( $project, 'finish-pro-time', true ), 'Persian digits become a canonical value.' );

		ProjectMetaboxes::persist( $project, array( 'finish-pro-time' => '1404-02-30' ) );
		$this->assertSame( '1404/02/30', get_post_meta( $project, 'finish-pro-time', true ) );

		foreach ( array( 'yesterday', '9999/01/01', '1404/13/01', '1404/00/10' ) as $bad ) {
			ProjectMetaboxes::persist( $project, array( 'finish-pro-time' => $bad ) );
			$this->assertFalse( metadata_exists( 'post', $project, 'finish-pro-time' ), $bad . ' must be rejected.' );
		}

		$this->assertSame( '', ProjectMetaboxes::normalise_jalali( '' ) );
		$this->assertSame( '1404/01/01', ProjectMetaboxes::normalise_jalali( ' 1404/01/01 ' ) );
	}

	/**
	 * Product category thumbnail and FAQ round-trip.
	 */
	public function testTermMetaRoundTrip(): void {
		$thumbnail = $this->make_attachment( 'es-term-thumb' );
		$term      = wp_insert_term( 'es-test-cat-' . wp_rand( 1000, 9999 ), 'product_cat' );
		$this->assertIsArray( $term );
		$term_id = (int) $term['term_id'];
		$this->terms[] = $term_id;

		TermMetaboxes::persist(
			$term_id,
			array(
				'thumbnail' => (string) $thumbnail,
				'faq'       => array(
					array(
						'name_question'   => 'این دسته مناسب کجاست؟',
						'answer_question' => 'میادین و بلوارها.',
					),
					array(
						'name_question'   => '',
						'answer_question' => '',
					),
				),
			)
		);

		$this->assertSame( $thumbnail, (int) get_term_meta( $term_id, 'thumbnail', true ) );
		$faq = TermMetaboxes::term_faq( $term_id );
		$this->assertCount( 1, $faq, 'An empty row is not stored.' );
		$this->assertSame( 'این دسته مناسب کجاست؟', $faq[0]['name_question'] );
		$this->assertSame( 'میادین و بلوارها.', $faq[0]['answer_question'] );

		// A non-attachment id clears the thumbnail.
		TermMetaboxes::persist( $term_id, array( 'thumbnail' => (string) $project_that_is_not_an_attachment = $this->make_project() ) );
		$this->assertFalse( metadata_exists( 'term', $term_id, 'thumbnail' ) );
	}

	/**
	 * hreflang triplets and the shared key round-trip on every content type.
	 */
	public function testHreflangWorksOnPostsAndTerms(): void {
		$ids = array();
		foreach ( array( 'post', 'page', 'product', 'projects' ) as $type ) {
			$id = wp_insert_post(
				array(
					'post_type'   => $type,
					'post_status' => 'draft',
					'post_title'  => 'es-href-' . $type,
				)
			);
			$this->assertIsInt( $id );
			$ids[ $type ] = (int) $id;
			$this->posts[] = (int) $id;
		}

		foreach ( $ids as $type => $id ) {
			ContentMetaboxes::persist(
				$id,
				array(
					'hrefmeta_fa'    => 'https://erfansanat.test/fa/',
					'hrefmeta_en'    => 'https://erfansanat.test/en/',
					'hrefmeta_ar'    => 'https://erfansanat.test/ar/',
					'same_key_hrefl' => 'lighting-42',
				)
			);
			$links = LegacyMeta::hreflang( $id );
			$this->assertSame( 'https://erfansanat.test/fa/', $links['fa'], $type . ' fa' );
			$this->assertSame( 'https://erfansanat.test/en/', $links['en'], $type . ' en' );
			$this->assertSame( 'https://erfansanat.test/ar/', $links['ar'], $type . ' ar' );
			$this->assertSame( 'lighting-42', get_post_meta( $id, 'same_key_hrefl', true ), $type . ' shared key' );
		}

		// Hostile URL and empty key are dropped.
		ContentMetaboxes::persist( $ids['post'], array( 'hrefmeta_en' => 'javascript:alert(1)', 'same_key_hrefl' => '' ) );
		$this->assertFalse( metadata_exists( 'post', $ids['post'], 'hrefmeta_en' ) );
		$this->assertFalse( metadata_exists( 'post', $ids['post'], 'same_key_hrefl' ) );
		$this->assertSame( '', LegacyMeta::hreflang( $ids['post'] )['en'] );
	}

	/**
	 * The view counter prefers number2-viewc and falls back to number-viewc.
	 */
	public function testViewCounterFallbackAndIncrement(): void {
		$post = wp_insert_post(
			array(
				'post_type'   => 'post',
				'post_status' => 'draft',
				'post_title'  => 'es-views',
			)
		);
		$this->assertIsInt( $post );
		$post_id = (int) $post;
		$this->posts[] = $post_id;

		$this->assertSame( 0, LegacyMeta::views( $post_id ), 'No counter means zero.' );

		update_post_meta( $post_id, 'number-viewc', '17' );
		$this->assertSame( 17, LegacyMeta::views( $post_id ), 'The legacy key is the fallback.' );
		$this->assertFalse( LegacyMeta::has_new_counter( $post_id ) );

		update_post_meta( $post_id, 'number2-viewc', '42' );
		$this->assertSame( 42, LegacyMeta::views( $post_id ), 'The new key wins when present.' );
		$this->assertTrue( LegacyMeta::has_new_counter( $post_id ) );

		update_post_meta( $post_id, 'number2-viewc', 'not-a-number' );
		$this->assertSame( 17, LegacyMeta::views( $post_id ), 'A corrupt new value falls back instead of returning 0.' );
		$this->assertFalse( LegacyMeta::has_new_counter( $post_id ) );

		$this->assertSame( 18, LegacyMeta::increment_views( $post_id ), 'Increment continues from the fallback value.' );
		$this->assertSame( '18', (string) get_post_meta( $post_id, 'number2-viewc', true ) );
		$this->assertSame( '17', (string) get_post_meta( $post_id, 'number-viewc', true ), 'The legacy key is never rewritten.' );

		ContentMetaboxes::persist( $post_id, array( 'number2-viewc' => '-5' ) );
		$this->assertSame( 0, LegacyMeta::views( $post_id ), 'Negative input clamps to zero.' );
	}

	/**
	 * An editor looking at their own content does not inflate the counter.
	 */
	public function testRecordViewIgnoresEditors(): void {
		$post = wp_insert_post(
			array(
				'post_type'   => 'post',
				'post_status' => 'draft',
				'post_title'  => 'es-record-view',
			)
		);
		$this->assertIsInt( $post );
		$post_id = (int) $post;
		$this->posts[] = $post_id;

		wp_set_current_user( 1 );
		$this->assertSame( 0, ContentMetaboxes::record_view( $post_id ) );
		$this->assertSame( 0, LegacyMeta::views( $post_id ), 'An editor view is not counted.' );

		wp_set_current_user( 0 );
		$this->assertSame( 1, ContentMetaboxes::record_view( $post_id ) );
		$this->assertSame( 2, ContentMetaboxes::record_view( $post_id ), 'Each anonymous view adds one.' );
		$this->assertSame( 2, LegacyMeta::views( $post_id ) );
	}

	/**
	 * Saving the theme's boxes must not delete other plugins' meta.
	 */
	public function testForeignMetaSurvivesSavingOurBoxes(): void {
		$product = wp_insert_post(
			array(
				'post_type'   => 'product',
				'post_status' => 'draft',
				'post_title'  => 'es-foreign-meta',
			)
		);
		$this->assertIsInt( $product );
		$product_id = (int) $product;
		$this->posts[] = $product_id;

		$foreign = array(
			'_rank_math_title'     => 'SEO title',
			'_seopress_titles_title' => 'SEOPress title',
			'_yoast_wpseo_metadesc' => 'Description',
			'_jet_engine_custom'   => '{"a":1}',
			'_product_attributes'  => array( 'pa_brand' => array( 'name' => 'برند' ) ),
		);
		foreach ( $foreign as $key => $value ) {
			update_post_meta( $product_id, $key, $value );
		}

		ContentMetaboxes::persist( $product_id, array( 'hrefmeta_en' => 'https://erfansanat.test/en/' ) );

		foreach ( $foreign as $key => $value ) {
			$this->assertTrue( metadata_exists( 'post', $product_id, $key ), $key . ' was deleted.' );
		}
		$this->assertSame( 'SEO title', get_post_meta( $product_id, '_rank_math_title', true ) );
		$this->assertSame( '{"a":1}', get_post_meta( $product_id, '_jet_engine_custom', true ) );
	}

	/**
	 * The boxes write nothing without a nonce or capability.
	 */
	public function testBoxesRefuseUnauthorisedWrites(): void {
		$project = $this->make_project();
		wp_set_current_user( 1 );

		$_POST = array();
		ContentMetaboxes::save( $project );
		ProjectMetaboxes::save( $project );
		$this->assertFalse( metadata_exists( 'post', $project, 'hrefmeta_en' ) );
		$this->assertFalse( metadata_exists( 'post', $project, 'finish-pro-time' ) );

		$_POST = array(
			'erfansanat_content_nonce' => wp_create_nonce( ContentMetaboxes::NONCE ),
			'erfansanat_content'       => array( 'hrefmeta_en' => 'https://erfansanat.test/en/' ),
		);
		$contributor = wp_insert_user(
			array(
				'user_login' => 'es_cm_' . wp_rand( 1000, 9999 ),
				'user_pass'  => wp_generate_password( 20 ),
				'user_email' => 'cm_' . wp_rand( 1000, 9999 ) . '@example.test',
				'role'       => 'contributor',
			)
		);
		wp_set_current_user( (int) $contributor );
		ContentMetaboxes::save( $project );
		$this->assertFalse( metadata_exists( 'post', $project, 'hrefmeta_en' ), 'A contributor cannot edit someone else\'s draft.' );

		wp_set_current_user( 1 );
		ContentMetaboxes::save( $project );
		$this->assertSame( 'https://erfansanat.test/en/', get_post_meta( $project, 'hrefmeta_en', true ) );

		$_POST = array();
		wp_set_current_user( 0 );
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user( (int) $contributor );
	}

	/**
	 * Term meta writes require their own nonce and a term capability.
	 */
	public function testTermMetaRefusesUnauthorisedWrites(): void {
		$term = wp_insert_term( 'es-term-guard-' . wp_rand( 1000, 9999 ), 'product_cat' );
		$this->assertIsArray( $term );
		$term_id = (int) $term['term_id'];
		$this->terms[] = $term_id;

		wp_set_current_user( 1 );
		$_POST = array( 'erfansanat_term' => array( 'faq' => array( array( 'name_question' => 'بله؟' ) ) ) );
		TermMetaboxes::save( $term_id );
		$this->assertFalse( metadata_exists( 'term', $term_id, 'faq' ), 'No nonce means no write.' );

		$_POST['erfansanat_term_nonce'] = wp_create_nonce( TermMetaboxes::NONCE );
		TermMetaboxes::save( $term_id );
		$this->assertCount( 1, TermMetaboxes::term_faq( $term_id ) );

		wp_set_current_user( 0 );
		$_POST = array();
	}

	/**
	 * Every historical key in the blueprint is reachable from the theme code.
	 */
	public function testEveryBlueprintKeyIsImplemented(): void {
		$product_keys = array(
			'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id', 'zireonvan',
			'english_titl', 'before-sale-c', 'true_sale', 'product-catalog-file', 'galerry_produ',
			'project-ids', 'usage_countr', 'product-feature', 'product-feature2_copy', 'usage_product', 'faq',
		);
		$project_keys = array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image', 'finish-pro-time', 'mortabet_mahsole_2' );
		$shared_keys  = array( 'hrefmeta_en', 'hrefmeta_ar', 'hrefmeta_fa', 'same_key_hrefl', 'number2-viewc' );
		$term_keys    = array( 'thumbnail', 'faq' );

		$source = '';
		foreach ( array(
			'/inc/Data/LegacyMeta.php',
			'/inc/Admin/ProductMetaboxes.php',
			'/inc/Admin/ProjectMetaboxes.php',
			'/inc/Admin/ContentMetaboxes.php',
			'/inc/Admin/TermMetaboxes.php',
			'/inc/ContentTypes.php',
		) as $file ) {
			$source .= (string) file_get_contents( get_template_directory() . $file );
		}

		foreach ( array_merge( $product_keys, $project_keys, $shared_keys, $term_keys ) as $key ) {
			$this->assertStringContainsString( $key, $source, $key . ' is missing from the implementation.' );
		}
	}

	/**
	 * The theme does not print competing hreflang tags.
	 */
	public function testThemeDoesNotPrintAlternateTags(): void {
		$files = array();
		foreach ( array( 'header.php', 'footer.php', 'inc/Frontend/SiteParts.php', 'inc/ContentTypes.php' ) as $file ) {
			$files[] = (string) file_get_contents( get_template_directory() . '/' . $file );
		}
		$source = implode( "\n", $files );
		$this->assertStringNotContainsString( 'rel="alternate"', $source );
		$this->assertStringNotContainsString( 'hreflang=', $source );
	}
}
