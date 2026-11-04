<?php
/** Native WordPress menu-item metadata fields for mega-menu configuration. */

namespace ErfanSanat\Theme\Nav;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Adds fields directly to Appearance → Menus; it creates no custom admin page. */
final class MenuItemFields implements ModuleInterface {

	/** @var array<string,array{label:string,type:string}> */
	private const FIELDS = array(
		'panel_type'   => array( 'label' => 'نوع پنل مگامنو', 'type' => 'select' ),
		'column'       => array( 'label' => 'ستون والد', 'type' => 'number' ),
		'icon'         => array( 'label' => 'آیکون', 'type' => 'select' ),
		'label'        => array( 'label' => 'برچسب مگامنو', 'type' => 'text' ),
		'image'        => array( 'label' => 'شناسه تصویر پیوست', 'type' => 'number' ),
		'tab'          => array( 'label' => 'نمایش به‌عنوان تب', 'type' => 'checkbox' ),
		'column_width' => array( 'label' => 'عرض ستون', 'type' => 'select' ),
		'description'  => array( 'label' => 'توضیح کوتاه', 'type' => 'text' ),
	);

	public function register(): void {
		add_action( 'wp_nav_menu_item_custom_fields', array( self::class, 'render_fields' ), 10, 5 );
		add_action( 'wp_update_nav_menu_item', array( self::class, 'save_fields' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
	}

	/** Output only on the existing menu editor and only for the first two levels. */
	public static function render_fields( int $item_id, $item = null, int $depth = 0, $args = null, int $current_object_id = 0 ): void {
		if ( $depth > 1 || ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_theme_options' ) ) ) { return; }
		$values = array();
		foreach ( self::FIELDS as $key => $field ) {
			$value = function_exists( 'get_post_meta' ) ? get_post_meta( $item_id, self::meta_key( $key ), true ) : '';
			$values[ $key ] = is_scalar( $value ) ? (string) $value : '';
		}
		$prefix = 'menu-item-es-';
		?>
		<div class="es-nav-fields">
			<strong class="es-nav-fields__title">تنظیمات مگامنو</strong>
			<?php foreach ( self::FIELDS as $key => $field ) : ?>
				<?php $name = $prefix . str_replace( '_', '-', $key ) . '[' . absint( $item_id ) . ']'; ?>
				<p class="es-nav-fields__field">
					<label for="es-nav-<?php echo esc_attr( $key . '-' . $item_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
					<?php self::render_control( $key, $name, $field['type'], $values[ $key ], $item_id ); ?>
				</p>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/** Persist whitelisted menu-item values only after core's menu nonce/capability checks. */
	public static function save_fields( int $menu_id, int $menu_item_db_id, $args = array() ): void {
		if ( $menu_item_db_id < 1 || ! function_exists( 'current_user_can' ) || ! current_user_can( 'edit_theme_options' ) ) { return; }
		$posted = isset( $_POST ) && is_array( $_POST ) ? ( function_exists( 'wp_unslash' ) ? wp_unslash( $_POST ) : $_POST ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verify the core Appearance → Menus nonce below.
		$nonce = is_string( $posted['update-nav-menu-nonce'] ?? null ) ? $posted['update-nav-menu-nonce'] : '';
		if ( ! function_exists( 'wp_verify_nonce' ) || ! wp_verify_nonce( $nonce, 'update-nav_menu' ) ) { return; }

		foreach ( self::FIELDS as $key => $field ) {
			$post_key = 'menu-item-es-' . str_replace( '_', '-', $key );
			if ( ! isset( $posted[ $post_key ] ) || ! is_array( $posted[ $post_key ] ) ) { continue; }
			$raw = $posted[ $post_key ][ $menu_item_db_id ] ?? '';
			$value = self::sanitize_value( $key, $raw );
			if ( null === $value ) { continue; }
			if ( '' === $value || ( 'tab' === $key && '0' === $value ) ) {
				if ( function_exists( 'delete_post_meta' ) ) { delete_post_meta( $menu_item_db_id, self::meta_key( $key ) ); }
			} elseif ( function_exists( 'update_post_meta' ) ) {
				update_post_meta( $menu_item_db_id, self::meta_key( $key ), $value );
			}
		}
	}

	/** Enqueue the tiny stylesheet on the existing menu editor only. */
	public static function enqueue_assets( string $hook_suffix ): void {
		if ( 'nav-menus.php' !== $hook_suffix || ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_theme_options' ) ) ) { return; }
		$base = function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() : '';
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		wp_enqueue_style( 'es-nav-fields', $base . '/assets/admin/nav-fields.css', array(), $version );
	}

	/** Return the exact menu item meta key. */
	public static function meta_key( string $key ): string { return '_menu_item_es_' . $key; }

	/** Validate and normalize a posted field; null means reject. */
	public static function sanitize_value( string $key, $raw ) {
		if ( ! array_key_exists( $key, self::FIELDS ) || ! is_scalar( $raw ) ) { return null; }
		$value = trim( (string) $raw );
		switch ( $key ) {
			case 'panel_type': return in_array( $value, array( '', 'products', 'projects', 'services', 'corporate' ), true ) ? $value : null;
			case 'column': return '' === $value ? '' : (string) min( 12, max( 1, absint( $value ) ) );
			case 'icon': return '' === $value || isset( Icons::allowed()[ $value ] ) ? $value : null;
			case 'label':
			case 'description': return function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : trim( strip_tags( $value ) );
			case 'image':
				$id = absint( $value );
				if ( ! $id ) { return ''; }
				if ( function_exists( 'get_post' ) ) {
					$attachment = get_post( $id );
					if ( ! $attachment || 'attachment' !== ( $attachment->post_type ?? '' ) || ( function_exists( 'wp_attachment_is_image' ) && ! wp_attachment_is_image( $id ) ) ) { return null; }
				}
				return (string) $id;
			case 'tab': return in_array( $value, array( '1', 'true', 'on' ), true ) ? '1' : '0';
			case 'column_width': return in_array( $value, array( '', '1', '2', '3', '4', '5', '6' ), true ) ? $value : null;
		}
		return null;
	}

	private static function render_control( string $key, string $name, string $type, string $value, int $item_id ): void {
		$id = 'es-nav-' . $key . '-' . $item_id;
		if ( 'select' === $type ) {
			$options = array();
			if ( 'panel_type' === $key ) { $options = array( '' => 'پیش‌فرض', 'products' => 'محصولات', 'projects' => 'پروژه‌ها', 'services' => 'خدمات', 'corporate' => 'شرکت' ); }
			if ( 'icon' === $key ) { $options = array( '' => 'بدون آیکون' ) + array_combine( array_keys( Icons::allowed() ), array_keys( Icons::allowed() ) ); }
			if ( 'column_width' === $key ) { $options = array( '' => 'پیش‌فرض', '1' => '۱/۱۲', '2' => '۲/۱۲', '3' => '۳/۱۲', '4' => '۴/۱۲', '5' => '۵/۱۲', '6' => '۶/۱۲' ); }
			?>
			<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
				<?php foreach ( $options as $option => $label ) : ?><option value="<?php echo esc_attr( (string) $option ); ?>" <?php selected( $value, (string) $option ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?>
			</select>
			<?php
		} elseif ( 'checkbox' === $type ) {
			?>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0"><label class="es-nav-fields__check"><input id="<?php echo esc_attr( $id ); ?>" type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( '1' === $value ); ?>> این آیتم تب باشد</label>
			<?php
		} else {
			$input_type = 'number' === $type ? 'number' : 'text';
			$attrs = 'column' === $key ? ' min="1" max="12" step="1"' : ( 'image' === $key ? ' min="0" step="1"' : '' );
			?>
			<input id="<?php echo esc_attr( $id ); ?>" type="<?php echo esc_attr( $input_type ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static attribute fragments only. ?>>
			<?php
		}
	}
}
