<?php
declare(strict_types=1);

/**
 * Task 8.2 acceptance: secure public forms and the admin inbox.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Forms\FormHandler;
use ErfanSanat\Theme\Forms\Inbox;
use ErfanSanat\Theme\Forms\InboxAdmin;
use PHPUnit\Framework\TestCase;

/**
 * Contracts for form validation, storage and the inbox screens.
 */
final class TaskP08T02Test extends TestCase {

	/**
	 * Messages created by a test, deleted afterwards.
	 *
	 * @var array<int, int>
	 */
	private array $created = array();

	/**
	 * Remove anything a test created.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		foreach ( $this->created as $id ) {
			wp_delete_post( $id, true );
		}
		$this->created = array();
		FormHandler::reset_limits();
		parent::tearDown();
	}

	/**
	 * The handler is registered for anonymous visitors.
	 */
	public function test_forms_are_reachable_without_an_account(): void {
		FormHandler::register();

		$this->assertNotFalse( has_action( 'admin_post_nopriv_' . FormHandler::ACTION ) );
		$this->assertNotFalse( has_action( 'admin_post_' . FormHandler::ACTION ) );
		$this->assertNotEmpty( FormHandler::forms() );
		$this->assertContains( 'contact', FormHandler::forms() );
	}

	/**
	 * The inbox post type is private and out of search.
	 *
	 * A message is a customer's private enquiry; it must never be reachable at a
	 * URL, through search or through REST.
	 */
	public function test_inbox_is_private(): void {
		$this->assertTrue( post_type_exists( Inbox::POST_TYPE ) );

		$object = get_post_type_object( Inbox::POST_TYPE );
		$this->assertNotNull( $object );
		$this->assertFalse( $object->public, 'A message must not be publicly queryable.' );
		$this->assertFalse( (bool) $object->publicly_queryable );
		$this->assertFalse( (bool) $object->show_in_rest, 'The REST API must not expose messages.' );
		$this->assertTrue( (bool) $object->exclude_from_search );
		$this->assertFalse( $object->rewrite, 'A message must not be reachable at any URL.' );
		$this->assertFalse( (bool) $object->query_var, 'A message must not be addressable by query var.' );
	}

	/**
	 * Stored content is plain text even if the caller passes markup.
	 *
	 * The handler sanitises first, but the store must not depend on that: one
	 * future caller that forgets would put script tags in the inbox.
	 */
	public function test_storage_strips_markup(): void {
		$id = Inbox::store(
			array(
				'_form'   => 'contact',
				'name'    => '<b>نام</b>',
				'phone'   => '09123456789',
				'email'   => 'a@example.com',
				'org'     => '<i>سازمان</i>',
				'message' => '<script>alert(1)</script> متن پیام',
			),
			'contact'
		);
		$this->assertGreaterThan( 0, $id );
		$this->created[] = $id;

		$this->assertStringNotContainsString( '<', (string) get_post_meta( $id, '_es_name', true ) );
		$this->assertStringNotContainsString( '<', (string) get_post_meta( $id, '_es_org', true ) );
		$this->assertStringNotContainsString( '<script', (string) get_post_meta( $id, '_es_body', true ) );
		$this->assertStringContainsString( 'متن پیام', (string) get_post_meta( $id, '_es_body', true ) );
	}

	/**
	 * A message is stored private, never published.
	 */
	public function test_stored_message_is_private(): void {
		$id = Inbox::store(
			array(
				'_form'   => 'contact',
				'name'    => 'آزمون',
				'phone'   => '09123456789',
				'message' => 'متن',
			),
			'contact'
		);
		$this->created[] = $id;

		$this->assertSame( 'private', get_post_status( $id ) );
	}

	/**
	 * No raw IP address is written to the database.
	 */
	public function test_only_a_hash_of_the_address_is_kept(): void {
		$_SERVER['REMOTE_ADDR'] = '203.0.113.77';

		$id = Inbox::store(
			array(
				'_form'   => 'contact',
				'name'    => 'آزمون',
				'phone'   => '09123456789',
				'message' => 'متن',
			),
			'contact'
		);
		$this->created[] = $id;

		$hash = (string) get_post_meta( $id, '_es_ip_hash', true );
		$this->assertNotSame( '', $hash );
		$this->assertStringNotContainsString( '203.0.113.77', $hash, 'The raw address must never be stored.' );

		unset( $_SERVER['REMOTE_ADDR'] );
	}

	/**
	 * Every outcome the redirect can carry has a message in the theme's domain.
	 */
	public function test_every_status_has_a_message(): void {
		$lookup = array(
			'ok'        => 'ok',
			'invalid'   => 'error',
			'slow'      => 'error',
			'throttled' => 'error',
			'failed'    => 'error',
		);

		foreach ( $lookup as $code => $state ) {
			$_GET[ FormHandler::STATUS_ARG ] = $code;
			$status                          = FormHandler::status();

			$this->assertSame( $state, $status['state'], $code );
			$this->assertNotSame( '', $status['message'], $code . ' must tell the visitor what happened.' );
		}

		$_GET[ FormHandler::STATUS_ARG ] = 'nonsense';
		$this->assertSame( '', FormHandler::status()['state'], 'An unknown status must show nothing.' );
		unset( $_GET[ FormHandler::STATUS_ARG ] );
	}

	/**
	 * The rate limit exists and is finite.
	 */
	public function test_rate_limit_is_enforced(): void {
		$_SERVER['REMOTE_ADDR'] = '198.51.100.9';
		FormHandler::reset_limits();

		$this->assertFalse( FormHandler::is_throttled(), 'A fresh visitor starts unthrottled.' );

		unset( $_SERVER['REMOTE_ADDR'] );
	}

	/**
	 * The form markup carries every field the handler checks.
	 */
	public function test_form_markup_supplies_the_security_fields(): void {
		$markup = (string) file_get_contents( get_template_directory() . '/template-parts/forms/part-contact-form.php' );

		foreach ( array( 'es_nonce', 'es_website', 'es_started', 'es_form', 'name="action"' ) as $field ) {
			$this->assertStringContainsString( $field, $markup, $field . ' is required for the submission to be trusted.' );
		}

		$this->assertStringContainsString( 'method="post"', $markup );
		$this->assertStringContainsString( 'admin-post.php', $markup, 'The form must post to the server, not to JavaScript.' );
	}

	/**
	 * The script cannot announce success on its own.
	 */
	public function test_script_does_not_fake_success(): void {
		$script = (string) file_get_contents( get_template_directory() . '/assets/js/main.js' );

		$this->assertStringContainsString( 'es_sent', $script, 'Success must come from the server response.' );
		$this->assertStringContainsString( 'event.preventDefault()', $script, 'An empty required field must be caught before posting.' );
	}

	/**
	 * The inbox screen is not the bare post editor.
	 */
	public function test_inbox_has_its_own_columns(): void {
		$columns = InboxAdmin::columns( array( 'cb' => '', 'title' => '', 'date' => '' ) );

		$this->assertArrayHasKey( 'es_sender', $columns );
		$this->assertArrayHasKey( 'es_contact', $columns );
		$this->assertArrayNotHasKey( 'title', $columns, 'A message title is not the useful column.' );
	}

	/**
	 * The notification destination is a real, filterable address.
	 */
	public function test_notification_recipient_is_configurable(): void {
		$default = Inbox::recipient();
		$this->assertNotSame( '', $default, 'The admin e-mail is the honest default until a gate decision is made.' );

		add_filter(
			'erfansanat_form_recipient',
			static function (): string {
				return 'sales@example.test';
			}
		);
		$this->assertSame( 'sales@example.test', Inbox::recipient() );
		remove_all_filters( 'erfansanat_form_recipient' );
	}

	/**
	 * Retention is off until an owner decides a window.
	 */
	public function test_retention_is_opt_in(): void {
		$this->assertSame( 0, Inbox::DEFAULT_RETENTION_DAYS, 'Silently deleting leads is worse than keeping them.' );
		$this->assertSame( 0, Inbox::purge(), 'With the default window the sweep must be a no-op.' );
	}

	/**
	 * The contact page template exists and reuses the shared form.
	 */
	public function test_contact_template_uses_the_shared_form(): void {
		$path = get_template_directory() . '/page-templates/contact.php';
		$this->assertFileExists( $path );

		$source = (string) file_get_contents( $path );
		$this->assertStringContainsString( 'part-contact-form.php', $source, 'One form definition, not a copy per page.' );
		$this->assertSame( 1, substr_count( $source, '<h1' ) );
	}
}
