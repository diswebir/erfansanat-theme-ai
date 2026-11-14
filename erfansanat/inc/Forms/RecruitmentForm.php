<?php
/**
 * Recruitment form handler (P08-T015).
 *
 * Job application pipeline: whitelisted organizational units (R&D / project /
 * accounting / IT), optional resume file with strict extension allowlist and
 * size cap, sanitized payload stored as an unread es_form_entry.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Core\Ajax;

defined( 'ABSPATH' ) || exit;

final class RecruitmentForm {

	private static bool $booted = false;

	/** Resume upload cap (AC-03): 2 MB. */
	public const MAX_RESUME_SIZE = 2097152;

	/** Allowed resume file extensions (AC-02). */
	public const RESUME_EXTENSIONS = array( 'pdf', 'doc', 'docx', 'rtf', 'txt' );

	/** Reference organizational units (AC-01) — keys from the recruitment tabs. */
	public const UNITS = array(
		'rnd'        => 'تحقیق و توسعه (R&D)',
		'project'    => 'پروژه و خدمات پس از فروش',
		'accounting' => 'حسابداری و مالی',
		'it'         => 'فناوری اطلاعات (IT) و دیجیتال',
	);

	/** Reference experience buckets. */
	public const EXPERIENCES = array( '1-3', '3-5', '5+', 'junior' );

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		if ( did_action( 'init' ) ) {
			self::register_endpoint();
		} else {
			add_action( 'init', array( __CLASS__, 'register_endpoint' ), 20 );
		}
	}

	public static function register_endpoint(): void {
		Ajax::register(
			'recruitment_submit',
			array( __CLASS__, 'ajax_submit' ),
			array(
				'public' => true,
				'inputs' => array(
					'es_rf_name'       => array( 'type' => 'text', 'default' => '' ),
					'es_rf_phone'      => array( 'type' => 'text', 'default' => '' ),
					'es_rf_unit'       => array( 'type' => 'text', 'default' => '' ),
					'es_rf_experience' => array( 'type' => 'text', 'default' => '' ),
					'es_rf_background' => array( 'type' => 'text', 'default' => '' ),
					'es_rf_hp'         => array( 'type' => 'text', 'default' => '' ),
					'page'             => array( 'type' => 'text', 'default' => '' ),
				),
			)
		);
	}

	/**
	 * Endpoint callback → submit(); resume arrives via $_FILES['es_rf_resume'].
	 *
	 * @param array<string,mixed> $input Whitelisted input.
	 * @return array<string,mixed>
	 */
	public static function ajax_submit( array $input ): array {
		return self::submit( $input, isset( $_FILES['es_rf_resume'] ) && is_array( $_FILES['es_rf_resume'] ) ? $_FILES['es_rf_resume'] : array(), array( 'page' => $input['page'] ?? '' ) );
	}

	/**
	 * Validate + store an optional resume upload (AC-02 extension allowlist,
	 * AC-03 size cap). Returns absolute path or WP_Error.
	 *
	 * @param array<string,mixed>|array<string,string> $file $_FILES element.
	 * @return array{name:string,path:string,size:int}|WP_Error
	 */
	public static function handle_resume( array $file ) {
		if ( empty( $file['name'] ) || empty( $file['tmp_name'] ) ) {
			return new \WP_Error( 'es_recruit_no_file', 'فایل رزومه قابل خواندن نیست.' );
		}
		if ( (int) $file['size'] > self::MAX_RESUME_SIZE ) {
			return new \WP_Error( 'es_recruit_too_big', 'حجم فایل رزومه باید کمتر از ۲ مگابایت باشد.' );
		}
		$ext = strtolower( (string) pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) );
		if ( ! in_array( $ext, self::RESUME_EXTENSIONS, true ) ) {
			return new \WP_Error( 'es_recruit_bad_ext', 'پسوند فایل مجاز نیست؛ فقط PDF/DOC/DOCX/RTF/TXT.' );
		}
		$uploads = wp_upload_dir();
		if ( ! empty( $uploads['error'] ) ) {
			return new \WP_Error( 'es_recruit_uploads', 'مسیر آپلود در دسترس نیست.' );
		}
		$dir = trailingslashit( $uploads['basedir'] ) . 'es-resumes';
		if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
			return new \WP_Error( 'es_recruit_uploads', 'ساخت پوشه رزومه‌ها ناموفق بود.' );
		}
		$safe  = strtolower( (string) wp_basename( sanitize_file_name( (string) $file['name'] ) ) );
		$safe  = preg_replace( '/[^a-z0-9._-]/', '-', $safe ) ?? 'resume.' . $ext;
		$token = strtolower( wp_generate_password( 12, false, false ) );
		$final = $dir . '/' . $token . '-' . $safe;
		if ( ! move_uploaded_file( (string) $file['tmp_name'], $final ) && ! @copy( (string) $file['tmp_name'], $final ) ) {
			return new \WP_Error( 'es_recruit_move', 'ذخیره فایل ناموفق بود.' );
		}
		// Second line of defence: verify what landed on disk has an allowed extension.
		if ( strtolower( pathinfo( $final, PATHINFO_EXTENSION ) ) !== $ext || ! in_array( strtolower( pathinfo( $final, PATHINFO_EXTENSION ) ), self::RESUME_EXTENSIONS, true ) ) {
			wp_delete_file( $final );
			return new \WP_Error( 'es_recruit_bad_ext', 'پسوند فایل مجاز نیست؛ فقط PDF/DOC/DOCX/RTF/TXT.' );
		}
		return array(
			'name' => (string) $file['name'],
			'path' => $final,
			'size' => (int) $file['size'],
		);
	}

	/**
	 * Full pipeline: honeypot → unit/experience whitelists → schema validate →
	 * resume handling → store as unread recruitment entry (SC-01).
	 *
	 * @param array<string,mixed> $payload Raw payload.
	 * @param array<string,mixed> $file    $_FILES element (optional resume).
	 * @param array<string,mixed> $context Optional context.
	 * @return array{entry:int,message:string}|WP_Error
	 */
	public static function submit( array $payload, array $file = array(), array $context = array() ) {
		if ( isset( $payload['es_rf_hp'] ) && '' !== trim( (string) $payload['es_rf_hp'] ) ) {
			return new \WP_Error( 'es_form_honeypot', 'ارسال درخواست ممکن نیست. لطفاً صفحه را دوباره بارگذاری کنید.' );
		}
		$unit = (string) ( $payload['es_rf_unit'] ?? '' );
		if ( ! isset( self::UNITS[ $unit ] ) ) {
			return new \WP_Error( 'es_recruit_bad_unit', 'واحد سازمانی معتبر نیست.' );
		}
		$experience = (string) ( $payload['es_rf_experience'] ?? '' );
		if ( '' !== $experience && ! in_array( $experience, self::EXPERIENCES, true ) ) {
			return new \WP_Error( 'es_recruit_bad_experience', 'میزان سابقه معتبر نیست.' );
		}
		$data = Schema::validate(
			'recruitment',
			array(
				'name'       => $payload['es_rf_name'] ?? '',
				'phone'      => $payload['es_rf_phone'] ?? '',
				'background' => $payload['es_rf_background'] ?? '',
			)
		);
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$data['unit']       = $unit;
		$data['unit_label'] = self::UNITS[ $unit ];
		$data['experience'] = $experience;
		$entry_id           = Entries::store( 'recruitment', $data, $context );
		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}
		$resume_meta = array();
		if ( is_array( $file ) && ! empty( $file['name'] ) ) {
			$saved = self::handle_resume( $file );
			if ( is_wp_error( $saved ) ) {
				wp_delete_post( (int) $entry_id, true ); // AC-02/AC-03: no half-entries.
				return $saved;
			}
			$resume_meta = array(
				'resume_name' => (string) $saved['name'],
				'resume_path' => (string) $saved['path'],
				'resume_size' => (int) $saved['size'],
			);
		}
		if ( $resume_meta ) {
			update_post_meta( (int) $entry_id, '_es_entry_meta', array_merge( (array) get_post_meta( (int) $entry_id, '_es_entry_meta', true ), $resume_meta ) );
		}
		return array(
			'entry'   => (int) $entry_id,
			'message' => 'درخواست همکاری شما ثبت شد؛ منابع انسانی با شما تماس می‌گیرد.',
		);
	}
}
