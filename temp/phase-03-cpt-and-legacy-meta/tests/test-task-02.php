<?php
declare(strict_types=1);
/**
 * Task 3.2 acceptance: product meta keys, formats and sale modes.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;
use ErfanSanat\Theme\Data\LegacyMeta;
use PHPUnit\Framework\TestCase;

/**
 * Exercises every documented product key and the raw formats it stores.
 */
final class TaskP03T02Test extends TestCase {

	/**
	 * Products created by the test.
	 *
	 * @var array<int, int>
	 */
	private array $created = array();

	/**
	 * Create a draft product.
	 *
	 * @return int
	 */
	private function make_product(): int {
		$id = wp_insert_post(
			array(
				'post_type'   => 'product',
				'post_status' => 'draft',
				'post_title'  => 'es-test-product',
			)
		);
		$this->assertIsInt( $id );
		$this->created[] = (int) $id;
		return (int) $id;
	}

	/**
	 * Create an attachment with a real file.
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
		$this->created[] = (int) $id;
		return (int) $id;
	}

	/**
	 * Remove created posts.
	 */
	protected function tearDown(): void {
		foreach ( $this->created as $id ) {
			wp_delete_post( $id, true );
		}
		$this->created = array();
		parent::tearDown();
	}

	/**
	 * Every documented key is read and written without changing its spelling.
	 */
	public function testAllDocumentedKeysRoundTrip(): void {
		$id = $this->make_product();
		$payload = array(
			'gs1_id'        => '06261010123456',
			'national_code' => '1234567890123',
			'hs_id'         => '9405.40',
			'isic_id'       => '27400',
			'cpc_id'        => '46510',
			'product_id'    => 'ES-1001',
			'zireonvan'     => 'زیرعنوان آزمایشی',
			'english_titl'  => 'Urban Lighting Element',
			'before-sale-c' => 'اطلاعیه قبل از خرید',
			'true_sale'     => 'false',
		);
		ProductMetaboxes::persist( $id, $payload );

		foreach ( array_keys( ProductMetaboxes::identifier_fields() ) as $key ) {
			$this->assertSame( $payload[ $key ], get_post_meta( $id, $key, true ), $key . ' must round-trip verbatim.' );
		}
		$this->assertSame( $payload['zireonvan'], LegacyMeta::text( $id, 'zireonvan' ) );
		$this->assertSame( 'Urban Lighting Element', LegacyMeta::text( $id, 'english_titl' ) );
		$this->assertSame( 'اطلاعیه قبل از خرید', LegacyMeta::text( $id, 'before-sale-c' ) );
		$this->assertSame( 'false', get_post_meta( $id, 'true_sale', true ) );
		$this->assertFalse( LegacyMeta::is_direct_sale( $id ) );
	}

	/**
	 * JSON, serialised, comma and plain values all survive a read.
	 */
	public function testRawFormatsAreDetectedAndPreserved(): void {
		$id = $this->make_product();

		update_post_meta( $id, 'project-ids', wp_json_encode( array( 11, 12 ) ) );
		$this->assertSame( 'json', LegacyMeta::detect_format( get_post_meta( $id, 'project-ids', true ) ) );
		$this->assertSame( array( 11, 12 ), LegacyMeta::read_ids( $id, 'project-ids' ) );

		update_post_meta( $id, 'mortabet_mahsole_2', maybe_serialize( array( 21, 22, 23 ) ) );
		$this->assertSame( 'serialized', LegacyMeta::detect_format( get_post_meta( $id, 'mortabet_mahsole_2', true ) ) );
		$this->assertSame( array( 21, 22, 23 ), LegacyMeta::read_ids( $id, 'mortabet_mahsole_2' ) );

		update_post_meta( $id, 'usage_countr', '31, 32,32 , 33' );
		$this->assertSame( 'comma', LegacyMeta::detect_format( get_post_meta( $id, 'usage_countr', true ) ) );
		$this->assertSame( array( 31, 32, 33 ), LegacyMeta::read_ids( $id, 'usage_countr' ), 'Duplicates collapse, order is kept.' );

		update_post_meta( $id, 'usage_countr', 'oops, not-an-id, 34' );
		$this->assertSame( array( 34 ), LegacyMeta::read_ids( $id, 'usage_countr' ) );

		update_post_meta( $id, 'usage_countr', '' );
		$this->assertSame( 'empty', LegacyMeta::detect_format( '' ) );
		$this->assertSame( array(), LegacyMeta::read_ids( $id, 'usage_countr' ) );
	}

	/**
	 * A repeater keeps its JetEngine shape and drops empty rows on write only.
	 */
	public function testRepeaterRoundTrip(): void {
		$id = $this->make_product();
		$raw = '{"item-0":{"add-new-feature":"ویژگی یک","vaule_of_variable":"۲۴ ماه"},"item-1":{"add-new-feature":"ویژگی دو","vaule_of_variable":"IP68"}}';
		update_post_meta( $id, 'product-feature2_copy', $raw );

		$rows = LegacyMeta::read_repeater( $id, 'product-feature2_copy' );
		$this->assertCount( 2, $rows );
		$this->assertSame( 'ویژگی یک', $rows[0]['add-new-feature'] );
		$this->assertSame( '۲۴ ماه', $rows[0]['vaule_of_variable'] );

		$encoded = LegacyMeta::encode_repeater( $rows );
		$decoded = json_decode( $encoded, true );
		$this->assertSame( 'ویژگی دو', $decoded['item-1']['add-new-feature'] );
		$this->assertSame( 'IP68', $decoded['item-1']['vaule_of_variable'] );

		ProductMetaboxes::persist(
			$id,
			array(
				'faq' => array(
					array(
						'name_question'   => 'سوال؟',
						'answer_question' => 'پاسخ.',
					),
					array(
						'name_question'   => '',
						'answer_question' => '',
					),
				),
			)
		);
		$faq = LegacyMeta::read_repeater( $id, 'faq' );
		$this->assertCount( 1, $faq, 'A fully empty row is not stored.' );
		$this->assertSame( 'سوال؟', $faq[0]['name_question'] );
	}

	/**
	 * Backslash escapes must survive the write path.
	 *
	 * update_metadata() unslashes its input; an unslashed JSON value turned
	 * every \uXXXX escape into literal text and corrupted Persian content.
	 */
	public function testJsonEscapesSurviveTheWritePath(): void {
		$id = $this->make_product();
		ProductMetaboxes::persist(
			$id,
			array(
				'faq' => array(
					array(
						'name_question'   => 'سوال با ؟ و ۲۴',
						'answer_question' => 'پاسخ آزمایشی',
					),
				),
			)
		);

		$raw = (string) get_post_meta( $id, 'faq', true );
		$this->assertStringNotContainsString( 'u0633u0648', $raw, 'Escapes were stripped before storage.' );
		$this->assertStringContainsString( '\\u', $raw, 'Storage keeps the escaped JSON shape.' );

		$rows = LegacyMeta::read_repeater( $id, 'faq' );
		$this->assertSame( 'سوال با ؟ و ۲۴', $rows[0]['name_question'] );
		$this->assertSame( 'پاسخ آزمایشی', $rows[0]['answer_question'] );
	}

	/**
	 * The gallery falls back through every legacy key, in a fixed order.
	 */
	public function testGalleryFallbackOrder(): void {
		$id   = $this->make_product();
		$one  = $this->make_attachment( 'es-img-1' );
		$two  = $this->make_attachment( 'es-img-2' );
		$three = $this->make_attachment( 'es-img-3' );
		$four = $this->make_attachment( 'es-img-4' );

		// Nothing set: empty gallery, no warning.
		$this->assertSame( array(), LegacyMeta::product_gallery( $id ) );

		// Only the WooCommerce key: still found.
		update_post_meta( $id, '_product_image_gallery', (string) $two );
		$this->assertSame( array( $two ), LegacyMeta::product_gallery( $id ) );

		// Legacy slots add the rest, deduplicated.
		update_post_meta( $id, 'gallery-filed', (string) $three );
		update_post_meta( $id, 'image-2', (string) $four );
		update_post_meta( $id, 'image-3', (string) $one );
		$this->assertSame( array( $two, $three, $four, $one ), LegacyMeta::product_gallery( $id ) );

		// `galerry_produ` wins the head of the list.
		update_post_meta( $id, 'galerry_produ', (string) $one );
		$this->assertSame( array( $one, $two, $three, $four ), LegacyMeta::product_gallery( $id ) );

		// A deleted attachment disappears instead of leaving a broken <img>.
		wp_delete_post( $four, true );
		$this->assertNotContains( $four, LegacyMeta::product_gallery( $id ) );
	}

	/**
	 * Saving the gallery writes both the legacy key and WooCommerce's copy.
	 */
	public function testGallerySaveWritesBothKeys(): void {
		$id  = $this->make_product();
		$one = $this->make_attachment( 'es-save-1' );
		$two = $this->make_attachment( 'es-save-2' );

		ProductMetaboxes::persist( $id, array( 'galerry_produ' => $one . ',' . $two . ',not-an-id,' . $one ) );
		$this->assertSame( $one . ',' . $two, get_post_meta( $id, 'galerry_produ', true ) );
		$this->assertSame( $one . ',' . $two, get_post_meta( $id, '_product_image_gallery', true ) );
		$this->assertSame( array( $one, $two ), LegacyMeta::product_gallery( $id ) );
	}

	/**
	 * `true_sale` separates a purchasable product from a quote-only one.
	 */
	public function testSaleModeIsDetectedFromLegacyStrings(): void {
		$id = $this->make_product();
		$this->assertTrue( LegacyMeta::is_direct_sale( $id ), 'A product without the key behaves as purchasable.' );

		foreach ( array( 'false', '0', 'no', 'off' ) as $value ) {
			update_post_meta( $id, 'true_sale', $value );
			$this->assertFalse( LegacyMeta::is_direct_sale( $id ), $value . ' must mean quote-only.' );
		}
		foreach ( array( 'true', '1', 'yes', 'on', 'TRUE' ) as $value ) {
			update_post_meta( $id, 'true_sale', $value );
			$this->assertTrue( LegacyMeta::is_direct_sale( $id ), $value . ' must mean purchasable.' );
		}

		ProductMetaboxes::persist( $id, array( 'true_sale' => 'false' ) );
		$this->assertSame( 'false', get_post_meta( $id, 'true_sale', true ), 'The stored value keeps the historical string shape.' );
	}

	/**
	 * An empty submission deletes the key instead of storing an empty string.
	 */
	public function testEmptyValuesDeleteTheKey(): void {
		$id = $this->make_product();
		ProductMetaboxes::persist( $id, array( 'gs1_id' => '12345' ) );
		$this->assertSame( '12345', get_post_meta( $id, 'gs1_id', true ) );

		ProductMetaboxes::persist( $id, array( 'gs1_id' => '   ' ) );
		$this->assertFalse( metadata_exists( 'post', $id, 'gs1_id' ) );

		ProductMetaboxes::persist( $id, array( 'product-catalog-file' => 'javascript:alert(1)' ) );
		$this->assertFalse( metadata_exists( 'post', $id, 'product-catalog-file' ), 'An invalid URL is not stored.' );

		ProductMetaboxes::persist( $id, array( 'product-catalog-file' => 'https://example.test/catalog.pdf' ) );
		$this->assertSame( 'https://example.test/catalog.pdf', get_post_meta( $id, 'product-catalog-file', true ) );
	}

	/**
	 * Keys that are not in the payload are never touched.
	 */
	public function testSavingOneFieldPreservesTheOthers(): void {
		$id = $this->make_product();
		update_post_meta( $id, 'project-ids', wp_json_encode( array( 501 ) ) );
		update_post_meta( $id, 'faq', '{"item-0":{"name_question":"پیشین","answer_question":"پاسخ"}}' );
		update_post_meta( $id, 'zireonvan', 'زیرعنوان پیشین' );

		ProductMetaboxes::persist( $id, array( 'gs1_id' => '999' ) );

		$this->assertSame( array( 501 ), LegacyMeta::read_ids( $id, 'project-ids' ) );
		$this->assertCount( 1, LegacyMeta::read_repeater( $id, 'faq' ) );
		$this->assertSame( 'زیرعنوان پیشین', get_post_meta( $id, 'zireonvan', true ) );
	}

	/**
	 * Hostile payloads cannot reach the database unescaped.
	 */
	public function testHostilePayloadIsSanitised(): void {
		$id = $this->make_product();
		ProductMetaboxes::persist(
			$id,
			array(
				'national_code' => '<script>alert(1)</script>123',
				'faq'           => array(
					array(
						'name_question'   => '<script>x</script>سوال',
						'answer_question' => 'پاسخ <b>تست</b>',
					),
					'not-a-row',
				),
				'project-ids'   => array( array( 'nested' => 7 ), '-3', '0' ),
			)
		);

		$this->assertStringNotContainsString( '<script>', (string) get_post_meta( $id, 'national_code', true ) );
		$faq = LegacyMeta::read_repeater( $id, 'faq' );
		$this->assertCount( 1, $faq );
		$this->assertStringNotContainsString( '<script>', $faq[0]['name_question'] );
		$this->assertSame( array( 7 ), LegacyMeta::read_ids( $id, 'project-ids' ), 'Only positive ids survive.' );
	}

	/**
	 * Quick Edit requires its own nonce and an edit capability.
	 */
	public function testQuickEditRefusesWithoutNonceOrCapability(): void {
		$id = $this->make_product();
		wp_set_current_user( 1 );

		$_POST = array();
		ProductMetaboxes::save_quick_edit( $id );
		$this->assertFalse( metadata_exists( 'post', $id, 'gs1_id' ), 'No nonce means no write.' );

		$_POST = array(
			'erfansanat_quick_nonce' => wp_create_nonce( ProductMetaboxes::NONCE ),
			'erfansanat_quick'       => array( 'gs1_id' => 'Q-1' ),
		);
		$contributor = wp_insert_user(
			array(
				'user_login' => 'es_quick_' . wp_rand( 1000, 9999 ),
				'user_pass'  => wp_generate_password( 20 ),
				'user_email' => 'quick_' . wp_rand( 1000, 9999 ) . '@example.test',
				'role'       => 'contributor',
			)
		);
		wp_set_current_user( (int) $contributor );
		ProductMetaboxes::save_quick_edit( $id );
		$this->assertFalse( metadata_exists( 'post', $id, 'gs1_id' ), 'Without edit_post there is no write.' );

		wp_set_current_user( 1 );
		ProductMetaboxes::save_quick_edit( $id );
		$this->assertSame( 'Q-1', get_post_meta( $id, 'gs1_id', true ) );

		$_POST = array();
		wp_set_current_user( 0 );
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user( (int) $contributor );
	}

	/**
	 * The meta box markup carries every group and its nonce.
	 */
	public function testMetaboxRendersEveryGroup(): void {
		$id = $this->make_product();
		wp_set_current_user( 1 );
		ob_start();
		ProductMetaboxes::render( get_post( $id ) );
		$html = (string) ob_get_clean();
		wp_set_current_user( 0 );

		$this->assertStringContainsString( 'erfansanat_product_nonce', $html );
		foreach ( array( 'gs1_id', 'national_code', 'english_titl', 'true_sale', 'galerry_produ', 'product-feature', 'product-feature2_copy', 'usage_product', 'faq' ) as $needle ) {
			$this->assertStringContainsString( $needle, $html, $needle . ' must be editable.' );
		}
		$this->assertStringContainsString( 'vaule_of_variable', $html, 'The misspelled sub-field must stay exactly as stored.' );
		$this->assertStringContainsString( 'project-ids', $html );
		$this->assertStringContainsString( 'usage_countr', $html );
	}
}
